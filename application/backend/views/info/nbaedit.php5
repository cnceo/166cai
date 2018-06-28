<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">资讯中心管理</a></div>
<div class="data-table-list mt10">
  <h2 class="team-part"><?php echo $teamName?></h2>
  	<form method="post" action="">
    <table>
      <colgroup>
        <col width="20%" />
        <col width="20%" />
        <col width="16%" />
        <col width="24%" />
        <col width="14%" />
        <col width="6%" />
      </colgroup>
      <thead>
        <tr>
          <th>球员</th>
          <th>位置</th>
          <th>更新时间</th>
          <th>伤情状态</th>
          <th>影响指数</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody id="inqury-table">
        <?php if (empty($data)) {?>
        <!-- 无伤病信息 -->
        <tr>
          <td colspan="6">全员健康</td>
        </tr>
        <!-- 无伤病信息 -->
        <?php }else {
        foreach ($data as $k => $dt) {?>
        <tr data-k="<?php echo $k?>">
          <td><input type="text" class="ipt w180 tac" name="info[<?php echo $k?>][name]" value="<?php echo $dt['name']?>"></td>
          <td><input type="text" class="ipt w80 tac" name="info[<?php echo $k?>][position]" value="<?php echo $dt['position']?>"></td>
          <td><input type="text" class="ipt w80 tac" name="info[<?php echo $k?>][updateTime]" value="<?php echo $dt['updateTime']?>"></td>
          <td><input type="text" class="ipt w222 tac" name="info[<?php echo $k?>][injury]" value="<?php echo $dt['injury']?>"></td>
          <td class="effect-rank">
          	<input type="hidden" name="info[<?php echo $k?>][team]" value="<?php echo $team?>">
          	<input type="hidden" name="info[<?php echo $k?>][indices]" class="indices" value="<?php echo $dt['indices']?>">
          	<?php for ($i = 1; $i <= 5; $i++) {?>
          		<span <?php if ($i <= $dt['indices']) {?>class="current"<?php }?> data-i="<?php echo $i?>"></span>
          	<?php }?>
          </td>
          <td><a href="javascript:;" class="cBlue removeTr">删除</a></td>
        </tr>
        <?php }
		}?>
      </tbody>
    </table>
    <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a>
    <div class="tac">
    	<input type="submit" name="submit" class="btn-blue mt20" value="保存">
    </div>
    </form>
</div>
<script>
$(function() {
var k = <?php echo count($data)?>;
	// 添加一行
	$('#add-row').click(function(){
		var str = '<tr><td><input type="text" class="ipt w180 tac" name="info['+k+'][name]"></td><td><input type="text" class="ipt w80 tac" name="info['+k+'][position]"></td>';
		str += '<td><input type="text" class="ipt w80 tac" name="info['+k+'][updateTime]"></td><td><input type="text" class="ipt w80 tac" name="info['+k+'][injury]"></td>';
		str += '<td class="effect-rank"><input type="hidden" name="info['+k+'][team]" value="<?php echo $team?>">';
		str += '<input type="hidden" name="info['+k+'][indices]" class="indices" value="1"><span class="current" data-i="1"></span><span data-i="2">';
		str += '</span><span data-i="3"></span><span data-i="4"></span><span data-i="5"></span></td><td><a href="javascript:;" class="cBlue removeTr">删除</a></td>';
		$('#inqury-table').append(str);
		k++;
	});

	// 删除操作
	$('.removeTr').live("click",function(){
		$(this).parent().parent('tr').remove();
	})

		// 评分点击
	$('.effect-rank span').live("click",function(){
		var index=$(this).index();
		$(this).addClass('current');
		$(this).prevAll().addClass('current');
		$(this).nextAll().removeClass('current');
		$(this).parents('.effect-rank').find('.indices').val($(this).data('i'));
	});

});
</script>