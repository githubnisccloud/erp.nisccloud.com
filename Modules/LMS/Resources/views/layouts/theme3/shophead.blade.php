@php
    $favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

    @yield('meta-data')

    <title>@yield('page-title')</title>



    <!-- Preloader -->
    <style>

    </style>
    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    @stack('css-page')
<!-- Favicon -->
    <link rel="icon" href="{{ check_file($favicon) ? get_file($favicon) : get_file('uploads/logo/favicon.png')  }}{{'?'.time()}}" type="image/x-icon" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/css/jquery.fancybox.min.css')}}">
    <!-- Font Awesome -->
    <!-- Quick CSS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/css/all.min.css')}}"><!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/plugins/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('js/swiper/dist/css/swiper.min.css')}}">
    <!-- site CSS -->
    <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/css/bootstrap.min.3.3.5.css')}}">


    @if(!empty($store->store_theme))
        <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/themes/theme3/css/'.$store->store_theme)}}" id="stylesheet">
    @else
        <link rel="stylesheet" href="{{asset('Modules/LMS/Resources/assets/themes/theme3/css/light-blue-style.css')}}" id="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('Modules/LMS/Resources/assets/themes/theme3/css/responsive.css') }}">

</head>
