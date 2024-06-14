@extends('layouts.main')
@section('page-title')
    {{ __('Business') }}
@endsection
@section('page-breadcrumb')
    {{ __('Business') }}
@endsection
@section('page-action')
    <div>
        @permission('user manage')
            <a href="{{ route('business.index') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('List View') }}"
                class="btn btn-sm btn-primary btn-icon ">
                <i class="ti ti-list"></i>
            </a>
        @endpermission
        @permission('business create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="xl" data-title="{{ __('Create New Business') }}"
                data-url="{{ route('business.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        @endpermission
    </div>
@endsection
@php
    $logo = get_file('uploads/card_logo/');
@endphp
@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div id="loading-bar-spinner" class="spinner">
            <div class="spinner-icon"></div>
        </div>
        @foreach ($business as $val)
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <span
                                class="badge @if ($val->status == 'locked') bg-danger @else bg-info @endif p-2 px-3 rounded">{{ ucFirst($val->status) }}</span>
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="true">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end">
                                    @if ($val->status != 'locked')
                                        <a href="#" class="dropdown-item cp_link1"
                                            data-link="{{ url('/cards/' . $val->slug) }}">
                                            <i class="ti ti-copy"></i>
                                            <span>{{ __('Copy Business') }} </span>
                                        </a>
                                        @permission('card appointment calendar')
                                            <a href="{{ route('appointment.calendar', ['business' => $val->id]) }}"
                                                class="dropdown-item"><i class="ti ti-calendar"></i>
                                                <span>{{ __('Appointment Calendar') }}</span></a>
                                        @endpermission
                                        @permission('card contact manage')
                                            <a href="{{ route('business.contacts.show', $val->id) }}" class="dropdown-item"
                                                data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Business Contacts') }}"><i
                                                    class="ti ti-phone"></i>
                                                <span>{{ __('Business Contacts') }}</span></a>
                                        @endpermission
                                        @permission('business edit')
                                            <a href="{{ route('business.edit', $val->id) }}" class="dropdown-item">
                                                <i class="ti ti-edit "></i> <span>{{ __('Edit') }}</span></a>
                                        @endpermission
                                        {{ Form::open(['route' => ['business.status', $val->id], 'class' => 'm-0']) }}
                                        @method('POST')
                                        <a class="dropdown-item bs-pass-para show_confirm" title=""
                                            aria-label="Business lock" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can business lock. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $val->id }}"><i
                                                class="ti ti-lock"></i><span class="ms-1">{{ __('Lock') }}</span>
                                        </a>
                                        {!! Form::close() !!}
                                        @permission('business delete')
                                            {{ Form::open(['route' => ['business.destroy', $val->id], 'class' => 'm-0']) }}
                                            @method('DELETE')
                                            <a href="#!" class="dropdown-item bs-pass-para show_confirm"
                                                aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $val->id }}">
                                                <i class="ti ti-trash"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </a>
                                            {{ Form::close() }}
                                        @endpermission
                                    @else
                                        {{ Form::open(['route' => ['business.status', $val->id], 'class' => 'm-0']) }}
                                        @method('POST')
                                        <a class="dropdown-item bs-pass-para show_confirm" data-bs-toggle="tooltip"
                                            title="" data-bs-original-title="Business Unlock"
                                            aria-label="Business Unlock" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can business unlock. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $val->id }}">
                                            <i class="ti ti-lock-open"></i><span class="ms-1">{{ __('Unlock') }}</span></a>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body  text-center">
                        <img style="width: 120px;height: 120px;" class="rounded-circle img_users_fix_size"
                            src="{{ isset($val->logo) && !empty($val->logo) ? get_file($val->logo)   : asset('Modules/VCard/Resources/assets/custom/img/logo-placeholder-image-21.png') }}"
                            alt="">
                        <h4 class="mt-2"><a class="" href="{{ route('business.edit', $val->id) }}">{{ ucFirst($val->title) }}</a></h4>
                        <small>{{ $val->created_at }}</small>
                    </div>
                </div>
            </div>
        @endforeach
        @auth('web')
            @permission('business create')
                <div class="col-md-3 All">
                    <a href="#" class="btn-addnew-project " style="padding: 90px 10px;" data-ajax-popup="true" data-size="xl"
                        data-title="{{ __('Create New Business') }}" data-url="{{ route('business.create') }}">
                        <div class="bg-primary proj-add-icon">
                            <i class="ti ti-plus my-2"></i>
                        </div>
                        <h6 class="mt-4 mb-2">{{ __('New Business') }}</h6>
                        <p class="text-muted text-center">{{ __('Click here to Create New Business') }}</p>
                    </a>
                </div>
            @endpermission
        @endauth
    </div>
    <!-- [ Main Content ] end -->
@endsection
@push('scripts')
    <script type="text/javascript">
        $('.cp_link1').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            toastrs('{{ __('Success') }}', '{{ __('Link Copy on Clipboard') }}', 'success');
        });
    </script>
@endpush
