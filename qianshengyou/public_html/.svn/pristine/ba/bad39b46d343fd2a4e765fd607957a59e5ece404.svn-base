<?php

namespace app\admin\model;

use think\Model;

class Netpoint extends Model
{
    // 表名
    protected $name = 'netpoint';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text',
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
    
    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function mtadmin()
    {
        return $this->belongsTo('Admin', 'mt_admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function malladmin()
    {
        return $this->belongsTo('Admin', 'mall_admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
