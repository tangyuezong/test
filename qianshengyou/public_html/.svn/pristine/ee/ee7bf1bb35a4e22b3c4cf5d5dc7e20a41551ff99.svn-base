<?php

namespace app\admin\model;

use think\Model;

class Trans extends Model
{
    // 表名
    protected $name = 'trans';
    
    // 追加属性
    protected $append = [
        'payment_text',
        'item_text'
    ];

    public function getPaymentList()
    {
        return ['1' => __('APP'), '1' => __('JSAPI')];
    }
    
    public function getPaymentTextAttr($value, $data)
    {
        $value = $value ? $value : $data['payment'];
        $list = $this->getPaymentList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getItemList()
    {
        return ['4' => __('Order')];
    }
    
    public function getItemTextAttr($value, $data)
    {
        $value = $value ? $value : $data['item'];
        $list = $this->getItemList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}
