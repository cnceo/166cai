<?php if($fromType != 'ajax'): $this->load->view("templates/head") ?>
    <div class="path">您的位置：上下架配置&nbsp;>&nbsp;<a href="">春节停售</a></div>
<?php endif; ?>
<div align = "center">
<?php if ($state) {?>
	<input class = "btn-blue mr10" type="button" name="subbtn" value="关闭" onclick="javascript:rsyncstart('close');"/>
<?php }else {?>
	<input class = "btn-blue mr10" type="button" name="subbtn" value="开启" onclick="javascript:rsyncstart('start');"/>
<?php }?>
</div>
<script type="text/javascript">
	function rsyncstart(flag)
	{
		$.ajax({
            type: 'post',
            url:  '/backend/Stop/index',
            data: {'action':flag},
            success: function(response) {
                if(response) {
                	alert('操作成功!');
                	location.reload();
                }
            }
        });
	}
</script>
<?php if($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>