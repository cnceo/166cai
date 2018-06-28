<?php
/**
 * Copyright (c) 2013,上海瑞创网络科技股份有限公司
 * 文件名称：ProcessLock.php
 * 摘    要：进程锁功能
 * 作    者：胡小明
 * 修改日期：2013.12.03
 */
class ProcessLock
{
	public $LOCK;
	private $base_path;
	private $CI;
	public function __construct()
	{
		$this->base_path = APPPATH;
	}
	/*进程锁*/
	public function getLock($lfile){
		$lock =  $this->base_path . 'logs';
		if(!is_dir($lock))
		{
			mkdir($lock);
		}
		if(!is_dir("$lock/plock"))
		{
			mkdir("$lock/plock");
		}
		$fileName = "$lock/plock/$lfile";
		$this->LOCK = fopen ($fileName, "w+");
		return flock($this->LOCK, LOCK_EX|LOCK_NB);
	}
	/*解锁*/
	public function unLock(){
		fclose($this->LOCK);
	}
	//拷贝脚本
	public function copy($datas)
	{
		$cpath = $this->base_path.'/cron/mcrons';
		$dpath = $this->base_path.'/tools';
		if(!empty($datas))
		{
			foreach ($datas as $row)
			{
				if(!empty($row['copys']))
				{
					$crons = explode(',', $row['copys']);
					if(!empty($crons))
					{
						foreach ($crons as $cron)
						{
							$cron = trim(preg_replace('/[\n\r]/', '', $cron), '/');
							if(file_exists("$cpath/$cron"))
							{
								@copy("$cpath/$cron", "$dpath/$cron");
							}
						}
					}
				}
			}
		}
	}
	//删除脚本
	public function delete($datas)
	{
		$cpath = $this->base_path.'/cron/mcrons';
		$dpath = $this->base_path.'/tools';
		if(!empty($datas))
		{
			foreach ($datas as $row)
			{
				if(!empty($row['dels']))
				{
					$crons = explode(',', $row['dels']);
					if(!empty($crons))
					{
						foreach ($crons as $cron)
						{
							$cron = trim(preg_replace('/[\n\r]/', '', $cron), '/');
							if(file_exists("$dpath/$cron"))
							{
								@unlink("$dpath/$cron");
							}
						}
					}
				}
			}
		}
	}
	public function __destruct() 
	{
		if($this->LOCK)
		{
			$this->unLock();
		}
   	}
}
?>