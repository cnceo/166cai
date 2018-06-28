<?php
class Rsync extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->switch_file_path = BASEPATH . "../application/rsync/";
	}
	
	public function index()
	{
        $this->check_capacity("10_1_1");
		$act = $this->input->post('action', true);
		if($act)
		{
			if( $act == 'start' )
			{
			    $this->check_capacity("10_1_2");
				echo $this->start();
                $this->syslog(24, "开启预上线操作" );
			}
			elseif( $act == 'close')
			{
			    $this->check_capacity("10_1_2");
				echo $this->close();
                $this->syslog(24, "关闭预上线操作" );
			}
			elseif($act == 'static_start')
			{
			    $this->check_capacity("10_1_3");
				echo $this->static_start();
				$this->syslog(24, "开启静态化页面同步" );
			}
			elseif($act == 'static_close')
			{
			    $this->check_capacity("10_1_3");
				echo $this->static_close();
				$this->syslog(24, "关闭静态化页面同步" );
			}
			exit;
		}
		// 获取配置任务表
		$this->load->model('Model_task');
		$taskInfo = $this->Model_task->getRestartTask();
		$this->load->view("rsync", array('taskInfo' => $taskInfo));
		
	}
	
	private function start()
	{
		$fname = "{$this->switch_file_path}rsync.start";
		if(!file_exists($fname))
		{
			@fopen($fname, 'w');
		}

		return true;
	}
	
	private function close()
	{
		$fname = "{$this->switch_file_path}switch.start";
		if(file_exists($fname))
		{
			@unlink($fname);
		}

		return true;
	}
	
	private function static_start()
	{
		$fname = "{$this->switch_file_path}static_rsync.stop";
		if(file_exists($fname))
		{
			@unlink($fname);
		}

		return true;
	}
	
	private function static_close()
	{
		$fname = "{$this->switch_file_path}static_rsync.stop";
		if(!file_exists($fname))
		{
			@fopen($fname, 'w');
		}

		return true;
	}

	public function restartTask()
	{
	    $this->check_capacity("10_1_4");
		$task = $this->input->post('task', true);
		$status = $this->input->post('status', true);
		$folder = $this->input->post('folder', true);

		$fpath = APPPATH . "../logs/plock/";
		if($folder)
		{
			$fpath .= "$folder/";
		}

		if($status)
		{
			// 开启
			$file = $fpath . $task . '.start';	
			if(file_exists($fpath . $task . '.stop'))
			{
				unlink($fpath . $task . '.stop');
			}
		}
		else
		{
			// 关闭
			$file = $fpath . $task . '.stop';
			if(file_exists($fpath . $task . '.start'))
			{
				unlink($fpath . $task . '.start');
			}
		}

		if(!file_exists($file))
		{
			$hfile = @fopen($file, 'w');
			fclose($hfile);
		}
        echo 1;
	}
	public function process($type='pro'){
		$this->check_capacity("10_1_5",true);
		$this->load->library('tools');
        if (ENVIRONMENT === 'production') {
            $baseUrl = 'http://120.132.33.198/';
        } else {
            $baseUrl = 'http://123.59.105.39/';
        }
        $res = $this->tools->request($baseUrl.'api/reload/process/pro');
        if($res){
        	$this->syslog(66, "彩票脚本重启成功" );
        	echo json_encode(array('status'=>'y','message'=>'彩票脚本重启成功'));die;
        }else{
        	$this->syslog(66, "彩票脚本重启失败" );
        	echo json_encode(array('status'=>'n','message'=>'彩票脚本重启失败'));die;
        }
	}
	public function getIp()
	{
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
		else
		$ip = "unknown";
		return $ip;
	}
}