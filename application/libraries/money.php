<?php

class Money {

	public function format($money) {
		$unit = '';
		if ($money > 10000) {
			$money /= 10000;
			$unit = '万';
		}
		return number_format($money, 2) . $unit;
	}

}
