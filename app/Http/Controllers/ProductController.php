<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();
        if($request->ajax()) {
            $nestedsearch = false;
            if(!is_null($request->get('variant'))) {
                $nestedsearch = true;
                $searchTerm = $request->get('variant');
                $products = $products->whereHas('variants', function($query) use ($searchTerm) {
                    $query->where('product_variant_one', $searchTerm)->orWhere('product_variant_two', $searchTerm)->orWhere('product_variant_three', $searchTerm);
                });
                $products = $products->with(['variants' => function ($query) use ($searchTerm) {
                    $query->where('product_variant_one', $searchTerm)->orWhere('product_variant_two', $searchTerm)->orWhere('product_variant_three', $searchTerm);
                }, 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
            }
            if(!is_null($request->get('price_from')) || !is_null($request->get('price_to'))) {
                $nestedsearch = true;
                $from = $request->get('price_from');
                $to = $request->get('price_to');
                if(!is_null($request->get('price_from')) && !is_null($request->get('price_to'))) {
                    $products = $products->whereHas('variants', function($query) use ($from, $to) {
                        $query->where('price', '>=', $from)->where('price', '<=', $to);
                    });
                    $products = $products->with(['variants' => function ($query) use ($from, $to) {
                        $query->where('price', '>=', $from)->where('price', '<=', $to);
                    }, 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
                } else if(!is_null($request->get('price_from'))) {
                    $products = $products->whereHas('variants', function($query) use ($from) {
                        $query->where('price', '>=', $from);
                    });
                    $products = $products->with(['variants' => function ($query) use ($from) {
                        $query->where('price', '>=', $from);
                    }, 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
                } else {
                    $products = $products->whereHas('variants', function($query) use ($to) {
                        $query->where('price', '<=', $to);
                    });
                    $products = $products->with(['variants' => function ($query) use ($to) {
                        $query->where('price', '<=', $to);
                    }, 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
                }
            }
            if(!$nestedsearch) $products = $products->with(['variants', 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
            if(!is_null($request->get('title'))) $products = $products->where('title', $request->get('title'));
            if(!is_null($request->get('date'))) $products = $products->whereDate('created_at', $request->get('date'));
            $data['products'] = $products->paginate(5); 
            return view('products.paginate', $data)->render();
        }
        $products = $products->with(['variants', 'variants.variantOne:id,variant', 'variants.variantTwo:id,variant', 'variants.variantThree:id,variant']);
        $data['products'] = $products->paginate(5);

        $data['variants'] = Variant::with('product_variants:id,variant_id,variant')->select('id', 'title')->get();
        return view('products.index', $data);
    }

    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    public function store(Request $request)
    {   
        $this->uporcreate($request);
        return redirect()->route('product.index')->with(['message' => 'Insert Successfully', 'alert-type' => 'success']);
    }

    public function uporcreate($request, $productId = null)
    {
        $input = $request->all();
        // dd($productId);
        $product = new Product(); //create product
        $product = (is_null($productId)) ? new Product() : Product::find($productId);
        $product->title = $input['product_name'];
        $product->sku = $input['product_sku'];
        $product->description = $input['product_description'];
        $product->save();
        $new_product_variants[] = ['variant' => null];
        $product_variants = $input['product_variant'];
        foreach($product_variants as $section) {
            foreach($section['value'] as $option_variant) {
                $product_variant = new ProductVariant(); //create product variant

                $product_variant->variant_id = $section['option'];
                $product_variant->product_id = $product->id;
                $product_variant->variant = $option_variant;
                $product_variant->save();
                $new_product_variants[] = ['variant' => $product_variant->variant, 'id' => $product_variant->id];
            }
        }
        foreach($input['product_preview'] as $input_variant_price) {
            $price_variants = array();
            $split_variants = explode('/', $input_variant_price['variant']);
            foreach($split_variants as $new_variant) {
                $chproduct_variant = $new_product_variants[array_search($new_variant, array_column($new_product_variants, 'variant'))];
                if(!is_null($chproduct_variant['variant'])) $price_variants[] = $chproduct_variant['id'];
            }
            $product_variant_prices = new ProductVariantPrice(); //create product variant price
            $product_variant_prices->product_variant_one = array_key_exists(0, $price_variants) ? $price_variants[0] : null;
            $product_variant_prices->product_variant_two = array_key_exists(1, $price_variants) ? $price_variants[1] : null;
            $product_variant_prices->product_variant_three = array_key_exists(2, $price_variants) ? $price_variants[2] : null;
            $product_variant_prices->price = $input_variant_price['price'];
            $product_variant_prices->stock = $input_variant_price['stock'];
            $product_variant_prices->product_id = $product->id;
            $product_variant_prices->save();
        }
    }

    public function edit($id)
    {
        $data['product'] = Product::with(['variants', 'product_variants'])->findorfail($id);
        $data['variants'] = Variant::select('id', 'title')->get();
        return view('products.edit', $data);
    }

    public function update(Request $request, $id)
    {
        ProductVariant::where('product_id', $id)->delete();
        $this->uporcreate($request, $id);
        return redirect()->route('product.index')->with(['message' => 'Insert Successfully', 'alert-type' => 'success']);

    }

    public function destroy(Product $product)
    {
        //
    }
}
