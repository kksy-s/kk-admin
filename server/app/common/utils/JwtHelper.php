<?php
// +----------------------------------------------------------------------
// | JwtHelper
// +----------------------------------------------------------------------
// | 基于thinkphp的JWT工具类，用于生成和验证JWT token
// +----------------------------------------------------------------------

namespace app\common\utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JwtHelper
{
    // JWT密钥
    private static $key = 'kk-admin-jwt-secret-key-2024';
    
    // 算法
    private static $algorithm = 'HS256';
    
    // 过期时间（秒）- 默认2小时
    private static $expire = 7200;

    /**
     * 生成JWT token
     * @param int $userId 用户ID
     * @param string $username 用户名
     * @return string
     */
    public static function encode($userId, $username)
    {
        $payload = [
            'user_id' => $userId,  // 修改为 user_id 保持一致性
            'username' => $username,
            'iat' => time(),     // 签发时间
            'exp' => time() + self::$expire // 过期时间
        ];
        
        return FirebaseJWT::encode($payload, self::$key, self::$algorithm);
    }

    /**
     * 解析JWT token
     * @param string $token
     * @return object
     */
    public static function decode($token)
    {
        return FirebaseJWT::decode($token, new Key(self::$key, self::$algorithm));
    }

    /**
     * 验证token是否有效
     * @param string $token
     * @return bool
     */
    public static function validate($token)
    {
        try {
            self::decode($token);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 从token中获取用户ID
     * @param string $token
     * @return int|null
     */
    public static function getUserId($token)
    {
        try {
            $payload = self::decode($token);
            return $payload->user_id ?? null;  // 修改为 user_id
        } catch (\Exception $e) {
            return null;
        }
    }
}