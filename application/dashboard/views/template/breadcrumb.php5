<?php
$path1 = '合作商管理系统';
$url1 = $this->config->item ( 'base_url' ) . "/shop";
switch ($con)
{
	case 'shop' :
		$path2 = '投注站管理';
		$url2 = $this->config->item ( 'base_url' ) . "/shop";
		break;
}
?>

<div class="path">
	您的位置：<a href="<?php echo $url1?>"><?php echo $path1?></a>&nbsp;&gt;&nbsp;<a
		href="<?php echo $url2?>"><?php echo $path2?></a>
</div>