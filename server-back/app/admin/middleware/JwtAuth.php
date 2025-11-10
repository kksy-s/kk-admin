<?php
declare (strict_types = 1);

namespace app\admin\middleware;

use app\admin\model\SysUser;
use app\common\Jwt;
// 移除这行：use think\facade\Request;

class JwtAuth
{
    /**
     * 处理请求
     * @param \think\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
         // 不需要验证的路由
        $except = [
            'admin/auth/login',
            'admin/auth/logout',
        ];

        $path = strtolower(trim($request->url(), '/'));

        // 路由排除
        foreach ($except as $route) {
            if (str_starts_with($path, $route)) {
                return $next($request);
            }
        }
        
        // 获取token
        $token = $request->header('Authorization');
        
        if (empty($token)) {
            return json([
                'code' => 401,
                'message' => '未授权访问',
                'timestamp' => time()
            ], 401);
        }
        
        // 去除Bearer前缀
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        // 验证token
        try {
            $payload = Jwt::decode($token);
            
            // 验证用户是否存在且状态正常
            $user = SysUser::find($payload->user_id);

            if (!$user) {
                return json([
                    'code' => 401,
                    'message' => '用户不存在',
                    'timestamp' => time()
                ], 401);
            }
            
            if ($user['status'] !== SysUser::STATUS_NORMAL) {
                return json([
                    'code' => 403,
                    'message' => '账号已被禁用',
                    'timestamp' => time()
                ], 403);
            }
            
            // 将用户信息存储到请求中
            $request->user = $payload;
            $request->userInfo = $user;
            
            return $next($request);
        } catch (\Exception $e) {
            return json([
                'code' => 401,
                'message' => 'Token验证失败：' . $e->getMessage(),
                'timestamp' => time()
            ], 401);
        }
    }
}