<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">五大联赛管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li class="current"><a href="javascript:;">五大联赛资讯管理</a></li>
    </ul>
  </div>
  <div class="mt20">
  	<div class="mod0-tab-hd">
		<ul class="clearfix">
		<?php foreach ($team as $k => $val) {?>
			<li <?php if ($k == $index){?> class="current"<?php }?>><a href="javascript:;" <?php if ($k == 4){?>class="nobdr"<?php }?>><?php echo $val?></a></li>
		<?php }?>
		</ul>
	</div>
  	<div class="mod0-tab-bd">
  		<ul>
  		<?php for ($j = 0; $j < 5; $j++) {?>
  			<li <?php if ($j == $index) {?>class="current"<?php }?>><!-- 数字彩开始 -->
			  	<div class="data-table-list mt10">
					<form action="/backend/fiveLeague/index" method="post" id="form<?php echo $j?>">
						<input type="hidden" name="index" value="<?php echo $j?>">
						<table>
							<colgroup><col width="10%" /><col width="30%" /><col width="50%" /><col width="10%" /></colgroup>
							<thead><tr><th>位置</th><th>标题（长度建议在<span class="cRed">9-10</span>个字之间）</th><th>URL链接</th><th>标红</th></tr></thead>
			                    <tbody>
			                      <?php foreach ($type as $p => $tp) {
			                      	for ($i = 1; $i <= 3; $i++) {?>
			                      		<tr>
					                        <td><?php echo $tp.$i?></td>
					                        <td><input type="text" class="ipt tac w222" name="wdls<?php echo $j.($p+1)."[".$i?>][title]" value="<?php echo $data['wdls'.$j.($p+1)][$i]['title']?>"></td>
					                        <td><input type="text" class="ipt tac w264" name="wdls<?php echo $j.($p+1)."[".$i?>][url]" value="<?php echo $data['wdls'.$j.($p+1)][$i]['url']?>"></td>
					                        <td><input type="checkbox" class="vam" name="wdls<?php echo $j.($p+1)."[".$i?>][redflag]" <?php if ($data['wdls'.$j.($p+1)][$i]['redflag'] == 1) {?>checked<?php }?>></td>
					                      </tr>
								  <?php }
							  		}?>
			                    </tbody>
							</table>
							<div class="tac mt20 mb20"><a type="submit" class="btn-blue mt20 submit">保存并预览</a>&nbsp;<a href="/backend/fiveLeague/onlinezx/<?php echo $j?>" class="btn-blue mt20">上线</a></div>
						</form>
					</div><!-- 数字彩结束 -->
				</li>
  			<?php }?>
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
</div>
<style>
.mod0-tab-bd li{display:none}
</style>
  <script>
  <?php if ($opennew) {?>
  window.open('/backend/fiveLeague/zixun/<?php echo $index?>');
  <?php }?>
  $('.mod0-tab-hd li').click(function(){
		$(this).addClass('current').siblings().removeClass('current');
		var _this=$(this).index();
		$('.mod0-tab-bd li').eq(_this).addClass('current').siblings().removeClass('current');
	})
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
  </script>