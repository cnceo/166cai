<?php

class Bjdc extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //$this->load->library('webapi');
    }

    public static $TYPE_MAP = array(
        'spf' => array(
            'cnName' => '胜平负',
        ),
        'cbf' => array(
            'cnName' => '比分',
        ),
        'jqs' => array(
            'cnName' => '总进球',
        ),
    );

    public static $cbfWinOptions = array(
        '10' => '1:0',
        '20' => '2:0',
        '21' => '2:1',
        '30' => '3:0',
        '31' => '3:1',
        '32' => '3:2',
        '40' => '4:0',
        '41' => '4:1',
        '42' => '4:2',
        '50' => '5:0',
        '51' => '5:1',
        '52' => '5:2',
        '93' => '胜其他',
    );

    public static $cbfDrawOptions = array(
        '00' => '0:0',
        '11' => '1:1',
        '22' => '2:2',
        '33' => '3:3',
        '91' => '平其他',
    );

    public static $cbfLoseOptions = array(
        '01' => '0:1',
        '02' => '0:2',
        '12' => '1:2',
        '03' => '0:3',
        '13' => '1:3',
        '23' => '2:3',
        '04' => '0:4',
        '14' => '1:4',
        '24' => '2:4',
        '05' => '0:5',
        '15' => '1:5',
        '25' => '2:5',
        '90' => '负其他',
    );

    public static $jqsOptions = array(
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7+',
    );

    private function _requestData(){
        $date = date('Ymd');
       $response = $this->tools->get(self::BUSI_URL . 'ticket/data/jil', array(
            'lid' => 42,
            'state' => 100,
        ));
        $matches = array();
        $leagues = array();
        $weekDays = array('日', '一', '二', '三', '四', '五', '六');
        if (!empty($response['data'])) {
            foreach ($response['data'] as $key => $match) {
                $mid = $match['mid'];
                $date = substr($mid, 0, 8);
                $date = date('Y-m-d', strtotime($date));
                $match['weekId'] = '周' . $weekDays[date('w', strtotime($date))] . substr($mid, 8);
                if (!empty($match['homeSname'])) {
                    $match['home'] = $match['homeSname'];
                }
                if (!empty($match['awarySname'])) {
                    $match['awary'] = $match['awarySname'];
                }
                $dateWithWeek = $date . ' 周' . $weekDays[date('w', strtotime($date))];
                if (!isset($matches[$dateWithWeek])) {
                    $matches[$dateWithWeek] = array();
                }
                $matches[$dateWithWeek][] = $match;
                $leagues[] = $match['name'];
            }
        }
        $leagues = array_flip(array_unique($leagues));

        return array(
            'matches' => $matches,
            'leagues' => $leagues,
            'weekDays' => $weekDays,
        );
    }

    public function spf(){
        $data = $this->_requestData();
        extract($data);
        $this->display('bjdc/spf', array(
            'matches' => $matches,
            'leagues' => $leagues,
            'weekDays' => $weekDays,
            'bjdcType' => 'spf',
            'typeMAP' => self::$TYPE_MAP,
            'type' => 'cast',
            'cnName' => '北单',
            'enName' => 'bjdc',
            'lotteryId' => BJDC,
        ));
    }

    public function cbf(){
        $data = $this->_requestData();
        extract($data);
        $this->display('bjdc/cbf', array(
            'matches' => $matches,
            'leagues' => $leagues,
            'weekDays' => $weekDays,
            'cbfWinOptions' => self::$cbfWinOptions,
            'cbfDrawOptions' => self::$cbfDrawOptions,
            'cbfLoseOptions' => self::$cbfLoseOptions,
            'bjdcType' => 'cbf',
            'typeMAP' => self::$TYPE_MAP,
            'type' => 'cast',
            'cnName' => '北单',
            'enName' => 'bjdc',
            'lotteryId' => BJDC,
        ));
    }

    public function jqs(){
        $data = $this->_requestData();
        extract($data);
        $this->display('bjdc/jqs', array(
            'matches' => $matches,
            'leagues' => $leagues,
            'weekDays' => $weekDays,
            'jqsOptions' => self::$jqsOptions,
            'bjdcType' => 'jqs',
            'typeMAP' => self::$TYPE_MAP,
            'type' => 'cast',
            'cnName' => '北单',
            'enName' => 'jczq',
            'lotteryId' => BJDC,
        ));
    }

    public function index() {
        $data = $this->_requestData();
        extract($data);
        $this->display('bjdc/spf', array(
            'matches' => $matches,
            'leagues' => $leagues,
            'weekDays' => $weekDays,
            'cbfWinOptions' => self::$cbfWinOptions,
            'cbfDrawOptions' => self::$cbfDrawOptions,
            'cbfLoseOptions' => self::$cbfLoseOptions,
            'jqsOptions' => self::$jqsOptions,
            'bjdcType' => 'spf',
            'typeMAP' => self::$TYPE_MAP,
            'type' => 'cast',
            'cnName' => '北单',
            'enName' => 'bjdc',
            'lotteryId' => BJDC,
        ));
    }
}
