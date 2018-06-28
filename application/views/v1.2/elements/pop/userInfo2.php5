<div class="mod-result result-success">
	<div class="mod-result-bd">
		<i class="icon-result"></i>
		<div class="result-txt">
			<h2 class="result-txt-title">恭喜您，实名认证成功</h2>
			<ul>
				<li>证件类型：身份证</li>
				<li>真实姓名：<?php echo $real_name;?></li>
				<li>身份证号：<?php echo substr_replace($id_card, '****', 14, 4);?></li>
			</ul>
			<a href="javascript:;" class="btn-b btn-main btn-result-lnk cancel"><?php if ($rfsh) {?>确定<?php }else {?>继续购彩<?php }?></a>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function () {
	$('.cancel').click(function() {
		<?php if ($rfsh) {?>
        location.reload();
        <?php }?>
        cx.PopCom.hide('.pop-perfect-info');
        $('.not-bind').removeClass('not-bind');
        $('.needTigger').trigger('click');
    });
});
</script>