<?php if($fromType != 'ajax'): $this->load->view("templates/head") ?>
    <div class="path">您的位置：上下架配置&nbsp;>&nbsp;<a href="">服务承诺弹层</a></div>
<?php endif; ?>
<div class="data-table-list mt10">
<table>
	<tr><td>勾选框点击数</td><td><?php echo $count[0]?></td></tr>
	<tr>
		<td colspan="2">
		<?php if ($state) {?>
			<input class = "btn-blue mr10" type="button" name="subbtn" value="关闭" onclick="javascript:rsyncstart('close');"/>
		<?php }else {?>
			<input class = "btn-blue mr10" type="button" name="subbtn" value="开启" onclick="javascript:rsyncstart('start');"/>
		<?php }?>
		</td>
	</tr>
</table>
<br>


</div>
<script type="text/javascript">
	function rsyncstart(flag)
	{
		$.ajax({
            type: 'post',
            url:  '/backend/Stop/uservpop',
            data: {'action':flag},
            success: function(response) {
                if(response == 1) {
                	alert('操作成功!');
                	location.reload();
                }else{
                    alert(response);
                }
            }
        });
	}
</script>
<?php if($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>