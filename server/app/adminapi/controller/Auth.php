<?php

declare(strict_types=1);

namespace app\adminapi\controller;

use app\adminapi\controller\Base;
use app\adminapi\service\AuthService;
use think\App;

class Auth extends Base
{
    protected $authService;
    
    /**
     * 构造方法
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->authService = new AuthService();
    }
    
    /**
     * 用户登录
     * @return \think\Response
     */
    public function login()
    {
        $username = $this->request->param('username', '');
        $password = $this->request->param('password', '');

        $result = $this->authService->login($username, $password);
        
        if ($result['success']) {
            return $this->success($result['data']);
        } else {
            return $this->error($result['error'], $result['code']);
        }
    }
    
    /**
     * 获取当前用户信息
     * @return \think\Response
     */
    public function user()
    {
        $userId = $this->request->user_id;
        
        // 调用service层获取用户信息
        $result = $this->authService->getUserInfo($userId);
        
        if ($result['success']) {
            return $this->success($result['data'], '获取成功');
        } else {
            return $this->error($result['error'], $result['code']);
        }
    }
    
    /**
     * 用户退出登录
     * @return \think\Response
     */
    public function logout()
    {
        // 调用service层处理退出逻辑
        $result = $this->authService->logout();
        
        if ($result['success']) {
            return $this->success($result['data'], '退出成功');
        } else {
            return $this->error($result['error'], $result['code']);
        }
    }
}