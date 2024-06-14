@extends('layouts.main')

@section('page-title')
    {{__('Manage Category')}}
@endsection

@section('page-breadcrumb')
   {{__('Category')}}
@endsection
@section('page-action')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="wp_category">
                            <thead>
                                <tr>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Display type')}}</th>
                                    <th>{{__('Description')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wp_categorys as $wp_category)
                                    <tr>
                                        <td>
                                            <div>
                                                @if (!empty($wp_category['image']))
                                                    <a href="{{$wp_category['image']['src']}}" target="_blank">
                                                        <img alt="Image placeholder" src="{{$wp_category['image']['src']}}" class="rounded" style="width:70px; height:50px;">
                                                    </a>
                                                @else
                                                    <a href="{{asset('Modules/WordpressWoocommerce/Resources/assets/image/woocommerce.png')}}" target="_blank">
                                                        <img alt="Image placeholder" src="{{asset('Modules/WordpressWoocommerce/Resources/assets/image/woocommerce.png')}}" class="rounded" style="width:70px; height:50px;">
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $wp_category['name'] }}</td>
                                        <td>{{ $wp_category['display'] }}</td>
                                        <td>{{ !empty($wp_category['description'])?$wp_category['description']:'-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
