<div class="tit-b">
	<h2>验证真实身份</h2>
        <p class="tip cOrange">绑定真实身份才能顺利领奖</p>
</div>
<ul class="steps-bar clearfix">
	<li><i>1</i><span class="des">填写身份信息</span></li>
	<li><i>2</i><span class="des">核对信息</span></li>
	<li class="last cur"><i>3</i><span class="des">验证完成</span></li>
</ul>
<div class="safe-item-box safe-success">
	<div class="sc-tip"><i class="icon icon-cYes"></i>恭喜您，实名认证成功！</div>
	<ul class="form uc-form-list">
		<li class="form-item">
			<label class="form-item-label">证件类型</label>
			<div class="form-item-con"><span class="form-item-txt">身份证</span></div>
		</li>
		<li class="form-item">
			<label class="form-item-label">真实姓名</label>
			<div class="form-item-con"><span class="form-item-txt"><?php echo cutstr($real_name, 0, 1);?></span></div>
		</li>
		<li class="form-item">
			<label class="form-item-label">身份证号</label>
			<div class="form-item-con"><span class="form-item-txt"><?php echo cutstr($id_card, 0, 15);?></span></div>
		</li>
	</ul>
	<p><a href="safe/">返回安全中心>></a></p>
</div>