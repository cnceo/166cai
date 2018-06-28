<?php $this->load->view("templates/head") ?>
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery-1.8.3.min.js"></script>
<div class="path">您的位置：<a href="">修改密码</a></div>
<div class="data-table-list mt20 table-no-border">
<form>
	<table class="table">
		<colgroup>
	        <col width="10%">
	        <col width="90%">
	    </colgroup>
		<tbody>
			<tr>
	            <td class="tar"><label for="">密码：</label></td>
	            <td class="tal pl10"><input type="password" class="ipt w222" id="pass"></td>
	        </tr>
	        <tr>
	            <td class="tar"><label for="">确认密码：</label></td>
	            <td class="tal pl10"><input type="password" class="ipt w222" id="confirm"></td>
	        </tr>
		</tbody>
	</table>
	<div class="audit-detail-btns mt20 ml10bf">
    <a href="javascript:;" class="btn-blue " type="submit">保存</a>
</div>
</form>
</div>
<script>
$("#confirm, #pass").blur(function(){
	if ($(this).val().length > 16 || $(this).val().length < 6)
	{
		alert('密码应该在6-16位之间！');
	}
})
$(".btn-blue").click(function(){
	if($("#confirm").val() !== $("#pass").val()) {
		alert('两次输入密码不一致！');
	}else if($("#pass").val().length > 16 || $("#pass").val().length < 6) {
		alert('密码应该在6-16位之间！');
	}else {
		$.post('/backend/Account/pass',{pass:$("#pass").val()},
		function(data){
			if (data > 0){
				alert('更新成功！');
			}else {
				alert('更新失败！');
			}
		})
	}
	
})
</script>