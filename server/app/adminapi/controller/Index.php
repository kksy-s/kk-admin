<?php
declare (strict_types = 1);

namespace app\adminapi\controller;

use app\adminapi\controller\Base;

class Index extends Base
{
    public function index()
    {
        return '您好！这是一个[adminapi]示例应用';
    }
}
