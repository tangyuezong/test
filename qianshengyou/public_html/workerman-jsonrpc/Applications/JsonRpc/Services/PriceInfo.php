<?php
/**
 * 价格信息
 */
class PriceInfo
{
    public $id = 0;                     // 价格ID
    public $lightstart = 0;             // 日间可用开始时间
    public $lightend = 0;               // 日间可用开始时间
    public $nightstart = 0;             // 夜间可用开始时间
    public $nightend = 0;               // 夜间可用开始时间
    public $hourdeposit = 0;            // 按时租押金
    public $hourprice = 0;              // 小时收费价格
    public $timesdeposit = 0;           // 按次租押金
    public $lighttimesprice = 0;        // 日间按次收费价格
    public $nighttimesprice = 0;        // 夜间按次收费价格
    public $daydeposit = 0;             // 按日租押金
    public $dayprice = 0;               // 按日租用价格
    public $status = 0;                 // 状态
    
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