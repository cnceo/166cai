<?php
class Limitcode extends MY_Controller
{
	private $_lidMap = array(
		52 => array(
			'1' => array('num'=>3, 'cname' => '直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'2' => array('num'=>3, 'cname' => '组三', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'3' => array('num'=>3, 'cname' => '组六', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
		),
		33 => array(
			'1' => array('num'=>3, 'cname' => '直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'2' => array('num'=>3, 'cname' => '组三', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'3' => array('num'=>3, 'cname' => '组六', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
		),
		35 => array(
			'1' => array('num'=>5, 'cname' => '直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
		),
		21406 => array(
			'1' => array('num'=>1, 'cname' => '前一', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>2, 'cname' => '任选二', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'3' => array('num'=>3, 'cname' => '任选三', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'4' => array('num'=>4, 'cname' => '任选四', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'5' => array('num'=>5, 'cname' => '任选五', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'6' => array('num'=>6, 'cname' => '任选六', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'7' => array('num'=>7, 'cname' => '任选七', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'8' => array('num'=>8, 'cname' => '任选八', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'9' => array('num'=>2, 'cname' => '前二直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'10' => array('num'=>3, 'cname' => '前三直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'11' => array('num'=>2, 'cname' => '前二组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'12' => array('num'=>3, 'cname' => '前三组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'13' => array('num'=>3, 'cname' => '乐选三', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'14' => array('num'=>4, 'cname' => '乐选四', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'15' => array('num'=>5, 'cname' => '乐选五', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
		),
		21407 => array(
			'1' => array('num'=>1, 'cname' => '前一', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>2, 'cname' => '任选二', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'3' => array('num'=>3, 'cname' => '任选三', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'4' => array('num'=>4, 'cname' => '任选四', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'5' => array('num'=>5, 'cname' => '任选五', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'6' => array('num'=>6, 'cname' => '任选六', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'7' => array('num'=>7, 'cname' => '任选七', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'8' => array('num'=>8, 'cname' => '任选八', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'9' => array('num'=>2, 'cname' => '前二直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'10' => array('num'=>3, 'cname' => '前三直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'11' => array('num'=>2, 'cname' => '前二组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'12' => array('num'=>3, 'cname' => '前三组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
		),
		21408 => array(
			'1' => array('num'=>1, 'cname' => '前一', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>2, 'cname' => '任选二', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'3' => array('num'=>3, 'cname' => '任选三', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'4' => array('num'=>4, 'cname' => '任选四', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'5' => array('num'=>5, 'cname' => '任选五', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'6' => array('num'=>6, 'cname' => '任选六', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'7' => array('num'=>7, 'cname' => '任选七', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'8' => array('num'=>8, 'cname' => '任选八', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'9' => array('num'=>2, 'cname' => '前二直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'10' => array('num'=>3, 'cname' => '前三直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
			'11' => array('num'=>2, 'cname' => '前二组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
			'12' => array('num'=>3, 'cname' => '前三组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
		),
	    21421 => array(
	        '1' => array('num'=>1, 'cname' => '前一', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '2' => array('num'=>2, 'cname' => '任选二', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '3' => array('num'=>3, 'cname' => '任选三', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '4' => array('num'=>4, 'cname' => '任选四', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '5' => array('num'=>5, 'cname' => '任选五', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '6' => array('num'=>6, 'cname' => '任选六', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '7' => array('num'=>7, 'cname' => '任选七', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '8' => array('num'=>8, 'cname' => '任选八', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '9' => array('num'=>2, 'cname' => '前二直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
	        '10' => array('num'=>3, 'cname' => '前三直选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => '|', 'maxlength' => 2),
	        '11' => array('num'=>2, 'cname' => '前二组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	        '12' => array('num'=>3, 'cname' => '前三组选', 'txt'=> array(0 => '01-11:01-11'), 'separator' => ',', 'maxlength' => 2),
	    ),
		53 => array(
			'1' => array('num'=>1, 'cname' => '和值', 'txt'=> array(0 => '3-18:3-18'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>3, 'cname' => '三同号通选', 'txt'=> array(0 => '三同号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
			'3' => array('num'=>3, 'cname' => '三同号单选', 'txt'=> array(0 => '111-666:1,1,1-6,6,6'), 'separator' => ',', 'maxlength' => 1),
			'4' => array('num'=>3, 'cname' => '三不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
			'5' => array('num'=>3, 'cname' => '三连号通选', 'txt'=> array(0 => '三连号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
			'6' => array('num'=>3, 'cname' => '二同号复选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
			'7' => array('num'=>3, 'cname' => '二同号单选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
			'8' => array('num'=>3, 'cname' => '二不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
		),
		54 => array(
			'1' => array('num'=>1, 'cname' => '任选一', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>2, 'cname' => '任选二', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'3' => array('num'=>3, 'cname' => '任选三', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'4' => array('num'=>4, 'cname' => '任选四', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'5' => array('num'=>5, 'cname' => '任选五', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'6' => array('num'=>6, 'cname' => '任选六', 'txt'=> array(0 => 'A-K：01-13'), 'separator' => ',', 'maxlength' => 2),
			'7' => array('num'=>1, 'cname' => '同花', 'txt'=> array(0 => '黑桃：01  红桃：02  梅花：03  方块04： 包选：00'), 'separator' => ',', 'maxlength' => 2),
			'8' => array('num'=>1, 'cname' => '同花顺', 'txt'=> array(0 => '黑桃：01  红桃：02  梅花：03  方块04： 包选：00'), 'separator' => ',', 'maxlength' => 2),
			'9' => array('num'=>1, 'cname' => '顺子', 'txt'=> array(0 => 'A23-QKA：01-12  包选：00'), 'separator' => ',', 'maxlength' => 2),
			'10' => array('num'=>1, 'cname' => '豹子', 'txt'=> array(0 => 'AAA-KKK：01-13  包选：00'), 'separator' => ',', 'maxlength' => 2),
			'11' => array('num'=>1, 'cname' => '对子', 'txt'=> array(0 => 'AA-KK：01-13  包选：00'), 'separator' => ',', 'maxlength' => 2),
		),
		55 => array(
			'10' => array('num'=>1, 'cname' => '一星直选', 'txt'=> array(0 => '0-9:0-9'), 'maxlength' => 1),
			'20' => array('num'=>2, 'cname' => '二星直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'23' => array('num'=>2, 'cname' => '二星组选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'30' => array('num'=>3, 'cname' => '三星直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'33' => array('num'=>3, 'cname' => '三星组三', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'34' => array('num'=>3, 'cname' => '三星组六', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'40' => array('num'=>5, 'cname' => '五星直选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'43' => array('num'=>5, 'cname' => '五星通选', 'txt'=> array(0 => '0-9:0-9'), 'separator' => ',', 'maxlength' => 1),
			'1' => array('num'=>2, 'cname' => '大小单双', 'txt'=> array(0 => '大小单双:大小单双'), 'separator' => ','),
		),
		56 => array(
			'1' => array('num'=>1, 'cname' => '和值', 'txt'=> array(0 => '3-18:3-18'), 'separator' => ',', 'maxlength' => 2),
			'2' => array('num'=>3, 'cname' => '三同号通选', 'txt'=> array(0 => '三同号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
			'3' => array('num'=>3, 'cname' => '三同号单选', 'txt'=> array(0 => '111-666:1,1,1-6,6,6'), 'separator' => ',', 'maxlength' => 1),
			'4' => array('num'=>3, 'cname' => '三不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
			'5' => array('num'=>3, 'cname' => '三连号通选', 'txt'=> array(0 => '三连号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
			'6' => array('num'=>3, 'cname' => '二同号复选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
			'7' => array('num'=>3, 'cname' => '二同号单选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
			'8' => array('num'=>3, 'cname' => '二不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
		),
	    57 => array(
	        '1' => array('num'=>1, 'cname' => '和值', 'txt'=> array(0 => '3-18:3-18'), 'separator' => ',', 'maxlength' => 2),
	        '2' => array('num'=>3, 'cname' => '三同号通选', 'txt'=> array(0 => '三同号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
	        '3' => array('num'=>3, 'cname' => '三同号单选', 'txt'=> array(0 => '111-666:1,1,1-6,6,6'), 'separator' => ',', 'maxlength' => 1),
	        '4' => array('num'=>3, 'cname' => '三不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
	        '5' => array('num'=>3, 'cname' => '三连号通选', 'txt'=> array(0 => '三连号通选：0,0,0'), 'separator' => ',', 'maxlength' => 1, 'value' => array('0', '0', '0')),
	        '6' => array('num'=>3, 'cname' => '二同号复选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
	        '7' => array('num'=>3, 'cname' => '二同号单选', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1),
	        '8' => array('num'=>3, 'cname' => '二不同号', 'txt'=> array(0 => '1-6:1-6'), 'separator' => ',', 'maxlength' => 1, 'value' => array('', '', '*')),
	    ),
	);
	
	private $_cName = array(52 => '福彩3D', 33 => '排列三', 35 => '排列五', 21406 => '十一选五', 21407 => '江西十一选五', 21408 => '湖北十一选五', 53 => '上海快三', 54 => '快乐扑克', 55 => '重庆时时彩', 56 => '吉林快三', 57 => '江西快三', 21421 => '广东十一选五');
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_limit_codes', 'Model');
	}
	
	public function index()
	{
		$this->check_capacity('3_13_1');
		$this->load->view('limitcode/index');
	}
	
	public function jc($lid = 42) {
	    $this->check_capacity('3_18_1');
	    $page = $this->input->get('p', true);
	    $page = !empty($page) ? $page : 1;
	    $data = $this->Model->getData($lid, ($page-1) * self::NUM_PER_PAGE, self::NUM_PER_PAGE);
	    $pageConfig = array(
	        "page" => $page,
	        "npp" => self::NUM_PER_PAGE,
	        "allCount" => $data['total']
	    );
	    $data['pages'] = get_pagination($pageConfig);
	    $this->load->view('limitcode/jc', $data);
	}
	
	public function createLimit()
	{
		$this->check_capacity('3_13_2', true);
		$post = $this->input->post();
		if ($post['playType'] && $post['codes1'] && $post['codes2'])
		{
			if ($post['codes1'] === $post['codes2'])
			{
				if ($this->checkCode($post['lid'], $post['playType'], $post['codes1']))
				{
					$data = array(
							'lid' 		=> $post['lid'],
							'playType'	=> $post['playType'],
							'codes'		=> $post['codes1']
					);
					if ($this->Model->addCode($data))
					{
						$this->Model->refreshCache($data['lid']);
						$this->syslog(50, "新增限号：".$this->_cName[$data['lid']].$this->_lidMap[$data['lid']][$data['playType']]['cname'].$data['codes']);
						$this->ajaxReturn('200', '添加成功');
					}
					else 
					{
						$this->ajaxReturn('600', '该方案已存在限号');
					}
				}
				else
				{
					$this->ajaxReturn('500', '限号校验失败');
				} 
				
			}
			$this->ajaxReturn('400', '两次输入不一致');
		}
		$this->ajaxReturn('300', '参数不对');
	}
	
	public function createJcLimit() {
	    $this->check_capacity('3_18_2', true);
	    $commentArr = array(
	        'SPF' => array('胜' => 3, '平' => 1, '负' => 0),
	        'RQSPF' => array('胜' => 3, '平' => 1, '负' => 0),
	        'CBF' => array(
	            '1:0' => '1:0', '2:0' => '2:0', '2:1' => '2:1', '3:0' => '3:0', '3:1' => '3:1', '3:2' => '3:2', '4:0' => '4:0', '4:1' => '4:1', '4:2' => '4:2', '5:0' => '5:0', '5:1' => '5:1', '5:2' => '5:2', '胜其他' => '9:0',
	            '0:1' => '0:1', '0:2' => '0:2', '1:2' => '1:2', '0:3' => '0:3', '1:3' => '1:3', '2:3' => '2:3', '0:4' => '0:4', '1:4' => '1:4', '2:4' => '2:4', '0:5' => '0:5', '1:5' => '1:5', '2:5' => '2:5', '负其他' => '0:9',
	            '0:0' => '0:0', '1:1' => '1:1', '2:2' => '2:2', '3:3' => '3:3', '平其他' => '9:9'),
	        'BQC' => array('胜胜' => '3-3', '胜平' => '3-1', '胜负' => '3-0', '平胜' => '1-3', '平平' => '1-1', '平负' => '1-0', '负胜' => '0-3', '负平' => '0-1', '负负' => '0-0'),
	        'JQS' => array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'),
	    );
	    $info = $this->input->post();
	    $matchs = array();
	    if (!empty($info) && !empty($info['matches']) && !empty($info['ggType'])) {
	        foreach ($info['matches'] as $match) {
	            if (array_key_exists($match['playtype'], $commentArr) && array_key_exists($match['comment'], $commentArr[$match['playtype']])) {
	                $matchs[$match['mid']][$match['playtype']][] = str_replace(array_keys($commentArr[$match['playtype']]), array_values($commentArr[$match['playtype']]), $match['comment']);
	            }else {
	                $this->ajaxReturn('n', '方案内容有误！');
	            }
	        }
	    }
	    $code = '';
	    $msg = '';
	    foreach ($matchs as $mid => $mch) {
	        foreach ($mch as $ptype => $comments) {
	            $code .= ",".$ptype.">".$mid."=".implode('/', $comments);
	            $msg .= ";".$mid." ".$ptype." ".implode('/', $comments);
	        }
	    }
	    $data = array(
	        'lid' => 42,
	        'playType' => 0,
	        'codes' => substr($code, 1)."|".$info['ggType'],
	        'msg'  => $info['msg']
	    );
	    if ($this->Model->addJcCode($data)) {
	        $this->Model->refreshJcCache(42);
	        $this->syslog(50, "对".substr($msg, 1)."进行了新增限号操作");
	        $this->ajaxReturn('y', '添加成功');
	    }
	}
	
	public function overLimit()
	{
	    
		$this->check_capacity('3_13_3', true);
		$id = $this->input->post('id');
		$data = $this->Model->overLimit($id);
		$this->syslog(50, "结束限号：".$this->_cName[$data['lid']].$this->_lidMap[$data['lid']][$data['playType']]['cname'].$data['codes']);
	}
	
	public function overJcLimit()
	{
	    $this->check_capacity('3_18_2', true);
	    $id = $this->input->post('id');
	    $data = $this->Model->overLimit($id);
	    $this->syslog(50, "结束限号：".$this->_cName[$data['lid']].$this->_lidMap[$data['lid']][$data['playType']]['cname'].$data['codes']);
	    $this->ajaxReturn('y', '操作成功');
	}
	
	public function getLimit()
	{
		$this->check_capacity('3_13_1', true);
		$lid = $this->input->get('lid');
		$page = $this->input->get('pageNum');
		$page = $page ? $page : 1;
		$data = $this->Model->getData($lid, ($page-1) * 20, 20);
		foreach ($data['data'] as &$val)
		{
			if ($lid == 55 && $val['playType'] == 1) $val['codes'] = str_replace(array(1, 2, 4, 5), array('大', '小', '单', '双'), $val['codes']);
			$val['playType'] = $this->_lidMap[$lid][$val['playType']]['cname'];
		}
		exit(json_encode($data));
	}
	
	public function getLimitLottory()
	{
		$this->check_capacity('3_13_1', true);
		exit(json_encode($this->_lidMap));
	}
	
	private function checkCode($lid, $playType, &$codes)
	{
		switch ($lid) {
			case 33:
			case 52:
				if (preg_match('/^(\d)\,(\d)\,(\d)$/', $codes, $matches)) 
				{
					switch ($playType) 
					{
						case 2:
							if (($matches[1] == $matches[2] && $matches[2] < $matches[3]) || ($matches[1] < $matches[2] && $matches[2] == $matches[3])) return true;
							break;
						case 3:
							if ($matches[1] < $matches[2] && $matches[2] < $matches[3]) return true;
							break;
						default:
							return true;
							break;
					}
				}
				break;
			case 35:
				if (preg_match('/^(\d)\,(\d)\,(\d)\,(\d)\,(\d)$/', $codes, $matches)) return true;
				break;
			case 21406:
			case 21407:
			case 21408:
			case 21421:
				switch ($playType)
				{
					case 1:
						if (preg_match('/^(0[1-9]|1[0-1])$/', $codes, $matches)) return true;
						break;
					case 2:
					case 11:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches) && $matches[1] < $matches[2]) return true;
						break;
					case 3:
					case 12:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches) 
						&& $matches[1] < $matches[2] && $matches[2] < $matches[3]) return true;
						break;
					case 4:
					case 14:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches)
						&& $matches[1] < $matches[2] && $matches[2] < $matches[3] && $matches[3] < $matches[4]) return true;
						break;
					case 5:
					case 15:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches)
						&& $matches[1] < $matches[2] && $matches[2] < $matches[3] && $matches[3] < $matches[4] && $matches[4] < $matches[5]) return true;
						break;
					case 6:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches)
						&& $matches[1] < $matches[2] && $matches[2] < $matches[3] && $matches[3] < $matches[4] && $matches[4] < $matches[5] && $matches[5] < $matches[6]) return true;
						break;
					case 7:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches)
		&& $matches[1] < $matches[2] && $matches[2] < $matches[3] && $matches[3] < $matches[4] && $matches[4] < $matches[5] && $matches[5] < $matches[6] && $matches[6] < $matches[7]) return true;
						break;
					case 8:
						if (preg_match('/^(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])\,(0[1-9]|1[0-1])$/', $codes, $matches)
		&& $matches[1] < $matches[2] && $matches[2] < $matches[3] && $matches[3] < $matches[4] && $matches[4] < $matches[5] && $matches[5] < $matches[6] && $matches[6] < $matches[7] && $matches[7] < $matches[8]) return true;
						break;
					case 9:
						if (preg_match('/^(0[1-9]|1[0-1])\|(0[1-9]|1[0-1])$/', $codes)) return true;
						break;
					case 10:
					case 13:
						if (preg_match('/^(0[1-9]|1[0-1])\|(0[1-9]|1[0-1])\|(0[1-9]|1[0-1])$/', $codes)) return true;
						break;
				}
				break;
			case 53:
			case 56:
			case 57:
				switch ($playType) 
				{
					case 1:
						if (preg_match('/^([3-9]|1[0-8])$/', $codes)) return true;
						break;
					case 2:
					case 5:
						if ($codes === '0,0,0') return true;
						break;
					case 3:
						if (preg_match('/^([1-6])\,([1-6])\,([1-6])$/', $codes, $matches) && $matches[1] == $matches[2] && $matches[2] == $matches[3]) return true;
						break;
					case 4:
						if (preg_match('/^([1-6])\,([1-6])\,([1-6])$/', $codes, $matches) && $matches[1] < $matches[2] && $matches[2] < $matches[3]) return true;
						break;
					case 6:
						if (preg_match('/^([1-6])\,([1-6])\,\*$/', $codes, $matches) && $matches[1] == $matches[2]) return true;
						break;
					case 7:
						if (preg_match('/^([1-6])\,([1-6])\,([1-6])$/', $codes, $matches) && $matches[1] == $matches[2] && $matches[1] != $matches[3]) return true;
						break;
					case 8:
						if (preg_match('/^([1-6])\,([1-6])\,\*$/', $codes, $matches) && $matches[1] < $matches[2]) return true;
						break;
				}
				break;
			case 54:
				switch ($playType)
				{
					case 1:
						if (preg_match('/^(0[1-9]|1[0-3])$/', $codes)) return true;
						break;
					case 2:
						if (preg_match('/^(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2] ) return true;
						break;
					case 3:
						if (preg_match('/^(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])$/', $codes, $matches)
						&& (int)$matches[1] < (int)$matches[2] && (int)$matches[2] < (int)$matches[3]) return true;
						break;
					case 4:
						if (preg_match('/^(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2]
						&& (int)$matches[2] < (int)$matches[3] && (int)$matches[3] < (int)$matches[4]) return true;
						break;
					case 5:
						if (preg_match('/^(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2]
						&& (int)$matches[2] < (int)$matches[3] && (int)$matches[3] < (int)$matches[4] && (int)$matches[4] < (int)$matches[5]) return true;
						break;
					case 6:
						if (preg_match('/^(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])\,(0[1-9]|1[0-3])$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2]
						&& (int)$matches[2] < (int)$matches[3] && (int)$matches[3] < (int)$matches[4] && (int)$matches[4] < (int)$matches[5] && (int)$matches[5] < (int)$matches[6]) return true;
						break;
					case 10:
					case 11:
						if (preg_match('/^(0\d|1[0-3])$/', $codes, $matches)) return true;
						break;
					case 9:
						if (preg_match('/^(0\d|1[0-2])$/', $codes, $matches)) return true;
						break;
					case 7:
					case 8:
						if (preg_match('/^(0[0-4])$/', $codes, $matches)) return true;
						break;
				}
				break;
			case 55:
				switch ($playType) {
					case 1:
						if (preg_match('/^(大|小|单|双)\,(大|小|单|双)$/', $codes)) {
							$codes = str_replace(array('大', '小', '单', '双'), array(1, 2, 4, 5), $codes);
							return true;
						}
						break;
					case 10:
						if (preg_match('/^\d$/', $codes)) return true;
						break;
					case 20:
						if (preg_match('/^\d\,\d$/', $codes)) return true;
						break;
					case 23:
						if (preg_match('/^(\d)\,(\d)$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2]) return true;
						break;
					case 30:
						if (preg_match('/^\d\,\d\,\d$/', $codes)) return true;
						break;
					case 33:
						if (preg_match('/^(\d)\,(\d)\,(\d)$/', $codes, $matches)) {
							$n1 = (int)$matches[1];
							$n2 = (int)$matches[2];
							$n3 = (int)$matches[3];
							if (count(array_unique(array($n1, $n2, $n3))) == 2 && $n1 <= $n2 && $n2 <= $n3) return true;
						}
						break;
					case 34:
						if (preg_match('/^(\d)\,(\d)\,(\d)$/', $codes, $matches) && (int)$matches[1] < (int)$matches[2] && (int)$matches[2] < (int)$matches[3]) return true;
						break;
					case 40:
					case 43:
						if (preg_match('/^\d\,\d\,\d\,\d\,\d$/', $codes)) return true;
						break;
				}
				break;
		}
		return false;
	}

}