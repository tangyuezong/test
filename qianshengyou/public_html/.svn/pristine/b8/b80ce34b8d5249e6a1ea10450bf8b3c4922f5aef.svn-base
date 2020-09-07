<?php

namespace app\admin\model;

use think\Model;

class Cabinetorder extends Model
{
    // 表名
    protected $name = 'cabinetorder';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'step_text',
        'status_text',
        'endtime_text'
    ];
    
    public function getStepList()
    {
        return ['1500' => __('Submitted'), '9000' => __('Finished')];
    }
    
    public function getStepTextAttr($value, $data)
    {
        $value = $value ? $value : $data['step'];
        $list = $this->getStepList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getStatusList()
    {
        return ['1' => __('Normal'), '0' => __('Invalid'), '-1' => __('Cancel')];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setEndtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function users()
    {
        return $this->belongsTo('Users', 'users_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function netpoint()
    {
        return $this->belongsTo('Netpoint', 'netpoint_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function entity()
    {
        return $this->belongsTo('Entity', 'entity_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function device()
    {
        return $this->belongsTo('Device', 'dev_id', 'dev_id', [], 'LEFT')->setEagerlyType(0);
    }
}
