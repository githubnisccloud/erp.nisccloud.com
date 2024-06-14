@extends('layouts.main')
@section('page-title')
    {{__('Course Order')}}
@endsection

@section('page-breadcrumb')
    {{ __('Course Order') }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive overflow_hidden">
                    <table class="table mb-0 pc-dt-simple" id="assets">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{__('Orders')}}</th>
                                <th scope="col" class="sort">{{__('Date')}}</th>
                                <th scope="col" class="sort">{{__('Name')}}</th>
                                <th scope="col" class="sort">{{__('Value')}}</th>
                                <th scope="col" class="sort">{{__('Payment Type')}}</th>
                                <th scope="col" class="sort">{{ __('Receipt') }}</th>
                                <th scope="col">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        @if(!empty($Course_orders) && count($Course_orders) > 0)
                            <tbody>
                                @foreach($Course_orders as $course_order)
                                    <tr>
                                        <td scope="row">
                                            @if(\Auth::user()->isAbleTo('course order show'))
                                                <a href="{{route('course_orders.show',$course_order->id)}}" class="btn btn-outline-primary">
                                                    <span class="btn-inner--text">{{$course_order->order_id}}</span>
                                                </a>
                                            @else
                                                <span class="btn-inner--text">{{$course_order->order_id}}</span>
                                            @endif
                                        </td>
                                        <td class="order">
                                            <span class="h6 text-sm font-weight-bold mb-0">{{ company_date_formate($course_order->created_at)}}</span>
                                        </td>
                                        <td>
                                            <span class="client">{{$course_order->name}}</span>
                                        </td>
                                        <td>
                                            <span class="value text-sm mb-0">{{ currency_format_with_sym($course_order->price)}}</span>
                                        </td>
                                        <td>
                                            <span class="taxes text-sm mb-0">{{$course_order->payment_type}}</span>
                                        </td>
                                        <td>
                                            @if(!empty($course_order->receipt))
                                            <a href="{{ get_file($course_order->receipt) }}" title="Invoice"
                                                target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            @else
                                             -
                                             @endif
                                        </td>
                                        <td>
                                            <div>
                                                <!-- Actions -->
                                                <div class="actions">
                                                    @if($course_order->payment_status == 'Pending' && $course_order->payment_type == 'Bank Transfer')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a  class="mx-3 btn btn-sm  align-items-center"
                                                                data-url="{{ route('course.bank.request.edit',$course_order->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title=""
                                                                data-title="{{ __('Payment Status') }}"
                                                                data-bs-original-title="{{ __('Payment Status') }}">
                                                                <i class="ti ti-caret-right text-white"></i>
                                                            </a>
                                                        </div>

                                                    @endif
                                                    @permission('course order show')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="{{route('course_orders.show',$course_order->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Details') }}"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                        </div>
                                                    @endpermission

                                                    @permission('course order delete')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['course_orders.destroy', $course_order->id]]) !!}
                                                                <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endpermission
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan="7">
                                        <div class="text-center">
                                            <i class="fas fa-folder-open text-primary" style="font-size: 48px;"></i>
                                            <h2>{{__('Opps')}}...</h2>
                                            <h6>{{__('No data Found')}}. </h6>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
