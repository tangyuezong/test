<?php
/**
 * 订单信息
 */
class BedorderInfo
{
    public $order_no = "";              // 订单号
    public $users_id = 0;               // 用户ID
    public $phone = "";                 // 用户手机
    public $dev_id = 0;                 // 设备ID
    public $admin_id = 0;               // 管理员ID
    public $entity_id = 0;              // 设备安装实体ID
    public $netpoint_id = 0;            // 网点ID
    public $pricetype = 0;              // 计费类型
    public $price_id = 0;               // 价格ID
    public $deposit = 0;                // 押金
    public $lightnight = 0;             // 日间夜间标识
    public $canusestart = 0;            // 计费开始时间
    public $canuseend = 0;              // 计费结束时间
    public $hourprice = 0;              // 按时计费价格
    public $lighttimesprice = 0;        // 日间按次计费价格
    public $nighttimesprice = 0;        // 夜间按次计费价格
    public $dayprice = 0;               // 按天计费价格
    public $days = 0;                   // 租用天数
    public $pricedata = "";             // 计费json数据
    public $total = 0;                  // 订单总金额
    public $save = 0;                   // 优惠券
    public $pay = 0;                    // 线上实付金额
    public $tran_id = "";               // 微信订单号
    public $refund = 0;                 // 退款金额
    public $refundnote = "";            // 取消订单退款标注
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