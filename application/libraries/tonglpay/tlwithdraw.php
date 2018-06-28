<?php
/**
 * 
 * 单笔实时接口
 * TRX_CODE:100014--单笔实时代付
 * TRX_CODE:100011--单笔实时代收
 * @var unknown_type
 */
require_once('libs/ArrayXml.class.php');
require_once('libs/cURL.class.php');
require_once('libs/PhpTools.class.php');
class TlWithdraw
{
	public $tools = null;
	public function __construct()
	{
		header('Content-Type: text/html; Charset=UTF-8');
		$this->tools = new PhpTools();
	}
	
	public function main($act, $params = array())
	{
		if(method_exists($this, $act))
		{
			return $this->$act($params[$act]);
		}
	}
	
	private function withdraw($params)
	{
		return $this->tools->send($params);
	}
}
