<?php
namespace app\common;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use think\facade\Env;

class Jwt
{
    // JWT密钥
    private static $key;
    
    // 算法
    private static $algorithm;
    
    // 过期时间（秒）
    private static $expire;

    /**
     * 获取配置
     */
    private static function getConfig()
    {
        if (self::$key === null) {
            self::$key = Env::get('JWT_SECRET', 'kk-admin-jwt-secret-key-2024');
            self::$algorithm = Env::get('JWT_ALGORITHM', 'HS256');
            self::$expire = Env::get('JWT_EXPIRE', 7200);
        }
    }

    /**
     * 生成JWT token
     * @param array $payload 载荷数据
     * @return string
     */
    public static function encode($payload)
    {
        self::getConfig();
        
        $payload = array_merge([
            'iss' => 'kk-admin', // 签发者
            'iat' => time(),     // 签发时间
            'exp' => time() + self::$expire, // 过期时间
        ], $payload);
        
        return FirebaseJWT::encode($payload, self::$key, self::$algorithm);
    }

    /**
     * 解析JWT token
     * @param string $token
     * @return object
     */
    public static function decode($token)
    {
        self::getConfig();
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
     * 刷新token
     * @param string $token
     * @return string
     */
    public static function refresh($token)
    {
        $payload = self::decode($token);
        $payloadArray = (array)$payload;
        
        // 更新签发时间和过期时间
        $payloadArray['iat'] = time();
        $payloadArray['exp'] = time() + self::$expire;
        
        return self::encode($payloadArray);
    }
}