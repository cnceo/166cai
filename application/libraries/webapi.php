<?php

class Webapi {

    public function post($url, $params=array(), $isJson = 0) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($isJson) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } else {
            $params = $this->_serialize($params);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response= curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if ($response == null) {
            $response = array(
                'code' => -9999,
                'msg' => '',
                'data' => array(),
            );
        } else {
            if ($response['code'] == 6) {
                $response['msg'] = '请退出并重新登录';
            }
        }
        return $response;
    }

    public function get($url, $params=array()) {
        $query = "?";
        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                $query .= rawurlencode($key) . "=" . rawurlencode(json_encode($value));
            } else {
                $query .= rawurlencode($key) . "=" . rawurlencode($value);
            }
            $query .= "&";
        }
        $query = substr($query, 0, strlen($query) - 1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if ($response == null) {
            $response = array(
                'code' => -9999,
                'msg' => '',
                'data' => array(),
            );
        }
        return $response;
    }

    private function _serialize($arr) {
        $str = array();
        foreach($arr as $key => $value) {
            $str[] = $key . '=' . $value;
        }
        $str = implode('&', $str);
        return $str;
    }

}
