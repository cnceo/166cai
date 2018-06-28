<?php

class Log_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertLog($matches, $code, $business)
    {
        if (!empty($matches[1])) {
            $res_value = array();
            $req_value = array();
            foreach ($matches[1] as $key => $match) {
                if ($matches[4][$key] == 'REQ') {
                    $req_value[] = array('time' => $matches[1][$key], 'messageid' => $matches[2][$key], 'secret' => $matches[3][$key], 'content' => $matches[5][$key], 'code' => $code, 'business' => $business, 'created' => date('Y-m-d H:i:s', time()));
                }
                if ($matches[4][$key] == 'RES') {
                    $res_value[] = array('time' => $matches[1][$key], 'messageid' => $matches[2][$key], 'secret' => $matches[3][$key], 'content' => $matches[5][$key], 'code' => $code, 'business' => $business, 'created' => date('Y-m-d H:i:s', time()));
                }
            }
        }
        $this->db->trans_start();
        $res1 = true;
        $res2 = true;
        if (!empty($req_value)) {
            $res1 = $this->db->insert_batch('cp_ticket_req', $req_value);
        }
        if (!empty($res_value)) {
            $res2 = $this->db->insert_batch('cp_ticket_res', $res_value);
        }
        if (!$res1 || !$res2) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_complete();
        }
    }

    public function getLogStart($business, $code, $hour)
    {
        $time1 = date('Y-m-d', time());
        $time2 = date('Ymd', time());
        $log = "log-{$time1}-{$time2}{$hour}";
        return $this->db->query('select * from cp_start_record where business=? and code=? and date=? and file=? limit 1', array($business, $code, $time2, $log))->getRow();
    }

    public function insertLogStart($business, $code, $hour)
    {
        $time1 = date('Y-m-d', time());
        $time2 = date('Ymd', time());
        $log = "log-{$time1}-{$time2}{$hour}";
        $sql = 'INSERT cp_start_record (file,start,code,business,date,created) VALUES (?,?,?,?,?,NOW())';
        $this->db->query($sql, array($log, '0', $code, $business, $time2));

        return $this->getLogStart($business, $code, $hour);
    }

    public function updateLogStart($business, $code, $hour, $start, $scan, $total)
    {
        $time1 = date('Y-m-d', time());
        $time2 = date('Ymd', time());
        $log = "log-{$time1}-{$time2}{$hour}";
        $sql = 'UPDATE cp_start_record set scan = ?,start = ?,total = ? where business=? and code=? and date=? and file=?';
        $this->db->query($sql, array($scan, $start, $total, $business, $code, $time2, $log));

        return $this->getLogStart($business, $code, $hour);
    }

    public function insertNoticeStart($business, $code, $day)
    {
        $time1 = date('Y-m-d', strtotime("{$day} day"));
        $time2 = date('Ymd', strtotime("{$day} day"));
        $log = "log-{$time1}-{$code}";
        $sql = 'INSERT cp_start_record (file,start,code,business,date,created) VALUES (?,?,?,?,?,NOW())';
        $this->db->query($sql, array($log, '0', $code, $business, $time2));

        return $this->getNoticeStart($business, $code, $day);
    }

    public function insertNotice($matches, $code, $business)
    {
        $value = array();
        if ($business == 'caidou') {
            foreach ($matches[3] as $key => $match) {
                foreach ($match as $k => $orderId) {
                    $value[] = array('time' => $matches[1][$key], 'orderId' => $orderId, 'code' => $code, 'content' => $matches[2][$key][$k], 'business' => $business, 'created' => date('Y-m-d H:i:s', time()));
                }
            }
        } else {
            foreach ($matches[1] as $key => $match) {
                $value[] = array('time' => $matches[1][$key], 'orderId' => $matches[3][$key], 'code' => $code, 'content' => $matches[2][$key], 'business' => $business, 'created' => date('Y-m-d H:i:s', time()));
            }
        }
        if (!empty($value)) {
            $this->db->insert_batch('cp_notice_record', $value);
        }
    }

    public function getNoticeStart($business, $code, $day)
    {
        $date = date('Ymd', strtotime("{$day} day"));
        return $this->db->query('select * from cp_start_record where business=? and code=? and date=? limit 1', array($business, $code, $date))->getRow();
    }

    public function updateNoticeStart($business, $code, $day, $start, $scan, $total)
    {
        $date = date('Ymd', strtotime("{$day} day"));
        $sql = 'UPDATE cp_start_record set start = ?,scan=?,total=? where business=? and code=? and date=?';
        $this->db->query($sql, array($start, $scan, $total, $business, $code, $date));

        return $this->getNoticeStart($business, $code, $day);
    }

    public function getCurlStart($business, $day, $code = '100')
    {
        $date = date('Ymd', strtotime("{$day} day"));
        return $this->db->query('select * from cp_start_record where business=? and code=? and date=? limit 1', array($business, $code, $date))->getRow();
    }

    public function insertCurlStart($business, $day, $code = '100')
    {
        $time1 = date('Y-m-d', strtotime("{$day} day"));
        $time2 = date('Ymd', strtotime("{$day} day"));
        $log = "log-{$time1}-curl_error";
        $sql = 'INSERT cp_start_record (file,start,code,business,date,created) VALUES (?,?,?,?,?,NOW())';
        $this->db->query($sql, array($log, '0', $code, $business, $time2));

        return $this->getCurlStart($business, $day);
    }

    public function insertCurlError($matches)
    {
        $value = array();
        foreach ($matches[1] as $key => $match) {
            $content = addslashes($matches[2][$key]);
            $value[] = "('{$matches[1][$key]}','{$content}',NOW())";
        }
        if (!empty($value)) {
            $value = implode(',', $value);
        }
        $sql = 'INSERT cp_curl_error (time,content,created) VALUES '.$value;
        $this->db->query($sql);
    }
    
    public function deleteLog()
    {
        $time1 = date('Y-m-d H:i:s', strtotime("-1 day"));
        $this->db->trans_start();
        $sql1 = 'delete from cp_ticket_req where created<=?';
        $this->db->query($sql1, $time1);
        $sql2 = 'delete from cp_ticket_res where created<=?';
        $this->db->query($sql2, $time1);
        $sql3 = 'delete from cp_notice_record where created<=?';
        $this->db->query($sql3, $time1);
        $sql4 = 'delete from cp_curl_error where created<=?';
        $this->db->query($sql4, $time1);
        $this->db->trans_complete();
    }
}
