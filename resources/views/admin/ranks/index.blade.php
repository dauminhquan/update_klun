@extends('layout.manage_layout')
@section('header-content')
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Quản lý hình thức việc làm</span></h4>

            <ul class="breadcrumb breadcrumb-caret position-right">
                <li><a href="index.html">Home</a></li>
                <li><a href="#">Quản lý hạng</a></li>
                <li class="active">Danh sách hạng</li>
            </ul>
        </div>

    </div>
@endsection
@section('page-content')
    <div class="page-content" id="page-content">
        <table-content></table-content>
    </div>

@endsection

@section("js-page")
    <script type="text/javascript" src="{{asset("assets/js/build/pages/admin/ranks/index.js")}}"></script>
@endsection
@section('theme-asset')
    {{--<script type="text/javascript" src="{{asset('assets/js/plugins/forms/selects/select2.min.js')}}"></script>--}}
@endsection