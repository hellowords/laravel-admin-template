<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HappyHack') }}</title>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/css/_all-skins.min.css">

    <!-- Styles -->
    {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
</head>
<body class="hold-transition {{ config('website.adminSkin')}} {{ config('website.sidebar') }} sidebar-mini" >
<div class="wrapper">
    @include('admin.layouts.header')
    @include('admin.layouts.siderbar')
    <div class="content-wrapper" id="app">
        @yield('content')
    </div>
    @include('admin.layouts.footer')
</div>

<!-- jQuery 3 -->
<script src="/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/js/adminlte.min.js"></script>

{{--当前所选菜单自动展开--}}
<script>
    $(function () {
        $('.sidebar-menu li:not(.treeview) > a').on('click', function () {
            var $parent = $(this).parent().addClass('active');
            $parent.siblings('.treeview.active').find('> a').trigger('click');
            $parent.siblings().removeClass('active').find('li').removeClass('active');
        });

        $(window).on('load', function () {
            $('.sidebar-menu a').each(function () {
                if (this.href === window.location.href) {
                    $(this).parent().addClass('active')
                        .closest('.treeview-menu').addClass('.menu-open')
                        .closest('.treeview').addClass('active');
                }
            });

            if (!localStorage.getItem('MenuClass')) {
                localStorage.setItem('MenuClass', null);
            } else {
                $('body').addClass(localStorage.getItem('MenuClass'));
            }

            $('.sidebar-toggle').click(function () {
                if (localStorage.getItem('MenuClass') === 'null') {
                    localStorage.setItem('MenuClass', 'sidebar-collapse');
                } else {
                    localStorage.setItem('MenuClass', null);
                }
            });
        });
    });
</script>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>