<?php

declare(strict_types=1);

namespace app\adminapi\service;

use app\common\utils\JwtHelper;
use app\adminapi\model\SysUser;
use app\adminapi\validate\AuthValidate;
use think\db\exception\DbException;

class AuthService
{
    protected $authValidate;
    
    /**
     * 初始化
     */
    public function __construct()
    {
        $this->authValidate = new AuthValidate();
    }
    
    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array ['success' => bool, 'data' => mixed, 'error' => string, 'code' => int]
     */
    public function login(string $username, string $password): array
    {
        // 1. 参数验证
        if (!$this->authValidate->checkLogin(['username' => $username, 'password' => $password])) {
            return [
                'success' => false,
                'error' => $this->authValidate->getErrorMsg(),
                'code' => 400
            ];
        }
        
        // 移除try-catch，让数据库异常冒泡到全局异常处理器
        // 2. 查询用户信息（可能抛出数据库异常）
        $user = SysUser::where('username', $username)->find();
        
        if (!$user) {
            return [
                'success' => false,
                'error' => '用户名或密码错误',
                'code' => 401
            ];
        }
        
        // 3. 验证密码
        if (!password_verify($password, $user->password)) {
            // 如果password_verify失败，尝试md5验证（兼容现有数据）
            if (md5($password) !== $user->password) {
                return [
                    'success' => false,
                    'error' => '用户名或密码错误',
                    'code' => 401
                ];
            }
        }
        
        // 4. 检查用户状态
        if ($user->status !== SysUser::STATUS_NORMAL) {
            return [
                'success' => false,
                'error' => '账户已被禁用，请联系管理员',
                'code' => 403
            ];
        }
        
        // 5. 生成JWT Token
        $token = JwtHelper::encode($user->user_id, $user->username);  // 确保传递正确的user_id
        
        // 6. 更新登录信息
        $user->save([
            'login_ip' => request()->ip(),
            'login_date' => date('Y-m-d H:i:s')
        ]);
        
        return [
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'avatar' => $user->avatar,
                    'email' => $user->email,
                    'phone' => $user->phone
                ]
            ]
        ];
    }
    
    /**
     * 获取当前用户信息
     * @param int $userId 用户ID（通过Auth中间件验证过的）
     * @return array ['success' => bool, 'data' => mixed]
     */
    public function getUserInfo(int $userId): array
    {
        // 直接查询用户信息，不需要再次验证存在性
        // 因为Auth中间件已经确保了用户存在
        $user = SysUser::find($userId);
        
        // 直接返回用户信息，不需要判断用户是否存在
        return [
            'success' => true,
            'data' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'phone' => $user->phone,
                'dept_id' => $user->dept_id,
                'sex' => $user->sex,
                'status' => $user->status,
                'created_at' => $user->created_at
            ]
        ];
    }
    
    /**
     * 用户退出登录
     * @return array ['success' => bool, 'data' => mixed]
     */
    public function logout(): array
    {
        // 可以在这里添加退出日志记录等逻辑
        return [
            'success' => true,
            'data' => [
                'message' => '退出成功'
            ]
        ];
    }
}