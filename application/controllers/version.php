<?php
class Version extends MY_Controller {
	
	public function index() {
		$version = $this->input->post('version');
		if ($version) 
		{
			$CI = &get_instance();
			$staticDomain = array(
					'imgwx1.2345.com',
					'imgwx2.2345.com',
					'imgwx3.2345.com',
					'imgwx4.2345.com',
					'imgwx5.2345.com',
			);
			$rand = hexdec(substr(md5($filePath), 0, 1)) % 5;
			if(in_array(ENVIRONMENT, array('development', 'checkout')))
			{
				$host = '';
			}
			if(!isset($host))
			{
				$host = (time() - $ver) < 20 ? '' : $staticDomain[$rand];
			}
			$dir = $CI->config->item('base_path')."/caipiaoimg/v1.1/styles";
			$mydir = dir($dir);
			while ($file = $mydir->read()) 
			{
				if (!in_array($file, array('.', '..'), true)) 
				{
					$contents = file_get_contents($dir."/".$file);
					$contents = preg_replace('/(url\([\'"]?)(\/caipiaoimg\/v1\.1\/.*?)\?.*?([\'"]?\))/is', '$1'. '$2' . '$3', $contents);
					$contents = preg_replace('/(url\([\'"]?)(\/caipiaoimg\/v1\.1\/.*?)([\'"]?\))/is', '$1' . $host . '$2'. "?" .$version. '$3', $contents);
					file_put_contents($dir."/".$file, $contents);
				}
			}
			echo '刷新成功！';
		}else 
		{
			$this->displayLess('version/index');
		}
	}
	
}