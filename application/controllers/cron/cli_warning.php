<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 每隔一分钟需要执行的脚本
 * 作    者: shigx
 * 修改日期: 2016/3/11
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Warning extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('warning_model');
        $this->load->library('tools');
    }

    /**
     * 报警执行主方法
     */
    public function index()
    {
    	$cname = strtolower(__CLASS__);
    	$stop = $this->warning_model->ctrlRun($cname);
    	while(!$stop)
    	{
    		$this->execAlert();
    		$stop = $this->warning_model->ctrlRun($cname);
    		sleep(5);
    	}
    }
    
    /**
     * 执行报警
     */
    public function execAlert()
    {
    	$result = $this->warning_model->getAlerts();
    	$datas = array();
    	$ids = array();
    	foreach ($result as $content)
    	{
    		$datas[$content['ctype']]['ids'][] = $content['id'];
    		$datas[$content['ctype']]['phone'] = $content['phone'];
    		$datas[$content['ctype']]['email'] = $content['email'];
    		$datas[$content['ctype']]['sendType'] = $content['sendType'];
    		$datas[$content['ctype']]['title'][] = $content['title'];
    		$datas[$content['ctype']]['content'] .= $content['content'] . '<br>';
    		$ids[] = $content['id'];
    	}
    	
    	foreach ($datas as $data)
    	{
    		$this->sendNotice($data);
    	}
    }
    
    /**
     * 通知消息
     * @param array $content
     */
    private function sendNotice($content)
    {
    	switch ($content['sendType'])
    	{
    		//短信
    		case 1:
    			$this->sendPhone($content);
    			$this->warning_model->updateAlert($content['ids'], 1);
    			break;
    		//邮件
    		case 2:
    			$content['title'] = implode('|', array_unique($content['title']));
    			$this->sendEmail($content);
    			$this->warning_model->updateAlert($content['ids'], 2);
    			break;
    		//后台消息
    		case 3:
    			$this->sendMessage($content);
    			$this->warning_model->updateAlert($content['ids'], 3);
    			break;
    		//短信和邮件
    		case 4:
    			$this->sendPhone($content);
    			$content['title'] = implode('|', array_unique($content['title']));
    			$this->sendEmail($content);
    			$this->warning_model->updateAlert($content['ids'], 4);
    			break;
    		//邮件和消息
    		case 5:
    			$content['title'] = implode('|', array_unique($content['title']));
    			$this->sendEmail($content);
    			$this->sendMessage($content);
    			$this->warning_model->updateAlert($content['ids'], 5);
    			break;
    		default:
    	}
    }
    
    /**
     * 发短信
     * @param unknown_type $content
     */
    private function sendPhone($content)
    {
    	if($content['phone'])
    	{
    		$phones = explode(',', $content['phone']);
    		foreach ($phones as $phone)
    		{
    			$this->tools->sendSms('null', $phone, $content['content'], 10, '127.0.0.1', 193);
    		}
    	}
    }
    
    /**
     * 发邮件
     * @param unknown_type $content
     */
    private function sendEmail($content)
    {
    	$mail_config = array(
            'smtp_host' => 'smtp.exmail.qq.com',
            'smtp_user' => 'lijun1@km.com',
            'smtp_pass' => 'hUQq51DmMk2qS0nL',
    		'newline'  => "\r\n"
        );
        
    	if($content['email'])
    	{
    		$info['from'] = 'lijun1@km.com';
    		$emails = explode(',', $content['email']);
    		$info['subject'] = $content['title'];
    		$info['message'] = $content['content'];
    		if(count($emails) > 1)
    		{
    			$info['to'] = array_shift($emails);
    			$info['cc'] = implode(',', $emails);
    			//$info['bcc']= '166cai@km.com'; 去掉密送
    		}
    		else
    		{
    			$info['to'] = $emails[0];
    		}
    		$this->tools->sendMail($info, $mail_config);
    	}
    }
    
    /**
     * 后台消息
     * @param unknown_type $content
     */
    private function sendMessage($content)
    {
    	//TODO
    }
}