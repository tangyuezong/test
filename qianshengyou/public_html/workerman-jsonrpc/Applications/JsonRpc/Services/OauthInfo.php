<?php
/**
 * 用户信息
 */
class OauthInfo
{
    public $id = 0;                     // 用户ID
    public $access_token = "";          // 访问令牌
    public $expire_in = 86400;          // 过期时长，0表示不过期
    public $clientip = "";              // 客户端ip
    public $createtime = 0;             // 微信设置性别
    
    public function __construct()
    {
        $this->createtime = time();
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __set($name, $val)
    {
        $this->$name = $val;
    }
    
}
