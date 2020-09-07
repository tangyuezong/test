<?php

namespace app\admin\model;

use think\Model;

class Bedorder extends Model
{
    // 表名
    protected $name = 'bedorder';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'refundtime_text',
        'endtime_text',
        'step_text',
        'status_text',
        'lightnight_text',
        'pricetype_text',
        'payment_text',
        'item_text'
    ];
    
    public function getStepList()
    {
        return ['1500' => __('Paid'), '9000' => __('Finished')];
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
    
    public function getLightnightList()
    {
        return ['1' => __('Light'), '2' => __('Night')];
    }
    
    public function getLightnightTextAttr($value, $data)
    {
        $value = $value ? $value : $data['lightnight'];
        $list = $this->getLightnightList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getPricetypeList()
    {
        return ['1' => __('Hourtype'), '2' => __('Timestype'), '3' => __('Daytype')];
    }
    
    public function getPricetypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['pricetype'];
        $list = $this->getPricetypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getPaymentList()
    {
        return ['1' => __('APP'), '1' => __('JSAPI')];
    }
    
    public function getPaymentTextAttr($value, $data)
    {
//         var_dump($value);
//         var_dump($data);
//         $value = $value ? $value : $data['payment'];
        $list = $this->getPaymentList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getItemList()
    {
        return ['4' => __('Order')];
    }
    
    public function getItemTextAttr($value, $data)
    {
//         $value = $value ? $value : $data['item'];
        $list = $this->getItemList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getRefundtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refundtime']) ? $data['refundtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setRefundtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
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


    public function trans()
    {
        return $this->belongsTo('Trans', 'order_no', 'order_no', [], 'LEFT')->setEagerlyType(0);
    }
}
