<?php
/**
 * 订单信息
 */
class GoodsorderInfo
{
    public $order_no = "";              // 订单号
    public $users_id = 0;               // 用户ID
    public $goods_id = 0;               // 商品ID
    public $price = 0;                  // 商品价格
    public $num = 0;                    // 商品数量
    public $total = 0;                  // 总价
    public $netpoint_id = 0;            // 网点ID
    public $department = "";            // 科室
    public $room = "";                  // 病房
    public $admin_id = 0;               // 操作员ID
    public $createtime = 0;             // 订单创建时间
    public $updatetime = 0;             // 订单更新时间
    public $status = 1;                 // 订单状态
    
    public function __construct()
    {
        $this->createtime = $this->updatetime = time();
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