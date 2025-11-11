<?php

declare(strict_types=1);

namespace app\common\utils;

use think\Response;

class ResponseHelper
{
    // 成功状态码
    const SUCCESS_CODE = 200;
    
    // 错误状态码
    const ERROR_CODE = 400;
    const UNAUTHORIZED_CODE = 401;
    const FORBIDDEN_CODE = 403;
    const NOT_FOUND_CODE = 404;
    const SERVER_ERROR_CODE = 500;

    /**
     * 成功响应
     * @param mixed $data 响应数据
     * @param string $message 提示信息
     * @param int $code 状态码
     * @return Response
     */
    public static function success($data = null, string $message = '操作成功', int $code = self::SUCCESS_CODE): Response
    {
        return json([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'timestamp' => time()
        ]);
    }

    /**
     * 错误响应
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param mixed $data 额外数据
     * @return Response
     */
    public static function error(string $message = '操作失败', int $code = self::ERROR_CODE, $data = null): Response
    {
        return json([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'timestamp' => time()
        ]);
    }

    /**
     * 未授权响应
     * @param string $message 错误信息
     * @return Response
     */
    public static function unauthorized(string $message = '未授权访问'): Response
    {
        return self::error($message, self::UNAUTHORIZED_CODE);
    }

    /**
     * 禁止访问响应
     * @param string $message 错误信息
     * @return Response
     */
    public static function forbidden(string $message = '禁止访问'): Response
    {
        return self::error($message, self::FORBIDDEN_CODE);
    }

    /**
     * 资源不存在响应
     * @param string $message 错误信息
     * @return Response
     */
    public static function notFound(string $message = '资源不存在'): Response
    {
        return self::error($message, self::NOT_FOUND_CODE);
    }

    /**
     * 服务器错误响应
     * @param string $message 错误信息
     * @return Response
     */
    public static function serverError(string $message = '服务器内部错误'): Response
    {
        return self::error($message, self::SERVER_ERROR_CODE);
    }

    /**
     * 分页数据响应
     * @param array $list 数据列表
     * @param int $total 总记录数
     * @param int $page 当前页码
     * @param int $pageSize 每页数量
     * @param string $message 提示信息
     * @return Response
     */
    public static function paginate(array $list, int $total, int $page, int $pageSize, string $message = '获取成功'): Response
    {
        $data = [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPage' => ceil($total / $pageSize)
        ];

        return self::success($data, $message);
    }

    /**
     * 列表数据响应
     * @param array $list 数据列表
     * @param string $message 提示信息
     * @return Response
     */
    public static function list(array $list, string $message = '获取成功'): Response
    {
        return self::success($list, $message);
    }

    /**
     * 创建成功响应
     * @param mixed $data 创建的数据
     * @param string $message 提示信息
     * @return Response
     */
    public static function created($data = null, string $message = '创建成功'): Response
    {
        return self::success($data, $message, 201);
    }

    /**
     * 更新成功响应
     * @param mixed $data 更新的数据
     * @param string $message 提示信息
     * @return Response
     */
    public static function updated($data = null, string $message = '更新成功'): Response
    {
        return self::success($data, $message);
    }

    /**
     * 删除成功响应
     * @param string $message 提示信息
     * @return Response
     */
    public static function deleted(string $message = '删除成功'): Response
    {
        return self::success(null, $message);
    }
}