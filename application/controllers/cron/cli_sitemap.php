<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 类 Sitemap
 * php 生成 sitemap
 * @Author liusijia
 */
class Cli_Sitemap extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->load('seo');
        $url_prefix = $this->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';
    }

    /**
     * 生成 sitemap.xml
     * $data 格式：
     * array(
     *      array(
     *          'loc' => 'http://www.zgguan.com/',  //当前url
     *          'priority' => '1.0', //权重
     *          'lastmod'   =>'2014-12-18', //目前，建议使用当前时间
     *          'changefreq' => 'daily'   //此参数的值，有两种：daily(每天）和　always(总是),现在我们每天都生成，因此，建议使用 daily
     *      )
     * )
     * @Author liusijia
     */
    public function createSitemap() {
        $data = $this->config->item('sitemap_0');
        $content = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($data as $v) {
            $content .= $this->createItem(array(
                        'loc' => $v['loc'],
                        'lastmod' => $v['lastmod'],
                        'changefreq' => $v['changefreq'],
                        'priority' => $v['priority']
                            )
            );
        }
        $content .= $this->getInfo();
        $content .= $this->getKaijiang();
        $content .= $this->getHelp();
        $data = $this->config->item('sitemap_1');
        foreach ($data as $v) {
        	$content .= $this->createItem(array(
        			'loc' => $v['loc'],
        			'lastmod' => $v['lastmod'],
        			'changefreq' => $v['changefreq'],
        			'priority' => $v['priority']
        		)
        	);
        }
        $content .= "\n</urlset>";
        $fp = fopen('sitemap.xml', 'w+');
        fwrite($fp, $content);
        fclose($fp);
        if (ENVIRONMENT === 'production')
        {
        	system("/bin/bash /opt/shell/rsync_sitemap.sh", $status);
        }
    }
    
    public function getInfo() {
    	$infoArr = array(1 => 'csxw', 2 => 'ssq', 3 => 'qtfc', 4 => 'dlt', 5 => 'qttc', 6 => 'jczq', 7 => 'sfc', 8 => 'jclq');
    	$this->load->model('info_model');
    	$content = '';
    	for ($i = 1; $i <= 8; $i++) {
    		$res[$i] = $this->info_model->getPagesByCategory($i);
    		$pageCont = ceil(count($res[$i])/20);
    		for ($j = 1; $j <= $pageCont; $j++) {
    			$content .= $this->createItem(array(
    					'loc' => $this->url_prefix.'://' . $this->config->item('domain') . '/info/lists/'.$i.'?cpage='.$j,
    					'lastmod' => date('Y-m-d'),
    					'changefreq' =>'daily',
    					'priority' => '0.8'
    				)
    			);
    			
    		}
    	}
    	foreach ($res as $k => $v) {
    		foreach ($v as $id) {
    			$content .= $this->createItem(array(
    					'loc' => $this->url_prefix.'://' . $this->config->item('domain') . '/info/'.$infoArr[$k].'/'.$id,
    					'lastmod' => date('Y-m-d'),
    					'changefreq' =>'daily',
    					'priority' => '0.7'
    				)
    			);
    		}
    	}
    	return $content;
    }
    
    public function getKaijiang()
    {
    	$content = '';
    	$lidArr = array(SSQ => '2015001', DLT => '15001', SYXW => '2015-06-30', JXSYXW => '2015-06-30', FCSD => '2015001', PLW => '15001', 
    	PLS => '15001', QLC => '2008001', QXC => '08001', KS => '2015-06-30', HBSYXW => '2015-06-30', SFC => '15051', RJ => '15051', SFC => '15051');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->load->model('lottery_model', 'model');
    	foreach ($lidArr as $lid => $sIssue) {
    		$enName = $this->model->getEnName($lid);
    		if (in_array($lid, array(SYXW, JXSYXW, HBSYXW, KS, GDSYXW))) {
    			$dateList = $this->model->getAllAwarddate($lid);
    			$start = array_search(array('date' => '2015-06-30'), $dateList);
    			if ($start) {
    				$dateList = array_slice($dateList, 0, $start + 1);
    			}
    			foreach ($dateList as $date) {
    				$content .= $this->createItem(array(
    						'loc' => $this->url_prefix.'://' . $this->config->item('domain') . '/kaijiang/'.$enName.'/'.$date['date'],
    						'lastmod' => date('Y-m-d'),
    						'changefreq' =>'daily',
    						'priority' => '0.7'
    				)
    				);
    			}
    		}else {
    			if ($enName == 'pls') {
    				$enName = 'pl3';
    			} elseif ($enName == 'plw') {
    				$enName = 'pl5';
    			} elseif ($enName == 'fcsd') {
    				$enName = 'fc3d';
    			}
    			$issues = $this->model->getAllIssue($lid, $sIssue);
    			foreach ($issues as $issue) {
    				$content .= $this->createItem(array(
    						'loc' => $this->url_prefix.'://' . $this->config->item('domain') . '/kaijiang/'.$enName.'/'.$issue['issue'],
    						'lastmod' => date('Y-m-d'),
    						'changefreq' =>'daily',
    						'priority' => '0.7'
    				)
    				);
    			}
    		}
    	}
    	return $content;
    }
    
    public function getHelp()
    {
    	$content = '';
    	$this->config->load('help');
    	$help = $this->config->item('help_center');
    	foreach ($help as $b => $value) {
    		foreach ($value as $s => $val) {
    			$content .= $this->createItem(array(
    					'loc' => $this->url_prefix."://" . $this->config->item('domain') . "/help/index/b".$b."-s".$s,
    					'lastmod' => date('Y-m-d'),
    					'changefreq' =>'daily',
    					'priority' => '0.7'
    				)
    			);
    		}
    	}
    	return $content;
    }

    /**
     * 拼接 sitemap 数据
     * @param type $data
     * @return string 
     */
    public function createItem($data) {
        $item = "\n<url>\n";
        $item.="<loc>" . $data['loc'] . "</loc>\n";
        $item.="<lastmod>" . $data['lastmod'] . "</lastmod>\n";
        $item.="<changefreq>" . $data['changefreq'] . "</changefreq>\n";
        $item.="<priority>" . $data['priority'] . "</priority>\n";
        $item.="</url>";
        return $item;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */