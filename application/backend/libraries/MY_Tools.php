<?php
class MY_Tools extends CI_Tools
{
	private static $SECRET ;
	private static $public_key;
	private static $private_kay;
    const IV = "\0\1\2\3\4\5\6\7";
    
	public function __construct($params = array())
	{
		parent::__construct($params);
		self::$SECRET = $this->CI->config->item('SECRET');
		$config_path = $this->CI->config->item('base_path') . '/application/config/';
		self::$public_key = $config_path . 'public.key';
		self::$private_kay = $config_path . 'private.key';
	}	
	
	function rsa_encrypt($sourcestr)  
    {  
    	$maxlen = 5;
    	$output='';
        $key_content = file_get_contents(self::$public_key); 
        $pubkeyid    = openssl_get_publickey($key_content);  
		while($sourcestr){
		  	$input = substr($sourcestr, 0, $maxlen);
		  	$sourcestr = rtrim(substr($sourcestr, $maxlen));
		  	 
		  	if(openssl_public_encrypt($input, $crypttext, $pubkeyid))  
	        {   
	            $output .= base64_encode("".$crypttext) . ' ';  
	        }  
		}
		return rtrim($output);
    }  
    
	function rsa_decrypt($crypttext, $fromjs = FALSE)  
    {  
        $key_content = file_get_contents(self::$private_kay);  
        $prikeyid    = openssl_get_privatekey($key_content);
        $padding = OPENSSL_PKCS1_PADDING;
        if($fromjs)  
        {
        	$padding = OPENSSL_NO_PADDING;
        	$crypttext = base64_encode(pack("H*", $crypttext));
        }
        $crypttext   = base64_decode($crypttext);  
        if (openssl_private_decrypt($crypttext, $sourcestr, $prikeyid, $padding))  
        {  
            return $fromjs ? rtrim(strrev($sourcestr), "/0") : "".$sourcestr;  
        }  
        return ;  
    }  
    
    public function encrypt($plain) 
    {
        $plain = $this->_addPadding($plain);
        $cipher = mcrypt_encrypt(MCRYPT_TRIPLEDES, self::$SECRET, $plain, MCRYPT_MODE_CBC, self::IV);
        $cipher = base64_encode($cipher);

        return $cipher;
    }

    public function decrypt($cipher) 
    {
        $plain = base64_decode($cipher);
        $plain = mcrypt_decrypt(MCRYPT_TRIPLEDES, self::$SECRET, $plain, MCRYPT_MODE_CBC, self::IV);
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