@extends('layouts.main')
@section('page-title')
    {{ __('Business') }}
@endsection
@section('title')
    {{ __('Business') }}
@endsection
@section('page-breadcrumb')
    {{ __('Business') }}
@endsection
@section('page-action')
    <div>
        @permission('business create')
            <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="xl" data-title="{{ __('Create New Business') }}"
                data-url="{{ route('business.create') }}" data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        @endpermission
        @permission('business manage')
            <a href="{{ route('business.grid.view') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Grid View') }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-layout-grid"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('#') }}</th>
                                    <th>{{ __('Business Logo') }}</th>
                                    <th>{{ __('Businesses') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Generate Date') }}</th>
                                    <th class="text-end">{{ __('Operations') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($business as $val)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>
                                            <div class="avatar">
                                                <img style="width: 55px;height: 55px;" class="rounded-circle img_users_fix_size"
                                                    src="{{ isset($val->logo) && !empty($val->logo) ? get_file($val->logo): asset('Modules/VCard/Resources/assets/custom/img/logo-placeholder-image-21.png') }}"
                                                    alt="">
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <a class="" style="min-width: 180px;"
                                                href="{{ route('business.edit', $val->id) }}"><b>{{ ucFirst($val->title) }}</b></a>
                                        </td>
                                        <td><span
                                                class="badge fix_badge @if ($val->status == 'locked') bg-danger @else bg-info @endif p-2 px-3 rounded">{{ ucFirst($val->status) }}</span>
                                        </td>
                                        @php
                                            $now = $val->created_at;
                                            $date=$now->format('Y-m-d');
                                            $time=$now->format('H:i:s');
                                        @endphp
                                        <td>{{ company_date_formate($date).' '.$time }}</td>
                                        <div class="row ">
                                            <td class="text-end">
                                                @if ($val->status != 'locked')
                                                    <div class="action-btn bg-secondary  ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                            data-link="{{ url('/cards/' . $val->slug) }}"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Click to copy card link') }}" onclick="copyToClipboard(this)">
                                                            <span class="text-white"> <i
                                                                    class="ti ti-copy text-white"></i></span></a>
                                                    </div>
                                                    @permission('card appointment calendar')
                                                        <div class="action-btn bg-primary  ms-2">
                                                            <a href="{{ route('appointment.calendar', ['business' => $val->id]) }}"
                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Appointment Calendar') }}">
                                                                <span class="text-white"> <i
                                                                        class="ti ti-calendar text-white"></i></span></a>
                                                        </div>
                                                    @endpermission
                                                    @permission('card contact manage')
                                                        <div class="action-btn bg-warning  ms-2">
                                                            <a href="{{ route('business.contacts.show', $val->id) }}"
                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Business Contacts') }}"> <span
                                                                    class="text-white"> <i
                                                                        class="ti ti-phone text-white"></i></span></a>
                                                        </div>
                                                    @endpermission
                                                    @permission('business edit')
                                                        <div class="action-btn bg-info  ms-2">
                                                            <a href="{{ route('business.edit', $val->id) }}"
                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Business Edit') }}"> <span
                                                                    class="text-white"> <i
                                                                        class="ti ti-edit text-white"></i></span></a>
                                                        </div>
                                                    @endpermission
                                                    <div class="action-btn bg-dark ms-2">
                                                        {{ Form::open(['route' => ['business.status', $val->id], 'class' => 'm-0']) }}
                                                        @method('POST')
                                                        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Business lock"
                                                            aria-label="Business lock"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can business lock. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $val->id }}"><i
                                                                class="ti ti-lock text-white text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                    @permission('business delete')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {{ Form::open(['route' => ['business.destroy', $val->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $val->id }}"><i
                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                            {!! Form::close() !!}
                                                        @endpermission
                                                    @else
                                                        <div class="action-btn bg-dark  ms-2">
                                                            {{ Form::open(['route' => ['business.status', $val->id], 'class' => 'm-0']) }}
                                                            @method('POST')
                                                            <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Business Unlock"
                                                                aria-label="Business Unlock"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can business unlock. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $val->id }}">
                                                                <i class="ti ti-lock-open"></i></a>

                                                            {!! Form::close() !!}
                                                        </div>
                                                @endif
                                            </td>
                                        </div>
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
@push('scripts')
    <script type="text/javascript">
         function copyToClipboard(element) {
            var value = $(element).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('{{ __('Success') }}', '{{ __('Link Copy on Clipboard') }}', 'success');
        }
    </script>
@endpush
