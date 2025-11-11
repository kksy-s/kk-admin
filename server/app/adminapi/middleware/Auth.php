<?php

declare(strict_types=1);

namespace app\adminapi\middleware;

use app\adminapi\model\SysUser;
use app\common\utils\JwtHelper;
use app\common\utils\ResponseHelper;
use think\Response;

class Auth
{
    /**
     * 处理请求
     * @param \think\Request $request
     * @param \Closure $next
     * @param array $config 配置参数
     * @return Response
     */
    public function handle($request, \Closure $next, array $config = [])
    {
        // 1. 获取Token
        $token = $request->header('Authorization');
        
        // 2. 检查Token是否存在
        if (!$token) {
            return ResponseHelper::unauthorized('未提供认证Token');
        }
        
        // 3. 提取Bearer Token
        if (!preg_match('/^Bearer\s+(.*)$/i', $token, $matches)) {
            return ResponseHelper::unauthorized('Token格式错误，请使用Bearer Token');
        }
        
        $token = $matches[1];
        
        // 4. 验证Token有效性
        if (!JwtHelper::validate($token)) {
            return ResponseHelper::unauthorized('Token无效或已过期');
        }
        
        // 5. 解析Token获取用户信息
        try {
            $payload = JwtHelper::decode($token);
            $userId = JwtHelper::getUserId($token);

            // 验证用户是否存在且状态正常
            $user = SysUser::find($payload->user_id);

            if (!$user) {
                return ResponseHelper::unauthorized('用户不存在');
            }
            
            if ($user['status'] !== SysUser::STATUS_NORMAL) {
                return ResponseHelper::forbidden('账号已被禁用');
            }
            
            // 6. 将用户信息存储到请求中，供后续使用
            $request->user_id = $userId;
            $request->user_info = (array)$payload;
            
        } catch (\Exception $e) {
            return ResponseHelper::unauthorized('Token解析失败: ' . $e->getMessage());
        }
        
        // 8. 继续处理请求
        return $next($request);
    }
}