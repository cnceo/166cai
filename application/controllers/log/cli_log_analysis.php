<?php

/**
 * 日志分析.
 *
 * @date:2017-04-24
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Log_Analysis extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $curhour = sprintf('%02d', date("H", time()));
        if ($curhour != '00') {
            $lasthour = sprintf('%02d', $curhour - 1);
            foreach (array($lasthour, $curhour) as $hour) {
                $this->caidouAnalysis($hour);
                $this->qihuiAnalysis($hour);
            }
        } else {
            $this->caidouAnalysis($curhour);
            $this->qihuiAnalysis($curhour);
        }
        $this->noticeCaidouAnalysis('0');
        $this->noticeQihuiAnalysis('0');
        $this->curlAnalysis('0');
        $this->deleteLog();
    }

    public function caidouAnalysis($hour) {
        $this->load->model('log_model');
        $codes = array('10001', '10002', '10003', '20002', '20004', '20005', '20006', '20008', '20009');
        foreach ($codes as $key => $code) {
            $start = $this->log_model->getLogStart('caidou', $code, $hour);
            if (empty($start)) {
                $start = $this->log_model->insertLogStart('caidou', $code, $hour);
            }
            $filedir = APPPATH . "logs/{$start['business']}{$start['code']}/{$start['file']}.php";
            if (!file_exists($filedir) || $start['scan'] == 1) {
                continue;
            }
            $handle = new SplFileObject($filedir, 'r');
            if ($handle) {
                $handle->seek($start['start']);
                $num = 0;
                while ($handle->current()) {
                    $num++;
                    $handle->next();
                }
                $handle->seek($start['start']);
                for ($i = 0; $i < ceil($num / 100); ++$i) {
                    $str = '';
                    if ($i > 0) {
                        $handle->seek(100 * $i + $start['start']);
                    }
                    for ($j = 0; $j < 100; ++$j) {
                        if ($handle->current()) {
                            $str .= $handle->current();
                            $handle->next();
                        } else {
                            break;
                        }
                    }
                    if (in_array($code, array('20002', '20004', '20005', '20006'))) {
                        preg_match_all('/LOG - (.*?) --\> (.*?\-.*?)-(.*?)\[(.*?)\]:(.*?\n)/is', $str, $matches);
                    } else {
                        preg_match_all('/LOG - (.*?) --\> (.*?)-(.*?)\[(.*?)\]:(.*?\n)/is', $str, $matches);
                    }
                    $this->log_model->insertLog($matches, $code, 'caidou');
                }
                if ($hour >= date('H', time())) {
                    $this->log_model->updateLogStart('caidou', $start['code'], $hour, $num + $start['start'], 0, $num + $start['start']);
                } else {
                    $this->log_model->updateLogStart('caidou', $start['code'], $hour, $num, 1, $num);
                }
            }
        }
    }

    public function qihuiAnalysis($hour) {
        $this->load->model('log_model');
        $this->load->library('encrypt_qihui');
        $codes = array('1002', '1003', '1006', '1020', '1023');
        foreach ($codes as $key => $code) {
            $start = $this->log_model->getLogStart('qihui', $code, $hour);
            if (empty($start)) {
                $start = $this->log_model->insertLogStart('qihui', $code, $hour);
            }
            $filedir = APPPATH . "logs/{$start['business']}{$start['code']}/{$start['file']}.php";
            if (!file_exists($filedir) || $start['scan'] == 1) {
                continue;
            }
            $handle = new SplFileObject($filedir, 'r');
            if ($handle) {
                $handle->seek($start['start']);
                $num = 0;
                while ($handle->current()) {
                    $num++;
                    $handle->next();
                }
                $handle->seek($start['start']);
                for ($i = 0; $i < ceil($num / 100); ++$i) {
                    $str = '';
                    if ($i > 0) {
                        $handle->seek(100 * $i + $start['start']);
                    }
                    for ($j = 0; $j < 100; ++$j) {
                        if ($handle->current()) {
                            $str .= $handle->current();
                            $handle->next();
                        } else {
                            break;
                        }
                    }
                    if ($code == '1003') {
                        preg_match_all('/LOG - (.*?) --\> (.*?\-.*?)-(.*?)\[(.*?)\]:(.*?\n)/is', $str, $matches);
                    } else {
                        preg_match_all('/LOG - (.*?) --\> (.*?)-(.*?)\[(.*?)\]:(.*?\n)/is', $str, $matches);
                    }
                    foreach ($matches[5] as $k => $match) {
                        $xmlObj = simplexml_load_string(trim($matches[5][$k]));
                        $datas = $this->encrypt_qihui->decrypt($xmlObj->body);
                        $matches[5][$k] = $datas;
                    }
                    $this->log_model->insertLog($matches, $code, 'qihui');
                }
                if ($hour >= date('H', time())) {
                    $this->log_model->updateLogStart('qihui', $start['code'], $hour, $num + $start['start'], 0, $num + $start['start']);
                } else {
                    $this->log_model->updateLogStart('qihui', $start['code'], $hour, $num, 1, $num);
                }
            }
        }
    }

    public function noticeCaidouAnalysis($date) {
        $this->load->model('log_model');
        $start = $this->log_model->getNoticeStart('caidou', 30000, $date);
        if (empty($start)) {
            $start = $this->log_model->insertNoticeStart('caidou', 30000, $date);
        }
        $filedir = APPPATH . "logs/{$start['business']}{$start['code']}/{$start['file']}.php";
        if (!file_exists($filedir) || $start['scan'] == 1) {
            return ;
        }
        $handle = new SplFileObject($filedir, 'r');
        if ($handle) {
            $handle->seek($start['start']);
            $num = 0;
            while ($handle->current()) {
                $num++;
                $handle->next();
            }
            $handle->seek($start['start']);
            for ($i = 0; $i < ceil($num / 100); ++$i) {
                $str = '';
                if ($i > 0) {
                    $handle->seek(100 * $i + $start['start']);
                }
                for ($j = 0; $j < 100; ++$j) {
                    if ($handle->current()) {
                        $str .= $handle->current();
                        $handle->next();
                    } else {
                        break;
                    }
                }
                preg_match_all('/LOG - (.*?) --\> .*?(<ticket(.*?)\><\/notify>)/is', $str, $matches);
                foreach ($matches[2] as $key => $match) {
                    preg_match_all('/(<ticket.*?apply=\"(.*?)\".*?>)/is', $match, $tickets);
                    $matches[2][$key] = $tickets[1];
                    $matches[3][$key] = $tickets[2];
                }
                $this->log_model->insertNotice($matches, 30000, 'caidou');
            }
            $this->log_model->updateNoticeStart('caidou', 30000, $date, $num + $start['start'], 0, $num + $start['start']);
        }
    }

    public function noticeQihuiAnalysis($date) {
        $this->load->model('log_model');
        $codes = array('1100', '1101');
        foreach ($codes as $key => $code) {
            $start = $this->log_model->getNoticeStart('qihui', $code, $date);
            if (empty($start)) {
                $start = $this->log_model->insertNoticeStart('qihui', $code, $date);
            }
            $filedir = APPPATH . "logs/{$start['business']}{$start['code']}/{$start['file']}.php";
            if (!file_exists($filedir) || $start['scan'] == 1) {
                continue;
            }
            $handle = new SplFileObject($filedir, 'r');
            if ($handle) {
                $handle->seek($start['start']);
                $num = 0;
                while ($handle->current()) {
                    $num++;
                    $handle->next();
                }
                $handle->seek($start['start']);
                for ($i = 0; $i < ceil($num / 100); ++$i) {
                    $str = '';
                    if ($i > 0) {
                        $handle->seek(100 * $i + $start['start']);
                    }
                    for ($j = 0; $j < 100; ++$j) {
                        if ($handle->current()) {
                            $str .= $handle->current();
                            $handle->next();
                        } else {
                            break;
                        }
                    }
                    preg_match_all('/LOG - (.*?) --\> (.*?<messageId>(.*?)<\/messageId>.*?\n)/is', $str, $matches);
                    $this->log_model->insertNotice($matches, $code, 'qihui');
                }
                $this->log_model->updateNoticeStart('qihui', $start['code'], $date, $num + $start['start'], 0, $num + $start['start']);
            }
        }
    }

    public function curlAnalysis($date) {
        $this->load->model('log_model');
        $start = $this->log_model->getCurlStart('curl', $date);
        if (empty($start)) {
            $start = $this->log_model->insertCurlStart('curl', $date);
        }
        $filedir = APPPATH . "logs/{$start['file']}.php";
        if (!file_exists($filedir) || $start['scan'] == 1) {
            return ;
        }
        $handle = new SplFileObject($filedir, 'r');
        if ($handle) {
            $handle->seek($start['start']);
            $num = 0;
            while ($handle->current()) {
                $num++;
                $handle->next();
            }
            $handle->seek($start['start']);
            for ($i = 0; $i < ceil($num / 100); $i++) {
                $str = "";
                if ($i > 0) {
                    $handle->seek(100 * $i + $start['start']);
                }
                for ($j = 0; $j < 100; $j++) {
                    if ($handle->current()) {
                        $str .= $handle->current();
                        $handle->next();
                    } else {
                        break;
                    }
                }
                preg_match_all('/LOG - (.*?) --\> (.*?\n)/is', $str, $matches);
                $this->log_model->insertCurlError($matches);
            }
            $this->log_model->updateNoticeStart('curl', $start['code'], $date, $num + $start['start'], 0, $num + $start['start']);
        }
    }
    
    public function deleteLog()
    {
        $this->load->model('log_model');
        $this->log_model->deleteLog();
    }
}
