@extends('admin.layouts.admin')

@section('content')
    <section class="content-header">
        <h3>
            个人中心
        </h3>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">个人中心</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <profile :user="{{ $user_info }}"></profile>
        </div>
    </section>
@endsection