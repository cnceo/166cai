<?php

/**
 * 连连快捷
 * @author yindefu
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';

class LlPay extends RechargeAbstract
{

    /**
     * 商户id
     * @var unknown_type
     */
    private $merId;

    /**
     * 秘钥
     * @var unknown_type
     */
    private $key;

    /**
     * 秘钥类型
     * @var unknown_type
     */
    private $sign_type;

    /**
     * 配置信息
     * @var unknown_type
     */
    private $config;
    private $RSA_PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDrjxRvweJCWvIKNi5OHEXgS3wgbnQ1tif+cUwAKkGGVFhPfmr5
2ETol07/audGVFgGEoi7ZROvmxdb9KMDTgP6OgZxPIJyxZsiKaI3jFCOfx9shADv
obntYt4UesZaY4brf1sI+FdK0Yu9VMV22fKlaj41O6A+eM5ZDwWeWDhqIwIDAQAB
AoGBAKZGgEGHFaSLN/EXX8ZJVNXH0t29uhAz/bUw2ln/efNNVG0AqpikHbglHmFT
X9+YJ+5ZZOUKq0O48Vs6q1ro1gqP72vpqi04/jzoy3jJShmZ7nmkW+/kATT9Lus0
UMTvsWbZXL4vD2Dy0+k7v77jmf27/xG4us1ucYSZMQ6Qb2+hAkEA+gIUaKWvCXXa
QoTM06Ag29YZEbJgGq8iy+iEZ63RtWPf7H1S9pv31Zc9UaRxGkiBZltZTE5pMhiY
wvIrg6RB8wJBAPE0WOGpyDo7LFayXKfiMzYrj9lcJ+bLFJ5z2RHlKBLjpv4H3VTH
ubOpe7beF8l6C14cUuhT2iDFN8vK+qCpExECQFS+tbpPR0j2qPhZWbD2m4zJQxAr
ncYNzca+13rpgadx5mqchK3Raq39KSzuh+Q35Z0To+5oueHgUo/qVPO3jx8CQQDv
p69YKDWFhh271mQxepKflBDNSr9qlQTbmwdmvGVgv0jAxlenUPq2BAOj4m+IA/cf
fszxgb8NKGcT2Y3D67nBAkBpAmjdgRZoaysV6305F8b0VxvFHDD9RnnX2QQUqgIA
rxK7y/C+xgLUm5UZP9zl/z9KwL71Rz/J+3vrrnn/kSpt
-----END RSA PRIVATE KEY-----';
    private $LIANLIAN_PUBLICK_KEY = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+
q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQ
kPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB
-----END PUBLIC KEY-----';

    /**
     * 请求第三方网关地址
     * @var unknown_type
     */
    private $payGateway;
    private $baseUrl;

    public function __construct($config = array())
    {
        $this->merId = $config['oid_partner'];
        $this->payGateway = $config['url'];
        unset($config['url']);
        $this->config = $config;
    }

    /**
     * curl请求支付信息
     * @see RechargeAbstract::requestHttp()
     */
    public function requestHttp($params)
    {
        $returnData = array(
            'code' => false,
            'msg' => '请求错误',
            'data' => $params,
        );
        //$params['money'] = 1;
        $resParams = $this->getParams();
        $resParams['user_id'] = $params['uid'];
        $resParams['timestamp'] = date("YmdHis", time());  // 单位：元保留两位
        $resParams['no_order'] = $params['trade_no'];
        $resParams['dt_order'] = date("YmdHis", time());
        $resParams['money_order'] = number_format(ParseUnit($params['money'], 1), 2, '.', '');
        $resParams['id_type'] = 0;
        $resParams['id_no'] = $params['id_card'];
        $resParams['acct_name'] = $params['real_name'];
        $resParams['flag_modify'] = 1;
        $resParams['risk_item'] = json_encode(array(
            'frms_ware_category' => $resParams['frms_ware_category'],
            'user_info_mercht_userno' => $params['uid'],
            'user_info_dt_register' => date("YmdHis", time())));
        $resParams['risk_item'] = stripslashes($resParams['risk_item']);
        unset($resParams['frms_ware_category']);
        $resParams['sign'] = $this->sign($resParams);
        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => $resParams,
        );

        return $returnData;
    }

    /**
     * 充值异步通知
     * @see RechargeAbstract::notify()
     */
    public function notify()
    {
        $res = array(
            'ret_code' => '0000',
            'ret_msg' => '交易成功'
        );
        
        $errores = array(
            'ret_code' => '9999',
            'ret_msg' => '验签失败'
        );
        
        $returnData = array(
            'code' => false,
            'errMsg' => json_encode($errores),
            'succMsg' => json_encode($res),
            'data' => array(),
        );

        $postData = file_get_contents("php://input");
        if (!empty($postData)) {
            $val = $this->addslashes_deep_obj(json_decode(stripslashes($postData), 0));
            $oid_partner = trim($val->{
                    'oid_partner' });
            $sign_type = trim($val->{
                    'sign_type' });
            $sign = trim($val->{
                    'sign' });
            $dt_order = trim($val->{
                    'dt_order' });
            $no_order = trim($val->{
                    'no_order' });
            $oid_paybill = trim($val->{
                    'oid_paybill' });
            $money_order = trim($val->{
                    'money_order' });
            $result_pay = trim($val->{
                    'result_pay' });
            $settle_date = trim($val->{
                    'settle_date' });
            $info_order = trim($val->{
                    'info_order' });
            $pay_type = trim($val->{
                    'pay_type' });
            $bank_code = trim($val->{
                    'bank_code' });
            $no_agree = trim($val->{
                    'no_agree' });
            $id_type = trim($val->{
                    'id_type' });
            $id_no = trim($val->{
                    'id_no' });
            $acct_name = trim($val->{
                    'acct_name' });
            $parameter = array(
                'oid_partner' => $oid_partner,
                'sign_type' => $sign_type,
                'dt_order' => $dt_order,
                'no_order' => $no_order,
                'oid_paybill' => $oid_paybill,
                'money_order' => $money_order,
                'result_pay' => $result_pay,
                'settle_date' => $settle_date,
                'info_order' => $info_order,
                'pay_type' => $pay_type,
                'bank_code' => $bank_code,
                'no_agree' => $no_agree,
                'id_type' => $id_type,
                'id_no' => $id_no,
                'acct_name' => $acct_name
            );
            $verifyRes = $this->verify($parameter, $sign);
            if ($verifyRes) {
                    $payData = array(
                        'trade_no' => $no_order,
                        'pay_trade_no' => $oid_paybill,
                        'status' => '1', // 成功
                    );
                    $returnData = array(
                        'code' => true,
                        'errMsg' => json_encode($res),
                        'succMsg' => json_encode($res),
                        'data' => $payData,
                    );
            } else {
                // TODO 验签失败 这里记录错误日志或邮件报警
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>连连支付商户号：' . $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($postData);
                $this->alertEmail($message);
                $this->log($postData, __CLASS__, $this->merId, __FUNCTION__);
            }
        }
        return $returnData;
    }

    /**
     * 充值同步通知
     * @see RechargeAbstract::syncCallback()
     */
    public function syncCallback()
    {
        $returnData = array(
                'code' => false,
                'errMsg' => 'failure',
                'succMsg' => 'success',
                'data' => array(),
        );
        if (!empty($_POST)) {
            $parameter = array(
                'oid_partner' => $_POST['oid_partner'],
                'sign_type' => $_POST['sign_type'],
                'dt_order' => $_POST['dt_order'],
                'no_order' => $_POST['no_order'],
                'oid_paybill' => $_POST['oid_paybill'],
                'money_order' => $_POST['money_order'],
                'result_pay' => $_POST['result_pay'],
                'settle_date' => $_POST['settle_date'],
                'info_order' => $_POST['info_order'],
                'pay_type' => $_POST['pay_type'],
                'bank_code' => $_POST['bank_code'],
            );
            if ($this->verify($parameter, trim($_POST['sign']))) {
                $payData = array(
                    'trade_no' => $parameter['no_order'],
                );
                $returnData = array(
                    'code' => true,
                    'errMsg' => 'failure',
                    'succMsg' => 'success',
                    'data' => $payData,
                );
            }
        } else {
            //对返回数据处理
            $input = file_get_contents('php://input');
            $gPost = $GLOBALS['HTTP_RAW_POST_DATA'];
            $get = $_GET;
            $msg = '';
            if ($input) {
                $msg .= $this->printJson($input);
            }
            if ($gPost) {
                $msg .= $this->printJson($gPost);
            }
            if ($get) {
                $msg .= $this->printJson($get);
            }
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>连连支付商户号：' . $this->merId . '同步通知接口接收数据异常，请及时留意。<br/>请求返回数据：' . $msg;
            $this->alertEmail($message);
            $this->log(json_encode($_POST), __CLASS__, $this->merId, __FUNCTION__);
        }
        return $returnData;
    }

    /**
     * [formSubmit 连连支付]
     * @author LiKangJian 2017-06-19
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function formSubmit($params)
    {
        $res = $this->requestHttp($params);
        if ($res['code'] != 1)
            return array();
        $params = $res['data'];
        $html = '<body onLoad="document.autoForm.submit();">';
        $html .= '<form name="autoForm" action="' . $this->payGateway . '" method="post">';
        foreach ($params as $k => $v) {
            if($k == 'risk_item'){
                $html .= "<input type='hidden' name='risk_item' value='" . $v . "'/><br/>";
            }else{
                $html .= '<input type="hidden" name="' . $k . '" value="' . $v . '"/><br/>';
            }
        }
        $html .= '</form></body>';
        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => array('charset' => 'utf-8', 'html' => $html, 'params' => $params),
        );
        return $returnData;
    }

    public function queryOrder($params)
    {
        $returnData = array(
            'code' => false,
            'msg' => '操作失败',
            'data' => array(),
        );

        $resParams = array(
            'sign_type' => 'RSA',
            'oid_partner' => $this->merId,
            'no_order' => $params['trade_no']
        );
        $resParams['sign'] = $this->sign($resParams);
        $respone = $this->curlPost("https://queryapi.lianlianpay.com/orderquery.htm", json_encode($resParams));
        $respone = json_decode($respone, true);
        if ($respone['ret_code'] == '0000') {
            $pstatus = array('SUCCESS' => '成功', 'WAITING' => '等待支付', 'PROCESSING' => '银行支付处理中', 'REFUND' => '退款', 'FAILURE' => '失败');
            $payData = array(
                'code' => '0',
                'ptype' => '连连快捷支付',
                'pstatus' => $pstatus[$respone['result_pay']],
                'ptime' => $respone['result_pay'] == 'SUCCESS' ? date('Y-m-d H:i:s', strtotime($respone['settle_date'])) : '',
                'pmoney' => $respone['money_order'],
                'pbank' => '',
                'ispay' => $respone['result_pay'] == 'SUCCESS' ? true : false,
                'pay_trade_no' => $respone['oid_paybill'],
            );

            $returnData = array(
                'code' => true,
                'msg' => '操作成功',
                'data' => $payData,
            );
        }else{
            $returnData['data']['msg'] = $respone['ret_msg'];
        }
        return $returnData;
    }

    public function queryBill($params)
    {
        
    }

    public function refundSubmit($params)
    {
        
    }

    public function queryRefund($params)
    {
        
    }

    /**
     * 返回请求参数数组
     * @return multitype:string unknown_type
     */
    private function getParams()
    {
        $params = array();
        foreach ($this->config as $key => $value) {
            $params[$key] = $value;
        }
        return $params;
    }

    // RSA 加密初始化
    public function init($initDatas)
    {
        return $this->signArrayData($initDatas, $this->merId);
    }

    // 过滤数据
    public function signArrayData($dataArray, $mer_id)
    {
        $source = $this->sortArrayData($dataArray);
        return $this->sign($source, $mer_id);
    }

    // 过滤数据 排序
    public function sortArrayData($dataArray)
    {
        //去除 sign 和  sign_type 其余参数均要参与签名
        $targetArray = array();
        foreach ($dataArray as $key => $val) {
            //跳过 sign sign_type和空值参数
            if ($key == 'sign' || $key == 'sign_type' || strlen($val) == 0) {
                continue;
            }
            $kvItem = $key . '=' . $val;
            $targetArray[] = $kvItem;
        }

        //数据排序
        asort($targetArray);

        //拼接待签名数据
        $source = implode('&', $targetArray);
        return $source;
    }

    // RSA签名
    public function sign($params)
    {
        $para_sort = $this->argSort($params);
        $prestr = $this->createLinkstring($para_sort);
        $res = openssl_get_privatekey($this->RSA_PRIVATE_KEY);
        openssl_sign($prestr, $sign, $res, OPENSSL_ALGO_MD5);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    // RSA 验签
    public function verify($params, $sign)
    {
        $params = $this->paraFilter($params);
        $para_sort = $this->argSort($params);
        $prestr = $this->createLinkstring($para_sort);
        $res = openssl_get_publickey($this->LIANLIAN_PUBLICK_KEY);
        $result = (bool) openssl_verify($prestr, base64_decode($sign), $res, OPENSSL_ALGO_MD5);
        openssl_free_key($res);
        return $result;
    }

    public function addslashes_deep_obj($obj)
    {
        if (is_object($obj) == true) {
            foreach ($obj AS $key => $val) {
                $obj->$key = $this->addslashes_deep($val);
            }
        } else {
            $obj = $this->addslashes_deep($obj);
        }

        return $obj;
    }

    public function addslashes_deep($value)
    {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
        }
    }

}
