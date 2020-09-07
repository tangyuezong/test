<?php
namespace app\admin\controller\order;
/**
 * 报表信息
 */
class ReportInfo
{
    public $netpoint_name = "";         // 网点名称
    public $devicenums = "";            // 网点设备数量
    public $hourorders = 0;             // 按时订单数
    public $houramount = 0;             // 按时订单金额
    public $lightorders = 0;            // 日间按次订单数
    public $lightamount = 0;            // 日间订单金额
    public $nightorders = 0;            // 夜间按次订单数
    public $nightamount = 0;            // 夜间订单金额
    public $timesorders = 0;            // 按次订单数
    public $timesamount = 0;            // 按次订单金额
    public $dayorders = 0;              // 按天订单数
    public $dayamount = 0;              // 按天订单金额
    public $freeorders = 0;             // 免费订单数
    public $totalorders = 0;            // 总订单数
    public $totalamount = 0;            // 总订单金额
    public $userate = 0;                // 设备使用率
    public $incomerate = 0;             // 设备收益率
    
    public function __construct()
    {
        
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