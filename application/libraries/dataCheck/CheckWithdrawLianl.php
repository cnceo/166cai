<?php
/**
 * 提现-连连代付
 * @author Administrator
 *
 */
class checkWithdrawLianl
{
	private $CI;
	private $config;
	private $ftpConfig = array(
		'hostname' => 'hz-sftp1.lianlianpay.com',
		'username' => 'sh-caika',
		'password' => 'Y4B8IFA1',
		'port' => '2122',
	);
	private $logPath = 'application/logs/dataCheck/';
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('other_data_check_model', 'dataModel');
	}
	
	/**
	 * 拉取彩豆对账文件
	 * @param array $config 对账配置信息
	 * @param int $r_flag 重新拉取标识   1 重拉  0 否
	 */
	public function exec($config, $r_flag = 0)
	{
		$this->config = $config;
		$checkTime = ($config['exc_date'] == '0000-00-00') ? strtotime("-1 day") : strtotime($config['exc_date']) + 86400;
		$checkDate = date('Y-m-d', $checkTime);
		$remFile = '/sh-caika/' . $config['extend'] . '/FKMX_' . $config['extend'] . '_'. date('Ymd', $checkTime) .'.txt';
		$locFile = $this->logPath . date('Ymd', $checkTime) . '_lianlian_' . $config['extend'] .'.csv';
		if(!file_exists($locFile) || $r_flag)
		{
			//连接sftp
			$conn = ssh2_connect($this->ftpConfig['hostname'], $this->ftpConfig['port']);
			if(ssh2_auth_password($conn, $this->ftpConfig['username'], $this->ftpConfig['password']))
			{
				$ressftp = ssh2_sftp($conn);
				$stream = fopen("ssh2.sftp://".$ressftp.$remFile, 'r');
				if($stream)
				{
					file_put_contents($locFile, $stream);
					fclose($stream);
				}
				else
				{
					$this->CI->dataModel->updateCheckConfig($config['id'], array('fail_times' => $config['fail_times'] + 1));
					if(($config['fail_times'] + 1) >= 3)
					{
						$this->CI->dataModel->insertAlert($config['name'] . '_' . $checkDate, '对账单获取失败报警', $config['name'] . '提现对账单获取失败');
					}
					return ;
				}
			}
		}
		
		if(!file_exists($locFile))
		{
			return ;
		}
		
		//解析对账文件
		$spl_object = new SplFileObject($locFile, 'r');
		if($spl_object)
		{
			$spl_object->seek(filesize($locFile));
			$total = $spl_object->key();
			if($total < 1)
			{
				$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '提现对账单格式有误');
				return;
			}
			
			$pSize = 3000;
			$pages = ceil($total/ $pSize);
			$start = 0;
			$trunResult = $this->CI->dataModel->truncateWithdrawCheck();
			if(!$trunResult)
			{
				return ;
			}
			$this->CI->dataModel->trans_start('db');
			for($i = 1; $i <= $pages; $i++)
			{
				$datas = array();
				$spl_object->seek($start);
				$num = $pSize;
				while ($num-- && !$spl_object->eof()) 
				{
					$dataStr = $spl_object->current();
					$data = explode(',', $dataStr);
					if(($data['1'] != $config['extend']) || ($data['7'] != '1') || ($data['8'] !== '0'))
					{
						$spl_object->next();
						continue;
					}
					$datas[] = array('trade_no' =>trim($data['0']), 'date' => $checkDate, 'o_status' => 1, 'o_money' => trim($data['6']) * 100);
					$spl_object->next();
				}
				$start += $pSize;
				
				if($datas)
				{
					$res = $this->CI->dataModel->insertWithdrawCheck($datas, array('trade_no', 'date', 'o_status', 'o_money'));
					if(!$res)
					{
						$this->CI->dataModel->trans_rollback('db');
						return ;
					}
				}
			}
			
			//执行比对操作
			$result = $this->CI->dataModel->checkWithdraw($checkDate, $this->config, $r_flag);
			if(!$result)
			{
				$this->CI->dataModel->trans_rollback('db');
				return ;
			}
			
			$this->CI->dataModel->trans_complete('db');
		}
		else
		{
			$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '出票对账单格式有误');
		}
	}
}
