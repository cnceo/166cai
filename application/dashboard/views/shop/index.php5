<div class="frame-container">
	<?php $this->load->view('template/breadcrumb');?>
	<div class="new-mod">
		<a href="<?php echo $this->config->item('base_url')?>/shop/edit"
			class="btn-blue">新增投注站</a>
	</div>
	<div class="data-table-filter mt10">
		<table>
			<colgroup>
				<col width="30%" />
				<col width="70%" />
			</colgroup>
			<tbody>
				<tr>
					<td>编号：<input type="text" id="sch_shopNum" class="ipt w140"
						value="<?php echo $shopNum;?>" />
					</td>
					<td><a href="javascript:void(0);" class="btn-blue ml25" id="sch">查询</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="data-table-list mt10">
		<table>
			<colgroup>
				<col width="10%" />
				<col width="15%" />
				<col width="10%" />
				<col width="10%" />
				<col width="10%" />
				<col width="25%" />
				<col width="10%" />
				<col width="10%" />
			</colgroup>
			<thead>
				<tr>
					<th>编号</th>
					<th>名称</th>
					<th>电话</th>
					<th>QQ</th>
					<th>微信</th>
					<th>地址</th>
					<th>状态</th>
					<th>详情</th>
				</tr>
			</thead>
			<tbody>
	    <?php foreach ($datas as $data) {?>
	      <tr>
					<td><?php echo $data['shopNum']?></td>
					<td><?php echo print_str($data['cname'])?></td>
					<td><?php echo $data['phone']?></td>
					<td><?php echo print_str($data['qq'])?></td>
					<td><?php echo print_str($data['webchat'])?></td>
					<td><?php echo $data['address']?></td>
					<td><?php echo parse_shop_status($data['status'])?></td>
					<td><a
						href="<?php echo $this->config->item('base_url')?>/shop/detail?id=<?php echo $data['id']?>"
						target="_blank" class="cBlue">查看</a></td>
				</tr>
	     <?php }?>
	    </tbody>
		</table>
	</div>
	<div class="stat mt10">
		<span class="ml20">本页共&nbsp;<?php echo min($per_page, count($datas))?>&nbsp;条</span> <span
			class="ml20">共&nbsp;<?php echo ceil($total['num']/$per_page)?>&nbsp;页</span>
		<span class="ml20">总计&nbsp;<?php echo $total['num']?>&nbsp;</span>
	</div>
	<div class="page mt10"><?php echo $pageStr?></div>
</div>
<script>
$("#sch").click(function(){
	location.href = "shop?shopNum="+$("#sch_shopNum").val();
})
</script>