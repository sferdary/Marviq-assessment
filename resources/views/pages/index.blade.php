@php
// Chart results are stored in variables to prevent JS syntax errors
$chartData2x2 = $result['2x2']['production']['chartData'];
$chartData3x2 = $result['3x2']['production']['chartData'];
$chartData4x2 = $result['4x2']['production']['chartData'];
@endphp

@extends('layouts.app')                                         {{-- Extends the layout of the app --}}
@section('title', 'Ogel machine dashboard')                     {{-- Page title --}}
@section('stylesheets')                                         {{-- Section for additional CSS --}}
    <link href="{{asset('css/morris.css')}}" rel="stylesheet">  {{-- Additional stylesheet for charts --}}
@endsection

@section('content')                                             {{-- Section for all the data that is displayed on the page --}}
    @include('partials.searchForm')                             {{-- Inclusion of the date search form --}}

    {{-- inclusion of the cards provided with machine data --}}
    @include('includes.machineCard', $machine = $result['2x2'])
    @include('includes.machineCard', $machine = $result['3x2'])
    @include('includes.machineCard', $machine = $result['4x2'])
@endsection

@section('scripts')                                             {{-- Section for additional JS (Bottom of the body) --}}
    {{-- Required JS files to create the nett gross chart--}}
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('js/raphael-min.js')}}"></script>
    <script src="{{asset('js/prettify.min.js')}}"></script>
    <script src="{{asset('js/morris.js')}}"></script>
    <script type="text/javascript">                             {{-- Chart data in json format --}}
        const chart2x2JSON = [ {!! $chartData2x2 !!} ];
        const chart3x2JSON = [ {!! $chartData3x2 !!} ];
        const chart4x2JSON = [ {!! $chartData4x2 !!} ];
    </script>
    <script src="{{asset('js/charts.js')}}"></script>           {{-- The chart data is proccessed in this JS file --}}
@endsection
