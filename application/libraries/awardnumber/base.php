<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 开奖号码公用父类
 * @author Administrator
 *
 */
class Base
{
    protected $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->model('awardnumber_model');
	}
	
	/**
	 * 返回抓取页面数据
	 * @param string $url	url
	 * @param array $params	规定请求参数设置
	 */
	public function get_content($url, $params = array())
	{
	    $content = $this->CI->tools->request($url, $params);
	    if($this->CI->tools->recode == '200')
	    {
	        $encode = mb_detect_encoding($content, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
	        if($encode != 'UTF-8')
	        {
	            $content = iconv($encode, 'UTF-8', $content);
	        }
	    }
	    else
	    {
	        $content = '';
	    }
	    return $content;
	}
	
	/**
	 * 保存开奖信息
	 * @param array $awardArr      开奖号码
	 * @param string $bonusDetail  开奖详情
	 * @param string $lid          彩种lid
	 * @return boolean
	 */
	public function saveAwardData($awardArr, $bonusDetail, $lid)
	{
	    $return = true;
	    if($awardArr)
	    {
	        $synflag = false;
	        foreach ($awardArr as $issue => $awardNum)
	        {
	            $data = array('awardNum' => $awardNum, 'bonusDetail' => $bonusDetail, 'state' => 1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
	            $result = $this->CI->awardnumber_model->updateByIssue($this->paiqiTable, $issue, $data);
	            if($result)
	            {
	                $synflag = true;
	            }
	            else
	            {
	                $return = false;
	            }
	        }
	        
	        if($synflag)
	        {
	            //启动同步号码任务
	            $this->CI->awardnumber_model->updateTicketStop(1, $lid, 0);
	        }
	    }
	    
	    return $return;
	}
	
	/**
	 * 获取老11选5开奖详情信息
	 * @return string
	 */
	public function getSyxwBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['qy']['dzjj'] = '13';
	    $bonusDetail['r2']['dzjj'] = '6';
	    $bonusDetail['r3']['dzjj'] = '19';
	    $bonusDetail['r4']['dzjj'] = '78';
	    $bonusDetail['r5']['dzjj'] = '540';
	    $bonusDetail['r6']['dzjj'] = '90';
	    $bonusDetail['r7']['dzjj'] = '26';
	    $bonusDetail['r8']['dzjj'] = '9';
	    $bonusDetail['q2zhix']['dzjj'] = '130';
	    $bonusDetail['q2zux']['dzjj'] = '65';
	    $bonusDetail['q3zhix']['dzjj'] = '1170';
	    $bonusDetail['q3zux']['dzjj'] = '195';
	    $bonusDetail['lx3']['q3']['dzjj'] = '1384';
	    $bonusDetail['lx3']['z3']['dzjj'] = '214';
	    $bonusDetail['lx3']['r3']['dzjj'] = '19';
	    $bonusDetail['lx4']['r44']['dzjj'] = '154';
	    $bonusDetail['lx4']['r43']['dzjj'] = '19';
	    $bonusDetail['lx5']['r55']['dzjj'] = '1080';
	    $bonusDetail['lx5']['r54']['dzjj'] = '90';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取新11选5开奖详情信息
	 * @return string
	 */
	public function getJxsyxwBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['qy']['dzjj'] = '13';
	    $bonusDetail['r2']['dzjj'] = '6';
	    $bonusDetail['r3']['dzjj'] = '19';
	    $bonusDetail['r4']['dzjj'] = '78';
	    $bonusDetail['r5']['dzjj'] = '540';
	    $bonusDetail['r6']['dzjj'] = '90';
	    $bonusDetail['r7']['dzjj'] = '26';
	    $bonusDetail['r8']['dzjj'] = '9';
	    $bonusDetail['q2zhix']['dzjj'] = '130';
	    $bonusDetail['q2zux']['dzjj'] = '65';
	    $bonusDetail['q3zhix']['dzjj'] = '1170';
	    $bonusDetail['q3zux']['dzjj'] = '195';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取惊喜11选5开奖详情信息
	 * @return string
	 */
	public function getHbsyxwBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['qy']['dzjj'] = '13';
	    $bonusDetail['r2']['dzjj'] = '6';
	    $bonusDetail['r3']['dzjj'] = '19';
	    $bonusDetail['r4']['dzjj'] = '78';
	    $bonusDetail['r5']['dzjj'] = '540';
	    $bonusDetail['r6']['dzjj'] = '90';
	    $bonusDetail['r7']['dzjj'] = '26';
	    $bonusDetail['r8']['dzjj'] = '9';
	    $bonusDetail['q2zhix']['dzjj'] = '130';
	    $bonusDetail['q2zux']['dzjj'] = '65';
	    $bonusDetail['q3zhix']['dzjj'] = '1170';
	    $bonusDetail['q3zux']['dzjj'] = '195';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取惊喜11选5开奖详情信息
	 * @return string
	 */
	public function getGdsyxwBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['qy']['dzjj'] = '13';
	    $bonusDetail['r2']['dzjj'] = '6';
	    $bonusDetail['r3']['dzjj'] = '19';
	    $bonusDetail['r4']['dzjj'] = '78';
	    $bonusDetail['r5']['dzjj'] = '540';
	    $bonusDetail['r6']['dzjj'] = '90';
	    $bonusDetail['r7']['dzjj'] = '26';
	    $bonusDetail['r8']['dzjj'] = '9';
	    $bonusDetail['q2zhix']['dzjj'] = '130';
	    $bonusDetail['q2zux']['dzjj'] = '65';
	    $bonusDetail['q3zhix']['dzjj'] = '1170';
	    $bonusDetail['q3zux']['dzjj'] = '195';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取上海快三开奖详情信息
	 * @return string
	 */
	public function getKsBonus()
	{
	    //奖级信息
	    $bonusDetail = array();
	    $bonusDetail['hz']['z4'] = '80';
	    $bonusDetail['hz']['z5'] = '40';
	    $bonusDetail['hz']['z6'] = '25';
	    $bonusDetail['hz']['z7'] = '16';
	    $bonusDetail['hz']['z8'] = '12';
	    $bonusDetail['hz']['z9'] = '10';
	    $bonusDetail['hz']['z10'] = '9';
	    $bonusDetail['hz']['z11'] = '9';
	    $bonusDetail['hz']['z12'] = '10';
	    $bonusDetail['hz']['z13'] = '12';
	    $bonusDetail['hz']['z14'] = '16';
	    $bonusDetail['hz']['z15'] = '25';
	    $bonusDetail['hz']['z16'] = '40';
	    $bonusDetail['hz']['z17'] = '80';
	    $bonusDetail['sthtx'] = '40';
	    $bonusDetail['sthdx'] = '240';
	    $bonusDetail['sbth'] = '40';
	    $bonusDetail['slhtx'] = '10';
	    $bonusDetail['ethfx'] = '15';
	    $bonusDetail['ethdx'] = '80';
	    $bonusDetail['ebth'] = '8';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取快乐扑克开奖详情信息
	 * @return string
	 */
	public function getKlpkBonus()
	{
	    //奖级信息
	    $bonusDetail = array();
	    $bonusDetail['thbx']['dzjj'] = '22';
	    $bonusDetail['thdx']['dzjj'] = '90';
	    $bonusDetail['thsbx']['dzjj'] = '535';
	    $bonusDetail['thsdx']['dzjj'] = '2150';
	    $bonusDetail['szbx']['dzjj'] = '33';
	    $bonusDetail['szdx']['dzjj'] = '400';
	    $bonusDetail['bzbx']['dzjj'] = '500';
	    $bonusDetail['bzdx']['dzjj'] = '6400';
	    $bonusDetail['dzbx']['dzjj'] = '7';
	    $bonusDetail['dzdx']['dzjj'] = '88';
	    $bonusDetail['r1']['dzjj'] = '5';
	    $bonusDetail['r2']['dzjj'] = '33';
	    $bonusDetail['r3']['dzjj'] = '116';
	    $bonusDetail['r4']['dzjj'] = '46';
	    $bonusDetail['r5']['dzjj'] = '22';
	    $bonusDetail['r6']['dzjj'] = '12';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取重庆时时彩开奖详情信息
	 * @return string
	 */
	public function getCqsscBonus()
	{
	    //奖级信息
	    $bonusDetail = array();
	    $bonusDetail['1xzhix'] = '10';
	    $bonusDetail['2xzhix'] = '100';
	    $bonusDetail['2xzux'] = '50';
	    $bonusDetail['3xzhix'] = '1000';
	    $bonusDetail['3xzu3'] = '320';
	    $bonusDetail['3xzu6'] = '160';
	    $bonusDetail['5xzhix'] = '100000';
	    $bonusDetail['5xtx']['qw'] = '20440';
	    $bonusDetail['5xtx']['3w'] = '220';
	    $bonusDetail['5xtx']['2w'] = '20';
	    $bonusDetail['dxds'] = '4';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取吉林快三开奖详情信息
	 * @return string
	 */
	public function getJlksBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['hz']['z3'] = '240';
	    $bonusDetail['hz']['z4'] = '80';
	    $bonusDetail['hz']['z5'] = '40';
	    $bonusDetail['hz']['z6'] = '25';
	    $bonusDetail['hz']['z7'] = '16';
	    $bonusDetail['hz']['z8'] = '12';
	    $bonusDetail['hz']['z9'] = '10';
	    $bonusDetail['hz']['z10'] = '9';
	    $bonusDetail['hz']['z11'] = '9';
	    $bonusDetail['hz']['z12'] = '10';
	    $bonusDetail['hz']['z13'] = '12';
	    $bonusDetail['hz']['z14'] = '16';
	    $bonusDetail['hz']['z15'] = '25';
	    $bonusDetail['hz']['z16'] = '40';
	    $bonusDetail['hz']['z17'] = '80';
	    $bonusDetail['hz']['z18'] = '240';
	    $bonusDetail['sthtx'] = '40';
	    $bonusDetail['sthdx'] = '240';
	    $bonusDetail['sbth'] = '40';
	    $bonusDetail['slhtx'] = '10';
	    $bonusDetail['ethfx'] = '15';
	    $bonusDetail['ethdx'] = '80';
	    $bonusDetail['ebth'] = '8';
	    $bonusDetail = json_encode($bonusDetail);
	    
	    return $bonusDetail;
	}
	
	/**
	 * 获取吉林快三开奖详情信息
	 * @return string
	 */
	public function getJxksBonus()
	{
	    $bonusDetail = array();
	    $bonusDetail['hz']['z3'] = '240';
	    $bonusDetail['hz']['z4'] = '80';
	    $bonusDetail['hz']['z5'] = '40';
	    $bonusDetail['hz']['z6'] = '25';
	    $bonusDetail['hz']['z7'] = '16';
	    $bonusDetail['hz']['z8'] = '12';
	    $bonusDetail['hz']['z9'] = '10';
	    $bonusDetail['hz']['z10'] = '9';
	    $bonusDetail['hz']['z11'] = '9';
	    $bonusDetail['hz']['z12'] = '10';
	    $bonusDetail['hz']['z13'] = '12';
	    $bonusDetail['hz']['z14'] = '16';
	    $bonusDetail['hz']['z15'] = '25';
	    $bonusDetail['hz']['z16'] = '40';
	    $bonusDetail['hz']['z17'] = '80';
	    $bonusDetail['hz']['z18'] = '240';
	    $bonusDetail['sthtx'] = '40';
	    $bonusDetail['sthdx'] = '240';
	    $bonusDetail['sbth'] = '40';
	    $bonusDetail['slhtx'] = '10';
	    $bonusDetail['ethfx'] = '15';
	    $bonusDetail['ethdx'] = '80';
	    $bonusDetail['ebth'] = '8';
	    $bonusDetail = json_encode($bonusDetail);
	     
	    return $bonusDetail;
	}
}
