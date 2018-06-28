<?php

/**
 * 恒钜开奖号码抓取服务类
 */
include_once dirname(__FILE__) . '/base.php';
class hengju extends Base
{
    private $pctype_map = array
    (
        '56'    => 'JLK3',
        '57'    => 'JXK3',
        '21406' => 'SD115',
        '21407' => 'SYW',
        '21421' => 'GD115',
    );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    private function cmt_comm($mdid, $reqData, $datas = array())
    {
        $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
        // 请求参数组装
        $requestData = array(
            'wAgent'    =>  $this->CI->config->item('hjtob_sellerid'),
            'wAction'   =>  $mdid,
            'wMsgID'    =>  $UId,
            'wParam'    =>  $this->paramsFormat($reqData),
        );

        $requestData['wSign'] = iconv("UTF-8", "GBK", $this->getSign($requestData));
        $requestData['wParam'] = iconv("UTF-8", "GBK", $requestData['wParam']);

        /*请求前日志记录*/
        $pathTail = "hengju$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "hengju$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . print_r($requestData, true), $pathTail);
        $result = $this->CI->tools->request($this->CI->config->item('hjtob_pji'), $requestData);

        // 返回xml转码utf-8
        $result = str_replace('gb2312', 'UTF-8', $result);
        $result = iconv('GBK', 'UTF-8', $result);
        /*请求返回日志记录*/
        log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
        $xmlobj = simplexml_load_string($result);
        $rfun = "result_$mdid";
        
        return $this->$rfun($xmlobj, $datas);
    }

    // 业务参数格式化
    private function paramsFormat($reqData = array())
    {
        $params = array();
        if(!empty($reqData))
        {
            foreach ($reqData as $key => $val) 
            {
                $params[] = $key . '=' . $val;
            }
        }
        return implode('_', $params);
    }

    // 客户端签名 wAgent + wAction + wMsgID + wParam + 代理商密钥按顺序
    private function getSign($reqData)
    {
        return md5($reqData['wAgent'] . $reqData['wAction'] . $reqData['wMsgID'] . $reqData['wParam'] . $this->CI->config->item('hjtob_secret'));
    }
    
    /**
     * [med_110 开奖号码拉取]
     * @author LiKangJian 2017-11-13
     * @param  [type] $issue [description]
     * @param  [type] $lid   [description]
     * @return [type]        [description]
     */
    public function getAwardNumber($issue, $lid)
    {
        $reqData = array(
            'LotID'         =>  $this->pctype_map[$lid],
            'LotIssue'      =>  $issue,
        );
        
        return $this->cmt_comm('110', $reqData,array('lid'=>$lid,'issue'=>$issue, 'batch' => "{$lid}-$issue"));
        
    }
    
    /**
     * [result_110 开奖号处理]
     * @author LiKangJian 2017-11-13
     * @param  [type] $xmlobj [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function result_110($xmlobj, $params)
    {
        $awardNum = '';
        $result = (string)$xmlobj->xCode;
        if($result == 0)
        {
            $xValue = explode('_', (string)$xmlobj->xValue);
            if (in_array($params['lid'], array('56', '57'))) {
                $awardNum = trim(str_replace('0', ',', $xValue[1]),',');
            }else {
                $awardNum = $xValue[1];
            }
        }
        
        return $awardNum;
    }
}
