<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li class="current"><a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></li>
        </ul>
    </div>
    <div class="data-table-filter mt10">
        <form action="/backend/ChannelAnalysis/addChannelUser" method="post"  id="create_form">
        	<table>
        		<colgroup>
                    <col width="50"/>
                    <col width="140"/>
                </colgroup>
	            <tbody>
	            <tr>
	            	<th>渠道账号：</th>
	            	<td><?php echo $oneChannelUser['uname']; ?><a href="javascript:;" class="cBlue _modify_pwd ml10" data-id='<?php echo $oneChannelUser['id']?>' data-name='<?php echo $oneChannelUser['uname']; ?>'>重置账号密码</a></td>
	            </tr>
	            <tr>
	            	<th>备注内容：</th>
	            	<td><?php echo $oneChannelUser['mark']; ?></td>
	            </tr>
	            <tr>
	            	<th>渠道选择：</th>
	            	<td>
	            		<?php
	            		    $channelsArr = explode(',', $oneChannelUser['channels']);
	            		    foreach ($channels as $value) {
	            			    if (in_array($value['id'], $channelsArr)) {
	            		?>
	            		    <div>
	            		    	<select class="selectList" disabled="disabled">
	            				    <option><?php echo $value['name']; ?></option>
	            		        </select>
	            		    </div>
	            		<?php 
	            	            }
	            	        }
	            	    ?>
	            	</td>
	            </tr>
	            <tr>
	            	<th>展示字段：</th>
	            	<td>
	            		<?php
	            		$i = 1;
	            		foreach ($displayFields as $key => $value) {
	            			$checked = in_array($key, explode(",", $oneChannelUser["fields"])) ? 'checked' : '';
	            			echo '<div style="float:left;width:110px"><input type="checkbox" id="'.$key.'" value="'.$key.'" name="fields[]" '.$checked.' disabled="disabled"><label for="'.$key.'">'.$value.'</label></div>';
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
	            		注：<br />
	            		1、新用户购彩人数、新用户购彩总额、渠道购彩总人数，在合作商页面会自动乘以扣减比例展示；<br />
                        2、M版/PC端渠道，不涉及：结算新增字段，勾选后合作商页面展示为空；<br />
                        3、新增渠道账号/重置账号密码成功，会给对应邮箱发送账号、默认密码等信息；<br />
                        4、更新渠道账号权限时，不会发送邮件；<br />
	            	</td>
	            </tr>
	            </tbody>
        	</table>
        </form>   	
    </div>
</div>

<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
	$('._modify_pwd').click(function(){
		var username = "<?php echo $oneChannelUser['uname']; ?>";
		var userid = $(this).attr('data-id');
		layer.confirm('是否重置渠道密码：'+username+'(渠道账号)', {btn: ['确定', '取消'], title:'温馨提示', time:0},
            function(index) {
            	layer.close(index);
            	$.ajax({
		            type: "post",
		            url: "/backend/ChannelAnalysis/updateChannelUserPwd",
		            data: {'user_id':userid, 'user_name':username},
		            success: function(data)
		            {
		            	var json = jQuery.parseJSON(data);
		            	layer.closeAll();
		            	if(json.status == 'SUCCESSS')
		            	{
		            		layer.alert('恭喜您，操作成功', {icon: 1, btn:'', title:'温馨提示', time:0});
		            	} else {
		            		layer.alert(json.message, {icon: 2, btn:'', title:'温馨提示', time:0});
		            	}
		            }
		        });
            }
        );
    });
</script>
