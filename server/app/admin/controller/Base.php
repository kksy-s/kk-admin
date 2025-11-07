<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Request;
use think\Response;

class Base extends BaseController
{
    // HTTP 状态码常量
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_ERROR = 500;
    
    /**
     * 成功响应
     * @param mixed $data 返回数据
     * @param string $message 提示信息
     * @param int $code HTTP状态码
     * @return Response
     */
    protected function success($data = null, $message = '操作成功', $code = self::HTTP_OK)
    {
        return $this->jsonResponse($code, $message, $data);
    }
    
    /**
     * 创建成功响应
     * @param mixed $data 返回数据
     * @param string $message 提示信息
     * @return Response
     */
    protected function created($data = null, $message = '创建成功')
    {
        return $this->jsonResponse(self::HTTP_CREATED, $message, $data);
    }
    
    /**
     * 错误响应
     * @param string $message 错误信息
     * @param int $code HTTP状态码
     * @param mixed $data 额外数据
     * @return Response
     */
    protected function error($message = '操作失败', $code = self::HTTP_BAD_REQUEST, $data = null)
    {
        return $this->jsonResponse($code, $message, $data);
    }
    
    /**
     * 未找到资源响应
     * @param string $message 错误信息
     * @return Response
     */
    protected function notFound($message = '资源不存在')
    {
        return $this->jsonResponse(self::HTTP_NOT_FOUND, $message);
    }
    
    /**
     * 服务器错误响应
     * @param string $message 错误信息
     * @return Response
     */
    protected function internalError($message = '服务器内部错误')
    {
        return $this->jsonResponse(self::HTTP_INTERNAL_ERROR, $message);
    }
    
    /**
     * 统一的JSON响应格式
     * @param int $code HTTP状态码
     * @param string $message 消息
     * @param mixed $data 数据
     * @return Response
     */
    protected function jsonResponse($code, $message, $data = null)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'timestamp' => time()
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return json($response, $code);
    }
    
    /**
     * 获取分页参数
     * @return array
     */
    protected function getPaginationParams()
    {
        $page = Request::param('page/d', 1);
        $limit = Request::param('limit/d', 10);
        $limit = max(1, min(100, $limit)); // 限制每页最多100条
        
        return [
            'page' => max(1, $page),
            'limit' => $limit,
            'offset' => ($page - 1) * $limit
        ];
    }
    
    /**
     * 获取排序参数
     * @return array
     */
    protected function getSortParams()
    {
        $sort = Request::param('sort', 'id');
        $order = Request::param('order', 'desc');
        
        // 安全过滤，防止SQL注入
        $allowedOrders = ['asc', 'desc'];
        $order = in_array(strtolower($order), $allowedOrders) ? $order : 'desc';
        
        return [
            'sort' => $sort,
            'order' => $order
        ];
    }
    
    /**
     * 构建分页响应数据
     * @param array $list 数据列表
     * @param int $total 总记录数
     * @param int $page 当前页码
     * @param int $limit 每页数量
     * @return array
     */
    protected function buildPaginatedData($list, $total, $page, $limit)
    {
        return [
            'list' => $list,
            'pagination' => [
                'total' => (int)$total,
                'page' => (int)$page,
                'limit' => (int)$limit,
                'pages' => $limit > 0 ? ceil($total / $limit) : 0
            ]
        ];
    }
    
    /**
     * 获取请求参数（支持过滤）
     * @param array $fields 允许的字段列表
     * @return array
     */
    protected function getParams($fields = [])
    {
        $params = Request::param();
        
        if (!empty($fields)) {
            $params = array_intersect_key($params, array_flip($fields));
        }
        
        return $params;
    }
    
    /**
     * 验证ID参数
     * @param mixed $id ID值
     * @return int
     */
    protected function validateId($id)
    {
        $id = intval($id);
        if ($id <= 0) {
            $this->error('ID参数错误');
        }
        return $id;
    }
}