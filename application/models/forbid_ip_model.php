<?php
class Forbid_ip_Model extends MY_Model {

	public function countNum($ip) {
		$this->db->query("update cp_forbid_ip set num = num + 1 where ip = ? and status = 0", array($ip));
		return $this->db->affected_rows();
	}

}