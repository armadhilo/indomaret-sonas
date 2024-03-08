@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">REPORT</h1>
@endsection

@section('css')
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-lg-4">
                @include('layouts.report-menu')
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    
@endpush