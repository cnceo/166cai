<?php
/**
 * 充值对账-支付宝H5-现在支付
 * @author Administrator
 *
 */
class checkZfbXz
{
	private $CI;
	private $config;
	private $ftpConfig = array(
		'hostname' => 'file.ipaynow.cn',
		'username' => '000000000518573',
		'password' => 'hUAtOe0Cqj9SguoRUkum',
		'port' => '2211',
	);
	private $logPath = 'application/logs/dataCheck/';
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('Ftp');
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
		$remFile = 'daycount/' . $checkDate . '.zip';
		$name = explode('-', $config['name']);
		$locFile = $this->logPath . date('Ymd', $checkTime) . '_zfbxz_' . $name['2'] . '.csv';
		$this->CI->ftp->connect($this->ftpConfig);
		$locZip =  $this->logPath . date('Ymd', $checkTime) . '_zfbxz_' . $name[2] . '.zip';
		$result = $this->CI->ftp->download($remFile, $locZip);
		if(!$result)
		{
			$this->CI->dataModel->updateCheckConfig($config['id'], array('fail_times' => $config['fail_times'] + 1));
			if(($config['fail_times'] + 1) >= 3)
			{
				$this->CI->dataModel->insertAlert($config['name'] . '_' . $checkDate, '对账单获取失败报警', $config['name'] . '出票对账单获取失败');
			}
			return ;
		}
		$result = $this->unzip($locZip, $locFile);
		if(!$result)
		{
			$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '充值对账单格式有误');
			return;
		}
			
		unlink($locZip);
		unset($respone);
		
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
			if($total <= 1)
			{
				$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '出票对账单格式有误');
				return;
			}
			
			$pSize = 3000;
			$pages = ceil($total/ $pSize);
			$start = 0;
			$trunResult = $this->CI->dataModel->truncateRechargeCheck();
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
					if(iconv('GBK', 'UTF-8', trim($data[13])) != '成功')
					{
						$spl_object->next();
						continue;
					}
					$datas[] = array('trade_no' =>trim($data['1']), 'date' => $checkDate, 'o_status' => 1, 'o_money' => trim($data['17']) * 100);
					$spl_object->next();
				}
				$start += $pSize;
				
				if($datas)
				{
					$res = $this->CI->dataModel->insertRechargeCheck($datas, array('trade_no', 'date', 'o_status', 'o_money'));
					if(!$res)
					{
						$this->CI->dataModel->trans_rollback('db');
						return ;
					}
				}
			}
			
			//执行比对操作
			$result = $this->CI->dataModel->checkRecharge($checkDate, $this->config, $r_flag);
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
	
	/**
	 * 解压zip压缩包
	 * @param unknown_type $locZip
	 * @param unknown_type $locFize
	 */
	private function unzip($locZip, $locFize)
	{
		if ($zip = zip_open($locZip))
		{
			$fileFlag = false;
			while ($zip_entry = zip_read($zip))
			{
				// 打开包
				if (zip_entry_open($zip,$zip_entry,"r"))
				{
					$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					@file_put_contents($locFize, $fstream);
					// 关闭入口
					zip_entry_close($zip_entry);
				}
				if(is_file($locFize))
				{
					$fileFlag = true;
					break;
				}
			}
				
			// 关闭压缩包
			zip_close($zip);
				
			return $fileFlag;
		}
	
		return false;
	}
}
