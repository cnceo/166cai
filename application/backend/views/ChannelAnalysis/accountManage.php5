<?php $this->load->view("templates/head") ?>
<style  type="text/css">
    .w880 {
    	width: 880px;
    }
    .w110 {
    	width: 110px;
    }
</style>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
                <li class="current"><a href="/backend/ChannelAnalysis/accountManage">渠道账号管理</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd">
        	<ul>
        		<li style="display: list-item;">
        			<div class="data-table-filter mt10">
                    	<form action="/backend/ChannelAnalysis/accountManage" method="get" id="search_form" name="search_form">
	                        <table>
	                            <colgroup>
	                                <col width="190">
	                                <col width="190">
	                                <col width="190">
	                            </colgroup>
	                            <tbody>
	                                <tr>
	                                	<td>
	                                		<label for="">账号状态：
	                                			<select class="selectList w110" id="accountStatus" name="accountStatus">
	                                				<?php foreach ($channelAccountStatusArr as $key => $val):?>
	                                					<option value="<?php echo $key;?>" <?php if($search['accountStatus'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
	                                				<?php endforeach;?>
	                                			</select>
	                                		</label>
	                                	</td>
	                                	<td>渠道账号：
	                                		<input class="ipt w120" name="account" value="<?php echo $search['account']?>">
	                                	</td>
	                                	<td>
	                                        <a id="search" href="javascript:;" class="btn-blue ml10">查询</a>
	                                        <a href="/backend/ChannelAnalysis/addChannelUser" class="btn-blue ml10" target="_blank">新增渠道账号</a>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
                        </form>
                    </div>
                    <div class="data-table-list w880 mt10">
                    	<table id="accountManage">
                    		<thead>
                    			<tr>
                    				<th>渠道账号</th>
                    				<th>创建时间</th>
                    				<th>最后登录时间</th>
                    				<th>备注</th>
                    				<th>操作</th>
                    			</tr>
                    		</thead>
                    		<tbody>
                    			<?php foreach ($list as $val):?>
                    				<tr id="<?php echo $val['id'];?>">
                    					<td><?php echo $val['uname']; ?></td>
                    					<td><?php echo $val['created'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime($val['created'])) : ''; ?></td>
                    					<td><?php echo $val['last_login_time'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime($val['last_login_time'])) : ''; ?></td>
                    					<td><?php echo $val['mark']; ?></td>
                    					<td>
                    						<a href="<?php echo "/backend/ChannelAnalysis/channelUserDetail?id={$val['id']}";?>" class="cBlue" target="_blank">详情</a>
                    						<a href="<?php echo "/backend/ChannelAnalysis/modifyChannelUser?id={$val['id']}";?>" class="cBlue ml10" target="_blank">修改权限</a>
                    						<a href="javascript:;" class="cBlue ml10 switchAccount" data-status="<?php echo $val['status']; ?>">
                    							<?php echo $val['status'] == 1 ? '停用账号' : '启用账号' ?>
                    						</a>
                    					</td>
                    				</tr>
                    			<?php endforeach;?>
                    		</tbody>
                            <tfoot>
                                <tr >
                                    <td colspan="5">
                                        <div class="stat">
                                            <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                                            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                                            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                    	</table>
                        <div class="page mb20 order_info" >
                            <?php echo $pages[0] ?>
                        </div>
                    </div>
        		</li>
        	</ul>
        </div>
    </div>
</div>

<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
	$("#search").click(function(){
		$('#search_form').submit();
	});

	//停用和启用账号
	$(".switchAccount").click(function(){
		var res = 1;
		var defaultStatus = $(this).attr('data-status');
		var newStatus = defaultStatus == 1 ? 2 : 1;
		var text = newStatus == 1 ? '停用账号' : '启用账号';
		var accountId = $(this).parents('tr').attr('id');
		$.ajax({
            type: "post",
            async: false,
            url: '/backend/ChannelAnalysis/alterAccountStatus',
            data: {'accountId':accountId, 'newStatus':newStatus},
			dataType:"json",
            success: function (json) {
				if(json.status == 'y')
		    	{
		    		res = 2;
		    		layer.alert('恭喜您，操作成功', {icon: 1, closeBtn:0, title:'温馨提示', time:0}, function(){
		    			location.reload();
		    		});
		    	} else {
		    		layer.alert(json.message, {icon: 2, btn:'', title:'温馨提示', time:0});
		    	}
            }
        });
        if (res === 2) {
        	$(this).html(text);
        }
	});
</script>
