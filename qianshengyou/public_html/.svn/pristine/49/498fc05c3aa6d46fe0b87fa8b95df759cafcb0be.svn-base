<?php

namespace app\admin\model;

use think\Model;

class Goodsorder extends Model
{
    // 表名
    protected $name = 'goodsorder';
    
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
        return ['1' => __('Pending'), '9' => __('Finish'), '99999' => __('Cancel')];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function users()
    {
        return $this->belongsTo('Users', 'users_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function netpoint()
    {
        return $this->belongsTo('Netpoint', 'netpoint_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function trans()
    {
        return $this->belongsTo('Trans', 'order_no', 'order_no', [], 'LEFT')->setEagerlyType(0);
    }
}
