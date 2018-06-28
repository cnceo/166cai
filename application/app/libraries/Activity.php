<?php

/*
 * APP 活动关联类
 * @date:2016-07-26
 */

class Activity
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /*
    * 注册关联 - 188红包
    * @date:2016-07-26
    */
	public function regHookBy188($activityData)
	{
        if($this->hasAttend($activityData))
        {
        	$postData = array(
        		'userId' 	=> $activityData['uid'],
        		'userType'	=> 2
        	);
        	
        	if(ENVIRONMENT === 'checkout')
        	{
        		$postUrl = $this->CI->config->item('cp_host');
        		$postData['HOST'] = $this->CI->config->item('domain');
        	}
        	else
        	{
        		$postUrl = $this->CI->config->item('protocol') . $this->CI->config->item('pages_url');
        	}
        	
        	$attendResult = $this->CI->tools->request($postUrl . 'api/redpack/send', $postData);
        }
        else
        {
            // attend
            $postData = array(
                'phone' => $activityData['phone'],
                'platformId' => $activityData['platformId'],
                'channelId' => $activityData['channelId'],
            );

            if(ENVIRONMENT === 'checkout')
            {
                $postUrl = $this->CI->config->item('cp_host');
                $postData['HOST'] = $this->CI->config->item('domain');
            }
            else
            {
                $postUrl = $this->CI->config->item('protocol') . $this->CI->config->item('pages_url');
            }

            $attendResult = $this->CI->tools->request($postUrl . 'api/activity/attend', $postData);
        }
	}

    /*
    * 注册关联 - 188红包 - 是否参与
    * @date:2016-05-26
    */
    public function hasAttend($activityData)
    {
        $postData = array(
            'phone' => $activityData['phone'],
        );

        if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->CI->config->item('cp_host');
            $postData['HOST'] = $this->CI->config->item('domain');
        }
        else
        {
            $postUrl = $this->CI->config->item('protocol') . $this->CI->config->item('pages_url');
        }

        $attendResult = $this->CI->tools->request($postUrl . 'api/activity/hasAttend', $postData);
        $attendResult = json_decode($attendResult, true);

        if($attendResult['status'] == '200')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /*
    * 注册关联 - 拉新活动
    * @date:2016-07-26
    */
    public function regHookByLx($activityData)
    {
        $postData = array(
            'uid' => $activityData['uid'],
            'phone' => $activityData['phone'],
        );

        if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->CI->config->item('cp_host');
            $postData['HOST'] = $this->CI->config->item('domain');
        }
        else
        {
            $postUrl = $this->CI->config->item('protocol') . $this->CI->config->item('pages_url');
        }

        $attendResult = $this->CI->tools->request($postUrl . 'api/activity/regAdd', $postData);
    }

    /*
    * 实名关联 - 拉新活动
    * @date:2016-07-26
    */
    public function idcardHookByLx($activityData)
    {
        $postData = array(
            'uid' => $activityData['uid'],
            'idCard' => $activityData['idCard'],
        );

        if(ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->CI->config->item('cp_host');
            $postData['HOST'] = $this->CI->config->item('domain');
        }
        else
        {
            $postUrl = $this->CI->config->item('protocol') . $this->CI->config->item('pages_url');
        }

        $attendResult = $this->CI->tools->request($postUrl . 'api/activity/idcardAdd', $postData);
    }
}
