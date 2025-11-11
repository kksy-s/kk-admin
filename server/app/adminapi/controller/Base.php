<?php

declare(strict_types=1);

namespace app\adminapi\controller;

use app\common\utils\ResponseHelper;
use think\App;

class Base
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
    }

    /**
     * 成功响应
     * @param mixed $data 响应数据
     * @param string $message 提示信息
     * @param int $code 状态码
     * @return \think\Response
     */
    protected function success($data = null, string $message = '操作成功', int $code = 200)
    {
        return ResponseHelper::success($data, $message, $code);
    }

    /**
     * 错误响应
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param mixed $data 额外数据
     * @return \think\Response
     */
    protected function error(string $message = '操作失败', int $code = 400, $data = null)
    {
        return ResponseHelper::error($message, $code, $data);
    }

    /**
     * 分页响应
     * @param array $list 数据列表
     * @param int $total 总记录数
     * @param int $page 当前页码
     * @param int $pageSize 每页数量
     * @param string $message 提示信息
     * @return \think\Response
     */
    protected function paginate(array $list, int $total, int $page, int $pageSize, string $message = '获取成功')
    {
        return ResponseHelper::paginate($list, $total, $page, $pageSize, $message);
    }

    /**
     * 列表响应
     * @param array $list 数据列表
     * @param string $message 提示信息
     * @return \think\Response
     */
    protected function list(array $list, string $message = '获取成功')
    {
        return ResponseHelper::list($list, $message);
    }

    /**
     * 创建成功响应
     * @param mixed $data 创建的数据
     * @param string $message 提示信息
     * @return \think\Response
     */
    protected function created($data = null, string $message = '创建成功')
    {
        return ResponseHelper::created($data, $message);
    }

    /**
     * 更新成功响应
     * @param mixed $data 更新的数据
     * @param string $message 提示信息
     * @return \think\Response
     */
    protected function updated($data = null, string $message = '更新成功')
    {
        return ResponseHelper::updated($data, $message);
    }

    /**
     * 删除成功响应
     * @param string $message 提示信息
     * @return \think\Response
     */
    protected function deleted(string $message = '删除成功')
    {
        return ResponseHelper::deleted($message);
    }
}