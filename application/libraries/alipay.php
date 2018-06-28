<?php

class Alipay {

    public function getParams($params) {
        $params = $this->_paraFilter($params);
        $params = $this->_argSort($params);
        $sign = $this->_buildRequestMysign($params);
        $params['sign'] = $sign;
        $params['sign_type'] = strtoupper('md5');

        return $params;
    }

    private function _paraFilter($params) {
        $filtered = array();
        foreach($params as $key => $value) {
            if ($key == 'sign' || $key == 'sign_type' || $value == '') {
                continue;
            }
            $filtered[$key] = $value;
        }
        return $filtered;
    }

    private function _argSort($para) {
    	ksort($para);
    	reset($para);
    	return $para;
    }

    private function _buildRequestMysign($params) {
        $prestr = $this->_createLinkstring($params);
    	$mysign = $this->_md5Sign($prestr, 'j3ekj4xeikn7z9q7i8a1o89949pzh6pm');
    	return $mysign;
    }

    private function _createLinkstring($params) {
        $arg = "";
    	while (list ($key, $val) = each ($params)) {
    		$arg .= $key."=".$val."&";
    	}
    	$arg = substr($arg,0,count($arg)-2);
    	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

    	return $arg;
    }

    private function _md5Sign($prestr, $key) {
        $prestr = $prestr . $key;
    	return md5($prestr);
    }

}
