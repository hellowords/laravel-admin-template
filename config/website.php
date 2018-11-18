<?php
return [
    /**
     * skin-black-light blue blue-light green
     * green-light purple purple-light red yellow
     */
    'adminSkin'       => env('ADMIN_SKIN', 'skin-black'),
    'appSmallLog'     => env('APP_SMALL_NAME', 'Happy'),
    'sidebar'         => env('APP_SIDEBAR', ''),
    'version'         => env('APP_VERSION', '1.0'),
    'defaultPassword' => env('DEFAULT_PASSWORD', 123456),
    'jsVersion'       => env('JS_VERSION'),
    'dingUrl'         => env('DING_HOOK'),
    'cdnDomain'       => env('CND_DOMAIN'),
    'ak'              => env('ACCESS_KEY_ID'),
    'sk'              => env('ACCESS_KEY_SECRET'),
    'signName'        => env('SIGN_NAME'),
    'tempCheckCode'   => env('TEMP_CHECK_CODE'),
    'tempNoticeCode'  => env('TEMP_NOTICE_CODE'),

];