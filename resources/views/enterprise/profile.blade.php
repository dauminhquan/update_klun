@extends('layout.manage_layout')
@section('header-content')
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Thông tin doanh nghiệp</span></h4>

            <ul class="breadcrumb breadcrumb-caret position-right">
                <li><a href="{{route('admin.dashboard')}}">Home</a></li>
                <li class="active">Thông tin cá nhân </li>
            </ul>
        </div>
    </div>
@endsection
@section('page-content')
    <web-content></web-content>
@endsection
@section("js-page")
    <script type="text/javascript" src="{{asset("assets/js/build/pages/enterprise/profile/index.js")}}"></script>
@endsection
@section('theme-asset')
    <script type="text/javascript" src="{{asset('assets/js/plugins/ckeditor/ckeditor.js')}}"></script>
@endsection
@section("mobile-panel-left")
    <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
@endsection