@extends('layouts.main')
@section('page-title')
    {{ __('Calendar') }}
@endsection
@section('page-breadcrumb')
    {{ __('Calender') }}
@endsection
@section('title')
    {{ __('Calendar') }}
@endsection
@section('page-action')
    <div>
        <a href="{{ route('appointment.index') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('List View') }}">
            <i class="ti ti-list-check"></i>
        </a>
        @if (URL::previous() == URL::current())
            <a href="{{ route('appointment.index') }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @else
            <a href="{{ url(URL::previous()) }}" class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Back') }}">
                <i class=" ti ti-arrow-back-up"></i> </a>
        @endif
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/VCard/Resources/assets/custom/css/main.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12 col-lg-12 col-xl-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['appointment.calendar'], 'method' => 'get', 'id' => 'appointment_filter']) }}
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Date']) }}
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Date']) }}
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                            <div class="btn-box">
                                {{ Form::label('business', __('Business'), ['class' => 'form-label']) }}
                                {{ Form::select('business', $businessData, isset($_GET['business']) ? $_GET['business'] : '', ['class' => 'form-control select ', 'id' => 'user_id']) }}
                            </div>
                        </div>
                        <div class="col-auto float-end ms-2 mt-4">
                            <a class="btn btn-sm btn-primary"
                                onclick="document.getElementById('appointment_filter').submit(); return false;"
                                data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="{{ route('appointment.calendar') }}" class="btn btn-sm btn-danger"
                                data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                            </a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Appointment Calendar') }}</h5>
                    <small class="text-muted mt-2">{{__('This calendar is for business appointment ') }}</small>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card ">
                <div class="card-body ">
                    <h4 class="mb-4">{{ __('Appointments') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100 ">
                        @foreach ($arrayJson as $appointment)
                            @php
                                $month = date('m', strtotime($appointment['start']));
                            @endphp
                            @if ($month == date('m'))
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-calendar"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="">{{ $appointment['title'] }}</h6>
                                                    <small class="text-muted mt-2">{{ $appointment['start'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">

                                        </div>
                                    </div>
                                </li>
                            @endif
                            <input type="hidden" class="business_id" name="business_id"
                                value="{{ $appointment['business_id'] }}">
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('scripts')
    <script src="{{ asset('Modules/VCard/Resources/assets/custom/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('Modules/VCard/Resources/assets/custom/js/main.min.js') }}"></script>
    <script type="text/javascript">
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                },
                themeSystem: 'bootstrap',
                allDaySlot: false,
                // slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                height: 'auto',
                handleWindowResize: true,
                events: {!! $appointmentData !!},
                eventClick: function(e) {
                    e.jsEvent.preventDefault();
                    var title = e.title;
                    var url = e.el.href;
                    var size = 'md';
                    $("#commonModal .modal-title").html(e.event.title);
                    $("#commonModal .modal-dialog").addClass('modal-' + size);
                    $.ajax({
                        url: url,
                        success: function(data) {
                            $('#commonModal .body').html(data);
                            $("#commonModal").modal('show');
                            common_bind();
                            select2();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            toastrs('Error', data.error, 'error')
                        }
                    });
                }
            });
            calendar.render();
        })();
    </script>
@endpush
