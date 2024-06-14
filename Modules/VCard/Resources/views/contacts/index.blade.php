@php
if($id)
{
    $currentBusiness=$id;
}
else {
    $currentBusiness=Modules\VCard\Entities\Business::currentBusiness();
}

@endphp
@extends('layouts.main')
@section('page-breadcrumb')
    {{ __('Contacts') }}
@endsection
@section('page-title')
    {{ __('Contacts') }}
@endsection
@section('title')
    {{ __('Contacts') }}
@endsection
@section('page-action')
    <div class="d-flex align-items-center justify-content-end gap-2">
        @if(!$businessData->isempty())
        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false" aria-expanded="false"
                    id="dropdownLanguage">
                    @foreach ($businessData as $key => $value)
                        <span
                            class="drp-text hide-mob text-primary">{{ $currentBusiness == $key ? Str::ucfirst($value) : '' }}</span>
                    @endforeach
                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                    aria-labelledby="dropdownLanguage">
                    @foreach ($businessData as $key => $business_val)
                    <a href="{{ route('business.current',$key) }}" class="dropdown-item">
                        <i class="@if ($currentBusiness == $key) ti ti-checks text-primary @endif "></i>
                        <span>{{ ucfirst($business_val) }}</span>
                    </a>
                @endforeach
                </div>
            </li>
        </ul>
        @endif
        <a href="#" class="btn btn-sm btn-primary export-btn csv" data-title="{{ __('Export') }}" data-toggle="tooltip"
            title="{{ __('Export') }}"><i class="ti ti-file-export"></i>
        </a>
        
        @if (URL::previous() == URL::current())
            <a href="{{ route('contacts.index') }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @else
            <a href="{{ url(URL::previous()) }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @endif
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table pc-dt-simple pc-dt-export " id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('Business Name') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Message') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th id="ignore">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts_details as $val)
                                    <tr>
                                        <td>{{ ucFirst($val->business_name) }}</td>
                                        <td>{{ ucFirst($val->name) }}</td>
                                        <td>{{ $val->email }}</td>
                                        <td>{{ $val->phone }}</td>
                                        <td style="white-space: normal;width: 500px;">{{ $val->message }}</td>
                                        @if ($val->status == 'pending')
                                            <td><span
                                                    class="badge bg-warning p-2 px-3 rounded">{{ ucFirst($val->status) }}</span>
                                            </td>
                                        @else
                                            <td><span
                                                    class="badge bg-success p-2 px-3 rounded">{{ ucFirst($val->status) }}</span>
                                            </td>
                                        @endif
                                        <div class="row ">
                                            <td class="">
                                                @permission('card contact add note')
                                                    <div class="action-btn bg-success  ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center cp_link"
                                                            data-toggle="modal" data-target="#commonModal"
                                                            data-ajax-popup="true" data-size="lg"
                                                            data-url="{{ route('contact.add-note', $val->id) }}"
                                                            data-title="{{ __('Add Note & Change Status') }}"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Add Note & Change Status') }}">
                                                            <span class="text-white"><i class="ti ti-note"></i></span></a>
                                                    </div>
                                                @endpermission
                                                @permission('card contact delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['contacts.destroy', $val->id], 'class' => 'm-0']) }}
                                                        <a class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $val->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endpermission
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
    <script src="https://rawgit.com/unconditional/jquery-table2excel/master/src/jquery.table2excel.js"></script>
    <script>
        const table = new simpleDatatables.DataTable(".pc-dt-export", {
            searchable: true,
            fixedheight: true,
            dom: 'Bfrtip',
        });
        $('.csv').on('click', function() {
            $('#ignore').remove();
            $(".pc-dt-export").table2excel({
                filename: "contactDetail"
            });
            setTimeout(function() {
                location.reload();
            }, 2000);
        });
    </script>
@endpush
