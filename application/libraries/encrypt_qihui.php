<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class encrypt_qihui
{
	//测试密钥123456789123456789123456
	//static $SECRET = 'LsmzLbsmSQuJune4cTgfCkV6';
	const IV = "12345678";
	
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
	public function encrypt($plain) 
    {
        $plain = $this->_addPadding($plain);
        $cipher = mcrypt_encrypt(MCRYPT_TRIPLEDES, $this->CI->config->item('qhtob_secret'), $plain, MCRYPT_MODE_CBC, self::IV);
        $cipher = base64_encode($cipher);

        return $cipher;
    }

    public function decrypt($cipher) 
    {
        $plain = base64_decode($cipher);
        $plain = mcrypt_decrypt(MCRYPT_TRIPLEDES, $this->CI->config->item('qhtob_secret'), $plain, MCRYPT_MODE_CBC, self::IV);
        $plain = $this->_removePadding($plain);

        return $plain;
    }
    
	/*
     * PKCS5Padding
     */
    private function _addPadding($source) 
    {
        $block = mcrypt_get_block_size('tripledes', MCRYPT_MODE_CBC);
        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) 
        {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }

    private function _removePadding($source) 
    {
        $lastOrd = ord(substr($source, -1));
        $lastChr = chr($lastOrd);

        $source = substr($source, 0, strlen($source) - $lastOrd);
        return $source;
    }
}