@extends('layouts.main')

@section('page-title')
    {{__('Manage Product')}}
@endsection

@section('page-breadcrumb')
   {{__('Product')}}
@endsection
@section('page-action')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="wp_product">
                            <thead>
                                <tr>
                                    <th>{{__('Product Image')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('SKU')}}</th>
                                    <th>{{__('Stock')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Type')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($wp_products)
                                @foreach ($wp_products as $wp_product)
                                    @php
                                        $nameValues = array_column($wp_product['categories'], 'name');
                                        $category_name = implode(',', $nameValues);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <a href="{{$wp_product['images'][0]['src']}}" target="_blank">
                                                    <img alt="Image placeholder" src="{{$wp_product['images'][0]['src']}}" class="wid-75 rounded me-3">
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $wp_product['name'] }}</td>
                                        <td>{{ !empty($wp_product['sku'])?$wp_product['sku']:'-' }}</td>
                                        <td>{{ $wp_product['stock_status'] }}</td>
                                        <td>{{ $wp_product['price'] }}</td>
                                        <td>{{ $category_name }}</td>
                                        <td>{{ $wp_product['type'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>No Products Found</tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
