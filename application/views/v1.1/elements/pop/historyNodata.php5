<div class="pub-pop historyNodata  pop-bank-choose">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<p class="pop-txt tac fz18 pt10 yahei c333">
				<i class='icon-font'>&#xe611;</i>请至少选择1注号码进行对比
			</p>
		</div>
		<div class="pop-foot">
			<div class="btn-group">
				<a class="btn-pop-confirm" target="_self" href="javascript:;">帮我选</a>
				<a class="btn-pop-cancel" target="_self" href="javascript:;">自己选</a>
			</div>
		</div>
	</div>
</div>
<script>
$(".btn-pop-confirm").click(function(){
	cx._basket_.randSelect(1);
	cx.PopCom.hide(".historyNodata");
})
$(".btn-pop-cancel").click(function(){
	$(".historyNodata").remove();
    cx.Mask.hide();
})
</script>