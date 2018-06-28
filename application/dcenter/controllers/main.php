<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller 
{
	static $SECRET = '123456789123456789123456';
	const IV = "12345678";
	public function index()
	{
		$body = 
			"<?xml version='1.0' encoding='utf-8'?>
			<body>
			<lotteryId>D3</lotteryId>
			<issue>15083</issue>
			</body>";
		$body = $this->encrypt($body);
		$header = "<?xml version='1.0' encoding='utf-8'?>
		<message>
		<head>
		<version>V1</version>
		<command>1001</command>
		<venderId>TWOTOFIVE</venderId>
		<messageId>1</messageId>
		<md>" . md5($body) . "</md>
		</head>
		<body>$body</body>
		</message>"; 
		$this->load->library('tools');
		$result = $this->tools->request('http://112.124.100.187/twotofive/v1', $header);
		$str = 'EH968iR+lq5qYvT17bgXgV4J2uAnibWLDPWNHnQQsl0LyqbjBFHVvrZBrQJ7TKW8Ry1zN2vRIUgx6D6KI64J1MG+Xr67ll4yoDl7eOBmcqeG+6qVFVybpxOs4Ozxc75qeLnMVeZkfgGK2GweBJiJnqxlhBULqFyxF8+GmBa7+/GS0kE4eIat9+9+HIM2T9nfF/N8lXwfHCXeziQAJ4B2pSoW4qZMZnLCxFiTg77yFqPpt++VcTnPV63cl3P8UepDTPTBqjko43piUe6H236Mfu21P9NdJATwxbAmuE4Do4S5k7aeFTQppGNNFXFMDF3yRDfmGnkNB7Y=';
		$ss = $this->decrypt($str);
		print_r($ss);
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
    
	private function strCode ( $str , $action = 'DECODE' )
	{
		$action == 'DECODE' && $str = base64_decode ( urldecode($str) );
		$code = '';
		$hash = 'M#jM0NeSv#wMDG9+8rVsti80A==3g.0'; //此值需修改
		$key = md5 ( $hash );
		$keylen = strlen ( $key );
		$strlen = strlen ( $str );
		for($i = 0; $i < $strlen; $i ++)
		{
			$k = $i % $keylen; //余数  将字符全部位移
			$code .= $str[$i] ^ $key[$k]; //位移 
		}
		return ($action == 'DECODE' ? $code : urlencode(base64_encode ( $code )));
	}
}
