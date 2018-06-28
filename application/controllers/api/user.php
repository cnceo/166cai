<?php

/*
 * 订单信息处理 创建、付款通用接口
 * @date:2015-05-22
 */
class User extends CI_Controller 
{

	private $_paramArr = array('uid', 'msg_send');
	
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('newuser_model');
    }

    public function updateMsgsend()
    {
    	$params = $this->input->post();
    	foreach ($this->_paramArr as $prm) {
    		if (!isset($params[$prm])) {
    			$res = array('codes' => '300', 'msg' => '缺少必要参数');
    			exit(json_encode($res));
    		}
    	}
    	
    	if ($this->user_model->updateUserMsgsend($params)) {
    		$res = array('codes' => '200', 'msg' => '更新成功');
    		exit(json_encode($res));
    	}
    	$res = array('codes' => '400', 'msg' => '更新失败');
    	exit(json_encode($res));
    }

    /**
     * 修改中奖推送
     */
    public function updatePushsend()
    {
        $params = $this->input->post();

        if($this->user_model->updateUserPushsend($params))
        {
            $res = array('codes' => '200', 'msg' => '更新成功');
        }
        else
        {
            $res = array('codes' => '400', 'msg' => '更新失败');
        }
        exit(json_encode($res));
    }
    

    /**
     * 修改用户手机号
     */
    public function modifyPhone()
    {
        $uid = $this->input->post('uid', true);
        $phone = $this->input->post('phone', true);
        $isbck = $this->input->post('isbck', true);

        $result = array(
            'status' => '0',
            'msg' => '通讯异常',
            'data' => ''
        );

        if(!empty($uid) && !empty($phone))
        {
            // 手机格式检查
            $rule = '/1\d{10}$/';
            if (!preg_match($rule, $phone))
            {
                $result = array(
                    'status' => '0',
                    'msg' => '手机号码格式不正确',
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }

            // 组装参数
            $data = array(
                'uid'   => $uid,
                'phone' => $phone,
                'isbck'     => isset($isbck) ? 1 : 0
            );
            $result = $this->user_model->modifyUserPhone($data);
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '手机号码不能为空',
                'data' => ''
            );
        }

        die(json_encode($result));
    }
    
    /**
     * 修改真实姓名
     */
    public function modifyRealName()
    {
    	$uid = $this->input->post('uid', true);
    	$real_name = $this->input->post('real_name', true);
    	$isbck = $this->input->post('isbck', true);
    	$result = array(
    			'status' => '0',
    			'msg' => '通讯异常',
    			'data' => ''
    	);
    	if(!empty($uid) && !empty($real_name))
    	{
    		// 真实姓名格式检查
    		$rule = '/^[_\x{4e00}-\x{9fa5}\·\.\d]{2,10}$/iu';
    		if( !preg_match($rule, $real_name) )
    		{
    			$result = array(
    					'status' => '0',
    					'msg' => '请输入正确的中文名',
    					'data' => ''
    			);
    			echo json_encode($result);
    			exit();
    		}
    		// 组装参数
    		$data = array(
    				'uid'       => $uid,
    				'real_name' => $real_name,
    		        'isbck'     => isset($isbck) ? 1 : 0
    		);
    
    		if($this->user_model->SaveUserBase($data))
    		{
    			$result = array(
    					'status' => '1',
    					'msg' => '真实姓名修改成功',
    					'data' => ''
    			);
    		}
    		else
    		{
    			$result = array(
    					'status' => '0',
    					'msg' => '真实姓名修改失败',
    					'data' => ''
    			);
    		}
    	}
    	else
    	{
    		$result = array(
    				'status' => '0',
    				'msg' => '手机号码不能为空',
    				'data' => ''
    		);
    	}
    	die(json_encode($result));
    }
    /**
     * 修改身份证号
     */
    public function modifyIdCard()
    {
    	$uid = $this->input->post('uid', true);
    	$id_card = $this->input->post('id_card', true);
    	$isbck = $this->input->post('isbck', true);
    	$result = array(
    			'status' => '0',
    			'msg' => '通讯异常',
    			'data' => ''
    	);
    	if(!empty($uid) && !empty($id_card))
    	{
    		// 真实姓名格式检查
    		$this->load->library('IdCard');
    		$idInfo = IdCard::checkIdCard($id_card);
    		if ($idInfo !== false)
    		{
    			if (!IdCard::isEnoughAgeByIdCard($id_card, 18))
    			{
    				$result = array(
    						'status' => '0',
    						'msg' => '身份证未满18周岁',
    						'data' => ''
    				);
    				echo json_encode($result);
    				exit();
    			}
    			// 组装参数
    			$data = array(
    					'uid'   => $uid,
    					'id_card' => $id_card,
    			        'isbck'     => isset($isbck) ? 1 : 0
    			);
    
    			if($this->user_model->SaveUserBase($data))
    			{
    				$result = array(
    						'status' => '1',
    						'msg' => '身份证号修改成功',
    						'data' => ''
    				);
    			}
    			else
    			{
    				$result = array(
    						'status' => '0',
    						'msg' => '身份证号修改失败',
    						'data' => ''
    				);
    			}
    		}
    		else
    		{
    			$result = array(
    					'status' => '0',
    					'msg' => '身份证格式错误',
    					'data' => ''
    			);
    			echo json_encode($result);
    			exit();
    		}
    	}
    	else
    	{
    		$result = array(
    				'status' => '0',
    				'msg' => '身份证号不能为空',
    				'data' => ''
    		);
    	}
    	die(json_encode($result));
    }
    
    public function bindIdCard() {
    	$data = array();
    	$data['uid'] = $this->input->post('uid', true);
    	$data['id_card'] = $this->input->post('id_card', true);
    	$data['real_name'] = $this->input->post('real_name', true);
    	$data['ignoreRedpack'] = $this->input->post('ignoreRedpack', true);
    	$this->load->library('BindIdCard');
		$result = $this->bindidcard->appsetIdCardInfo($data);
		echo json_encode($result);
		exit();
    }
    
    /**
     * 修改用户邮箱
     */
    public function modifyEmail()
    {
    	$uid = $this->input->post('uid', true);
    	$email = $this->input->post('email', true);
    	$isbck = $this->input->post('isbck', true);
    
    	$result = array(
    		'status' => '0',
    		'msg' => '通讯异常',
    		'data' => ''
    	);
    
    	if(!empty($uid) && !empty($email))
    	{
    		// 手机格式检查
    		$rule = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/u';
    		if (!preg_match($rule, $email))
    		{
    			$result = array(
    				'status' => '0',
    				'msg' => '邮箱格式不正确',
    				'data' => ''
    			);
    			echo json_encode($result);
    			exit();
    		}
    
    		// 组装参数
    		$data = array(
    			'uid'   => $uid,
    			'email' => $email,
    		    'isbck'     => isset($isbck) ? 1 : 0
    		);
    		
    		$res = $this->newuser_model->bindEmail($data);
    		if($res)
    		{
    			$result = array(
    				'status' => '1',
    				'msg' => '邮箱修改成功',
    				'data' => ''
    			);
    			$this->user_model->freshUserInfo($uid);
    		}
    		else
    		{
    			$result = array(
    				'status' => '0',
    				'msg' => '邮箱修改失败',
    				'data' => ''
    			);
    		}
    	}
    	else
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '邮箱地址不能为空',
    			'data' => ''
    		);
    	}
    
    	die(json_encode($result));
    }

    /**
     * 删除银行卡
     */
    public function delBank()
    {
        $uid = $this->input->post('uid', true);
        $id = $this->input->post('id', true);

        $result = array(
            'status' => '0',
            'msg' => '通讯异常',
            'data' => ''
        );

        if(!empty($uid) && !empty($id))
        {
            // 组装参数
            $data = array(
                'uid'   => $uid,
                'id' => $id
            );

            $res = $this->newuser_model->delBank($data);
            if($res['status'])
            {
                $result = array(
                    'status' => '1',
                    'msg' => '银行卡删除成功',
                    'data' => ''
                );
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'msg' => '银行卡删除失败',
                    'data' => ''
                );
            }
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '银行卡信息不能为空',
                'data' => ''
            );
        }
        die(json_encode($result));
    }

    // 新版推送设置
    public function updatePushStatus()
    {
        $params = $this->input->post();

        if($this->user_model->updatePushStatus($params))
        {
            $res = array('codes' => '200', 'msg' => '更新成功');
        }
        else
        {
            $res = array('codes' => '400', 'msg' => '更新失败');
        }
        exit(json_encode($res));
    }
}
