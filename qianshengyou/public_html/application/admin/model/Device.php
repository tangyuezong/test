<?php

namespace app\admin\model;

use think\Model;

class Device extends Model
{
    // 表名
    protected $name = 'device';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
        'iscabinet_text',
    ];
    
    public function getStatusList()
    {
        return ['1' => "上线", '-1' => "下线"];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getTabList()
    {
        return ['1' => __('上线运营设备'), '-1' => __('下线维护设备')];
    }
    
    public function getIsCabinetList()
    {
        return ['' => __('请选择'), '1' => __('储物柜'), '2' => __('陪护床')];
    }
    
    public function getIsCabinetTextAttr($value, $data)
    {
        $value = $value ? $value : $data['iscabinet'];
        $list = $this->getIsCabinetList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function netpoint()
    {
        return $this->belongsTo('Netpoint', 'netpoint_id', 'id', [])->setEagerlyType(0);
    }


    public function entity()
    {
        return $this->belongsTo('Entity', 'entity_id', 'id', [])->setEagerlyType(0);
    }


    public function price()
    {
        return $this->belongsTo('Price', 'price_id', 'id', [])->setEagerlyType(0);
    }
    
    public function devtype()
    {
        return $this->belongsTo('Devtype', 'devtype_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
