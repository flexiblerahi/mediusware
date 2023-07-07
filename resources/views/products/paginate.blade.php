<div class="card-body">
    <div class="table-response">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Variant</th>
                <th width="150px">Action</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{$product->id}}</td>
                        <td>{{$product->title}}<br> Created at : {{\Carbon\Carbon::parse($product->created_at)->format('d-M-Y')}}</td>
                        <td>{{\Illuminate\Support\Str::words($product->description, 5 ) }}</td>
                        <td>
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{$product->id}}">
                                @foreach ($product->variants as $product_variant)
                                    <dt class="col-sm-3 pb-0">
                                        @php
                                            $variantTitle = '';
                                            if(!is_null($product_variant->variantTwo)) $variantTitle .= $product_variant->variantTwo->variant;
                                            if(!is_null($product_variant->variantOne)) $variantTitle .= '/ '.$product_variant->variantOne->variant;
                                            if(!is_null($product_variant->variantThree)) $variantTitle .= '/ '.$product_variant->variantThree->variant;
                                        @endphp
                                        {{$variantTitle }}
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($product_variant->price, 2) }}</dt>
                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($product_variant->stock, 2) }}</dd>
                                        </dl>
                                    </dd>
                                @endforeach
                            </dl>
                            <button onclick="$('#variant{{$product->id}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="card-footer">
    <div class="row justify-content-between">
        <div class="col-md-6">
            <p>{{ 'Showing '.$products->firstItem(). ' to '.$products->lastItem(). ' out of '.$products->total()}}</p>
        </div>
        <div class="col-md-2">
            {{ $products->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
</div>