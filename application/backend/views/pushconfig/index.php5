<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Apppush/index">推送管理</a></div>
<style type="text/css">
	.selectList {
	    color: rgb(153, 153, 153);
	    white-space: nowrap;
	    padding: 1px 5px;
	}
</style>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/Apppush/index">购彩提醒</a></li>
    	</ul>
  	</div>
    <div class="mod-tab-bd">
	    <ul>
	      	<li style="display: block">
	      		<form action="" method="post" id="select_form">
		      		<div class="data-table-filter mt10">
	          			<table>
	                        <colgroup>
	                            <col width="200">
	                            <col width="800">
	                        </colgroup>
	                        <tbody>
	                            <tr>
	                                <td>
	                                    彩种：
	                                    <select class="selectList w108" id="select_lid" name="lid">
	                                        <?php foreach ($lidArr as $key => $name):?>
	                                            <option value="<?php echo $key; ?>" <?php if($lid == $key){echo 'selected';} ?> ><?php echo $name; ?></option>
	                                        <?php endforeach;?>
	                                    </select>
	                                </td>
	                                <td><span style="color: red;">程序将在<b>每天下午六点</b>统一处理明天符合条件的推送配置信息，请提前确认保存和删除操作</span>
	                                </td>                   
	                            </tr>
	                        </tbody>
	                    </table>
	        		</div>
        		</form>
	    		<form action="" method="post" id="config_form">
	    			<div class="data-table-list mt10">
	          			<table>
	            			<colgroup>
	              				<col width="15%" />
	              				<col width="25%" />
	              				<col width="40%" />
	              				<col width="10%" />
	              				<col width="10%" />
	            			</colgroup>
		            		<thead>
			              		<tr>
			                		<th>推送日</th>
			                		<th>推送标题</th>
			                		<th>推送内容</th>
			                		<th>状态</th>
			                		<th>操作</th>
			              		</tr>
		            		</thead>
	            			<tbody id="config-table">
	            				<?php 
	            					$counts = count($configs) > 5 ? count($configs) : 5;
	            					for ($i = 0; $i < $counts; $i++) { 
	            						$selectWeek = $configs[$i]['week'] >= 0 ? $configs[$i]['week'] : 1;
	            				?>
	            				<tr>
	              					<td>
	                					<select class="selectList w108" id="select_lid" name="info[<?php echo $i?>][week]">
	                                        <?php foreach ($weekArr as $key => $name):?>
	                                            <option value="<?php echo $key; ?>" <?php if($key == $selectWeek){echo 'selected';} ?> ><?php echo $name; ?></option>
	                                        <?php endforeach;?>
	                                    </select>
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w222" name="info[<?php echo $i?>][title]" value="<?php echo $configs[$i]['title']?>" maxlength="50">
	              					</td>
	              					<td>
	                					<input type="text" class="ipt tac w390" name="info[<?php echo $i?>][content]" value="<?php echo $configs[$i]['content']?>" maxlength="128">
	              					</td>
	              					<td>
	              						<select class="selectList w64" name="info[<?php echo $i?>][status]">
	              							<option value="0" <?php if($configs[$i]['status'] == 0){echo 'selected';} ?> >关闭中</option>
	              							<option value="1" <?php if($configs[$i]['status'] == 1){echo 'selected';} ?> >开启中</option>
	              						</select>
	              					</td>
	              					<td>
	              						<input type="hidden" class="ipt tac w40" name="info[<?php echo $i?>][send_time]" value="<?php echo $configs[$i]['send_time'] ? $configs[$i]['send_time'] : '0000-00-00 00:00:00'; ?>">
	                					<a href="javascript:;" class="cBlue removeTr">删除</a>
	              					</td>
	            				</tr>
	            				<?php }?>
	            			</tbody>
	          			</table>
	          			<a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a>
	          			<div class="tac">
	          				<a class="btn-blue mt20 submit">保存</a>
	          			</div>
	          		</div>
	          		<input type="hidden" name="lid" value="<?php echo $lid; ?>">
	       		</form>
	       	</li>
	    </ul>
    </div>
</div>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div>是否确认修改？</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a>
			<a href="javascript:;" class="btn-b-white mlr15 config-submit">确认</a>
		</div>
	</div>
</div>
<script>
	$(function() {
		var lid = $('input[name="lid"]').val();
		var k = <?php echo count($configs) > 5 ? count($configs) : 5; ?>;
		// 添加一行
		$('#add-row').click(function(){
			var str = '<tr>';
			if(lid == '51'){
				str += '<td><select class="selectList w108" id="select_lid" name="info['+k+'][week]"><option value="0">星期日</option><option value="2">星期二</option><option value="4">星期四</option></select></td>';
			}else{
				str += '<td><select class="selectList w108" id="select_lid" name="info['+k+'][week]"><option value="1" selected="">星期一</option><option value="3">星期三</option><option value="6">星期六</option></select></td>';
			}	
			str += '<td><input type="text" class="ipt tac w222" name="info['+k+'][title]" value="" maxlength="50"></td>';
			str += '<td><input type="text" class="ipt tac w390" name="info['+k+'][content]" value="" maxlength="128"></td>';
			str += '<td><select class="selectList w64" name="info['+k+'][status]"><option value="0">关闭中</option><option value="1" selected>开启中</option></select></td>';
			str += '<td><a href="javascript:;" class="cBlue removeTr">删除</a></td>';
			str += '</td>';
			$('#config-table').append(str);
			k++;
		});

		// 删除操作
		$('.removeTr').live("click",function(){
			$(this).parent().parent('tr').remove();
		})

		// 选择彩种
		$("#select_lid").change(function(){
			$("#select_form").submit();
		})

		// 保存提示
		$(".submit").click(function(){
			popdialog("alertPop");
		})

		// 确认
		$(".config-submit").click(function(){
			$("#config_form").submit();
		});
	});
</script>
