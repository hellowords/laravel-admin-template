@extends('admin.layouts.admin')

@section('content')
    <section class="content-header">
        <h3>
            用户管理
        </h3>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">用户管理</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <user :roles="{{ $roles }}" :permissions="{{ $permissions }}"></user>
        </div>
    </section>
@endsection