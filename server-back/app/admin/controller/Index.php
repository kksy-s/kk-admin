<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\controller\Base;

class Index extends Base
{
    public function index()
    {
        return $this->jsonResponse(200, '您好！这是一个[admin]示例应用');
    }
}
