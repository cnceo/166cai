<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 红包 模型层
 * @date:2016-02-03
 */

class Redpack_Model extends MY_Model
{
    // 红包类型
    public $eventType = array(
        'all' => '0',       // 所有红包
        'recharge' => '3',   // 充值红包
        'bet' => '4',		//购彩红包
    );

    /*
     * 查询用户红包信息
     * @date:2016-02-03
     */
    public function getEventType()
    {
        return $this->eventType;
    }

	/*
     * 查询用户红包信息
     * @date:2016-02-03
     */
	public function getRedpackInfo($uid, $eventType, $page = 1, $pageNum = 10)
	{
        // 请求参数
        $postData = array(
            'userId' => $uid,
            'eventType' => $eventType,
            'page' => $page,
            'step' => $pageNum
        );
        // log_message('LOG', "请求参数: " . json_encode($postData), 'getRedpackInfo');
		if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->config->item('cp_host');
            $postData['HOST'] = $this->config->item('domain');
        }
        else
        {
            // $postUrl = $this->config->item('pages_url');
            $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        $returnData = $this->tools->request($postUrl . 'api/redpack/fetch', $postData);
        $returnData = json_decode($returnData, true);

        $info = array();
        if($returnData['status'] == '200')
        {
            // if(!empty($returnData['data']))
            // {
            //     foreach ($returnData['data'] as $key => $data) 
            //     {
            //         $returnData['data'][$key]['use_params'] = json_decode($data['use_params'], true);
            //     }
            // }
            $info = $returnData['data'];
        }
        return $info;
	}

    /*
     * 按使用状态查询用户红包信息
     * @date:2016-02-03
     */
    public function getUserRedpacks($uid, $ctype, $page = 1, $pageNum = 10)
    {
        // 请求参数
        $postData = array(
            'userId' => $uid,
            'ctype' => $ctype,
            'cpage' => $page,
            'psize' => $pageNum
        );

        if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->config->item('cp_host');
            $postData['HOST'] = $this->config->item('domain');
        }
        else
        {
            // $postUrl = $this->config->item('pages_url');
            $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        $returnData = $this->tools->request($postUrl . 'api/redpack/getUserRedpacks', $postData);
        $returnData = json_decode($returnData, true);

        $info = array();
        if($returnData['status'] == '200')
        {

            $info = $returnData['data'];
        }
        return $info;
    }

    /**
     * 根据id查询红包信息
     * @param unknown_type $uid
     * @param unknown_type $redpackId
     */
    public function getRedpackById($uid, $redpackId)
    {
        // 请求参数
        $postData = array(
            'uid' => $uid,
            'redpackId' => $redpackId,
        );

        if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->config->item('cp_host');
            $postData['HOST'] = $this->config->item('domain');
        }
        else
        {
            // $postUrl = $this->config->item('pages_url');
            $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        $returnData = $this->tools->request($postUrl . 'api/redpack/getRedpackById', $postData);
        $returnData = json_decode($returnData, true);

        $info = array();
        if($returnData['status'] == '200')
        {
            $info = $returnData['data'];
        }
        return $info;
    }
}
