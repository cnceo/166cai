<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Static_Page extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('file');
	}
	
	public function index() {
		$this->main();
		$this->tzmanpin(SSQ, 1, 2);
		$this->tzmanpin(DLT, 1, 4);
	}
	
	private function main() {
		$this->load->model('main_model', 'model');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		
		$jingtai = $this->model->getJingTai();
		$noticeList = $this->model->noticeList(array('status'=>1), 0, 3);
		$winnews = $this->model->getOrderWin();
		$this->load->model('lottery_model');
		$issueData = $this->lottery_model->getAllAwards();
		$this->load->model('match_model', 'match');
		$jzmatch = $this->match->getMatch('jczq');
		$jlmatch = $this->match->getMatch('jclq');
		$jz = $jzmatch['data'];
		$hotjz = $jzmatch['hotid'];
		$jl = $jlmatch['data'];
		$hotjl = $jlmatch['hotid'];
                $this->isMobile();
                $this->load->model('award_model');
                $awardInfo = array();
                $awardData = $this->award_model->getCurrentAward();
                foreach ($awardData as $items)
                {
                    $awardInfo[$items['seLotid']] = $items;
                }
                $dltPool = floor($awardInfo['23529']['awardPool'] / 100000000);
		$string = $this->load->view('v1.1/main/index', array(
				'issues' => $issueData,
				'jingtai' => $jingtai,
				'jczq' => json_encode($jz),
				'jclq' => json_encode($jl),
				'hotjl'=> json_encode($hotjl),
				'hotjz'=> json_encode($hotjz),
				'winnews' => $winnews,
				'noticeInfo'=> $noticeList,
				'uservpop' => $this->cache->get($this->REDIS['USERVPOP']),
                                'dltPool' => $dltPool
		), true);
		$string .= $this->displaySpring(true, 'main', 'index');
		write_file("./application/views/v1.1/static/main.php5", $string);
		if (ENVIRONMENT === 'production') {
			system("/bin/bash /opt/shell/rsync_static.sh", $status);
		}
	}
	
	private function tzmanpin($lid, $misscount, $infoid) {
		$this->load->model('lottery_model', 'Lottery');
		$data = $this->Lottery->getHistory($lid);
		$enName = $this->Lottery->getEnName($lid);
		$string = $this->load->view("v1.1/".$enName."/kj", compact('data'), true);
		write_file("./application/views/v1.1/static/".$enName."_kj.php5", $string);
		
		$this->load->model('missed_model', 'Miss');
		$miss = $this->Miss->getData($lid, (int)$misscount);
		$miss = each($miss);
		$miss = $miss['value'];
		$string = $this->load->view("v1.1/".$enName."/pickarea", compact('miss'), true);
		write_file("./application/views/v1.1/static/".$enName."_pickarea.php5", $string);
		
		$this->load->model('info_model', 'Info');
		$infoList = $this->Info->getListByCategory($infoid, 0, 5);
		$string = $this->load->view("v1.1/".$enName."/info", $infoList, true);
		write_file("./application/views/v1.1/static/".$enName."_info.php5", $string);
	}

}