<?php
/**
 * 用户信息
 */
class UserInfo
{
    public $id = 0;                     // 用户ID
    public $inviter = 0;                // 邀请人ID
    public $phone = "";                 // 用户手机
    public $openid = "";                // 微信openid
    public $nickname = "";              // 用户昵称
    public $head_image = "";            // 用户头像
    public $wxgender = 0;               // 微信性别
    public $brand = "";                 // 手机品牌
    public $model = "";                 // 手机型号
    public $city = "";                  // 用户所在城市
    public $province = "";              // 用户所在省
    public $balance = 0;                // 账户余额
    public $score = 0;                  // 积分
    public $credit = 0;                 // 信用
    public $deposit = 0;                // 押金
    public $realname = "";              // 用户实名
    public $id_type = 0;                // 证件类型
    public $id_no = "";                 // 证件号码
    public $id_image = "";              // 手持身份证照
    public $realstat = 0;               // 认证状态
    public $createtime = 0;             // 用户创建时间
    public $updatetime = 0;             // 用户更新时间
    public $status = 0;                 // 用户状态
    
    public function __construct()
    {
        $curtime = time();
        $this->createtime = $curtime;
        $this->updatetime = $curtime;
        $this->status = 1;
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