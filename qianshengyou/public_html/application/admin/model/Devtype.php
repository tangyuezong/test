<?php

namespace app\admin\model;

use think\Model;

class Devtype extends Model
{
    // 表名
    protected $name = 'devtype';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
        'type_text'
    ];
    
    public function getStatusList()
    {
        return ['1' => __('Normal'), '0' => __('Abnormal')];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    /*
     * 1：蓝牙 属性
     * 2：NB 属性
     * 4：xx 属性
     * 8：xx 属性
     */
    public function getTypeList()
    {
        return ['1' => __('蓝牙'), '2' => __('NB')];
    }
    
    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        $return = "";
        foreach ($list as $key=>$val) {
            if(intval($key)&$value) $return .= $val.",";
        }
        return rtrim($return, ",");
    }
    
}
