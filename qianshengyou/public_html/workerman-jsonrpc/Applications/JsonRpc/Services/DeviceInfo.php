<?php
/**
 * 车辆详情信息
 */
class DeviceInfo
{
    public $id = 0;                     // 车辆ID
    public $devtype_id = 0;             // 设备类型ID
    public $dev_id = 0;                 // 设备ID
    public $iscabinet = 0;              // 是否储物柜
    public $price_id = 0;               // 价格ID
    public $netpoint_id = 0;            // 网点ID
    public $entity_id = 0;              // 设备安装实体ID
    public $mac = "";                   // 设备mac
    public $blekey = "";                // 设备秘钥
    public $blepwd = "";                // 设备开锁密码
    public $mac2 = "";                  // 抽屉mac
    public $blekey2 = "";               // 抽屉秘钥
    public $blepwd2 = "";               // 抽屉开锁密码
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