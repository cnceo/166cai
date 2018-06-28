<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 使用查询类 接口
 * @version:V1.2
 * @date:2015-08-14
 */
class Tool extends MY_Controller 
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
    {
        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $this->getRequestHeaders()
        );
        echo json_encode($result);  
    }
	
	/*
 	 * 彩种奖金计算器
 	 * @version:V1.2
 	 * @date:2016-01-18
 	 */
	public function calculate()
	{
		$data = $this->input->post(NULL, TRUE);
		
		// 调试
		// $data = array(
		// 	'lid' => '51',
		// 	'issue' => '2015098',
		// 	'codes' => '06,09,13,27,26,33|05:1:1'
		// );

		// $data = array(
		// 	'lid' => '23529',
		// 	'issue' => '2015098',
		// 	'codes' => '09,14,21,27,33|06,09:1:1'
		// );

		if(empty($data['lid']) || empty($data['issue']) || empty($data['codes']))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		$this->load->model('calculate_model', 'Calculate');
		$libraryName = $this->Calculate->getLibraryName($data['lid']);
		$details = array();
		if(!empty($libraryName))
		{
			// 查询指定彩种期次的开奖详情
			$this->load->model('award_model', 'Award');
	    	$awards = $this->Award->getAwardDetailByDcenter($data['lid'], $data['issue']);

	    	if(!empty($awards))
	    	{
	    		$library = "calculate/{$libraryName}";
				$this->load->library($library);
				$details = $this->$libraryName->getBonusDetail($data, $awards);	
				$result = array(
					'status' => '1',
					'msg' => 'Success',
					'data' => $details
				);	
	    	}
	    	else
	    	{
	    		$result = array(
					'status' => '0',
					'msg' => '当前开奖期次获取失败',
					'data' => array()
				);
	    	}

		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '暂不支持当前彩种',
				'data' => array()
			);
		}
		echo json_encode($result);
	}
}
