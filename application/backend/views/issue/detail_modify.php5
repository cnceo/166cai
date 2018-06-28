<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/management">期次管理</a>&nbsp;&gt;&nbsp;开奖详情修改</div>
    <div class="kj-dtail-fix mt10">
        <?php $page = '/issue/modify_'.$lid; $this->load->view($page); ?>
    </div>
    <!-- 确认弹出层 -->
    <div class="pop-mask" style="display:none;width:200%"></div>
    <div class="pop-dialog" id="confirm-submit">
		<div class="pop-in">
			<div class="pop-head">
				<h2>开奖详情修改</h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					请确认修改
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
</div>
<script>
	//提交
	$("#confirm").click(function(){
		$('#submit_form').submit();
	});
</script>
</body>
</html>
