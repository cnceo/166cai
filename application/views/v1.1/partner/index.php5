<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/other.min.css');?>"/>
<div class="wrap friend-link">
	<div class="mod-box">
		<div class="mod-box-hd">
			<h1 class="mod-box-title">友情链接</h1>
		</div>
		<div class="mod-box-bd link-bd">
			<?php $this->load->view('v1.1/elements/common/partner')?>
		</div>
	</div>

	<ul class="friend-link-txt">
		<li>
			<h3 class="title">本站链接</h3>
			<p>文字链接：166彩票官网；链接地址：<a href="<?php echo $baseUrl?>" target="_blank" title="166彩票官网">http://<?php echo $this->config->item('domain')?>/</a></p>
		</li>
		<li>
			<h3 class="title">申请链接</h3>
			<p>请在贵站添加本站链接，并发邮件到<a href="mailto:caika166@126.com" title="点击发送邮件">caika166@126.com</a>。我们将在收到邮件后3个工作日内审核回复！</p>
			<p>链接QQ：497203973</p>
		</li>
	</ul>
</div>