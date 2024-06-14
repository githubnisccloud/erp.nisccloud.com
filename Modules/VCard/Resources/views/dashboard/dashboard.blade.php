@php

    $currentBusiness = Modules\VCard\Entities\Business::currentBusiness();

@endphp
@extends('layouts.main')

<style>
    .shareqrcode img {
        width: 85%;
        height: 85%;
    }

    /* Social Sharing  */
    .sharingButtonsContainer {
        position: absolute;
        top: 85%;
        right: 20px;
        transform: translateY(-50%);
        z-index: 9999;
    }

    .sharingButtonsContainer a {
        background-color: #ddd;
        display: flex;
        justify-content: center;
        min-width: 13px;
        border-radius: 20px;
        width: 35px;
        height: 35px;
        align-items: center;
    }
    
    .share-btn {
        background-color: #47dbcd;
        border: 1px solid #47dbcd;
    }

    .sharingButtonsContainer .Demo1 {
        margin-bottom: 0px !important;
    }

    @media screen and (max-width:1200px) {
        .sharingButtonsContainer {
            right: 25px;
        }
    }

    .socialJS {
        display: flex;
        gap: 0 10px;
    }
</style>
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('vCard') }}
@endsection
@section('page-action')
    <div class="d-flex align-items-center justify-content-end gap-2">

        <ul class="list-unstyled mb-0 m-2">
            <li class="dropdown dash-h-item drp-language">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false" id="dropdownLanguage">
                    @foreach ($businesses as $key => $value)
                        <span
                            class="drp-text hide-mob text-primary">{{ $currentBusiness == $key ? Str::ucfirst($value) : '' }}</span>
                    @endforeach
                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                    @foreach ($businesses as $key => $business_val)
                        <a href="{{ route('business.current', $key) }}" class="dropdown-item">
                            <i class="@if ($currentBusiness == $key) ti ti-checks text-primary @endif "></i>
                            <span>{{ ucfirst($business_val) }}</span>
                        </a>
                    @endforeach
                </div>
            </li>
        </ul>

    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">

                @php
                    $class = '';
                    if ($total_bussiness == 0) {
                        $class = 'col-lg-2 col-md-2';
                    } else {
                        $class = 'col-lg-2 col-md-2';
                    }
                @endphp
                <div class="{{ !empty($businessData) ? 'col-xxl-12' : 'col-xxl-12' }}">
                    <div class="row">
                        <div class="col-lg-4 col-6 d-flex">
                            <div class="card w-100">
                                <div class="card-body">
                                    <h3 class="mb-1 col-12" id="greetings"></h3>
                                    <h6> {{ !empty($businessData) ? __(ucfirst($businessData->title)) : '-' }} </h6>
                                    <p>{{ __('Have a nice day! Did you know that you can quickly access your favorite business or card?') }}
                                    </p>
                                    @if ($businessData)
                                        <div class="row">
                                            <div class="col-md-10 stats">
                                                <a href="#" class="btn btn-primary cp_link"
                                                    data-link="{{ url('/cards/' . $businessData->slug) }}"
                                                    data-bs-whatever="{{ __('Copy Link') }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Copy Link') }}"
                                                    title="{{ __('Click to copy link') }}">
                                                    <i class="ti ti-link"></i>
                                                    {{ __('Business Link') }}
                                                </a>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="#" id="socialShareButton"
                                                    class="socialShareButton btn btn-md btn-primary  share-btn">
                                                    <i class="ti ti-share"></i>
                                                </a>
                                                <div id="sharingButtonsContainer" class="sharingButtonsContainer"
                                                    style="display: none;">
                                                    <div
                                                        class="Demo1 d-flex align-items-center justify-content-center hidden">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="{{ $class }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-primary">
                                        <i class="ti ti-briefcase dash-micon"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total') }}</p>
                                    <h6 class="mb-3">{{ __(' Business') }}</h6>
                                    <h3 class="mb-0">{{ $total_bussiness }} </h3>
                                </div>
                            </div>
                        </div>
                        <div class="{{ $class }} ">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-warning">
                                        <i class="ti ti-calendar-time dash-micon"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total') }}</p>
                                    <h6 class="mb-3">{{ __(' Appointments') }}</h6>
                                    <h3 class="mb-0">{{ $total_app }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="{{ $class }} ">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-warning">
                                        <i class="ti ti-clipboard-check dash-micon"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total') }}</p>
                                    <h6 class="mb-3">{{ __(' Contacts') }}</h6>
                                    <h3 class="mb-0">{{ $total_contact }}</h3>
                                </div>
                            </div>
                        </div>

                        @if ($businessData)
                            <div class="col-lg-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div class="shareqrcode"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-12 ">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <span class="mb-0 float-right">{{ __('Last 7 Days') }}</span>
                                </div>
                                <h5>{{ __('Appointments') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id="apex-storedashborad" data-color="primary" data-height="280"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <img src="{{ isset($qr_detail->image) ? get_file($qr_detail->image) : '' }}" id="image-buffers"
            style="display: none">
    @endsection

    @push('scripts')
        <script src="{{ asset('Modules/VCard/Resources/assets/custom/js/purpose.js') }}"></script>
        <script src="{{ asset('Modules/VCard/Resources/assets/custom/js/jquery.qrcode.min.js') }}"></script>
        <script src="{{ asset('Modules/VCard/Resources/assets/custom/js/socialSharing.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
        <script type="text/javascript">
            $('.cp_link').on('click', function() {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('{{ __('Success') }}', '{{ __('Link Copy on Clipboard') }}', 'success');
            });
        </script>
        <script type="text/javascript">
            $(document).on("change", "select[name='select_card']", function() {
                var b_id = $("select[name='select_card']").val();
                if (b_id == '0') {
                    window.location.href = '{{ url('/dashboard') }}';
                } else {
                    window.location.href = '{{ url('business/analytics') }}/' + b_id;
                }

            });
        </script>
        <script>
            $(document).ready(function() {
                @if ($businessData)
                    var slug = '{{ $businessData->slug }}';
                    var url_link = `{{ url('/cards') }}/${slug}`;

                    $(`.qr-link`).text(url_link);
                    var foreground_color =
                        `{{ isset($qr_detail->foreground_color) ? $qr_detail->foreground_color : '#000000' }}`;
                    var background_color =
                        `{{ isset($qr_detail->background_color) ? $qr_detail->background_color : '#ffffff' }}`;
                    var radius = `{{ isset($qr_detail->radius) ? $qr_detail->radius : 26 }}`;
                    var qr_type = `{{ isset($qr_detail->qr_type) ? $qr_detail->qr_type : 0 }}`;
                    var qr_font = `{{ isset($qr_detail->qr_text) ? $qr_detail->qr_text : 'VCard' }}`;
                    var qr_font_color =
                        `{{ isset($qr_detail->qr_text_color) ? $qr_detail->qr_text_color : '#f50a0a' }}`;
                    var size = `{{ isset($qr_detail->size) ? $qr_detail->size : 9 }}`;

                    $('.shareqrcode').empty().qrcode({
                        render: 'image',
                        size: 500,
                        ecLevel: 'H',
                        minVersion: 3,
                        quiet: 1,
                        text: url_link,
                        fill: foreground_color,
                        background: background_color,
                        radius: .01 * parseInt(radius, 10),
                        mode: parseInt(qr_type, 10),
                        label: qr_font,
                        fontcolor: qr_font_color,
                        image: $("#image-buffers")[0],
                        mSize: .01 * parseInt(size, 10)
                    });
                @endif
            });
        </script>
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: {!! json_encode($chartData['data']) !!},
                    xaxis: {
                        labels: {
                            format: "MMM",
                            style: {
                                colors: PurposeStyle.colors.gray[600],
                                fontSize: "14px",
                                fontFamily: PurposeStyle.fonts.base,
                                cssClass: "apexcharts-xaxis-label"
                            }
                        },
                        axisBorder: {
                            show: !1
                        },
                        axisTicks: {
                            show: !0,
                            borderType: "solid",
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        type: "text",
                        categories: {!! json_encode($chartData['label']) !!}
                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: PurposeStyle.colors.gray[600],
                                fontSize: "12px",
                                fontFamily: PurposeStyle.fonts.base
                            }
                        },
                        axisBorder: {
                            show: !1
                        },
                        axisTicks: {
                            show: !0,
                            borderType: "solid",
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    },

                };
                var chart = new ApexCharts(document.querySelector("#apex-storedashborad"), options);
                chart.render();
            })();
        </script>
        <script type="text/javascript">
            @if ($businessData)
                $(document).ready(function() {
                    var customURL = {!! json_encode(url('/cards/' . $businessData->slug)) !!};
                    $('.Demo1').socialSharingPlugin({
                        url: customURL,
                        title: $('meta[property="og:title"]').attr('content'),
                        description: $('meta[property="og:description"]').attr('content'),
                        img: $('meta[property="og:image"]').attr('content'),
                        enable: ['whatsapp', 'facebook', 'twitter', 'pinterest', 'linkedin']
                    });

                    $('.socialShareButton').click(function(e) {
                        e.preventDefault();
                        $('.sharingButtonsContainer').toggle();
                    });
                });
            @endif
        </script>
    @endpush
