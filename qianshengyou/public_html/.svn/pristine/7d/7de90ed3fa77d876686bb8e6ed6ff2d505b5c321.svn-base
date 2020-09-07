<?php

namespace app\admin\model;

use think\Model;

class Fenrun extends Model
{
    // 表名
    protected $name = 'fenrun';
    
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
        return ['1' => __('已分润'), '0' => __('未分润'), '-1' => __('分润调用微信付款接口返回错误')];
    }
    
    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function accountset()
    {
        return $this->belongsTo('Accountset', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
