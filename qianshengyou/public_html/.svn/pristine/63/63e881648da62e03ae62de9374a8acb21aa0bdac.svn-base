<?php
use Workerman\Lib\Db;

/**
 * Advertise.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2016-7-27
 * */
class Advertise
{
    /**
     * 获取广告页活动接口
     * 
     * @param array $request
     * @return json
     */
    public static function adverlist($request)
    {
        $db = Db::instance('cobike_mysql');
        try {
            // 获取广告活动列表
            $adverlist = $db->orderByDESC(array("weigh"))->select(array("adver_image", "adver_url", "wx_url"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_ADVER))->where("status=1")->query();
            if(!empty($adverlist)) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取成功", ["list"=>$adverlist, "domain"=>$request['domain']]);
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
