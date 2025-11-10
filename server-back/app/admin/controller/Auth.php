<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\service\Auth as AuthService;
use app\admin\validate\Auth as AuthValidate;
use think\facade\Request;

class Auth extends Base
{
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * @var AuthValidate
     */
    protected $authValidate;

    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();
        $this->authService = new AuthService();
        $this->authValidate = new AuthValidate();
    }

    /**
     * 用户登录
     * @return \think\Response
     */
    public function login()
    {
        // 获取请求数据
        $data = Request::only(['username', 'password']);
        
        // 数据验证
        if (!$this->authValidate->scene('login')->check($data)) {
            return $this->error($this->authValidate->getError(), self::HTTP_BAD_REQUEST);
        }
        
        try {
            // 调用Service层处理业务逻辑
            $result = $this->authService->login($data['username'], $data['password']);
            
            return $this->success($result, '登录成功');
            
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: self::HTTP_INTERNAL_ERROR);
        }
    }
    
    /**
     * 用户退出登录
     * @return \think\Response
     */
    public function logout()
    {
        try {
            $result = $this->authService->logout();
            return $this->success($result, '退出成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: self::HTTP_INTERNAL_ERROR);
        }
    }
    
    /**
     * 刷新token - 通过中间件处理认证
     * @return \think\Response
     */
    public function refreshToken()
    {
        try {
            // 中间件已经验证token并设置用户信息 - 从请求对象中获取
            $userInfo = request()->userInfo;
            
            // 检查用户信息是否存在
            if (!$userInfo) {
                return $this->error('用户信息不存在', self::HTTP_UNAUTHORIZED);
            }
            
            // 直接使用中间件验证后的用户信息生成新token
            $result = $this->authService->refreshToken($userInfo);
            
            return $this->success($result, 'Token刷新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: self::HTTP_UNAUTHORIZED);
        }
    }
    
    /**
     * 获取当前用户信息 - 通过中间件处理认证
     * @return \think\Response
     */
    public function user()
    {
        try {
            // 添加详细的调试信息
            \think\facade\Log::info('Auth控制器user方法开始执行');
            
            $userInfo = request()->userInfo;
            \think\facade\Log::info('获取到的userInfo: ' . ($userInfo ? '非空' : 'null'));
            
            if ($userInfo) {
                \think\facade\Log::info('userInfo类型: ' . gettype($userInfo));
                \think\facade\Log::info('userInfo内容: ' . json_encode($userInfo));
            }
            
            if (!$userInfo) {
                \think\facade\Log::info('userInfo为空，返回401');
                return json([
                    'code' => 401,
                    'message' => '用户信息不存在',
                    'timestamp' => time()
                ], 401);
            }
            
            // 使用对象属性访问
            \think\facade\Log::info('准备返回用户信息');
            return json([
                'code' => 200,
                'message' => '获取用户信息成功',
                'data' => [
                    'id' => $userInfo->user_id,
                    'username' => $userInfo->username,
                    'nickname' => $userInfo->nickname ?? $userInfo->username,
                    'avatar' => $userInfo->avatar ?? '',
                    'role' => 'admin',
                    'permissions' => $this->getUserPermissions($userInfo->user_id)
                ],
                'timestamp' => time()
            ]);
        } catch (\Exception $e) {
            \think\facade\Log::error('Auth控制器user方法异常: ' . $e->getMessage());
            return json([
                'code' => 500,
                'message' => $e->getMessage(),
                'timestamp' => time()
            ], 500);
        }
    }

    /**
     * 获取用户权限列表
     * @param int $userId
     * @return array
     */
    private function getUserPermissions(int $userId): array
    {
        // 这里需要根据实际权限系统实现
        // 暂时返回基础权限
        return [
            'dashboard',
            'user:view',
            'user:edit',
            'system:view',
            'system:edit'
        ];
    }
}