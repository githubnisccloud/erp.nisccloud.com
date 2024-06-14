@extends('layouts.main')

@section('page-title')
    {{__('Manage Tax')}}
@endsection

@section('page-breadcrumb')
   {{__('Tax')}}
@endsection
@section('page-action')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="wp_tax">
                            <thead>
                                <tr>
                                    <th>{{__('Tax Name')}}</th>
                                    <th>{{__('Rate %')}}</th>
                                    <th>{{__('Country')}}</th>
                                    <th>{{__('State')}}</th>
                                    <th>{{__('City')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wp_taxs as $wp_tax)
                                    <tr>
                                        <td>{{ !empty($wp_tax['name'])?$wp_tax['name']:'-' }}</td>
                                        <td>{{ !empty($wp_tax['rate'])?$wp_tax['rate']:'0.0' }}</td>
                                        <td>{{ !empty($wp_tax['country'])?$wp_tax['country']:'-' }}</td>
                                        <td>{{ !empty($wp_tax['state'])?$wp_tax['state']:'-' }}</td>
                                        <td>{{ !empty($wp_tax['city'])?$wp_tax['city']:'-' }}</td>
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
