<?php
declare (strict_types = 1);

namespace app\admin\service;

use app\admin\model\SysUser;
use app\common\Jwt;
use think\Exception;
use think\facade\Request;

class Auth
{
    /**
     * 用户登录
     * @param string $username
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function login(string $username, string $password): array
    {
        // 查询用户信息
        $user = SysUser::where('username', $username)->find();
        
        if (!$user) {
            throw new Exception('用户名或密码错误', 401);
        }
        
        // 验证密码（MD5加密）
        if ($user['password'] !== md5($password)) {
            throw new Exception('用户名或密码错误', 401);
        }
        
        // 检查用户状态
        if ($user['status'] !== SysUser::STATUS_NORMAL) {
            throw new Exception('账号已被禁用', 403);
        }
        
        // 生成JWT token
        $token = Jwt::encode([
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'role' => 'admin'
        ]);
        
        // 更新最后登录时间
        $user->save([
            'login_date' => date('Y-m-d H:i:s'),
            'login_ip' => Request::ip()
        ]);
        
        return [
            'token' => $token,
            'user_info' => [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'] ?? $user['username'],
                'avatar' => $user['avatar'] ?? '',
                'role' => 'admin'
            ]
        ];
    }

    /**
     * 刷新token
     * @param array $userInfo 用户信息（从中间件获取）
     * @return array
     * @throws Exception
     */
    public function refreshToken(array $userInfo): array
    {
        // 基于用户信息生成新token
        $newToken = Jwt::encode([
            'user_id' => $userInfo['user_id'],
            'username' => $userInfo['username'],
            'role' => 'admin'
        ]);
        
        return [
            'token' => $newToken
        ];
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

    /**
     * 用户退出登录
     * @return array
     */
    public function logout(): array
    {
        // JWT是无状态的，客户端需要自行删除token
        return [];
    }
}