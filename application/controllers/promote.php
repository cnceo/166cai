<?php
class Promote extends MY_Controller {

	public function svip()
	{
		$cpk = $this->input->get('cpk');
		$key = preg_match('/10075_(k(\d+))/', $cpk, $matches);
		if ($matches) {
			$key = $matches[1];
			$this->primarysession->setArg('channelId', '10075');
			$domain = str_replace('www.', '', $this->config->item('domain'));
			$this->input->set_cookie('cpk', $key, 0, $domain, '/', '', false, true);
		}
		$this->redirect('/');
	}
}