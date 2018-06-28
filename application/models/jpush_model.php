<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 客户端APP - 数字彩开奖 - 小米推送
 * @date:2016-02-23
 */
class Jpush_Model extends MY_Model
{
	// 小米key
	private static $pushConfig = array(
		// android
		'app' => array(
			'appSecret' => 'pnYQnu3N82RIS95iC1hxug==',
			'packageName' => 'com.caipiao166'
		),
		'ios' => array(
			'appSecret' => 'dEDI0cjQeFF8LVbZTsl+/A==',
			'packageName' => 'com.166cai.lottery'
		)
	);

	public function __construct()
	{
		parent::__construct();
	}

	// 联表查询彩种期次推送状态
	public function getLastAwards($lid, $dbName)
	{
		// 数字彩cfg对应表名称
		if(in_array($dbName, array('sfc', 'rj')))
		{
			$table = "cp_rsfc_paiqi";
			$sql = "select mid as issue, result as awardNum from {$table} where `status` >= 50 ORDER BY issue DESC LIMIT 1";
		}
		else
		{
			$table = "cp_" . $dbName . "_paiqi";
			$sql = "select issue, awardNum from {$table} where `status` >= 50 ORDER BY issue DESC LIMIT 1";
		}
		$pData = $this->cfgDB->query($sql)->getRow();
		if(empty($pData))
		{
			return array();
		}
		$sql1 = "select lid, issue, synflag from cp_award_jpush where lid=? and issue=?";
		$aData = $this->cfgDB->query($sql1, array($lid, $pData['issue']))->getRow();
		$awardInfo = array(
			'p_issue' => $pData['issue'],
			'p_awardNum' => $pData['awardNum'],
			'j_lid' => isset($aData['lid']) ? $aData['lid'] : '',
			'j_issue' => isset($aData['issue']) ? $aData['issue'] : '',
			'j_synflag' => isset($aData['synflag']) ? $aData['synflag'] : 0,
		);

		return $awardInfo;
	}

	// 更新彩种期次推送状态
	public function updateSynflag($awards)
	{
		$upd = array('synflag');
		$fields = array_keys($awards);
		$sql = "insert cp_award_jpush(" . implode(',', $fields) . ",created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
		$this->cfgDB->query($sql, $awards);
	}

	// 安卓开奖信息推送
	public function awardsPushByAPP($msg_content, $lid, $tagName, $awardNum)
    {
        if(ENVIRONMENT !== 'production')
        {
            $pushResponse['result'] = 'ok';
            return $pushResponse['result'];
        }

        $pushConfig = self::$pushConfig;

        // 根据平台包不同请求    
    	$parmas = array(
            'appSecret' => $pushConfig['app']['appSecret'],
        );

    	$postData['MIPUSHJSON'] = $parmas;
    	$push_url = 'https://api.xmpush.xiaomi.com/v2/message/topic';		//推送接口
    	$push_url .= '?payload=' . '';
    	$push_url .= '&restricted_package_name=' . $pushConfig['app']['packageName'];	//包名     
    	$push_url .= '&pass_through=' . '1';							//消息类型
    	$push_url .= '&extra.title=' . urlencode($msg_content);					//标题
    	$push_url .= '&extra.result=' . $awardNum;						//开奖号码
    	$push_url .= '&extra.lid=' . $lid;								//彩种ID
    	$push_url .= '&topic=' . $tagName;						//推送标签

    	$pushResponse = $this->tools->request($push_url, $postData);
        $pushResponse = json_decode($pushResponse, true);
        return $pushResponse;
    }

    // 安卓开奖信息推送
	public function awardsPushByIOS($msg_content, $lid, $tagName, $awardNum)
    {
        $pushConfig = self::$pushConfig;

        // 根据平台包不同请求    
    	$parmas = array(
            'appSecret' => $pushConfig['ios']['appSecret'],
        );

    	$postData['MIPUSHJSON'] = $parmas;

    	if(ENVIRONMENT === 'production')
		{
			//线上推送接口
			$push_url = 'https://api.xmpush.xiaomi.com/v2/message/topic';	
		}
		else
		{
			//测试推送接口
			$push_url = 'https://sandbox.xmpush.xiaomi.com/v2/message/topic';	
		}

    	$push_url .= '?payload=' . '';
    	$push_url .= '&restricted_package_name=' . $pushConfig['ios']['packageName'];	//包名
    	$push_url .= '&description=' . urlencode($msg_content . $awardNum);
    	$push_url .= '&pass_through=' . '1';							//消息类型
    	$push_url .= '&extra.title=' . urlencode($msg_content);					//标题
    	$push_url .= '&extra.result=' . $awardNum;						//开奖号码
    	$push_url .= '&extra.lid=' . $lid;								//彩种ID
    	$push_url .= '&topic=' . $tagName;						//推送标签

    	$pushResponse = $this->tools->request($push_url, $postData);
     	$pushResponse = json_decode($pushResponse, true);
        return $pushResponse;
    }

	// 中奖信息推送
	public function bonusPush($uid, $money, $lid)
	{
		$parmas = array(
            'appSecret' => self::$appSecret,
        );

        $postData['MIPUSHJSON'] = $parmas;
        $push_url = 'https://api.xmpush.xiaomi.com/v2/message/topic';		//推送接口
        $push_url .= '?payload=' . '';
        $push_url .= '&restricted_package_name=' . 'com.caipiao166';	//包名     
        $push_url .= '&pass_through=' . '1';							//消息类型
        $push_url .= '&extra.title=' . urlencode($msg_content);					//标题
        $push_url .= '&extra.result=' . $awardNum;						//开奖号码
        $push_url .= '&extra.lid=' . $lid;								//彩种ID
        $push_url .= '&topic=' . $tagName;						//推送标签

        $pushResponse = $this->tools->request($push_url, $postData);
        
        return $pushResponse;
	}

}
