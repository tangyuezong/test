<?php
/**
 * BikeConstant.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2018-05-17
 * */
class BikeConstant
{
    const EMPTY_STRING = "";
    
    const HYPHEN = "_";
    
    const STAR = "*";
    
    const SEMICOLON = ";";
    
    const TIME_FORMAT_YMDHIS = "YmdHis";
    
    const TIME_FORMAT_YMDHIS_MINUS = "Y-m-d H:i:s";
    
    const TIME_FORMAT_YMDHI_CHN = "Y年m月d日 H:i";
    
    const DEFAULT_PAGESIZE = 10;
    
    const SOCKET_CONN_TIMEOUT = 30;
    
//     const SOCKET_CONN_ADDR_PORT = "tcp://127.0.0.1:8286";
    
    const SOCKET_CONN_ADDR_PORT = "tcp://127.0.0.1:8899";
    
    const SHORT_MSG_TYPE_VERICODE = 1;
    
    const SHORT_MSG_TYPE_DOORLOCK_ALARM = 2;
    
    /*
     * weixin app id & app secret
     */
    const AIRPLUS_WXAPP_APPID = "wxef8b2dde7a8b29a8";
    
    const AIRPLUS_WXAPP_APPSECRET = "967b3a80c604d175feb36e712f03a813";
    
    /*
     * the code for interface
     */
    const Interface_Pass_Code = 0;
    
    const Interface_Reauth_Code = 201;

    const Interface_User_Has_Register_Code = 202;
    
    const Interface_User_Not_Register_Code = 203;
    
    const Interface_Order_Has_Chg_Code = 204;
    
    const Interface_Invalid_Coupon_Code = 205;
    
    const Interface_Need_Deposit_Code = 210;
    
    const Interface_Has_Cabinet_Order_Code = 220;
    
    const Interface_Has_Bed_Order_Code = 230;
    
    const Interface_Error_Code = 400;
    
    /*
     * 支付方式，及支付项
     */
    
    const CUSTOMER_SERVICE = "";
    
    const PAY_CHANNEL_WEIXIN = 1;			// 微信APP支付
    
    const PAY_CHANNEL_ALIPAY = 2;			// 支付宝支付
    
    const PAY_CHANNEL_UPMP = 3;				// 银联支付
    
    const PAY_CHANNEL_COBIKE = 4;			// 钱包支付
    
    const PAY_CHANNEL_WXJSAPI = 5;			// 微信JSAPI支付
    
    const PAY_ITEM_RENT_FEE = 1;			// 订单费用支付
    
    const PAY_ITEM_RENT_FEE_DESC = "租车费用支付";
    
    const PAY_ITEM_BALANCE = 2;				// 平台账户充值
    
    const PAY_ITEM_BALANCE_DESC = "钱包余额充值";
    
    const PAY_ITEM_DEPOSIT = 3;			// 押金支付
    
    const PAY_ITEM_DEPOSIT_DESC = "押金支付";
    
    const PAY_ITEM_RENT_CARS = 4;
    
    const PAY_ITEM_RENT_CARS_DESC = "陪护床租用费";
    
    const PAY_ITEM_GOODS = 5;
    
    const PAY_ITEM_GOODS_DESC = "购买商品费用";
    
    public static $pay_item_desc_map = array(
    		self::PAY_ITEM_RENT_FEE=>self::PAY_ITEM_RENT_FEE_DESC,
    		self::PAY_ITEM_BALANCE=>self::PAY_ITEM_BALANCE_DESC,
    		self::PAY_ITEM_DEPOSIT=>self::PAY_ITEM_DEPOSIT_DESC,
    		self::PAY_ITEM_RENT_CARS=>self::PAY_ITEM_RENT_CARS_DESC,
    		self::PAY_ITEM_GOODS=>self::PAY_ITEM_GOODS_DESC,
    );
    
    //分时 时间 + 费用 最低消费
    const MIN_TIMING_FEE = 10;
    
    const QSTRING_EQUAL = "=";
    
    const QSTRING_SPLIT = "&";
    
    const PARA_WORNG_REASON = "参数错误";
    
    const SERVICE_UNAVAILABLE_MSG = "系统不可用";
    
    /*
     * redis cache key
     */
    const VERICODE_REDIS_PREFIX = "airplus_vericode_";
    
    const VERICODE_REDIS_TIMELIMIT = "airplus_vericode_timelimit_";
    
    const AIRPLUS_HASH_VERICODE_SENDSTAT = "airplus_vericode_sendstat";

    const AIRPLUS_HASH_VERICODE_IP_LIMIT = "airplus_hash_vericode_ip_limit";
    
    const AIRPLUS_HASH_VERICODE_USER_LIMIT = "airplus_hash_vericode_user_limit";
    
    const AIRPLUS_HASH_TOKEN_TIME = "airplus_hash_token_time";
    
    const AIRPLUS_HASH_ORDER = "airplus_hash_order_";
    
    const AIRPLUS_HASH_ADVER = "airplus_hash_adver";
    
    const AIRPLUS_HASH_USERINFO = "airplus_hash_userinfo";
    
    const AIRPLUS_HASH_PHONE_INDEX_TO_UID = "airplus_hash_phone_indexto_uid";

    const AIRPLUS_HASH_OPENID_INDEX_TO_UID = "airplus_hash_openid_indexto_uid";
    
    const AIRPLUS_HASH_CLIENT_INDEX_TO_COMPANY = "airplus_hash_client_indexto_company";
    
    const AIRPLUS_HASH_COMPANY_INFO = "airplus_hash_company_info";
    
    const AIRPLUS_HASH_CAR_DETAIL = "airplus_hash_car_detail";

    const AIRPLUS_HASH_CAR_TYPE = "airplus_hash_car_type";

    const AIRPLUS_HASH_CAR_MODEL = "airplus_hash_car_model";

    const AIRPLUS_HASH_CAR_DISCOUNT = "airplus_hash_car_discount";
    
    const AIRPLUS_HASH_COMPANY_SHORTMSG_TEMPLATE = "airplus_hash_company_shortmsg_template";
    
    const AIRPLUS_HASH_MESSAGE_APP = "airplus_hash_msg_app";
    
    const AIRPLUS_HASH_ACCESS_TOKEN_TO_UID = "airplus_hash_access_token_indexto_uid";
    
    const AIRPLUS_HASH_ACCESS_TOKEN = "airplus_hash_access_token";
    
    const AIRPLUS_HASH_OPENID_INDEX_TO_SESSION_KEY = "airplus_hash_openid_indexto_sessionkey";
    
    const AIRPLUS_HASH_FORMID = "airplus_hash_formid";
    
    const AIRPLUS_HASH_BEDORDER = "airplus_hash_bedorder";
    
    const AIRPLUS_HASH_CABINETORDER = "airplus_hash_cabinetorder";
    
    const AIRPLUS_HASH_USER_CURRENT_ORDER = "airplus_hash_user_current_order";
    
    const AIRPLUS_HASH_USER_CURRENT_CABINETORDER = "airplus_hash_user_current_cabinetorder";

    const AIRPLUS_HASH_TUOGUAN_NOTICE_MAILLIST = "airplus_hash_tuoguan_notice_maillist";
    
    const AIRPLUS_HASH_CARORDER_NOTICE_MAILLIST = "airplus_hash_carorder_notice_maillist";
    
    const AIRPLUS_HASH_WXPAY_CONFIG = "airplus_hash_wxpay_config";
    
    const AIRPLUS_HASH_DEV_IN_REPO = "airplus_hash_dev_in_repo";
    
    const AIRPLUS_HASH_REPO_ID_INDEXTO_SERIAL = "airplus_hash_repo_id_indexto_serial";
    
    const AIRPLUS_HASH_DEVICE_TYPE = "airplus_hash_device_type";
    
    const AIRPLUS_HASH_COUPON_INFO = "airplus_hash_coupon_info";
    
    const AIRPLUS_HASH_USER_COUPON = "airplus_hash_user_coupon";
    
    // 设备信息相关缓存
//     const AIRPLUS_HASH_ID_INDEX_TO_DEVICE_ID = "airplus_hash_id_indexto_devid";
    
    const AIRPLUS_HASH_DEVICE_INFO = "airplus_hash_device_info";
    
    const AIRPLUS_HASH_DEVSIM_INFO = "airplus_hash_devsim_info";
    
    const AIRPLUS_HASH_MAC_INDEXTO_DEVID = "airplus_hash_mac_indexto_devid";
    
    const AIRPLUS_HASH_UID_DEVID_INDEXTO_USER_LOCK = "airplus_hash_uid_devid_indexto_user_lock";
    
    const AIRPLUS_HASH_TEMP_PWD_INFO = "airplus_hash_temp_pwd_info";
    
    const AIRPLUS_HASH_DEV_POWER_AND_ONLINETIME = "airplus_hash_dev_power_and_onlinetime";
    
    const AIRPLUS_HASH_RENTED_DEVICE = "airplus_hash_rented_device";
    
    const AIRPLUS_HASH_NB_LOCK_STAT = "airplus_hash_nb_lock_stat";
    
    const AIRPLUS_HASH_PRICE_INFO = "airplus_hash_price_info";
    
    const AIRPLUS_HASH_NETPOINT_INFO = "airplus_hash_netpoint_info";

    const AIRPLUS_HASH_GOODS_INFO = "airplus_hash_goods_info";
    
    /*
     * DB Constant
     */
    const DB_AIRPLUS = "airplus";
    
    const DB_AIRPLUS_HYPHEN = "airplus_";
    
    const PROJECT_AIRPLUS_HYPHEN = "airplus_";
    
    const TABLE_PREFIX = "fa_";
    
    const TABLE_ADVER = "adver";
    
    const TABLE_ISSUE = "issue";
    
    const TABLE_LOWPOWER = "lowpower";
    
    const TABLE_DEVSIM = "devsim";
    
    const TABLE_USERS = "users";
    
    const TABLE_USERCOUPON = "user_coupon";

    const TABLE_COUPON = "coupon";
    
    const TABLE_CARS = "cars";
    
    const TABLE_CARTYPE = "cartype";
    
    const TABLE_BRANDMODEL = "brandmodel";
    
    const TABLE_DISCOUNT = "discount";
    
    const TABLE_OAUTH = "oauth";
    
    const TABLE_COMPANY = "company";
    
    const TABLE_MSG_APP = "msg_app";
    
    const TABLE_BEDORDER = "bedorder";

    const TABLE_CABINETORDER = "cabinetorder";
    
    const TABLE_TRANS = "trans";
    
    const TABLE_FENRUN = "fenrun";
    
    const TABLE_ORDEREASON = "ordereason";
    
    const TABLE_TUOGUAN = "tuoguan";
    
    const TABLE_MAILNOTICE = "mailnotice";
    
    const TABLE_WXPAY = "wxpay";
    
    const TABLE_DEVICE = "device";

    const TABLE_DEVTYPE = "devtype";
    
    const TABLE_PRICE = "price";
    
    const TABLE_NETPOINT = "netpoint";
    
    const TABLE_GOODS = "goods";
    
    const TABLE_GOODSORDER = "goodsorder";
    
    public static $adver_cols = array("adver_image","adver_url","wx_url");
    
    public static $compamy_cols = array("id","cname","clientId","clientSecret");
    
    public static $msg_template_cols = array("id","appid","appkey","msgsign");
    
    public static $userinfo_cols = array("id", "cid", "name", "nickname", "password", "mobile", "createtime", "status", "head_url_image", "push_type", "push_token", "role_id");
    
    public static $brandmodel_cols = array("id", "name", "level", "color", "engine", "gearbox", "bodywork", "carlevel", "price");
    
    public static $cartype_cols = array("id", "typename", "type_image", "drivetype", "slogan");
    
    public static $car_discount_cols = array("id", "cid", "pricedesc", "days1", "discount1", "days2", "discount2", "days3", "discount3", "days4", "discount4", "days5", "discount5", "days6", "discount6");
    
    public static $device_type_cols = array("id", "name", "type", "status");
    /*
     * User Authentication Constant
     */
    const INVITE_CODE_MIN_LENGTH = 3;
    
    const VERICODE_PASS_REASON = "验证码发送成功";
    
    const VERICODE_FAILED_REASON = "验证码发送失败";
    
    const VERICODE_EXCEPTION_REASON = "验证码发送异常";
    
    const VERICODE_TIMELIMIT_REASON = "验证码三分钟内不能重复发送";
    
    const AUTHENCODE_PASS_REASON = "登陆成功";
    
    const AUTHENCODE_FAILED_REASON = "登陆失败";
    
    const AUTHEN_WRONG_PASSWORD_REASON = "密码错误";
    
    const AUTHENCODE_WRONG_REASON = "验证码错误";
    
    const AUTHENCODE_EXPIRED_REASON = "验证码已失效";
    
    const AUTHENCODE_WRITE_DB_REASON = "登陆操作失败";
    
    const REGISTER_PASS_REASON = "注册成功";
    
    const REGISTER_WRITE_DB_REASON = "注册失败";
    
    const FORGET_PASSWORD_WRITE_DB_REASON = "修改密码操作失败";
    
    const FORGET_PASSWORD_PASS_REASON = "修改密码成功";
    
    const AUTHENCODE_GENERATE_ID_WRONG_REASON = "用户生成失败";
    
    const TOKEN_VALIDATION_FAILED_MSG = "账号已在其他设备登陆，请重新登录";
    
    const USER_NOT_EXIST_MSG = "用户信息获取失败";
    
    const USER_HAS_REGISTER_MSG = "手机已被绑定";
    
    const PHONE_BINDED_PASS_MSG = "手机绑定成功";
    
    const PHONE_BINDED_ERROR_MSG = "手机绑定失败";
    
    const USER_NOT_REGISTER_MSG = "手机未注册";
    
    const LOGOUT_PASS_MSG = "登陆退出成功";
    
    /*
     * order constant
     */
    const ORDER_SUBMITTED = 700;         // 订单已提交
    
    const ORDER_PAID = 1500;             // 已支付
    
    const ORDER_FINISHED = 9000;         // 订单完成
    
    /*
     * Save Redis Error Msg Constant
     */
    const INFO_SAVE_REDIS_SUCCESS_MSG = "内存缓存成功";
    
    const INFO_SAVE_REDIS_FAILED_MSG = "信息内存缓存失败";
    
    const INFO_SAVE_REDIS_EXCEPTION_MSG = "信息内存缓存出现异常";
    
    const INFO_SAVE_REDIS_EMPTY_KEY_MSG = "信息内存缓存key为空";
    
    const INFO_GET_REDIS_SUCCESS_MSG = "获取内存缓存成功";
    
    const INFO_GET_REDIS_EMPTY_KEY_MSG = "获取信息内存缓存key为空";
    
    const INFO_GET_REDIS_EXCEPTION_MSG = "获取信息内存缓存出现异常";
    
    /*
     * Pay Constant
     */
    const AIRPLUS_ALIPAY_APP_ID = "2016102902392667";
    
    const ALIPAY_DATA_FORMAT_JSON = "json";
    //api版本
    const ALIPAY_API_Version = "1.0";
    // 表单提交字符集编码
    const ALIPAY_CHARSET = "UTF-8";
    //加密密钥和类型
    const ALIPAY_SIGN_TYPE = "RSA";
    
    const AIRPLUS_COBIKE_RSA_PRIVATE_KEY = "MIICXAIBAAKBgQDSH2cFxMhSJimYTB7989ZQrI8dksd9WlaRlsU4+uTpwFytBcw4bMnj/j2hF53YKLbQl67YTP2e6p/+Rmum8jYGzocA9ofBAh5NJE3sdEJXMeiXUH3ouZgp6dpFk0dPk9vWouB689AFSnz8rBSUcWmYT7tryWQqh92+JgddZR2o1QIDAQABAoGAD6B6PvmVlFZ2PXdbzrM1uyY6No7V+0KesZEu9b/jCmdd/RgzSfb9RNGBr9tbx9mvTvAY9skzC4CTiYufMflNfy19qqKHdiP3LNzCWQKSo/sfKKiC7BkbHN4H1bLbCbI78jQLj6qZC2Ed9e7d4NwEwD3iYC/GWcQxPQxnNWZkbcECQQD7ExRdh27xxMd6wRxl0kOdeCvcVZXY8nIw1/sEIRMa+P+PBg+eSPJTUugkeqleGT+JFsHrl/ptDGaXK/m1caWJAkEA1j6onzGoVnO9jQTJ6lHmG11q8iGRltcyQLFrTDIT23BruEOxE7sS2hnZB48B+yxoLzzfiaBoRseczY6BVJrh7QJBAIGpT8ohaB05Z18wnW7EEKEg713BYTqBspEg6QQv5IL4dloxYh13RJXdaf90zUTIOzSb6Re3C+AHGHAXiMS4ZSkCQEs29DvvUwoG2CUJ6Vk6J26z/TfEUTiDlEDiCKlXa2E+tpKaMTCcHKI1MNxWeHuVu33aASBwECvJKGOCBCWPuoECQD7a08FXXRHbQOIzfVKUFlkoC5BllbxooEQ1B5gQX6MTrWC/hWG1/q9u2zxbShV0ZvdSG5GS7dcFXo37pdZ0OUw=";
    
    const AIRPLUS_ALIPAY_RSA_PUBLIC_KEY = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB";
    
    // biz content encrypt cipher and mode
    const AIRPLUS_ALIPAY_AES_KEY = "HNtfwSA5NeGu0wA3q19ObA==";
    
    const CIPHER_AES_256 = "rijndael-256";
    
    const CIPHER_MODE_CBC = "cbc";
    
    const ALIPAY_TRADE_APP_PAY = "alipay.trade.app.pay";
    
    const ALIPAY_TRADE_QUERY = "alipay.trade.query";
    
    const ALIPAY_TRADE_REFUND = "alipay.trade.refund";
    
    const AIRPLUS_ALIPAY_NOTIFY_URL = "https://bikeapi.roadar.cn/api/NotifyUrl/alinotify";
    
    const PAY_INVOKE_SUCCESS_MSG = "获取支付信息成功";
    
    const ALIPAY_ENCOUNTER_EXCEPTION_MSG = "支付宝支付调用异常";
    
    const WXPAY_ENCOUNTER_EXCEPTION_MSG = "微信支付调用异常";
    
    const REFUND_USER_ACCT_EMPTY_MSG = "获取退款用户账户信息为空";
    
    const REFUND_USER_HAS_ORDER_MSG = "用车过程中不能退押金";
    
    const ALIREFUND_ENCOUNTER_EXCEPTION_MSG = "支付宝退款调用异常";
    
    const WXREFUND_ENCOUNTER_EXCEPTION_MSG = "微信退款调用异常";
    
    const REFUND_INVOKE_SUCCESS_MSG = "退款成功";
    
    const REFUND_CHANNEL_ERROR_MSG = "退款渠道错误";
    
    const REFUND_TRANS_EMPTY_MSG = "押金充值记录为空";
    
    const REFUND_ENCOUNTER_EXCEPTION_MSG = "退款发生异常";
    
}