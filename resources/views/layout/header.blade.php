<!DOCTYPE html>

<html lang="en">



<head>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Landing PAGE Html5 Template">

    <meta name="keywords" content="landing,startup,flat">

    <meta name="author" content="Made By GN DESIGNS">



    {{-- @foreach ($systeminformation as $sysinfor)
        <title>{{ $sysinfor->system_name }}</title>
    @endforeach --}}



    <!-- // PLUGINS (css files) // -->

    <link href="{{ asset('assets/js/plugins/bootsnav_files/skins/color.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/bootsnav_files/css/animate.css') }}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/bootsnav_files/css/bootsnav.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/bootsnav_files/css/overwrite.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/owl-carousel/owl.theme.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/owl-carousel/owl.transitions.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/js/plugins/Magnific-Popup-master/Magnific-Popup-master/dist/magnific-popup.css')}}" rel="stylesheet">

    <!--// ICONS //-->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!--// BOOTSTRAP & Main //-->

    <link href="{{  asset('assets/bootstrap-3.3.7/bootstrap-3.3.7-dist/css/bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{  asset('assets/css/main.css')}}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{csrf_token()}}">

    {{-- <style>

            #home {
            position: relative;
            }

            #home::before {
            content: "";
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Change the color and opacity as needed */
            }

            #home img {
            display: block;
            max-width: 100%;
            height: auto;
        }

    </style> --}}

    {{-- <style>
           .microsoft-login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            background-color: #1b1717;
            color: white;
            font-size: 16px;
            text-decoration: none;
            border-radius: 30px;
            transition: background-color 0.3s ease;
            border: 1px solid #0078D4;
        }

        /* Styling for the Microsoft logo inside the button */
        .microsoft-logo {
            width: 20px; /* Adjust size of the logo */
            height: 20px;
            margin-right: 10px; /* Space between the logo and the text */
        }

        /* Change button color on hover */
        .microsoft-login-btn:hover {
            background-color: #1b1717;
            border-color: #0866ff;
            color: #fff;
        }

        .microsoft-login-btn:active {
            background-color: #004482;
            border-color: #004482;
        }
    </style> --}}
</head>

<body>
