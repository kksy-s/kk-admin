<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\BaseController;
use app\admin\model\SysUser;
use app\common\Jwt;
use think\facade\Db;
use think\facade\Request;

class Auth extends BaseController
{
    /**
     * 用户登录
     * @return \think\Response
     */
    public function login()
    {
        // 获取请求数据
        $username = Request::param('username');
        $password = Request::param('password');
        
        // 验证参数
        if (empty($username) || empty($password)) {
            return json([
                'code' => 400,
                'message' => '用户名和密码不能为空',
                'data' => null
            ]);
        }
        
        try {
            // 查询用户信息
            $user = SysUser::where('username', $username)->find();
            
            if (!$user) {
                return json([
                    'code' => 401,
                    'message' => '用户名或密码错误',
                    'data' => null
                ]);
            }
            
            // 验证密码（这里需要根据实际密码加密方式调整）
            // 假设密码是明文存储，实际应该使用password_hash加密
            if ($user['password'] !== md5($password)) {
                return json([
                    'code' => 401,
                    'message' => '用户名或密码错误',
                    'data' => null
                ]);
            }
            
            // 检查用户状态
            if ($user['status'] !== 1) {
                return json([
                    'code' => 403,
                    'message' => '账号已被禁用',
                    'data' => null
                ]);
            }
            
            // 生成JWT token
            $token = Jwt::encode([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'] ?? 'user'
            ]);
            
            // 更新最后登录时间
            $user->save([
                'last_login_time' => date('Y-m-d H:i:s'),
                'last_login_ip' => Request::ip()
            ]);
            
            // 返回登录成功信息
            return json([
                'code' => 200,
                'message' => '登录成功',
                'data' => [
                    'token' => $token,
                    'user_info' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'nickname' => $user['nickname'] ?? $user['username'],
                        'avatar' => $user['avatar'] ?? '',
                        'role' => $user['role'] ?? 'user'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => '登录失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
    
    /**
     * 用户退出登录
     * @return \think\Response
     */
    public function logout()
    {
        // JWT是无状态的，客户端需要自行删除token
        return json([
            'code' => 200,
            'message' => '退出成功',
            'data' => null
        ]);
    }
    
    /**
     * 刷新token
     * @return \think\Response
     */
    public function refreshToken()
    {
        $token = Request::header('Authorization');
        
        if (empty($token)) {
            return json([
                'code' => 401,
                'message' => 'Token不能为空',
                'data' => null
            ]);
        }
        
        // 去除Bearer前缀
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        try {
            $newToken = Jwt::refresh($token);
            
            return json([
                'code' => 200,
                'message' => 'Token刷新成功',
                'data' => [
                    'token' => $newToken
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 401,
                'message' => 'Token刷新失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
    
    /**
     * 获取当前用户信息
     * @return \think\Response
     */
    public function getUserInfo()
    {
        $token = Request::header('Authorization');
        
        if (empty($token)) {
            return json([
                'code' => 401,
                'message' => '未授权访问',
                'data' => null
            ]);
        }
        
        // 去除Bearer前缀
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        try {
            $payload = Jwt::decode($token);
            $user = SysUser::find($payload->user_id);
            
            if (!$user) {
                return json([
                    'code' => 401,
                    'message' => '用户不存在',
                    'data' => null
                ]);
            }
            
            return json([
                'code' => 200,
                'message' => '获取成功',
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nickname' => $user['nickname'] ?? $user['username'],
                    'avatar' => $user['avatar'] ?? '',
                    'role' => $user['role'] ?? 'user',
                    'permissions' => $this->getUserPermissions($user['id'])
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 401,
                'message' => 'Token验证失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
    
    /**
     * 获取用户权限列表
     * @param int $userId
     * @return array
     */
    private function getUserPermissions($userId)
    {
        // 这里需要根据实际权限系统实现
        // 暂时返回基础权限
        return [
            'dashboard',
            'user:view',
            'user:edit'
        ];
    }
}