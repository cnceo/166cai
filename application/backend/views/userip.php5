<?php $this->load->view("templates/head");?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">IP冻结</a></div>
<div class="mod-tab-hd mt20">
	<ul>
    	<li><a href="/backend/User">用户管理</a></li>
    	<li class="current"><a href="javascript:;">IP冻结</a></li>
	</ul>
</div>
<div class="data-table-filter mt10" style="width:1100px">
	<p><a href="javascript:;" class="btn-blue ml25 add">新增</a></p>
</div>
<div class="data-table-list mt20">
    <table>
        <colgroup>
            <col width="30" />
            <col width="120" />
            <col width="120" />
            <col width="130" />
            <col width="70" />
            <col width="70" />
            <col width="120" />
        </colgroup>
        <tbody>
            <tr>
                <th>序号</th>
                <th>新增时间</th>
                <th>登录IP</th>
                <th>所在地区</th>
                <th>涉及用户数</th>
                <th>操作</th>
                <th>删除时间</th>
            </tr>
            <?php foreach ($data as $ip) {?>
            <tr data-id="<?php echo $ip['id']?>">
            	<td><?php echo $ip['id']?></td>
            	<td><?php echo $ip['created']?></td>
            	<td><?php echo $ip['ip']?></td>
            	<td><?php echo $ip['address']." ".$ip['operator']?></td>
            	<td><?php echo $ip['num']?></td>
            	<td><?php if ($ip['status'] == 1) {echo '已删除';} else {?><a href="javascript:;" class="del">删除</a><?php }?></td>
            	<td><?php echo $ip['delete_time']?></td>
            </tr>
            <?php }?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">
                    <div class="stat">
                        <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                        <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                        <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="page mt10">
   <?php echo $pages[0] ?>
</div>
<!-- 新增 -->
<div class="pop-dialog" id="J-dc-add">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">新增IP</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="100" /><col width="280" /></colgroup>
                    <tbody><tr><th>新增IP</th><td><input type="text" class="ipt w184" id="input_add_ip"></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='sub_add'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<!-- 删除 -->
<div class="pop-dialog" id="J-dc-del">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">删除确认</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup><col width="280" /></colgroup>
                    <tbody><tr><td>确认删除IP</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='sub_del'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<script>
var delid = 0;
$('.data-table-filter').on('click', '.add', function(){
	popdialog("J-dc-add");
})
$('.data-table-list').on('click', '.del', function(){
	delid = $(this).parents('tr').data('id');
	$('#J-dc-del').find('.pop-body tbody td:first').html('确认删除IP'+$(this).parents('tr').find('td:eq(2)').html()+'吗？');
	popdialog("J-dc-del");
})
$('#sub_add').click(function(){
	$.ajax({
        type: 'post',
        url:  '/backend/User/addip',
        data: {ip:$('#input_add_ip').val()},
        dataType : 'json',
        success: function(response) {
            if(response.status == 'y'){
                alert(response.message);
                location.reload();
            }else{
                alert(response.message);
            }
        }
    });
})
$('#sub_del').click(function(){
	$.ajax({
        type: 'post',
        url:  '/backend/User/delip',
        data: {id:delid},
        dataType : 'json',
        success: function(response) {
            if(response.status == 'y'){
                alert(response.message);
                location.reload();
            }else{
                alert(response.message);
            }
        }
    });
})
</script>