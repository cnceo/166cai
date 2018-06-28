<?php
class sumpayfuns
{
    // RSA 加密初始化
	public function init($initDatas)
	{
		return $this->signArrayData($initDatas, $initDatas['mer_id'] );
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
        foreach ($dataArray as $key => $val) 
        {
            //跳过 sign sign_type和空值参数
            if ($key == 'sign' || $key == 'sign_type' || strlen($val) == 0) 
            {
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
    public function sign($data, $mer_id)
    {
        $priKeyFile = APPPATH . 'libraries/sumpay/key/' . $mer_id . '/rsa_private_key.pem';

        //读取私钥文件
        $priKey = file_get_contents($priKeyFile);

        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $priKeyId = openssl_get_privatekey($priKey);

        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $priKeyId);

        //释放资源
        openssl_free_key($priKeyId);

        //base64编码
        $sign = base64_encode($sign);

        return $sign;
    }

    // RSA 验签
    public function verify($data, $sign, $mer_id)
    {
        $pubKeyFile = APPPATH . 'libraries/sumpay/key/' . $mer_id . '/rsa_public_key.pem';

        //读取公钥文件
        $pubKey = file_get_contents($pubKeyFile);

        //转换为openssl格式密钥
        $pubKeyId = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $pubKeyId);

        //释放资源
        openssl_free_key($pubKeyId);

        return $result;
    }
}