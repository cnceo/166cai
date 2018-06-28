</div>
<div class="pop-mask" style="display:none"></div>
<!-- 修改密码 -->
<form id='updatePwdForm' name='updatePwdForm' method='post' action=''>
<div class="pop-dialog" id="updatePwd" >
	<div class="pop-in">
		<div class="pop-head">
			<h2>修改密码</h2>
			<span class="pop-close _cancle" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-list table-no-border">
				<table>
					<colgroup>
						<col width="100" />
		                <col width="220" />
					</colgroup>
					<tbody id='_tbody'>
						<tr>
							<td class="tar">
								<label for="">旧密码：</label>
							</td>
							 <td class="tal">
								<input type="password" class="ipt w202"  name="oldpwd" placeholder="旧密码">
		            		</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">新密码：</label>
							</td>
							<td class="tal">
								<input type="password" class="ipt w202 " name="newpwd" placeholder="新密码">
							</td>
						</tr>
						<tr>
							<td class="tar">
								<label for="">确认密码：</label>
							</td>
							<td class="tal">
								<input type="password" class="ipt w202 " name="surepwd" placeholder="确认密码">
							</td>
						</tr>
						<tr>
							<td class="tac" colspan="2">
								<a href="javascript:;" class="btn-blue-h32 mr10"  id="surePwdSubmit" name="surePwdSubmit">确定</a>
								<a href="javascript:;" class="btn-b-white mlr15 pop-cancel _cancle">关闭</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>
<!--修改密码结束-->
<script type="text/javascript">
$(function(){
   /**
    * [修改密码]
    * @author Likangjian  2017-04-30
    * @param  {[type]} ){                } [description]
    * @return {[type]}     [description]
    */
   $('._modify_pwd').click(function(){
   		$('input[name=oldpwd]').val('');
   		$('input[name=surepwd]').val('');
   		$('input[name=newpwd]').val('');
   		popdialog("updatePwd");
   });
   //提交密码修改
   $('#surePwdSubmit').click(function(){
   	 //验证
   	 var surepwd = $('input[name=surepwd]').val();
   	 var newpwd = $('input[name=newpwd]').val();
   	 var oldpwd = $('input[name=oldpwd]').val();
   	 var  _reg = /^\S{6,16}$/;
   	 if(!_reg.test($.trim(oldpwd)) || !_reg.test($.trim(newpwd)) || !_reg.test($.trim(surepwd)))
   	 {
   	 	layer.alert('密码应在6~16位之间', {icon: 2,btn:'',title:'温馨提示',time:0});
   	 }else if(surepwd != newpwd)
   	 {
   	 	layer.alert('两次输入密码不一致', {icon: 2,btn:'',title:'温馨提示',time:0});
   	 }else{
		$.ajax({
		    type: "post",
		    url: "/chansys/index/updateChannelPwd",
		    data: $('form[name=updatePwdForm]').serialize(),
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESS')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:5000,end:function(){$('.pop-cancel').trigger("click"); location.reload()}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
   	 }
   });	
});	
</script>
</body>
</html>