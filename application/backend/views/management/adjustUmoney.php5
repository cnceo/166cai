<?php $this->load->view("templates/head");?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/adjustUmoney">批量调账</a></div>
    <div class="mt10">
    	<div class="data-table-list mt10">
	    	<form method="post" enctype="multipart/form-data">
	    		<table>
	    			<colgroup><col width="62"><col width="262"></colgroup>
	    			<tbody>
	    				<tr><td style="text-align: right;padding:10px">上传批量文件：</td><td style="text-align: left;padding:10px"><input type="file" name="file"></td></tr>
	    				<tr><td style="text-align: right;padding:10px">金额类型：</td><td style="text-align: left;padding:10px"><input name="info[ismustcost]" value="0" type="radio" checked>可提现<input name="info[ismustcost]" value="1" type="radio">不可提现</td></tr>
	    				<tr>
	    					<td style="text-align: right;padding:10px">账户明细类型：</td>
	    					<td style="text-align: left;padding:10px">
		    					<select name="info[ctype]" id="adjust_ctype">
		    						<option value="1">彩金派送</option>
		    						<option value="2">奖金派送</option>
		    						<option value="3">其他</option>
		    					</select>
		    				</td>
	    				</tr>
	    				<tr id="tr_iscapital"><td style="text-align: right;padding:10px">是否在成本库扣除：</td><td style="text-align: left;padding:10px"><input name="info[iscapital]" type="radio" value="1" checked>是<input name="info[iscapital]" value="0" type="radio">否</td></tr>
	    				<tr><td style="text-align: right;padding:10px">批量加款订单数：</td><td style="text-align: left;padding:10px"><input name="info[count]" type="text" class="ipt w222"> 单</td></tr>
	    				<tr><td style="text-align: right;padding:10px">批量加款金额：</td><td style="text-align: left;padding:10px"><input name="info[money]" type="text" class="ipt w222"> 元</td></tr>
	    				<tr><td style="text-align: right;padding:10px">温馨提示：</td><td style="text-align: left;padding:10px">上传调账文件后，点击提交会对订单总数进行校验，如有差异需检查并修改上传文件</td></tr>
	    				<tr><td colspan="2"><a href="/backend/Management/downloadadjust" target="_self">批量调账模板下载</a>（上传前把文件编码改为utf-8）</td></tr>
	    				<tr><td colspan="2"><input type="submit" class="btn-blue ml20" value="提交"></td></tr>
	    			</tbody>
	    		</table>
	    	</form>
    	</div>
    </div>
</div>
<script>
<?php if ($dtwrong == 1) {?>
alert('批量上传文件成功！');
<?php }elseif ($dtwrong !== 0) {?>
alert('<?php echo $dtwrong?>');
location.href = '/backend/Management/adjustUmoney';
<?php }?>
$('#adjust_ctype').change(function(){
	if($(this).val() === '2') {
		$('#tr_iscapital').hide();
	}else {
		$('#tr_iscapital').show();
	}
})
</script>