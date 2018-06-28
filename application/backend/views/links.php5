<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">首页管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li <?php if ($action == 'kjdh') {?> class="current" <?php }?>><a href="/backend/links/index/kjdh">快捷导航</a></li>
      <li <?php if ($action == 'yqlj') {?> class="current" <?php }?>><a href="/backend/links/index/yqlj">友情链接</a></li>
      <li><a href="/backend/links/sensitiveWords">敏感词库</a></li>
    </ul>
  </div>
  <div class="mod-tab-bd">
    <ul>
      <!-- 轮播图管理 -->
      <li style="display:block">
        <div class="data-table-list mt10">
        <form action="/backend/links/index/<?php echo $action?>" method="post" id="<?php echo $action?>_form">
          <table style="width: auto" data-action="<?php echo $action?>">
            <colgroup><col width="50px" /><col width="100px" /><col width="280px" /><col width="80px" /></colgroup>
            <thead><tr><th>序号</th><th>名称</th><th>链接</th><th>操作</th></tr></thead>
            <tbody id="pic-table">
            <?php $p = 0;
            foreach ($data as $p => $v) {?>
            <tr>
              <td><input type="text" class="ipt tac w40" value="<?php echo $p?>" name="<?php echo $action?>[<?php echo $p?>][priority]"></td>
              <td><input type="text" class="ipt tac w84" name="<?php echo $action?>[<?php echo $p?>][title]" value="<?php echo $data[$p]['title']?>"></td>
              <td><input type="text" class="ipt tac w264" name="<?php echo $action?>[<?php echo $p?>][url]" value="<?php echo $data[$p]['url']?>"></td>
              <td><a href="javascript:;" class="cBlue removeTr">清空</a></td>
            </tr>
            <?php }?>
            <tr data-index="<?php echo ++$p?>">
              <td><input type="text" class="ipt tac w40" name="<?php echo $action?>[<?php echo $p?>][priority]"></td>
              <td><input type="text" class="ipt tac w84" name="<?php echo $action?>[<?php echo $p?>][title]"></td>
              <td><input type="text" class="ipt tac w264" name="<?php echo $action?>[<?php echo $p?>][url]"></td>
              <td><a href="javascript:;" class="cBlue removeTr">清空</a></td>
            </tr>
            <tr>
            	<td colspan="4">
            		<a href="javascript:;" style="float: left" class="btn-white" id="add-row">添加一行</a>
			        <div class="tac"><a class="btn-blue submit">保存并上线</a></div>
            	</td>
            </tr>
            </tbody>
          </table>
          </form>
        </div>
      </li>
      <!-- 轮播图管理结束 -->
    </ul>
  </div>
</div>
<div class="pop-dialog" id="confirm-submit">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<p>是否确认修改？</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-link">确认</a>
			<a href="javascript:;" class="btn-b-white" id="confirm-cancel">取消</a>
		</div>
	</div>
</div>

<!-- 操作成功 -->
<div class="pop-dialog" id="success" <?php if ($saved) {?>style="display:block"<?php }?>>
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body tac">
			<p>恭喜你，操作成功！</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32" onClick="return closePop();">确认</a>
		</div>
	</div>
</div>
<div class="pop-dialog" id="no_complete" <?php if ($notfull) {?>style="display:block;left:10%;top:25%"<?php }?>>
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body tac">
			<p>上传内容请填写完整</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32" onClick="return closePop();">确认</a>
		</div>
	</div>
</div>

<style>
.mod0-tab-bd li{display:none}
</style>
<script>
$("a.submit").click(function(){
	$("#confirm-link").attr('data-form', $(this).parents('form').attr('id'));
	popdialog("confirm-submit");
})
$("#confirm-link").click(function(){
	$("#"+$(this).attr('data-form')).submit();
})
$("#confirm-cancel").click(function(){
	closePop();
})
$('#add-row').click(function(){
	$tr = $(this).parents('tbody').find('tr:eq(-2)');
	var p = parseInt($tr.data('index')+1);
	var act = $(this).parents('table').data('action');
	var str = '<tr data-index='+p+'><td><input class="ipt tac w40" type="text" value="" name="'+act+'['+p+'][priority]"></td>'
	str += '<td><input type="text" class="ipt tac w84" name="'+act+'['+p+'][title]"></td>'
	str += '<td><input type="text" class="ipt tac w264" name="'+act+'['+p+'][url]"></td>';
	str += '<td><a href="javascript:;" class="cBlue removeTr">清空</a></td></tr>';
	$tr.after(str)
})

$(".removeTr").click(function(){
	$(this).parents('tr').find('input[type=text]').val('');
})

</script>