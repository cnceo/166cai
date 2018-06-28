<?php
class Links extends MY_Controller
{
	public function index($action = 'kjdh') {
	    if($action == 'kjdh')
	    {
	        $this->check_capacity("6_7_1");
	    }
	    else
	    {
	        $this->check_capacity("6_7_2");
	    }
		$post = $this->input->post($action);
		$this->load->model('model_shouye_link', 'model');
		$data = $this->model->getDataByPosition($action);
		$notfull = $this->input->get('notfull') ? 1 : 0;
		if ($post) {
		    if($action == 'kjdh')
		    {
		        $this->check_capacity("6_7_4");
		    }
		    else
		    {
		        $this->check_capacity("6_7_5");
		    }
			foreach ($post as $pst) {
				if ($pst['priority'] && $pst['url'] && $pst['title']) {
					$pst['position'] = $action;
					$dataList[] = $pst;
				}elseif ($pst['priority'] || $pst['url'] || $pst['title']) {
					$this->redirect("/backend/links/index/".$action."?notfull=1");
				}
			}
			$this->model->delByPosition($action);
			if ($dataList) {
				$this->model->insertAllData($dataList);
				if ($action == 'kjdh') {
					$title = '快捷导航';
				}else {
					$title = '外链';
				}
				foreach ($dataList as $dt){
					$this->syslog(32, "更新".$title."：".$dt['title']."，".$dt['url']);
				}
			}
			$this->refreshCache($action);
			$this->redirect("/backend/links/index/".$action);
		}
		$this->load->view('links', compact('action', 'data', 'notfull'));
	}
	
	private function refreshCache($position)
	{
		$this->load->model('model_shouye_link', 'model');
		$kjdh = $this->model->getDataByPosition('kjdh');
		$yqlj = $this->model->getDataByPosition('yqlj');
		foreach ($yqlj as $y) {
			$yStr .= "<a href='".$y['url']."'>".$y['title']."</a>";
		}
		$str = "<div class='wrap'><div class='m-qlink'><ul class='mod-tab'><li class='current'>快捷导航</li><li>友情链接</li></ul><div class='mod-tab-con'><div class='mod-tab-item' style='display: block;'>";
		foreach ($kjdh as $k) {
			$str .= "<a href='".$k['url']."' target='_blank'>".$k['title']."</a>";
		}
		$str .= "</div><div class='mod-tab-item' style='display: none;'>";
		foreach ($yqlj as $y) {
			$str .= "<a href='".$y['url']."' target='_blank'>".$y['title']."</a>";
		}
		$str .= "</div></div></div></div>";
		file_put_contents("../application/views/v1.1/elements/common/links.php5", $str);
		if ($position === 'yqlj') {
			$str = '<ul>';
			foreach ($yqlj as $y) {
				$str .= "<li><a target='_blank' href=".$y['url'].">".$y['title']."</a></li>";
			}
			$str .= '</ul>';
			file_put_contents("../application/views/v1.1/elements/common/partner.php5", $str);
		}
		if (ENVIRONMENT === 'production') {
			system("/bin/bash /opt/shell/rsync_static.sh", $status);
		}
	}
        
    public function sensitiveWords()
    {
        $this->check_capacity("6_7_3");
        $this->load->model('model_sensitiveword');
        $words = $this->model_sensitiveword->getAllWord(false, 1, 200);
        $num = $words['count'];
        $words = json_encode($words['words']);
        $this->load->view('words', compact('num','words'));
    }
    
    public function getsensitiveWords()
    {
        $this->load->model('model_sensitiveword');
        $word = $this->input->get('word', true);
        $pageNum = $this->input->get('pageNum', true);
        $num = $this->input->get('num', true);
        $words = $this->model_sensitiveword->getAllWord($word, $pageNum, $num);
        echo json_encode(array('words' => $words['words'], 'num' => $words['count']));
    }
    
    public function delsensitiveWords()
    {
        $this->check_capacity('6_7_6');
        $ids = json_decode(file_get_contents("php://input"), true);
        $this->load->model('model_sensitiveword');
        $this->model_sensitiveword->delWord($ids);
        $this->syslog(52,"删除敏感词操作");
        echo json_encode(array('msg'=>'恭喜您，操作成功。'));
    }
    
    public function uplaodsensitiveWords()
    {
        $this->check_capacity('6_7_6');
        $upload_path = dirname(BASEPATH) . '/uploads/txt/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $path = $upload_path . md5(time()) . '.txt';
        move_uploaded_file($_FILES['file']['tmp_name'], $path);
        $handle = fopen($path, 'r');
        if ($handle) {
            $txt = fread($handle, filesize($path));
            $txt = iconv('gbk', 'UTF-8', $txt);
            fclose($handle);
        }
        $this->load->model('model_sensitiveword');
        $this->model_sensitiveword->insertWord($txt);
        $this->syslog(52,"上传更新敏感词操作");
        die("恭喜您，操作成功。");
    }
}