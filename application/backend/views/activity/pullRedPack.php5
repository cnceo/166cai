<?php $this->load->view("templates/head");?>
<style type="text/css">
  .tab_box{width: 1000px;height: 40px;border-bottom:1px solid #ccc;margin-top:30px;}
  .tab_box a{display: block;width:100px;height: 40px;line-height: 40px;text-decoration: none;float: left;text-align: center;border:1px solid #ccc;background: #F9F9F9;margin-right: 5px;border-radius: 3px 3px 0px 0px;border-bottom: none;}
  .tab_box a.active{background: #5CA3E6;color: #fff;font-weight: bold;}
  .tab_box a:hover{background: #5CA3E6;color: #fff;font-weight: bold;}
  .form_box{width:1000px;height:450px; margin-top:30px;}
  .form_box table.data-table-list{width: 1000px;}
  .form_box table.data-table-list tr.tr1{ height: 50px; }
  .form_box table.data-table-list tr.tr2{ height: 150px; }
  .form_box table.data-table-list tr td.left_td{width: 15%;}
  .sub_btn{display: inline-block;height: 30px;width: 100px;border-radius: 5px;line-height: 30px;text-align: center;background:#5CA3E6;color: #fff;text-decoration: none;font-weight: bold;}
  .sub_btn:hover{color: #fff;text-decoration: none;font-weight: bold;}
  .form_box table.data-table-list tr td.right_td{text-align: left;text-indent: 20px;}
  .form_box table.data-table-list tr.tr3 td{text-align: center;text-indent: 450px;}
  .normal_input{width:140px;height: 24px;border:1px solid #ccc;outline: none;text-indent: 5px;}
  .msg_box{width: 800px;height: 140px;border:1px solid #ccc;line-height: 15px;padding: 5px;resize:none}
  .list_box{width: 830px;height: auto;text-indent: 0px;margin-left: 20px;overflow:hidden; }
  .list_box h2{}
  .list_box ul li{display: block;width: auto;height:25px;float: left;padding-right: 5px;position: relative;}
  .list_box ul li input.red_pack_id{position: absolute;top:4px;left:0px;}
  .list_box ul li input.red_pack_num{border:1px solid #ccc;width: 30px;margin-left: 3px;text-indent: 3px;margin-right: 5px;text-align: center;}
  .list_box ul li span{padding-left: 15px;cursor: pointer;}
  .pull_type{position: relative;}
  .pull_type input{position: relative;top:3px;}
  .pull_type span{padding-left: 5px;cursor: pointer;}
</style>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
$(function(){
	var href = window.location.href;
	$('.pull_type input').click(function(){
		var index = $(this).index('.pull_type input');
		$('.hide_input').addClass('hide');
		$('.hide_input').eq(index).removeClass('hide');
		if(index == 1) 
			$('.form_box tbody .tr1:eq(1) td:first').html('上传批量文件：')
		else 
			$('.form_box tbody .tr1:eq(1) td:first').html('用户名：')
	});
	$('.pull_type span').click(function(){
		var index = $(this).index('.pull_type span');
		$('.pull_type input').eq(index).trigger("click");
	});
	//验证提交
	$('.sub_btn').click(function(){

		var pull_type = $('input[name=pull_type]:checked').val();
		var len = $('.red_pack_id:checked').length;
		//var userTag = true;
		if(pull_type == 0)
		{
			if($('input[name=user]').val() == '')
			{
				layer.alert('用户名不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0});
				return ;
			}
		}else{
			if($('input[name=usersFile]').val() == '')
			{
				layer.alert('请选择文件csv格式文件', {icon: 2,btn:'',title:'温馨提示',time:0});
				return ;
			}
		}
		if(len == 0)
		{
			layer.alert('请选择红包~', {icon: 2,btn:'',title:'温馨提示',time:0});
			return ;
		}
		$tag = 1;
		$('.red_pack_num').each(function(){
			if($(this).val() > 99 || $(this).val() < 1){ $tag = 0 ; return ;}
		});
		if($tag == 0)
		{
			layer.alert('红包个数应该在1-99之间~', {icon: 2,btn:'',title:'温馨提示',time:0});
			return ;
		}
		$('form[name=pullRedPack]').submit();
		
	});
	//上传格式验证
	$('input[name=usersFile]').change(function(){
        var filepath = $(this).val();
        var extStart = filepath.lastIndexOf(".");
        var ext = filepath.substring(extStart, filepath.length).toUpperCase();
        if(ext != ".CSV"){$('input[name=usersFile]').val('');layer.alert('上传文件的格式必须是CSV格式', {icon: 2,btn:'',title:'温馨提示',time:0});}
	});
	//点击事件
	$('.list_box ul li span').click(function(){
		$(this).parent().find('input').trigger("click");
	});
	function checkUser(search)
	{
		$.ajax({
		    type: "post",
		    url: "/backend/Activity/ajaxCheckUser",
		    data: {'user':search},
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	if(json.status == 'ERROR')
		    	{
		    		userTag = false;
		    		layer.alert('派发用户不存在或被冻结注销', {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
	}
	<?php if(isset($_GET['error']) && !empty($_GET['error'])): ?>
	  var url = window.location.href;
	  var arr = url.split('?');	
	  var icon = <?php echo isset($_GET['flag']) && $_GET['flag'] == 1 ? 1: 2; ?>;
	  layer.alert('<?php echo $_GET['error'];?>', {icon: icon,btn:'',title:'温馨提示',time:0,end:function(){window.location.href=arr[0]}});
	<?php endif; ?>   
});
</script>
<div class="path">您的位置：运营活动&nbsp;>&nbsp;<a href="/backend/Activity/listRedpack">红包管理</a></div>
<div class='tab_box'>
	<a href="<?php echo '/backend/Activity/listRedpack'; ?>">概览</a>
	<a href="<?php echo '/backend/Activity/createRedPack'; ?>" >新增红包</a>
	<a href="<?php echo '/backend/Activity/pullRedPack'; ?>" class='active'>红包派发</a>
</div>
<div class='form_box'>
	<form name="pullRedPack" action="/backend/Activity/storePullRedPack" method="post" enctype="multipart/form-data">
		<table class="data-table-list mytable">
			<tr class='tr1'>
				<td class='left_td'>派送类型：</td>
				<td class="right_td pull_type"> 
					<input type="radio" name="pull_type" value="0" checked="true" class='pull_type_radio' /><span>单个用户</span>
					<input type="radio" name="pull_type" value="1" class='pull_type_radio' /><span>批量派发</span>
				</td>
			</tr>
			<tr class='tr1'>
				<td class='left_td'>用户名：</td>
				<td class="right_td">
					<input type="text" name='user' class='normal_input hide_input' />
					<div class="hide_input hide"><input type="file" name="usersFile" class='usersFile'/>（CSV文件模板：UID，手机号）</div>
				</td>
			</tr>
			<tr class='tr2'>
				<td class='left_td'>选择红包：</td>
				<td class="right_td">
					<div class='list_box'>
						<h2>购彩红包：</h2>
						<ul>
						<?php foreach ($result as $k => $v): ?>
							<?php if ($v['p_type'] == 3 && $v['hidden_flag'] == 0): ?>
							<li><input type="checkbox" name="red_pack_id[]" class="red_pack_id" value="<?php echo $v['id'] ;?>"><span><?php 
								$days = json_decode($v['use_params'],true);
								$days = $days['end_day'] - $days['start_day'];
								echo $v['use_desc'].'，'.$v['c_name'].'，'.$days.'天'.(empty($v['ismobile_used'])?'':'，客户端专享'); ?></span><input type="text" name="pack_num_<?php echo $v['id'] ;?>" class='red_pack_num' value="1"  onkeyup="this.value=this.value.replace(/\D/g,'')" />个</li>
							<?php endif; ?>
						<?php endforeach; ?>
						</ul> 
					</div>
					<div class='list_box'>
						<h2>充值红包：</h2>
						<ul>
						<?php foreach ($result as $k => $v): ?>
							<?php if ($v['p_type'] == 2 && $v['hidden_flag'] == 0): ?>
							<li><input type="checkbox" name="red_pack_id[]" class="red_pack_id" value="<?php echo $v['id'] ; ?>"><span><?php 
							$days = json_decode($v['use_params'],true);
							$days = $days['end_day'] - $days['start_day'];
							echo $v['use_desc'].'，'.$days.'天'.(empty($v['ismobile_used'])?'':'，客户端专享'); ?></span><input type="text" name="pack_num_<?php echo $v['id'] ;?>" class='red_pack_num' value="1"  onkeyup="this.value=this.value.replace(/\D/g,'')" />个</li>
							<?php endif; ?>
						<?php endforeach; ?>
						</ul>
					</div>
				</td>
			</tr>

            <tr class='tr1'>
                <td class='left_td'>生效日:</td>
                <td class="right_td">
                    <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo date('Y-m-d',time()) ?>" class="Wdate1" />（默认为当日生效）<i></i></span>
                </td>
            </tr>

			<tr class='tr2'>
				<td class='left_td'>通知消息：（短信）</td>
				<td class="right_td">
					<textarea class='msg_box' name='message'></textarea>
                                        <br>
                                        <span style="margin-left:20px;">短信模板：【166彩票】尊敬的扯蛋先生（用户名）您好，XXXXXXXXXX（输入框编辑）下载APP：t.cn/R9SyzIp </span>
				</td>
			</tr>
			<tr clas='tr3'>

				<td colspan="2"><a href="javascript:;" class='sub_btn'>提交</a></td>
			</tr>
		</table>
	</form>	
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(".Wdate1").focus(function(){
        dataPicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'});
    });
</script>
</body>
</html>