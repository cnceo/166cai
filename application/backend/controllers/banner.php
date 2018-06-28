<?php
class banner extends MY_Controller{
	
	
	
	public function index($action = 'tzy') {
	    $this->check_capacity('6_6_1');
		
		$tzylocationArr = array(
			'hall'     => array(array('购彩大厅', 'index')), 
			'hemai'    => array(array('合买大厅', 'index|ssq|dlt|fcsd|pls|qxc|qlc|sfc|jczq|jclq'), array('合买详情', 'detail')),
			'gendan'   => array(array('跟单大厅', 'index|ssq|dlt|fcsd|pls|qxc|qlc|sfc|jczq|jclq')),
			'user'     => array(array('个人主页', 'index')),
			'kaijiang' => array(array('全国开奖', 'index'), array('开奖详情', 'ssq|dlt|syxw|jxsyxw|fc3d|pl5|pl3|qlc|qxc|ks|klpk|hbsyxw|cqssc|sfc|rj|gdsyxw')), 
			'chart'    => array(array('走势图', 'index')), 
			'academy'  => array(array('彩票学院', 'index')), 
			'fcsd'     => array(array('福彩3D', 'index')), 
			'qlc'      => array(array('七乐彩', 'index')), 
			'qxc'      => array(array('七星彩', 'index')), 
			'pls'      => array(array('排列三', 'index')), 
			'plw'      => array(array('排列五', 'index')), 
			'ssq'      => array(array('双色球', 'index')), 
			'dlt'      => array(array('大乐透', 'index')), 
			'jczq'     => array(array('竞彩足球', 'bqc|cbf|dg|hh|jqs|rqspf|spf')), 
			'jclq'     => array(array('竞彩篮球', 'hh|sfc')), 
			'sfc|rj'   => array(array('胜负/任九', 'index|sg'))
		);
		$zcfclocationArr = array(
			'main'     => array(array('首页', 'index')), 
			'hall'     => array(array('购彩大厅', 'index')), 
			'hemai'    => array(array('合买大厅', 'index|ssq|dlt|fcsd|pls|qxc|qlc|sfc|jczq|jclq'), array('合买详情', 'detail'), array('跟单详情', 'gdetail')),
			'gendan'   => array(array('跟单大厅', 'index|ssq|dlt|fcsd|pls|qxc|qlc|sfc|jczq|jclq')),
			'user'     => array(array('个人主页', 'index')),
			'kaijiang' => array(array('全国开奖', 'index'), array('开奖详情', 'ssq|dlt|syxw|jxsyxw|fc3d|pl5|pl3|qlc|qxc|ks|klpk|hbsyxw|cqssc|sfc|rj|gdsyxw')), 
			'chart'    => array(array('走势图', 'index')), 
			'academy'  => array(array('彩票学院', 'index')), 
			'fcsd'     => array(array('福彩3D', 'index')), 
			'qlc'      => array(array('七乐彩', 'index')), 
			'qxc'      => array(array('七星彩', 'index')), 
			'pls'      => array(array('排列三', 'index')), 
			'plw'      => array(array('排列五', 'index')), 
			'ssq'      => array(array('双色球', 'index')), 
			'dlt'      => array(array('大乐透', 'index')), 
			'jczq'     => array(array('竞彩足球', 'bqc|cbf|dg|hh|jqs|rqspf|spf')), 
			'jclq'     => array(array('竞彩篮球', 'hh|sfc')), 
			'sfc|rj'   => array(array('胜负/任九', 'index|sg')),
			'orders'    => array(array('订单详情', 'detail')),
			'chases'    => array(array('追号详情', 'detail'))
		);
		$ycfclocationArr = array(
			'main'     => array(array('注册', 'register'), array('登录', 'login'), array('注册成功', 'welcome')),
			'mylottery' => array(array('支付', 'rchagscess')),
			'safe' => array(array('修改手机号', 'modifyUserPhone'), array('实名认证', 'userInfo'), array('修改密码', 'modifyUserPword'), array('忘记密码', 'findPword'), array('绑定邮箱', 'bindEmail|modifyEmail|bindEmailSucc|modifyEmailSucc')),
			'wechat' => array(array('微信绑定手机号', 'bindPhone')),
		);
		$info = $this->input->post();
		if ($info) $this->checkenv($info['env']);
		$this->load->model('model_banner', 'img');
		$data['tzy'] = $this->img->getListByPosition('tzy');
		$data['zcfc'] = $this->img->getListByPosition('zcfc');
		$data['ycfc'] = $this->img->getListByPosition('ycfc');
		$data['saved'] = $this->input->get('saved') ? 1 : 0;
		$data['notfull'] = $this->input->get('notfull') ? 1 : 0;
		if ($info['tzy']) {
		    $this->check_capacity('6_6_4');
			$arg = 'tzy';
			$ur = '/backend/banner';
			$tt = '投注页banner';
		}elseif ($info['zcfc']) {
		    $this->check_capacity('6_6_5');
			$arg = 'zcfc';
			$ur = '/backend/banner/index/zcfc';
			$tt = '左侧浮层广告';
		}
		elseif($info['ycfc'])
		{
		    $this->check_capacity('6_6_6');
			$arg = 'ycfc';
			$ur = '/backend/banner/index/ycfc';
			$tt = '右侧banner';
		}
		if ($arg) {
			$arrStr = $arg."locationArr";
			$arr = $$arrStr;
			foreach ($info[$arg] as $val) {
				$flag = false;
				$val['url'] = ($arg == 'ycfc' && !empty($val['location'])) ? 'default not used' : $val['url'];
				if ($val['title'] || $val['path'] || $val['url'] || $val['location']) {
					foreach (array('title', 'path', 'url', 'location') as $pa) {
						if (empty($val[$pa])) {
							$this->redirect($ur.'?notfull=1');
						}
					}
					$val['position'] = $arg;
					$location = '';
					foreach ($val['location'] as $v) 
					{
						$strArry = explode('/', $v);
				        foreach (explode('|', $strArry[0]) as $cons) 
				        {
				            foreach (explode('|', $strArry[1]) as $acts) 
				            {
				                $location .= $cons . '/' . $acts . '|';
				            }
				        }
					}
					$val['location'] = substr($location, 0, -1);
					$istData[] = $val;
				}
			}
			$this->img->delByPosition($arg);
			$this->img->insertAllData($istData);
			$this->refreshCache($arg);
			// 组合数据
			$config = $this->getConfigArry($arr);

			foreach ($istData as $dt){
				$lctstr = '';
				foreach (explode('|', $dt['location']) as $lct) {
					if(!empty($config[$lct]))
					{
						$tmp = $config[$lct];
						$lctstr .= $tmp.',';
					}
				}
				$lctstr = substr($lctstr, 0, -1);
				if($arg == 'ycfc')
				{
					$this->syslog(39, $tt."更新内容：（".$dt['title']."），页面：".$lctstr);
				}
				else
				{
					$this->syslog(39, $tt."更新内容：（".$dt['title']."），彩种：".$lctstr);
				}
			}
			$this->redirect($ur.'?saved=1');
		}
		$data['action'] = $action;
		$data['tzylocationArr'] = $tzylocationArr;
		$data['zcfclocationArr'] = $zcfclocationArr;
		$data['ycfclocationArr'] = $ycfclocationArr;
		$this->load->view('banner', $data);
	}	
	
	public function upload($position, $index)
	{
		if (! file_exists ( "../uploads/banner/" ))
		{
			mkdir ( "../uploads/banner/" );
		}
	
		$config ['upload_path'] = "../uploads/banner/";
		$config ['allowed_types'] = 'jpg|png|bmp|jpeg';
		$extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
	
		$config ['max_size'] = 10240;
		$this->load->library ( 'upload', $config );
		if ($this->upload->do_upload ( 'file' ))
		{
			$data = $this->upload->data ();
			$res = array (
					'name' => $data ['file_name'],
					'index'=> $index,
					'position'=>$position
			);
			exit ( json_encode ( $res ) );
		} else
		{
			$error = $this->upload->display_errors ();
			exit ( $error );
		}
	}
	
	private function refreshCache($position)
	{
		$this->load->model('model_banner', 'model');
		$data = $this->model->getDataByPosition($position);
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$this->cache->hSet($REDIS['BANNERS'], $position, json_encode($data));
	}

	public function getConfigArry($locationArr)
	{
		$config = array();
		foreach ($locationArr as $cons => $items) 
		{
			$conArry = explode('|', $cons);
			foreach ($items as $key => $val) 
			{
				$arr = explode('|', $val[1]);
				$config[$conArry[0].'/'.$arr[0]] = $val[0];
			}
		}
		return $config;
	}
}