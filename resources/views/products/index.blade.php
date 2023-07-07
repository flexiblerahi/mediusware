@extends('layouts.app')
@section('content')
    @push('style')
        <link rel="stylesheet" href="{{asset('css/tagsinput.css')}}">
    @endpush
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>
    <div class="card">
        <div class="card-header">
            <form id="onquery" method="post" class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" id="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="variant" class="form-control">
                        <option value="">-- Select Please --</option>
                        @foreach ($variants as $variant)
                            @php
                                $product_variants = $variant->product_variants->unique(function ($item) {
                                        return $item['variant'];
                                    });
                            @endphp
                            <optgroup label="{{$variant->title}}">
                                @foreach ($product_variants as $product_variant)
                                    <option value="{{$product_variant->id}}">{{$product_variant->variant}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </form>
        </div>
        @push('page_js')
            <script>
                $('#onquery').on('submit', function (e) {
                    e.preventDefault(); 
                    const query = $(this).serialize();
                    $.ajax({
                        url: '{{route("product.index")}}',
                        method: 'get',
                        data: query,
                        success: function(response) {
                            // handle the response from the server
                            console.log('response :>> ', response);
                            $('#paginatetable').html(response);
                        },
                        error: function(xhr, status, error) {
                            // handle errors
                        }
                    });
                });
            </script>
        @endpush
        <div id="paginatetable">
            @include('products.paginate')
        </div>
    </div>

@endsection
