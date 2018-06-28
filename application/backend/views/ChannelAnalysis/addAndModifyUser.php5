<?php $this->load->view("templates/head") ?>
<?php
$checkedChannel = $oneChannelUser['channels'] ? explode(',', $oneChannelUser['channels']) : array();
?>
<style  type="text/css">
    textarea {
        width: 180px;
        height: 100px;
        border: 1px solid #c6ced6;
        /*vertical-align: top;*/
    }
    .h110 {
    	height: 100px;
    }
    .w180 {
    	width: 180px;
    }
    .w100 {
    	width: 100px !important;
    }
</style>
<div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li class="current"><a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></li>
        </ul>
    </div>
    <div class="data-table-filter mt10">
        <form action="/backend/ChannelAnalysis/<?=$id?'modifyChannelUser':'addChannelUser'?>" method="post"  id="create_form">
        	<table>
        		<colgroup>
                    <col width="50"/>
                    <col width="140"/>
                </colgroup>
	            <tbody>
	            <tr>
	            	<th>渠道账号：</th>
	            	<td>
	            		<?php if($id): ?>
	            		<?=$oneChannelUser['uname']?>
	            		<?php else: ?>
	            		<input class="ipt w130" name="uname" value="<?php echo $oneChannelUser['uname'] ?>">
	            	    <?php endif; ?>
	            		<span class="ml10">仅支持邮箱，用于登录使用</span>
	            	</td>
	            </tr>
	            <tr>
	            	<th>备注内容：</th>
	            	<td><textarea name="mark"><?php echo $oneChannelUser['mark']; ?></textarea></td>
	            </tr>
	            <tr>
	            	<th>渠道选择：</th>
	            	<td>
	            		已选择<span id="checknum"><?=count($checkedChannel)?></span>个<a href="javascript:;" class="select_channel ml10">编辑选择</a>
	            	</td>
	            </tr>
	            <tr>
	            	<th>展示字段：</th>
	            	<td>
	            		<?php
	            		$i = 1;
	            		foreach ($displayFields as $key => $value) {
	            			$checked = in_array($key, explode(",", $oneChannelUser["fields"])) ? 'checked' : '';
	            			echo '<div style="float:left;width:110px"><input type="checkbox" id="'.$key.'" value="'.$key.'" name="fields[]" '.$checked.'><label for="'.$key.'">'.$value.'</label></div>';
	            			if ($i++%3 == 0){
	            				echo '<br />';
	            			}
	            		}
	            		?>
	            	</td>
	            </tr>
	            <tr>
	            	<th></th>
	            	<td>
	            		<a id="<?php echo $id ? 'modify' : 'create'; ?>" class="btn-blue"><?php echo $id ? '修改账号' : '创建账号'; ?></a>
	            		<input type="hidden" name="id" value="<?php echo $id; ?>">
	            	</td>
	            </tr>
	            <tr>
	            	<th></th>
	            	<td>
	            		注：<br />
	            		1、新用户购彩人数、新用户购彩总额、渠道购彩总人数，在合作商页面会自动乘以扣减比例展示；<br />
                        2、M版/PC端渠道，不涉及：结算新增字段，勾选后合作商页面展示为空；<br />
                        3、新增渠道账号/重置账号密码成功，会给对应邮箱发送账号、默认密码等信息；<br />
                        4、更新渠道账号权限时，不会发送邮件；<br />
	            	</td>
	            </tr>
	            </tbody>
        	</table>
        	<div class="pop-mask" style="display:none"></div>
        	<div class="pop-dialog" id="channelPop" style="display: none;">
	            <div class="pop-in">
		            <div class="pop-head">
			            <h2>涉及渠道选择</h2>
			            <span class="pop-close cancel" title="关闭">关闭</span>
		            </div>
		            <div class="pop-body">
			            <div class="del-percent padding overflow-y">
				            <table>
					            <tbody>
						            <?php
						            $i = 0;
						            $channelCount = count($channels);
						            foreach ($channels as $k => $v){
						            	if($i == 0){
						            		echo '<tr><td><strong>按渠道选择:</strong></td><td class="tal"><input type="checkbox" name="channels[]" value="'.$v['id'].'" id="'.$v['id'].'"'.(in_array($v['id'], $checkedChannel) ? ' checked' : '').'><label for="'.$v['id'].'">'.$v['name'].'</label></td>';
						            	} elseif($i%2 == 0 && $i <= $channelCount - 1) {
						            		echo '<tr><td></td><td class="tal"><input type="checkbox" name="channels[]" value="'.$v['id'].'" id="'.$v['id'].'"'.(in_array($v['id'], $checkedChannel) ? ' checked' : '').'><label for="'.$v['id'].'">'.$v['name'].'</label></td>';
						            	} elseif($i == $channelCount - 1 && $i%2 == 0) {
						            		echo '<td></td></tr>';
						            	} else {
						            		echo '<td class="tal"><input type="checkbox" name="channels[]" value="'.$v['id'].'" id="'.$v['id'].'"'.(in_array($v['id'], $checkedChannel) ? ' checked' : '').'><label for="'.$v['id'].'">'.$v['name'].'</label></td></tr>';
						            	}
						            	$i++;
						            } ?>
		    			        </tbody>
		    		        </table>
		    	        </div>
		            </div>
		            <div class="pop-foot tac">
	        			<a href="javascript:;" class="btn-blue-h32 w100 mlr15 confirmChannel" data-index="">确认</a>
	        			<a href="javascript:;" class="btn-b-white cancel">关闭</a>
	        		</div>
	            </div>
            </div>
        </form>   	
    </div>
</div>
<script src='/caipiaoimg/src/layer/layer.js'></script>
<script>
	$("#create").click(function(){
		var username = $("input[name='uname']").val();
		if (username == '') {
			layer.alert('渠道账号不能为空', {icon:2, btn:'', title:'温馨提示', time:0});
			return;
		}
		layer.confirm('是否创建渠道账号：'+username+'(渠道账号)', {btn: ['确定', '取消'], title:'温馨提示', time:0},
            function(index) {
            	layer.close(index);
            	$.ajax({
		            type: "post",
		            url: "/backend/ChannelAnalysis/addChannelUser",
		            data: $('#create_form').serialize(),
		            success: function(data)
		            {
		            	var json = jQuery.parseJSON(data);
		            	layer.closeAll();
		            	if(json.status == 'SUCCESSS')
		            	{
		    	        	layer.alert('恭喜您，操作成功', {icon:1, closeBtn:0, title:'温馨提示', time:0}, function(){
		    	        		window.location.href = "/backend/ChannelAnalysis/accountManage";
		    	        	});
		    	        } else {
		    	        	layer.alert(json.message, {icon:2, btn:'', title:'温馨提示', time:0});
		    	        }
		            }
		        });
            }
        );
	});
	$("#modify").click(function(){
		$.ajax({
		    type: "post",
		    url: "/backend/ChannelAnalysis/modifyChannelUser",
		    data: $('#create_form').serialize(),
		    success: function(data)
		    {
		        var json = jQuery.parseJSON(data);
		        layer.closeAll();
		        if(json.status == 'SUCCESSS')
		        {
		    	    layer.alert('恭喜您，操作成功', {icon:1, closeBtn:0, title:'温馨提示', time:0}, function(){
		    	        window.location.href = "/backend/ChannelAnalysis/accountManage";
		    	    });
		    	} else {
		    	    layer.alert(json.message, {icon:2, btn:'', title:'温馨提示', time:0});
		    	}
		    }
		});
	});
	$(".select_channel").click(function(){
		popdialog("channelPop");
	});
	$(".confirmChannel").click(function(){
		var count = $("#channelPop").find("input[type='checkbox']:checked").length;
		$("#checknum").html(count);
		closePop();
	});
	$(".cancel").click(function(){
		var checkbox = $("#channelPop").find("input[type='checkbox']");
		checkbox.attr("checked", false); 
		checkbox.each(function(){
			var channels = <?php echo json_encode($checkedChannel); ?>; 
			if (isInArray(channels, $(this).val())) {
				$(this).attr("checked", true);
			}
		});
		var count = $("#channelPop").find("input[type='checkbox']:checked").length;
		$("#checknum").html(count);
		closePop();
	});
	function isInArray(arr, value){
        for(var i = 0; i < arr.length; i++){
            if(value === arr[i]){
                return true;
            }
        }
        return false;
    }
</script>
