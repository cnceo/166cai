<div class="frame-container">
	<?php $this->load->view('template/breadcrumb');?>
	<div class="data-table-list mt20 table-no-border">
		<table>
			<colgroup>
				<col width="8%" />
				<col width="92%" />
			</colgroup>
			<tbody>
				<tr>
					<td class="tar"><label for="">编号：</label></td>
					<td class="tal pl10"><?php echo $data['shopNum']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">名称：</label></td>
					<td class="tal pl10"><?php echo $data['cname']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">彩种类别：</label></td>
					<td class="tal pl10"><?php echo $lotteryTypes[$data['lottery_type']]?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">电话：</label></td>
					<td class="tal pl10"><?php echo $data['phone']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">QQ：</label></td>
					<td class="tal pl10"><?php echo $data['qq']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">微信：</label></td>
					<td class="tal pl10"><?php echo $data['webchat']?></td>
				</tr>
				<tr>
					<td class="tar"><label for=""></label>其他联系方式：</td>
					<td class="tal pl10"><?php echo $data['other_contact']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">地址：</label></td>
					<td class="tal pl10"><label for=""><?php echo $data['address']?></label></td>
				</tr>
				<tr>
					<td class="tar"><label for="">创建时间：</label></td>
					<td class="tal pl10"><?php echo $data['created']?></td>
				</tr>
				<tr>
					<td class="tar"><label for="">附件：</label></td>
					<td class="tal pl10">
	         <?php
										if (! empty ( $files ))
										{
											foreach ( $files as $file )
											{
												?>
			         	<p>
							<a target="_blank" class="file_link"
								href="<?php echo $this->config->item('base_url')."/shop/download?filepath=".$file['filepath']?>"><?php echo $file['filename']?></a>
						</p>
			         <?php
											
}
										}
										?>
	         </td>
				</tr>
				<tr>
					<td class="tar"><label for="">状态：</label></td>
					<td class="tal pl10"><?php echo parse_shop_status($data['status'])?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if ($data['status'] < 20) {?>
		<div class="audit-detail-btns mt20 ml40">
		<a
			href="<?php echo $this->config->item('base_url')?>/shop/edit?id=<?php echo $data['id']?>"
			class="btn-blue">编辑</a>
	</div>
	<?php }?>
</div>