<?php
/**
 * 订单信息
 */
class CabinetorderInfo
{
    public $order_no = "";              // 订单号
    public $users_id = 0;               // 用户ID
    public $phone = "";                 // 用户手机
    public $dev_id = 0;                 // 设备ID
    public $admin_id = 0;               // 管理员ID
    public $entity_id = 0;              // 设备安装实体ID
    public $netpoint_id = 0;            // 网点ID
    public $step = 0;                   // 订单步骤
    public $status = 0;                 // 订单状态
    public $createtime = 0;             // 订单创建时间
    public $endtime = 0;                // 订单结束时间
    
    public function __construct()
    {
        $this->createtime = time();
        $this->step = BikeConstant::ORDER_PAID;
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