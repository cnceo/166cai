<?php
/**
 * 数字彩/竞技彩单式上传
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/core/CommonController.php';

class ajaxSingle extends CommonController
{
    private $lidCfg = array
        (
            '11'    => 'Sfc',
            '19'    => 'Rj',
            '33'    => 'Pls',
            '35'    => 'Plw',
            '51'    => 'Ssq',
            '52'    => 'Fcsd',
            '10022' => 'Qxc',
            '23528' => 'Qlc',
            '23529' => 'Dlt'
        );
    private $lids = array(
            '11'   => 'sfc',
            '19'   => 'rj',
            '33'   => 'pls',
            '35'   => 'plw',
            '42'  => 'jczq',
            '43'  => 'jclq',
            '51'   => 'ssq',
            '52' => 'fcsd',
            '10022' => 'qxc',
            '21406' => 'syxw',
            '23528' => 'qlc',
            '23529' => 'dlt',
            '53'  => 'ks',
            '21407' => 'jxsyxw',
            '21408' => 'hbsyxw',
            '54' => 'klpk',
            '21421' => 'gdsyxw',
        );
    private $upload_no;
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
    }
    /**
     * [uploadFile 上传文件]
     * @author LiKangJian 2017-07-25
     * @return [type] [description]
     */
    public function uploadFile()
    {
        
        $post = $this->input->post(null,true);
        $res = array('code'=>0,'msg'=>'文件格式有误，请修改后重新上传','data'=>array());
        $lid = $post['lid'];
        $playType = isset($post['playType']) && !empty($post['playType']) ? $post['playType'] : '1';
        $file =  $this->uploadFileProcess();
        $file_ext = pathinfo($file);
        $file_ext = strtoupper($file_ext['extension']);
        //文件类型
        if($file_ext!='TXT')
        {
            $res['msg'] = '请先选择文件（以.txt 结尾的文本文件）';
            return $this->ajaxResult($res);
        }
        $filesize = abs(filesize($file));
        if($filesize/(1024*256) > 1)
        {
            $res['msg'] = '上传文件超过最大256KB';
            return $this->ajaxResult($res);

        }
        //正则内容
        preg_match_all("/.*\\n/", trim(file_get_contents($file))."\n", $content);
        //删除文件
        @unlink($file);
        $libery = $this->lidCfg[$lid];
        require_once APPPATH . '/libraries/single/' . $libery . '.php';
        $lib = new $libery();
        $checkRes = $lib->check($content[0],$playType);
        if($checkRes['code'])
        {
            $tag = $this->writeCodes($checkRes['data']['codes'],$lid,$post['endTime']);
            if(!$tag) return $this->ajaxResult($res);
            //验证票数限制
            $limitRes = $this->getLimitByLidRes($lid,0,$checkRes['data']['betTnum']);
            if(!$limitRes['code'])
            {
                $res['msg'] = $limitRes['msg'];
                return $this->ajaxResult($res);
            }
            $checkRes['data']['upload_no'] = $tag;
            unset($checkRes['data']['codes']);
            return $this->ajaxResult($checkRes);
        }
        return $this->ajaxResult($res);

    }
    /**
     * [uploadFile 文件上传]
     * @author JackLee 2017-03-24
     * @return [type] [description]
     */
    private function uploadFileProcess()
    {
        $config['upload_path'] = dirname(BASEPATH).'/uploads/single/';
        if(!is_dir($config['upload_path'] )){mkdir($config['upload_path'],0777,true);}
        $config['allowed_types'] = 'txt';
        $config['max_size'] = '256';
        $this->load->library('upload', $config);
        $this->upload->do_upload('file');
        $data =  $this->upload->data();
        return $data['full_path'];
    }

    /**
    * 打印json数据，并终止程序
    * @param array $result
    */
    private function ajaxResult($result)
    {
        header('Content-type: application/json');
        die(json_encode($result));
    }
    /**
     * [writeCodes 写入临时表]
     * @author LiKangJian 2017-07-26
     * @param  [type] $codes [description]
     * @return [type]        [description]
     */
    private function writeCodes($codes,$lid,$endTime)
    {
        $this->load->model('tmp_code_model');
        $tmp_id = $this->tmp_code_model->writeCodes($codes,$lid,$endTime);
        return $tmp_id;
    }
    /**
     * [getLimitByLidRes 计算出票限制]
     * @author LiKangJian 2017-07-28
     * @param  [type] $lid     [description]
     * @param  [type] $endTime [description]
     * @return [type]          [description]
     */
    private function getLimitByLidRes($lid,$orderType,$betTnum)
    {
        $res = array('code'=>1,'msg'=>'');
        date_default_timezone_set('Asia/Shanghai');
        $endTime = $this->getEndTime($this->lids[$lid]);
        if($orderType==4)
        {
            
            $endTime = $endTime['hmendTime'] ;
        }else{
            $endTime = $endTime['seFsendtime'] /1000 ;
          
        }
        $minDiff = ($endTime - time())/60;
        if($minDiff > 45 ) return $res;
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $configItem = $this->lotteryConfig->getLimitByLid($lid);
        $configItem  = json_decode($configItem[0],true);
        $lidArray = array('52','19','11');
        //① y=对应时间后台配置票数，福彩3D、任选九、胜负彩；
        //② y=对应时间后台配置票数*5，适用于双色球、大乐透、七星彩、七乐彩、排列5、排列3；
        if($minDiff <= 5 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[0]['value'] : $configItem[0]['value'] *5)
        {
            $res['code'] = 0;
            $res['msg'] = '离截止不到5分钟，为及时出票请确保方案不超过'.$y.'注！';
            return $res;
        }

        if($minDiff > 5 && $minDiff <= 15 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[1]['value'] : $configItem[1]['value'] *5)
        {
            $res['code'] = 0;
            $res['msg'] = '离截止不到15分钟，为及时出票请确保方案不超过'.$y.'注！';
            return $res;
        }
        if($minDiff > 15 && $minDiff <= 45 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[2]['value'] : $configItem[2]['value'] *5)
        {
            $res['code'] = 0;
            $res['msg'] = '离截止不到45分钟，为及时出票请确保方案不超过'.$y.'注！';
            return $res;
        }
        return $res;
    }


    /**
     * [post 处理需求]
     * @author LiKangJian 2017-07-27
     * @return [type] [description]
     */
    public function post()
    {
        $this->recordChannel();
        $orderData = array();
        $data = $this->input->post(NULL, TRUE);
        if(isset($data['lid']))
        {
           //验证票数限制
            $limitRes = $this->getLimitByLidRes($data['lid'],$data['orderType'],$data['betTnum']);
            if(!$limitRes['code'])
            {
                $response = array(
                    'code' => 998,
                    'msg'  => $limitRes['msg'],
                    'data' => array(),
                );
                header('Content-type: application/json');
                echo json_encode($response);die;
            }
        }
        //添加必要保存字段
        $this->baseParams($orderData, $data);
        $check = TRUE;
        //是否登录监测
        if (empty($this->uid)) 
        {
            $check = FALSE;
            $response = array(
                'code' => 9,
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
        }
        if(empty($this->uinfo['real_name']))
        {
            $check = FALSE;
            $response = array(
                'code' => 9,
                'msg'  => '您尚未进行实名认证，请刷新页面后重试。',
                'data' => array(),
            );
        }
        // 账户是否注销
        if(isset($this->uinfo['userStatus']) && in_array($this->uinfo['userStatus'], array(1, 2)))
        {
            $check = FALSE;
            if($this->uinfo['userStatus'] == '1')
            {
                $response = array(
                    'code' => 3000,
                    'msg'  => '您的登录已超时，请重新登录！',
                    'data' => array(),
                );
            }
            else
            {
                $response = array(
                    'code' => 16,
                    'msg'  => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => array(),
                );
            }
        }

        $this->load->model('wallet_model');
        //将一些不必要的字段去掉
        $data = $this->wallet_model->_padBusiParams($data, $this->uid);
        //加入单式
        $data['singleFlag'] = 1;
        //设置订单数据
        $orderData = array_merge($orderData, $data);
        $orderData['userName'] = $this->uname;
        if ($check) 
        {
            if ($orderData['ctype'] == 'create') 
            {
                $orderData['channel'] = $this->getChannelId();
                switch ($orderData['orderType']) {
                    case '1':
                        $detail = explode(';', $orderData['chases']);
                        foreach ($detail as $k => $d){
                            if (!empty($d)){
                                $d = explode('|', $d);
                                $detail[$k] = array('issue' => $d[0], 'multi' => $d[1], 'money' => $d[2], 'award_time' => $d[3], 'endTime' => $d[4]);
                            }else{
                                unset($detail[$k]);
                            }
                        }
                        $orderData['app_version'] = '0';
                        $orderData['chaseDetail'] = json_encode($detail);
                        $this->load->model('chase_order_model');
                        $res = $this->chase_order_model->createChaseOrder($orderData);
                        $resdata['orderId'] = $res['data']['chaseId'];
                        $resdata['money'] = number_format($data['money'], 2);
                        break;
                    case '4':
                        $this->load->model('united_order_model');
                        $res = $this->united_order_model->createUnitedOrder($orderData);
                        $resdata['orderId'] = $res['data']['orderId'];
                        $resdata['money'] = number_format($data['buyMoney']+$data['guaranteeAmount'], 2);
                        break;
                    case '0':
                    default:
                        $this->load->model('neworder_model');
                        $res = $this->neworder_model->createOrder($orderData);
                        $resdata['orderId'] = $res['data']['orderId'];
                        $resdata['money'] = number_format($data['money'], 2);
                        if($res['status'])
                        {
                            //购彩红包查询
                            $redpack = $this->neworder_model->getBetRedPack($this->uid, $orderData);
                            if($redpack)
                            {
                                $resdata['redpack'] = $redpack;
                                $resdata['redpackId'] = ($redpack[0]['disable'] == 0) ? $redpack[0]['id'] : 0;
                            }
                        }
                        
                        break;
                }
                
                if ($res['status']) 
                {
                    $money = $this->wallet_model->getMoney($this->uid);
                    if (ParseUnit($orderData['orderType'] == 4 ? ($orderData['buyMoney']+$orderData['guaranteeAmount']) : $orderData['money']) > ((isset($redpack[0]) && ($redpack[0]['disable'] == 0)) ? ($money['money'] + ParseUnit(intval(preg_replace('/,/', '', strval($redpack[0]['money']))))) : $money['money'])) 
                    {
                        $response = array('code' => 12, 'msg'  => '订单支付，余额不足！', 'data' => $resdata);
                    }
                    else 
                    {
                        $response = array('code' => 0, 'msg'  => $res['msg'], 'data' => $resdata);
                    }
                    $response['data']['remain_money'] = number_format(ParseUnit($money['money'], 1), 2);
                } 
                else 
                {
                    $response = array('code' => isset($res['code']) ? $res['code'] : 13, 'msg'  => $res['msg'], 'data' => $resdata);
                }
            }if ($orderData['ctype'] == 'pay') {
                $_oid = isset($orderData['orderId'])?$orderData['orderId']:$orderData['chaseId'];
                $inf = $this->getUploadInfo($_oid);
                //验证票数限制
                $limitRes = $this->getLimitByLidRes($inf['lid'],$orderData['orderType'],$inf['betNum']);
                if(!$limitRes['code'])
                {
                    $response = array(
                        'code' => 998,
                        'msg'  => $limitRes['msg'],
                        'data' => array(),
                    );
                    header('Content-type: application/json');
                    echo json_encode($response);die;
                }
                $resdata['money'] = number_format($orderData['money'], 2);
                switch ($orderData['orderType']) {
                    case 1:
                        $resdata['chaseId'] = $orderData['chaseId'];
                        $this->load->model('chase_wallet_model');
                        $response = $this->chase_wallet_model->payChaseOrder($this->uid, $orderData, ParseUnit($data['money']));
                         
                        if ( ! empty($response)  && $response['code'] != 400) {
                            $response = array(
                                'code' => $response['code'],
                                'msg'  => ($response['code'] != 200) ? $response['msg'] : '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p><p style="position: relative; top: 10px;" class="fcw">正在送往投注站出票...</p></div></div></div>',
                                'data' => array('orderId' => $orderData['chaseId'])
                            );
                        }
                        if ($response['code'] == 400) {
                            $money = $this->wallet_model->getMoney($this->uid);
                            $response = array('code' => $response['code'], 'msg'  => $response['msg'], 'data' => array('remain_money' => number_format(ParseUnit($money['money'], 1), 2)));
                        }
                        break;
                    case 4:
                        $this->load->model('united_wallet_model');
                        if ($orderData['type'] == 1) {
                            $orderData['buyPlatform'] = 0;
                            $response = $this->united_wallet_model->payBuyOrder($this->uid, $orderData, ParseUnit($data['money']));
                        }else {
                            $response = $this->united_wallet_model->payUnitedOrder($this->uid, $orderData, ParseUnit($data['money']));
                        }
                        
                        if ( ! empty($response)  && $response['code'] != 400) {
                            $response = array(
                                'code' => $response['code'],
                                'msg'  => ($response['code'] != 200) ? $response['msg'] : '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，'.($orderData['type'] == 1 ? '参与' : '发起').'合买成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p></div></div></div>',
                                'data' => array('orderId' => $orderData['orderId'])
                            );
                        }
                        if ($response['code'] == 400) {
                            $money = $this->wallet_model->getMoney($this->uid);
                            $response = array('code' => $response['code'], 'msg'  => $response['msg'], 'data' => array('remain_money' => number_format(ParseUnit($money['money'], 1), 2)));
                        }
                        break;
                    case 0:
                    default:
                        $resdata['orderId'] = $orderData['orderId'];
                        $response = $this->wallet_model->payOrder($this->uid, array(), $orderData, ParseUnit($data['money']));
                        if ( ! empty($response) && ! in_array($response['code'], array('12', '16'))) {
                            $response = array(
                                'code' => 0,
                                'msg'  => '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p><p style="position: relative; top: 10px;" class="fcw">正在送往投注站出票...</p></div></div></div>',
                                'data' => array('orderId' => $resdata['orderId']),
                            );
                        }
                        if ($response['code'] == '12') {
                            $money = $this->wallet_model->getMoney($this->uid);
                            $response['data']['remain_money'] = number_format(ParseUnit($money['money'], 1), 2);
                        }
                        break;
                }
            }
        }
        header('Content-type: application/json');
        if(isset($response['data']['orderId']))
        {
            $this->updateOrderId($response['data']['orderId']);//更新
        }
        echo json_encode($response);die;
    }
    /**
     * [baseParams 组装基础数据]
     * @author LiKangJian 2017-08-08
     * @param  [type] &$orderData [description]
     * @param  [type] &$postData  [description]
     * @return [type]             [description]
     */
    private function baseParams(&$orderData, &$postData)
    {
        $params = array('ctype', 'endTime', 'codecc');
        foreach ($params as $param) {
            $orderData[$param] = empty($postData[$param]) ? '' : $postData[$param];
        }
        if (preg_match('/,/', $postData['money'])) {
            $postData['money'] = intval(preg_replace('/,/', '', strval($postData['money'])));
        }
        $this->load->model('tmp_code_model');
        $codes = $this->tmp_code_model->getCode($postData['upload_no']);
        $postData['codes'] = $codes;
        //对双色球单独处理
        if($postData['lid']=='23529' && $postData['isChase'] ==1)
        {
            $codes = explode(";", $codes);
            foreach ($codes as $k => $v) 
            {
                $codes[$k] = substr($v, 0,-3).'2:1';
            }
            $postData['codes'] = implode(";", $codes);
        }
        //
        $this->upload_no = $postData['upload_no'];
        unset($postData['upload_no']);
    }
    /**
     * [updateOrderId 更新]
     * @author LiKangJian 2017-08-08
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    private function updateOrderId($orderId)
    {
        $this->load->model('tmp_code_model');
        return $codes = $this->tmp_code_model->updateOrderId($orderId,$this->upload_no);
    }
    /**
     * [getUploadInfo 上传文件信息]
     * @author LiKangJian 2017-08-08
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    private function getUploadInfo($orderId)
    {
        $this->load->model('tmp_code_model');
        $res = $this->tmp_code_model->getCodeByOrderId($orderId);
        $betNum = explode(';', $res['codes']);
        $res['betNum'] = count($betNum);
        unset($res['codes']);
        return $res;
    }

    public function getEndTime($enName)
    {
        if ($enName === 'fcsd') {
            $current = json_decode($this->cache->get($this->REDIS['FC3D_ISSUE']), TRUE);
        }else {
            $current = json_decode($this->cache->get($this->REDIS[strtoupper($enName) . '_ISSUE']), TRUE);
        }
        $temp = array(
            'sfc'   => '11',
            'rj'    => '19',
            'pls'   => '33',
            'plw'   => '35',
            'jczq'  => '42',
            'jclq'  => '43',
            'ssq'   => '51',
            'fcsd' => '52',
            'qxc' => '10022',
            'syxw' => '21406',
            'qlc' => '23528',
            'dlt' => '23529',
            'ks'  => '53',
            'jxsyxw' => '21407',
            'hbsyxw' => '21408',
            'klpk' => '54',
        );
        //合买提前截止时间
        $this->load->model('lottery_model');
        $lotteryConfig = $this->lottery_model->getLotteryConfig($temp[$enName], 'united_ahead,ahead');
        $res = array(
                'seFsendtime' => $current['cIssue']['seFsendtime'],
                'hmendTime'   => $current['cIssue']['seFsendtime']/1000 - $lotteryConfig['united_ahead'] * 60,
                );
        return $res;

    }
}