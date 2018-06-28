<?php
class Test_Model extends MY_Model {
	
	public function getUserIdByRegistertime($start, $end) {
		return $this->slave->query("SELECT id FROM cp_user_register WHERE created >= ? AND created <= ?", array($start, $end))->getCol();
	}
}