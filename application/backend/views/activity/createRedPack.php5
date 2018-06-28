<?php $this->load->view("templates/head");?>
<?php
 $cate = array(
	        '101'=>'通用',
	        '102'=>'竞彩',
	        '103'=>'数字彩',
	        '104'=>'高频彩',
	        '105'=>'双色球',
	        '106'=>'大乐透',
	        '107'=>'福彩3D',
	        '108'=>'排列三',
	        '109'=>'排列五',
	        '110'=>'七乐彩',
	        '111'=>'七星彩',
	        '112'=>'竞足',
	        '113'=>'竞篮',
	        '114'=>'胜负彩',
	        '115'=>'任选九',
	        '116'=>'惊喜11选5',
	        '117'=>'老11选5',
	        '118'=>'新11选5', 
	        '119'=>'经典快3', 
	        '120'=>'快乐扑克',
	        '121'=>'老时时彩',
 			'122'=>'易快3',
            '123'=>'红快3',
            '124'=>'乐11选5',
        );
?>
<style type="text/css">
	._create_pack_a{color:#0099FF;}
	._hide_pack_a{color:#0099FF;}
	._tip_table{width: 500px;height: 200px;overflow: hidden;}
	._tip_table ul{margin-left: 30px;margin-top:30px;}
	._tip_table ul li {height: 36px;line-height: 36px;font-size: 13px;} 
	._tip_table ul li ._normal_input{width: 70px;border:1px solid #ccc;text-indent: 5px;height: 25px;line-height: 25px;}
	._create_gc_box{display: none;}
	._create_cz_box{display: none;}
	._hide_cz{height: 160px;}
	.yc_box{display: none;}
	.yc_ul{display:block;width:530px;margin-left: 30px;margin-top:20px;}
	.yc_ul li{display:block;float: left;line-height: 25px;margin-right: 2px;}
	.yc_ul li input{}
	.tab_box{width: 1000px;height: 40px;border-bottom:1px solid #ccc;margin-top:30px;}
	.tab_box a{display: block;width:100px;height: 40px;line-height: 40px;text-decoration: none;float: left;text-align: center;border:1px solid #ccc;background: #F9F9F9;margin-right: 5px;border-radius: 3px 3px 0px 0px;border-bottom: none;}
	.tab_box a.active{background: #5CA3E6;color: #fff;font-weight: bold;}
	.tab_box a:hover{background: #5CA3E6;color: #fff;font-weight: bold;}
</style>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
 $(function(){
 	//新增购彩 充值
 	$('._create_pack_a').click(function(){
 		var className = '.'+ $(this).attr('data-to');
	 	layer.open({
	 	  'title':'新增红包',
		  'type': 1,
		  'area': '504px;',
		  'closeBtn': 1, //不显示关闭按钮
		  'btn': ['新增红包', '取消'],
		  'shadeClose': true, //开启遮罩关闭
		  'content': $(className).html(), 
		  'btnAlign': 'c',
		  'yes': function()
		  	{
			  	var tag = validForm();
			  	if(tag[0] == false){
			  		layer.alert(tag[1], {icon: 2,btn:'',title:'温馨提示',time:0});
			  	}else{
				  	//发送插入红包
				  	ajaxStoreRedPack();
				  	layer.load(0, {shade: [0.5, '#393D49']});			  		
			  	}

	  	 	}
		});
 	});
 	//隐藏操作
 	$('._hide_pack_a').click(function(){
 		var className = '.'+ $(this).attr('data-to');
	 	layer.open({
	 	  'title':'隐藏红包',
		  'type': 1,
		  'area': '560px;',
		  'closeBtn': 1, //不显示关闭按钮
		  'btn': ['确认隐藏', '取消'],
		  'shadeClose': true, //开启遮罩关闭
		  'content': $(className).html(), 
		  'btnAlign': 'c',
		  'yes': function()
		  	{
		  		ajaxHideRedPack();
		  		layer.load(0, {shade: [0.5, '#393D49']});
	  		}
		});	
 	});
 	/**
 	 * [ajaxStoreRedPack 异步红包添加]
 	 * @author JackLee 2017-03-22
 	 * @return {[type]} [description]
 	 */
 	function ajaxStoreRedPack()
 	{
		$.ajax({
		    type: "post",
		    url: "/backend/Activity/storeRedPack",
		    data: $('.layui-layer-content form').serialize(),
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
 	}
 	/**
 	 * [validForm 条件验证合法]
 	 * @author JackLee 2017-03-28
 	 * @return {[type]} [description]
 	 */
 	function validForm()
 	{
 		var money = $('.layui-layer-content form').find('input[name=money]').val();
 		var money_bar = $('.layui-layer-content form').find('input[name=money_bar]').val();
 		var days = $('.layui-layer-content form').find('input[name=days]').val();
 		var preg =  /^[0-9]*[1-9][0-9]*$/;
 		var return_array =  new Array();
 		if(!preg.test(money_bar) || !preg.test(money_bar) || !preg.test(money_bar))
 		{
 			return_array[0] = false;
 			return_array[1] = '必要参数缺失或输入格式错误';
 			return return_array;
 		}
 		else if(parseInt(money_bar)>=10000 && !/^[1-9]\d*$/.test(parseInt(money_bar)/10000))
 			{
	 			return_array[0] = false;
	 			return_array[1] = '红包金额大于1万必须为万元整数倍';
	 			return return_array;
 			}
 		else if(parseInt(money)>1000)
 			{
	 			return_array[0] = false;
	 			return_array[1] = '赠送金额不能大于1000元';
	 			return return_array;
 			}
 		else if(parseInt(money_bar) < parseInt(money))
 			{
	 			return_array[0] = false;
	 			return_array[1] = '红包赠送金额大于红包金额';
	 			return return_array;
 			} 
 		else if(parseInt(days) <= 0 ) 
 			{
	 			return_array[0] = false;
	 			return_array[1] = '红包有效期必须大于0';
	 			return return_array;
 			}
 		else{
 				return_array[0] = true;
 				return return_array;
 		   } 
 	}
 	/**
 	 * [ajaxHideRedPack 异步隐藏红包]
 	 * @author JackLee 2017-03-22
 	 * @return {[type]} [description]
 	 */
 	function ajaxHideRedPack()
 	{
		$.ajax({
		    type: "post",
		    url: "/backend/Activity/hideRedPack",
		    data: $('.layui-layer-content form[name=yc_form]').serialize(),
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
 	}

 });
</script>
<div class="path">您的位置：运营活动&nbsp;>&nbsp;<a href="/backend/Activity/listRedpack">红包管理</a></div>
<div class='tab_box'>
	<a href="<?php echo '/backend/Activity/listRedpack'; ?>">概览</a>
	<a href="<?php echo '/backend/Activity/createRedPack'; ?>" class='active'>新增红包</a>
	<a href="<?php echo '/backend/Activity/pullRedPack'; ?>">红包派发</a>
</div>
<div class="data-table-list mt20">
  <table>
    <thead>
	    <tr>
	      <th>红包类型</th>
	      <th>现有红包</th>
	      <th>操作</th>
	    </tr>
    </thead>
    <tbody>
	    <tr>
	    	<td>购彩红包</td>
	        <td style="text-align: left;padding-left: 3px;padding-right: 3px;">
	        	<?php $str =''; ?>
				<?php foreach ($result as $k => $v): ?>
				<?php if ($v['p_type'] == 3): ?>
					<?php
						if($v['hidden_flag']==0)
						{
							$days = json_decode($v['use_params'],true);
							$days = $days['end_day'] - $days['start_day'];
						    $str .= $v['use_desc'].'（'.$v['c_name'].'，'.$days.'天），';
						}

					?>
				<?php endif; ?>
				<?php endforeach; ?>
				<?php echo trim($str,'，')?>
	        </td>
	        <td><a href="javascript:;" class='_create_pack_a _gz_a' data-to='_create_gc_box'>新增红包</a>&nbsp;&nbsp;<a href="javascript:;" class='_hide_pack_a' data-to='yc_gc'>隐藏红包</a></td>
	    </tr>
	    <tr>
	    	<td>充值红包</td>
	        <td style="text-align: left;padding-left: 3px;padding-right: 3px;">
				<?php foreach ($result as $k => $v): ?>
				<?php if ($v['p_type'] == 2): ?>
					<?php 
					if($v['hidden_flag']==0)
					{
						 $days = json_decode($v['use_params'],true);
						 $days = $days['end_day'] - $days['start_day'];
						 echo $v['use_desc'].'（'.$days.'天）';
					}
					?>
				<?php endif; ?>
				<?php endforeach; ?>
	        </td>
	        <td><a href="javascript:;" class='_create_pack_a _cz_a' data-to='_create_cz_box'>新增红包</a>&nbsp;&nbsp;<a href="javascript:;" class='_hide_pack_a' data-to='yc_cz'>隐藏红包</a></td>
	    </tr>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
</div>
<!--购彩-->
<div class='_create_gc_box'>
	<div class='_hide_gc _tip_table'>
	  <form name='_hide_gc' id='_hide_gc'>
	   <input type="hidden" name="p_type" value="3" />
	   <ul>
		<li>
			<span class="_left_span">红包类型：</span>
			<span>购彩红包</span>
		</li>
		<li>
			<span>针对彩种：</span>
			<span>
				<select name='c_type'>
				<?php foreach ($cate as $k => $v): ?>
					<option value="<?php echo $k ;?>"><?php echo $v ;?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</li>
		<li>
			<span>满：<input type="text" name="money_bar" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')"></span>
			<span>减：<input type="text" name="money" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')"></span>
		</li>
		<li>
			<span>有效期：<input type="text" name="days" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')" > 天</span>
			<span><input name="ismobile_used" type="checkbox" value="1" />客户端专享</span>
		</li>
	   </ul>
	  </form>
	</div>
</div>
<!--充值-->
<div class='_create_cz_box'>
	<div class='_hide_cz _tip_table'>
	    <form name='_hide_cz' >
	       <input type="hidden" name="p_type" value="2" />
		   <ul>
			<li>
				<span class="_left_span">红包类型：</span>
				<span>充值红包</span>
			</li>
			<li>
				<span>充：<input type="text" name="money_bar" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')"/></span>
				<span>送：<input type="text" name="money" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')"/></span>
			</li>
			<li>
				<span>有效期：<input type="text" name="days" class='_normal_input' onkeyup="this.value=this.value.replace(/\D/g,'')"/> 天</span>
				<span><input name="ismobile_used" type="checkbox" value="1" />客户端专享</span>
			</li>
		   </ul>
		 </form>
	</div>
</div>
<!--隐藏模块-->
<div class='yc_box yc_gc'>
	<form name='yc_form'>
		<input type="hidden" name="p_type" value="3" />
		<ul class='yc_ul'>
		<?php foreach ($result as $k => $v): ?>
			<?php if ($v['p_type'] == 3): ?>
			<li>
			<input type="checkbox" value="<?php echo $v['id'] ;?>" name='red_pack_id[]' <?php echo empty($v['hidden_flag']) ? '' : 'checked="true"'; ?>/>
			<?php 
			$days = json_decode($v['use_params'],true);
			$days = $days['end_day'] - $days['start_day'];
			echo $v['use_desc'].'（'.$v['c_name'].'，'.$days.'天'.(empty($v['ismobile_used'])?'':'，客户端专享').'）'; ?>
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</form>		
		</ul>
	</form>
</div>
<!--充值隐藏-->
<div class='yc_box yc_cz'>
	<form name='yc_form'>
		<input type="hidden" name="p_type" value="2" />
		<ul class='yc_ul'>
		<?php foreach ($result as $k => $v): ?>
			<?php if ($v['p_type'] == 2): ?>
			<li>
			<input type="checkbox" value="<?php echo $v['id'] ;?>" name='red_pack_id[]' <?php echo empty($v['hidden_flag']) ? '' : 'checked="true"'; ?> />
			<?php 
			$days = json_decode($v['use_params'],true);
			$days = $days['end_day'] - $days['start_day'];
			echo $v['use_desc'].'（'.$days.'天'.(empty($v['ismobile_used'])?'':'，客户端专享').'）';; ?>
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	</form>
</div>  
</body>
</html>