<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class SysUser extends Model
{
    // 状态常量
    const STATUS_NORMAL = 0;
    const STATUS_DISABLE = 1;

    // 设置表名
    protected $table = 'sys_user';
    
    // 设置主键 - 修改为匹配数据库的user_id
    protected $pk = 'user_id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 字段映射 - 修改为匹配数据库表结构
    protected $schema = [
        'user_id' => 'int',
        'username' => 'string',
        'password' => 'string',
        'nickname' => 'string',
        'avatar' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'dept_id' => 'int',
        'sex' => 'int',
        'status' => 'int',
        'login_ip' => 'string',
        'login_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    /**
     * 设置密码（加密存储）
     * @param string $value
     * @return string
     */
    public function setPasswordAttr($value)
    {
        return md5($value);
    }
    
    /**
     * 获取最后登录时间（映射login_date字段）
     * @return string
     */
    public function getLastLoginTimeAttr()
    {
        return $this->getAttr('login_date');
    }
    
    /**
     * 设置最后登录时间（映射login_date字段）
     * @param string $value
     * @return void
     */
    public function setLastLoginTimeAttr($value)
    {
        $this->setAttr('login_date', $value);
    }
    
    /**
     * 获取最后登录IP（映射login_ip字段）
     * @return string
     */
    public function getLastLoginIpAttr()
    {
        return $this->getAttr('login_ip');
    }
    
    /**
     * 设置最后登录IP（映射login_ip字段）
     * @param string $value
     * @return void
     */
    public function setLastLoginIpAttr($value)
    {
        $this->setAttr('login_ip', $value);
    }
}