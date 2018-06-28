<?php $this->load->view('elements/user/menu');?>
<?php 
	$rcode = array(
		'1' => '恭喜您，邮箱绑定成功！',
		'2' => '邮箱,已经被其他人使用',
		'3' => '已和其他邮箱绑定，得先解绑',
		'4' => '输入的邮箱格式有误',
		'5' => '该链接已过期',
	);
?>
<div class="article">
	<div class="tit-b">
		<h2>绑定邮箱</h2>
	</div>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">绑定邮箱</span></li>
		<li><i>2</i><span class="des">验证邮件</span></li>
		<li class="last cur"><i>3</i><span class="des">绑定完成</span></li>
	</ul>
	<div class="safe-item-box safe-success">
		<div class="sc-tip"><i class="icon icon-cYes"></i><?php echo $rcode[$return];?></div>
		<p><a href="/safe">返回安全中心>></a></p>
	</div>
</div> 
<?php $this->load->view('elements/user/menu_tail');?>