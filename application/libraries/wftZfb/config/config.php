<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',	//支付请求url，无需更改
        'mchId'=>'7551000001',		//测试商户号，商户正式上线时需更改为自己的
        'key'=>'9d101c97133837e13dde2d32a5054abb',   //测试密钥，商户需更改为自己的
		'notify_url'=>'http://zhangwei.dev.swiftpass.cn/payInterface_V1.3/request.php?method=callback',//测试通知url，商户需更改为自己的，保证能被外网访问到（否则支付成功后收不到威富通服务器所发通知）
		
        'version'=>'1.0'		//版本号
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>