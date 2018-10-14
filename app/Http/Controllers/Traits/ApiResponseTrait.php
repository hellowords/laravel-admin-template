<?php

namespace App\Http\Controllers\Traits;

trait ApiResponseTrait
{
    /**
     * @desc Api正确结果返回
     * @param int  $code
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponseData ($code = 200, $data = null)
    {
        return response()->json([
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    /**
     * @desc Api 错误结果返回
     * @param int    $code
     * @param string $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponseData ($code = 422, $data = '未知错误.')
    {
        return response()->json([
            'code' => $code,
            'data' => $data,
        ], $code);
    }
}