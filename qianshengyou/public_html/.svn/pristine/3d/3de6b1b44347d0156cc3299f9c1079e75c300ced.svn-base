<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Mall.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2016-7-27
 * */
class Mall
{
    /**
     * 商城订单列表
     * @param unknown $request
     */
    public static function orderlist($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        //参数
        $access_token = $request['access_token'];
        // token过期校验
        $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
        if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
        $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
        if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
        // 业务处理
        try {
            $tableorder = BikeUtil::table_full_name(BikeConstant::TABLE_GOODSORDER);
            $tablegoods = BikeUtil::table_full_name(BikeConstant::TABLE_GOODS);
            $cond['users_id'] = $uid;
            $list = $db->orderByASC(array("$tableorder.status"))->orderByDESC(array("$tableorder.createtime"))->select(array("order_no", "$tableorder.price", "num", "total", "$tablegoods.name", "$tableorder.status"))->
                from($tableorder)->innerJoin($tablegoods, "$tableorder.goods_id = $tablegoods.id")->
                where("users_id=:users_id and $tableorder.status in (1,9)")->bindValues($cond)->query();
            if(!empty($list)) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取成功", $list);
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "列表为空");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
}
