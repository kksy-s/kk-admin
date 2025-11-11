<?php

declare(strict_types=1);

namespace app\adminapi\validate;

use think\Validate;

class AuthValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'username' => 'require|min:2|max:50|alphaDash',
        'password' => 'require|min:6|max:32',
    ];
    
    /**
     * 错误提示信息 没有注册 所以 不需要提示那么详细 不给用户具体的错误信息
     * @var array
     */
    protected $message = [
        'username.require' => '用户名或密码不正确',
        'username.min' => '用户名或密码不正确',
        'username.max' => '用户名或密码不正确',
        'username.alphaDash' => '用户名或密码不正确',
        'password.require' => '用户名或密码不正确',
        'password.min' => '用户名或密码不正确',
        'password.max' => '用户名或密码不正确',
    ];
    
    /**
     * 登录场景验证
     * @var array
     */
    protected $scene = [
        'login' => ['username', 'password'],
    ];
    
    /**
     * 自定义验证 - 登录参数验证
     * @param array $data 登录数据
     * @return bool
     */
    public function checkLogin(array $data): bool
    {
        if (!$this->scene('login')->check($data)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取验证错误信息
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->getError();
    }
}