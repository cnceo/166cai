<?php

/**
 * 商户号添加处理类
1.易宝wap,2.易宝快捷、网银、银行卡,3.连连快捷,4.连连SDK,5.中信微信,6.统统付wap,7.统统付快捷,8.微信SDK,
        9.威富通支付宝,10.威富通微信sdk,11.威富通微信PC,12.现在支付宝h5,13.京东支付,
        14.卡前置-联动支付h5,15.汇聚无限支付宝h5,16.兴业支付宝H5,17.微众银行支付宝,
        18.微信H5-兴业银行,19.鸿粤浦发银行,20.恒丰银行支付宝
 */
class CreatePayConfig
{
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('model_pay_config', 'model');
	}
	public function payType1($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"productcatalog":"1","identitytype":0,"userua":"","callbackurl":"$web_url/api/recharge/notice/YeepayMPay/#id#","fcallbackurl":"$web_url/api/recharge/syncCallback/YeepayMPay/#id#","productname":"166彩票充值","productdesc":"彩咖充值","terminaltype":3,"terminalid":"05-16-DC-59-C2-34","cardno":"","idcardtype":"01","currency":156}';
        $add_ext = array('merchantaccount'=>'商户号','merchantpublicKey'=>'私钥','merchantPublicKey'=>'公钥','yeepayPublicKey'=>'易宝公钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            
            $para = '';
            $mark = '快捷-易宝支付wap';
            if($platform==4)
            {
                $para = '{"name":"银行卡快捷-易宝支付","tips":"最高5千/笔，1万/日，2万/月","class":"pay-ybzf","value":"yeepayMPay|5000","id":"payYbzf","lib":"YeepayMPay","pay_type":"1","view":"yeepay","payWay":"yeepayMPay","mode_str":"4_1"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['merchantaccount'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            
            return $this->returnRes($insertData);     
        }
    }

    public function payType2($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"p9_SAF":"0","p0_Cmd":"Buy","p4_Cur":"CNY","p5_Pid":"166cp","p6_Pcat":"166cp","p7_Pdesc":"166cp","p8_Url":"$web_url/api/recharge/syncAnotice/YeepayWPay/#id#","pa_MP":"","pd_FrpId":"YJZF-NET-B2C","pm_Period":"7","pr_NeedResponse":"1","reqURL_onLine":"https://www.yeepay.com/app-merchant-proxy/node"}';
        $add_ext = array('p1_MerId'=>'商户号','merchantKey'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '快捷-易宝支付pc快捷';
            $para = '{"lib":"YeepayWPay","ctype":"1","submit_url":"","mode":"yeepayKuaij","mode_str":"1_1","img_src":"\/caipiaoimg\/v1.1\/img\/bank\/ybzf.png","img_alt":"\u6613\u5b9d\u652f\u4ed8","img_w":"128","img_h":"38"}';
            if($params['ctype'] ==5)
            {
              $comm_ext = '{"p9_SAF":"0","p0_Cmd":"Buy","p4_Cur":"CNY","p5_Pid":"166cp","p6_Pcat":"166cp","p7_Pdesc":"166cp","p8_Url":"$web_url/api/recharge/syncAnotice/YeepayWPay/#id#","pa_MP":"","pm_Period":"7","pr_NeedResponse":"1","reqURL_onLine":"https://www.yeepay.com/app-merchant-proxy/node"}';  
              $para = '{"lib":"YeepayWPay","ctype":"5","submit_url":"","mode":"yeepayWangy,yeepayCredit","mode_str":"1_5,1_6","way_sort":"4,5","recharge_url":"\/wallet\/recharge\/bank,\/wallet\/recharge\/credit","directPay_url":"\/wallet\/directPay\/bank,\/wallet\/directPay\/credit","name":"\u7f51\u4e0a\u94f6\u884c,\u4fe1\u7528\u5361"}';
              $mark = '快捷-易宝支付pc网银、银行卡';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mer_id'=>$params['p1_MerId'],'mark'=>$mark,'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);    
        }
    }

    public function payType5($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"encoding":"UTF-8","signMethod":"02","txnType":"01","txnSubType":"010132","channelType":"6002","payAccessType":"02","backEndUrl":"$web_url/api/recharge/notice/ZxWeixinPay/#id#","productId":"1","orderBody":"彩咖充值","currencyType":"156","payGateway":"https://120.55.176.124:8090/MPay/backTransAction.do"}';
        $add_ext = array('merId'=>'商户号','key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '微信扫码-中信支付wap微信扫码';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['merId'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType6($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"payUrl":"https://wapcashier.sumpay.cn/service/rest.htm","goods_name":"彩咖充值","terminal_type":"wap","service":"sumpay.wap.trade.order.apply","apiUrl":"https://open.sumpay.cn/api.htm","notify_url":"$web_url/api/recharge/notice/SumPay/#id#","return_url":"$web_url/api/recharge/syncCallback/SumPay/#id#"}';
        $add_ext = array('mer_id'=>'商户号','app_id'=>'app_id');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '快捷-统统付wap';
            if($params['platform'] ==4)
            {
                $para = '{"name":"银行卡快捷-统统付","tips":"最高3千/笔，5千/日，1万/月","class":"pay-ttf","value":"sumpayWap|3000","id":"payTtf","lib":"SumPay","pay_type":"6","view":"sumpay","payWay":"sumpayWap","mode_str":"4_1"}';
            } 
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mer_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType7($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"payUrl":"https://pc.sumpay.cn/cashier/service/rest.htm","goods_name":"彩咖充值","terminal_type":"web","service":"sumpay.web.trade.order.apply","apiUrl":"https://open.sumpay.cn/api.htm","notify_url":"$web_url/api/recharge/notice/SumPay/#id#","return_url":"$web_url/api/recharge/syncCallback/SumPay/#id#"}';
        $add_ext = array('mer_id'=>'商户号','app_id'=>'app_id');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '{"lib":"SumPay","ctype":"'.$params['ctype'].'","submit_url":"","mode":"sumpayWeb","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"\/caipiaoimg\/v1.1\/img\/bank\/ttf.png","img_alt":"\u7edf\u7edf\u4ed8","img_w":"117","img_h":"38"}';
            $mark = '快捷-统统付pc';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mer_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType8($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"encoding":"UTF-8","signMethod":"02","txnType":"01","txnSubType":"010132","channelType":"6002","payAccessType":"02","backEndUrl":"$web_url/api/recharge/notice/ZxWeixinPay/#id#","productId":"1","orderBody":"彩咖充值","currencyType":"156","payGateway":"https://120.55.176.124:8092/MPay/backTransAction.do"}';
        $add_ext = array('merId'=>'商户号','key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '微信支付-中信微信sdk';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['merId'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType9($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://pay.swiftpass.cn/pay/gateway","service":"pay.alipay.native","body":"彩咖充值","version":"2.0","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/WftPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '{"lib":"WftPay","ctype":"'.$params['ctype'].'","submit_url":"//888.166cai.cn/wallet/recharge/getwftZfb/","mode":"payZfb","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"/caipiaoimg/v1.1/img/bank/zfbzf.png","img_alt":"u652fu4ed8u5b9du652fu4ed8","img_w":"128","img_h":"38"}';
            $mark = '支付宝-威富通支付宝pc扫码';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType31($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"http://mapi.bosc.uline.cc/wechat/orders","trade_type":"MWEB","payment_code":"WX_ONLINE_MWEB","body":"166充值","notify_url":"$web_url/api/recharge/notice/UlinePay/#id#","backurl":"$web_url/api/recharge/syncCallback/UlinePay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'秘钥');
	if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '微信H5-上海银行';
            if($params['platform'] == 4)
            {
                $para = '{"name":"微信支付","tips":"最高5千/笔","class":"pay-wx","value":"ulineWxWap|5000","id":"ulineWxWap","lib":"UlinePay","pay_type":"31","view":"ulineWxWap","payWay":"ulineWxWap","mode_str":"4_2"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType10($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://pay.swiftpass.cn/pay/gateway","service":"pay.weixin.raw.app","appid":"wx1315f7bd05e62fed","body":"彩咖充值","version":"2.0","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/WftPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '微信支付-威富通微信sdk';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType11($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://pay.swiftpass.cn/pay/gateway","service":"pay.weixin.native","body":"彩咖充值","version":"2.0","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/WftPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '微信扫码-威富通wap微信扫码';
            if($params['platform'] == 1)
            {
                $mark = '微信扫码-威富通pc微信扫码';
                $para = '{"lib":"WftPay","ctype":"'.$params['ctype'].'","submit_url":"\/\/888.166cai.cn\/wallet\/recharge\/getWinxin\/","mode":"wftWx","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"\/caipiaoimg\/v1.1\/img\/bank\/wxzf.png","img_alt":"\u5fae\u4fe1\u652f\u4ed8","img_w":"128","img_h":"38"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType12($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"timezone":"Shanghai","mhtOrderName":"\u5f69\u5496\u5145\u503c","mhtOrderDetail":"\u5f69\u5496\u5145\u503c","frontNotifyUrl":"$web_url/api/recharge/syncCallback/XzPay/#id#","notifyUrl":"$web_url/api/recharge/notice/XzPay/#id#","payChannelType":"12"}';
        $add_ext = array('appId'=>'商户号','secure_key'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '支付宝-现在支付宝h5';
            if($params['platform'] == 4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"xzpay|5000","id":"xzZfbWap","lib":"XzPay","pay_type":"12","view":"xzzfb","payWay":"xzpay","mode_str":"'.$params['platform'].'_'.$params['ctype'].'"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['appId'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType13($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"version":"V2.0","tradeName":"彩咖充值","tradeDesc":"彩咖充值","currency":"CNY","callbackUrl":"$web_url/api/recharge/syncCallback/JdPay/#id#","notifyUrl":"$web_url/api/recharge/notice/JdPay/#id#","orderType":"1","payGateway":"https://h5pay.jd.com/jdpay/saveOrder"}';
        $add_ext = array('merchant'=>'商户号','desKey'=>'秘钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '快捷-京东支付wap';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['merchant'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType14($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"plat_url":"https://pay.soopay.net","plat_pay_product_name":"spay","service":"pay_req_h5_frontpage","charset":"UTF-8","sign_type":"RSA","ret_url":"$web_url/api/recharge/syncCallback/UmPay/#id#","notify_url":"$web_url/api/recharge/notice/UmPay/#id#","version":"4.0","amt_type":"RMB","goods_inf":"彩咖充值","identity_type":"1","can_modify_flag":"0"}';
        $add_ext = array('mer_id'=>'商户号');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '卡前置-联动支付h5';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mer_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);     
        }
    }
    public function payType15($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"mhtOrderName":"彩咖充值","mhtOrderDetail":"彩咖充值","jump_url":"$web_url/api/recharge/syncCallback/HjPay/#id#","notifyUrl":"$web_url/api/recharge/notice/HjPay/#id#","payChannelType":"15","version":"2.0.0","gateway_url":"https://api.tectopper.com/pay/gateway","curl_proxy_host":"0.0.0.0","curl_proxy_port":"0"}';
        $add_ext = array('mch_id'=>'商户号','appId'=>'appId','mch_key'=>'密钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '汇聚无限支付宝h5';
            if($params['platform']==4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"hjpay|5000","id":"hjZfbPay","lib":"HjPay","pay_type":"15","view":"hjzfb","payWay":"hjpay","mode_str":"'.$params['platform'].'_'.$params['ctype'].'"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType16($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://pay.swiftpass.cn/pay/gateway","service":"pay.alipay.native","body":"彩咖充值","version":"2.0","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/WftPay/#id#","back_url":"$web_url/api/recharge/syncCallback/WftPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'密钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $para = '';
            $mark = '兴业支付宝H5';
            if($params['platform']==4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"wftpay|5000","id":"payZfb","lib":"WftPay","pay_type":"16","view":"wftZfbWap","payWay":"wftpay","mode_str":"'.$params['platform'].'_'.$params['ctype'].'"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);     
        }
    }
    public function payType17($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"masterSecret":"e14ae2db-608c-4f8b-b863-c8c18953eef2","testSecret":"4bfdd244-574d-4bf3-b034-0c751ed34fee","url":"https://api.beecloud.cn","body":"彩咖充值","version":"2","channel":"BC_ALI_QRCODE","return_url":"$web_url/api/recharge/notice/WzPay/#id#","notify_url":"$web_url/api/recharge/notice/WzPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','appId'=>'appId','appSecret'=>'密钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '微众银行支付宝-'.$params['mch_id'];
            $para = '{"lib":"WzPay","ctype":"'.$params['ctype'].'","submit_url":"//888.166cai.cn/wallet/recharge/getWzZfb/","mode":"wzPay","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"/caipiaoimg/v1.1/img/bank/zfbzf.png","img_alt":"u652fu4ed8u5b9du652fu4ed8","img_w":"128","img_h":"38"}';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType18($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"mhtOrderName":"彩咖充值","mhtOrderDetail":"彩咖充值","jump_url":"$web_url/api/recharge/syncCallback/HjPay/#id#","notifyUrl":"$web_url/api/recharge/notice/HjPay/#id#","payChannelType":"15","version":"2.0.0","gateway_url":"https://api.tnbpay.com/pay/gateway","curl_proxy_host":"0.0.0.0","curl_proxy_port":"0","pay_method":"mbupay.wxpay.jswap2","bill_method":"mbupay.wxpay.bill","query_method":"mbupay.wxpay.query","mer_name":"微信H5-兴业银行"}';
        $add_ext = array('mch_id'=>'商户号','appId'=>'appId','mch_key'=>'密钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '微信H5-兴业银行';
            $para = '';
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }

    public function payType28($platform,$opera,$params=array()){
        $web_url = $this->getWebUrl();
        $comm_ext = '{"plat_url":"http://api.mposbank.com/tdcctp/alipay/wap_pay.tran","plat_pay_product_name":"yzpay","service":"pay_req_h5_frontpage","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/YzPay/#id#","return_url":"$web_url/api/recharge/syncCallback/YzPay/#id#","version":"4.0","amt_type":"RMB","goods_inf":"彩咖充值","identity_type":"1","can_modify_flag":"0"}';
        if($platform == 1){
            $comm_ext = '{"plat_url":"http://api.mposbank.com/tdcctp/alipay/direct_pay.tran","qr_pay_mode":"4","qrcode_width":"200","plat_pay_product_name":"yzpay","service":"pay_req_h5_frontpage","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/YzPay/#id#","return_url":"$web_url/api/recharge/syncCallback/YzPay/#id#","version":"4.0","amt_type":"RMB","goods_inf":"彩咖充值","identity_type":"1","can_modify_flag":"0"}';
        }
        $add_ext = array('mer_id'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '支付宝H5-盈中平安银行渠道';
            $para = '';
            if($platform == 1){
                $mark = '支付宝扫码-盈中平安银行渠道';
                $para = '{"lib":"YzPay","ctype":"'.$params['ctype'].'","submit_url":"//888.166cai.cn/wallet/recharge/getYzZfb/","mode":"yzpayh","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"/caipiaoimg/v1.1/img/bank/zfbzf.png","img_alt":"支付宝","img_w":"128","img_h":"38"}';
            }
            foreach ($add_ext as $k=> $v)
            {
                $add_ext[$k] = $params[$k];
            }
            $comm_ext_arr = json_decode($comm_ext,true);
            foreach ($add_ext as $k=> $v)
            {
                $comm_ext_arr[$k] = $params[$k];
            }
            $comm_ext = json_encode($comm_ext_arr);
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mer_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);
        }

    }

    public function payType33($platform,$opera,$params=array()){
        $web_url = $this->getWebUrl();
        $comm_ext = '{"plat_url":"http://api.mposbank.com/papay/payment/wap_pay.tran","plat_pay_product_name":"yzpay","service":"pay_req_h5_frontpage","charset":"UTF-8","sign_type":"MD5","notify_url":"$web_url/api/recharge/notice/YzPay/#id#","result_url":"$web_url/api/recharge/syncCallback/YzPay/#id#","version":"4.0","amt_type":"RMB","goods_inf":"166充值","identity_type":"1","can_modify_flag":"0","scene_info":"{}"}';
        $add_ext = array('mer_id'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '盈中平安银行渠道微信h5';
            $para = '';
            if($platform == 4){
                $para = '{"name":"微信支付","tips":"最高5千/笔","class":"pay-wx","value":"yzWxWap|5000","id":"yzWxWap","lib":"YzPay","pay_type":"33","view":"yzWxWap","payWay":"yzWxWap","mode_str":"4_2"}';
            }
            foreach ($add_ext as $k=> $v)
            {
                $add_ext[$k] = $params[$k];
            }
            $comm_ext_arr = json_decode($comm_ext,true);
            foreach ($add_ext as $k=> $v)
            {
                $comm_ext_arr[$k] = $params[$k];
            }
            $comm_ext = json_encode($comm_ext_arr);
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mer_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);
        }
    }    
    
    public function payType19($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"mhtOrderName":"彩咖充值","mhtOrderDetail":"彩咖充值","jump_url":"$web_url/api/recharge/syncCallback/HjPay/#id#","notifyUrl":"$web_url/api/recharge/notice/HjPay/#id#","payChannelType":"15","version":"2.0.0","gateway_url":"https://api.tectopper.com/pay/gateway","curl_proxy_host":"0.0.0.0","curl_proxy_port":"0","pay_method":"mbupay.alipay.jswap","bill_method":"mbupay.alipay.bill","query_method":"mbupay.alipay.query","mer_name":"支付宝H5-鸿粤浦发银行","return_url":"$web_url/api/recharge/success"}';
        $add_ext = array('mch_id'=>'商户号','appId'=>'appId','mch_key'=>'密钥');
		if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '支付宝H5-鸿粤浦发银行';
            $para = '';
            if($params['platform']==4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"hjZfbWap|5000","id":"hjZfbWap","lib":"HjPay","pay_type":"19","view":"hjZfbWap","payWay":"hjZfbWap","mode_str":"'.$params['platform'].'_'.$params['ctype'].'"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType20($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://api-qr.z-bank.com/services/charge/create_pay","channel":"ali_pay_scan","subject":"彩咖充值","version": "V2.1.1","sdk_mark":"sdkv1.1.16","notify_url":"$web_url/api/recharge/notice/ZbPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','app_id'=>'app_id','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '众邦银行支付宝-'.$params['mch_id'];
            $para = '';
            if($params['platform']==1)
            {
                 $para = '{"lib":"ZbPay","ctype":"'.$params['ctype'].'","submit_url":"\/\/888.166cai.cn\/wallet\/recharge\/getzbZfb\/","mode":"payZbZfb","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"\/caipiaoimg\/v1.1\/img\/bank\/zfbzf.png","img_alt":"\u652f\u4ed8\u5b9d\u652f\u4ed8","img_w":"128","img_h":"38"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType21($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"http://xibtest.xib.com.cn:3080/ifspesi/merchQrs/api","channel":"OrderCodeApply","subject":"彩咖充值","notify_url":"$web_url/api/recharge/notice/XmPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '厦门国际银行支付宝-'.$params['mch_id'];
            $para = '';
            if($params['platform']==1)
            {
                $para = '{"lib":"XmPay","ctype":"'.$params['ctype'].'","submit_url":"//888.166cai.cn/wallet/recharge/getXmZfb/","mode":"payXmZfb","mode_str":"'.$params['platform'].'_'.$params['ctype'].'","img_src":"/caipiaoimg/v1.1/img/bank/zfbzf.png","img_alt":"u652fu4ed8u5b9du652fu4ed8","img_w":"128","img_h":"38"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    public function payType22($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://pay.swiftpass.cn/pay/gateway","service":"pay.weixin.wappay","body":"支付金额","version":"2.0","charset":"UTF-8","sign_type":"MD5","mch_app_name":"峻石优选","mch_app_id":"http://www.junshibuy.com","notify_url":"$web_url/api/recharge/notice/WftPay/#id#","callback_url":"$web_url/api/recharge/syncCallback/WftPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '微信H5-鸿粤兴业银行';
            $device_info = array(2=>'AND_SDK',3=>'iOS_SDK',4=>'AND_WAP');
            $para = '';
            if($params['platform']==4)
            {
                $para = '{"name":"微信支付","tips":"最高5千/笔","class":"pay-wx","value":"wftWxWap|5000","id":"wftWxWap","lib":"WftPay","pay_type":"22","view":"wftWxWap","payWay":"wftWxWap","mode_str":"4_2"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $add_ext['device_info'] = $device_info[$params['platform']];
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
     public function payType23($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://payh5.bbnpay.com/cpapi/place_order.php","goodsname":"166订单金额","currency":"CHY","notifyurl":"$web_url/api/recharge/notice/BbnPay/#id#","backurl":"$web_url/api/recharge/syncCallback/BbnPay/#id#"}';
        $add_ext = array('appid'=>'应用号','key'=>'密钥',"pcuserid"=>'应用号',"goodsid"=>"商品id");
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '微信H5-浦发白名单渠道';
            //$device_info = array(2=>'AND_SDK',3=>'iOS_SDK',4=>'AND_WAP');
            $para = '';
            if($params['platform']==4)
            {
                $para = '{"name":"微信支付","tips":"最高5千/笔","class":"pay-wx","value":"pfWxWap|5000","id":"pfWxWap","lib":"BbnPay","pay_type":"23","view":"pfWxWap","payWay":"pfWxWap","mode_str":"4_2"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            //$add_ext['device_info'] = $device_info[$params['platform']];
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['appid'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
    //平安银行支付宝
    public function payType24($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"https://api.orangebank.com.cn/mct1","pmt_tag":"AlipayPAZH","pmt_name":"彩咖充值","notify_url":"$web_url/api/recharge/notice/PaPay/#id#"}';
        $add_ext = array('mch_id'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '平安银行支付宝';
            $para = '';
            if($params['platform']==1)
            {
                $para = '{"lib":"PaPay","ctype":"4","submit_url":"//888.166cai.cn/wallet/recharge/getPaZfb/","mode":"payPaZfb","mode_str":"1_4","img_src":"/caipiaoimg/v1.1/img/bank/zfbzf.png","img_alt":"支付宝支付","img_w":"128","img_h":"38"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));

            return $this->returnRes($insertData);
        }
    }
    public function payType29($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"http://pay.pb78.cn/pay/gateway","sign_type":"MD5","subject":"166充值","version":"1.0","callback_url":"$web_url/api/recharge/syncCallback/TomatoPay/#id#","notify_url":"$web_url/api/recharge/notice/TomatoPay/#id#"}';
        $add_ext = array('down_num'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '番茄互娱支付宝H5';
            $para = '';
            if($params['platform'] == 4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"tomatoZfbWap|5000","id":"tomatoZfbWap","lib":"TomatoPay","pay_type":"29","view":"tomatoZfbWap","payWay":"tomatoZfbWap","mode_str":"4_4"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['down_num'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));

            return $this->returnRes($insertData);
        }
    }
    public function payType37($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"url":"http://pay.pb78.cn/pay/gateway","pay_service":"wx_wap","sign_type":"MD5","subject":"166充值","version":"1.0","callback_url":"$web_url/api/recharge/syncCallback/TomatoPay/#id#","notify_url":"$web_url/api/recharge/notice/TomatoPay/#id#"}';
        $add_ext = array('down_num'=>'商户号','key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '番茄互娱微信H5';
            $para = '';
            if($params['platform'] == 4)
            {
                $para = '{"name":"微信支付","tips":"最高5千/笔","class":"pay-wx","value":"tomatoWxWap|5000","id":"tomatoWxWap","lib":"TomatoPay","pay_type":"36","view":"tomatoWxWap","payWay":"tomatoWxWap","mode_str":"4_2"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['down_num'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));

            return $this->returnRes($insertData);
        }
    }
    private  function getWebUrl()
    {
        return ENVIRONMENT === 'production' ? 'https://888.166cai.cn' : 'http://123.59.105.39';
    }
    public function returnRes($params)
    {
        $ctyName = array(2=>'微信支付',3=>'微信扫码',4=>'支付宝');
        $platform = array(1=>'网站',2=>'Android',3=>'IOS',4=>'M版');
        $payTypes =  array(5=>'中信微信',
        9=>'威富通支付宝',10=>'威富通微信sdk',11=>'威富通微信PC',12=>'现在支付宝h5',15=>'汇聚无限支付宝h5',16=>'兴业支付宝H5',17=>'微众银行支付宝',
        18=>'微信H5-兴业银行',19=>'鸿粤浦发银行',20=>'众邦银行支付宝',21=>'厦门国际银行支付宝',22=>'微信H5-鸿粤兴业银行',24=>'平安银行支付宝',23=>'微信H5-浦发白名单',28=>'盈中平安银行',29=>'番茄互娱支付宝H5','31' => '上海银行微信h5', 33=>'盈中平安银行微信h5','34' => '支付宝H5-上海银行',37=>'番茄互娱微信H5');
        //验证是否存在
        if(!$params['id']){
            $tag = $this->CI->model->verifyPayConfig($params);
            if($tag===false)
            {
                return $res = array('tag'=>$tag,'para'=>array('platform'=>$platform[$params['platform']],'pay_type'=>$payTypes[$params['pay_type']],'ctype'=>$ctyName[$params['ctype']],'mer_id'=>$params['mer_id'] ),'msg'=>'商户号已经存在~');
            }          
        }
        //插入
        $tag = $this->CI->model->insertPayConfig($params);
        return $res = array('tag'=>$tag,'para'=>array('platform'=>$platform[$params['platform']],'pay_type'=>$payTypes[$params['pay_type']],'ctype'=>$ctyName[$params['ctype']],'mer_id'=>$params['mer_id'] ));

    }
    public function payType34($platform,$opera,$params=array())
    {
        $web_url = $this->getWebUrl();
        $comm_ext = '{"mhtOrderName":"彩咖充值","mhtOrderDetail":"彩咖充值","jump_url":"$web_url/api/recharge/syncCallback/HjPay/#id#","notifyUrl":"$web_url/api/recharge/notice/HjPay/#id#","payChannelType":"34","version":"2.0.0","gateway_url":"https://api.tectopper.com/pay/gateway","curl_proxy_host":"0.0.0.0","curl_proxy_port":"0","pay_method":"mbupay.alipay.jswap","bill_method":"mbupay.alipay.bill","query_method":"mbupay.alipay.query","mer_name":"支付宝H5-上海银行","return_url":"$web_url/api/recharge/success"}';
        $add_ext = array('mch_id'=>'商户号','appId'=>'appId','mch_key'=>'密钥');
        if($opera==1) return $add_ext;
        if($opera==2)
        {
            $mark = '支付宝H5-上海银行';
            $para = '';
            if($params['platform']==4)
            {
                $para = '{"name":"支付宝支付","tips":"最高5千/笔","class":"pay-zfb","value":"hjZfbSh|5000","id":"hjZfbSh","lib":"HjPay","pay_type":"34","view":"hjZfbWap","payWay":"hjZfbSh","mode_str":"'.$params['platform'].'_'.$params['ctype'].'"}';
            }
            foreach ($add_ext as $k=> $v) 
            {
                $add_ext[$k] = $params[$k];
            }
            $insertData = array('id'=>$params['id']?$params['id']:0,'platform'=>$params['platform'],'ctype'=>$params['ctype'],'pay_type'=>$params['pay_type'],'params'=>$para,'mark'=>$mark,'mer_id'=>$params['mch_id'],'extra'=>json_encode(array_merge($add_ext,json_decode(str_replace('$web_url', $web_url, $comm_ext),true))));
            return $this->returnRes($insertData);      
        }
    }
}