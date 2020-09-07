<?php

namespace app\admin\model;

use think\Model;

class Issue extends Model
{
    // 表名
    protected $name = 'issue';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
    ];
    
    public function getStatusList()
    {
        return ['0' => __('Pending'), '1' => __('Processing'), '9' => __('Finish')];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function netpoint()
    {
        return $this->belongsTo('Netpoint', 'netpoint_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function entity()
    {
        return $this->belongsTo('Entity', 'entity_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function users()
    {
        return $this->belongsTo('Users', 'users_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
