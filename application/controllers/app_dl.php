<?php

class App_dl extends  MY_Controller {

    public function index() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if ($this->_isIos($agent)) {
            $dlUrl = 'https://itunes.apple.com/cn/app/caixiang-lottery/id884052834?l=zh&ls=1&mt=8';
        } else {
            $dlUrl = $this->config->item('pages_url') . 'apk/lottery_android-' . $this->channelName . '.apk';
        }
        $this->displayWithData('app_dl/index', array(
            'dlUrl' => $dlUrl,
        ));
    }

    private function _isIos($agent) {
        $agent = strtolower($agent);
        $devices = array(
            'iphone', 'ipad', 'ipod',
        );
        $devices = implode('|', $devices);
        $pattern = '/(' . $devices . ')/';
        if (preg_match($pattern, $agent)) {
            return true;
        }
        return false;
    }

}
