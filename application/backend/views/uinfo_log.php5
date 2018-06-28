<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">报表系统</a>&nbsp;&gt;&nbsp;<a href="/backend/User/">用户管理</a>&nbsp;&gt;&nbsp;<a href="">详情</a>&nbsp;&gt;&nbsp;<a href="">信息修改记录</a></div>
<div class="mod-tab mt20">
    <div class="data-table-list mt10">
		<table>
    		<colgroup><col width="20"><col width="20"><col width="20"><col width="20"><col width="20"></colgroup>
    		<thead><tr><th>修改条目</th><th>修改前内容</th><th>修改后内容</th><th>修改日期</th><th>修改途径</th></tr></thead>
    		<tbody>
    		<?php foreach ($data as $ulog) {?>
    		<tr>
        		<td><?php echo $typeArr[$ulog['type']]?></td>
        		<td><?php echo $ulog['cbefore']?></td>
        		<td><?php echo $ulog['cafter']?></td>
        		<td><?php echo $ulog['created']?></td>
        		<td><?php echo $placeArr[$ulog['place']]?></td>
    		</tr>
    		<?php }?>
    		</tbody>
		</table>
	</div>
</div>