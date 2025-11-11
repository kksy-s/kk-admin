<?php
declare (strict_types = 1);

namespace app\adminapi\model;

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
    
    // 设置主键
    protected $pk = 'user_id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 字段映射
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
     * 设置密码（使用password_hash加密）
     * @param string $value
     * @return string
     */
    public function setPasswordAttr($value)
    {
        // 使用更安全的password_hash加密
        return password_hash($value, PASSWORD_DEFAULT);
    }
    
    /**
     * 验证密码
     * @param string $password 明文密码
     * @param string $hash 加密后的密码
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        // 先尝试password_verify验证
        if (password_verify($password, $hash)) {
            return true;
        }
        
        // 如果password_verify失败，尝试md5验证（兼容现有数据）
        if (md5($password) === $hash) {
            // 如果是md5加密的密码，升级为password_hash
            $this->password = $password; // 触发setPasswordAttr重新加密
            $this->save();
            return true;
        }
        
        return false;
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