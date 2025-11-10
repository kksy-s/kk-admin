<?php
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Auth extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'username' => 'require|min:3|max:20',
        'password' => 'require|min:6|max:20',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'username.require' => '用户名不能为空',
        'username.min'     => '用户名长度不能少于3个字符',
        'username.max'     => '用户名长度不能超过20个字符',
        'password.require' => '密码不能为空',
        'password.min'     => '密码长度不能少于6个字符',
        'password.max'     => '密码长度不能超过20个字符',
    ];

    /**
     * 场景验证 登录 新增用户 修改用户
     * @return Auth
     */
    public function sceneLogin()
    {
        return $this->only(['username', 'password']);
    }   
    
}