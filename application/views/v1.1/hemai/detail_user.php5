<!-- 参与用户 -->
<input type="hidden" name="action" class="vcontent" value="user">
<input type="hidden" name="orderId" class="vcontent" value="<?php echo $orderId?>">
<input class="submit" type="hidden" />
<table class="mod-tableA">
	<thead>
		<tr>
			<th width="8%">序号</th>
			<th width="17%">用户名</th>
			<th width="22%">认购时间</th>
			<th width="20%">认购金额</th>
			<th width="20%">税后奖金</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($users as $k => $user) {?>
	<tr>
			<td><em class="fcw"><?php echo str_pad(($cpage -1) * $perPage + $k+1, 2, '0', STR_PAD_LEFT)?></em></td>
			<td><?php echo empty($user['uname']) ? "<span class='main-color-s'>166彩票（保底）</span>" : uname_cut($user['uname'], 2, 3);?></td>
			<td><?php echo $user['created']?></td>
			<td><?php echo number_format(ParseUnit($user['buyMoney'], 1))?>元</td>
			<td>
		<?php if ($user['status'] == 2000) {?>
		<em class="main-color-s"><?php echo number_format(ParseUnit($user['margin'], 1), 2)?></em>元
		<?php }elseif ($user['status'] == 1000) {echo '0.00';}else {echo '---';}?>
		</td>
			<td>&nbsp;</td>
		</tr>
<?php }?>
<!-- 参与用户 -->
	</tbody>
</table>
<!-- pagination -->
<?php echo $pagestr;?>
<!-- pagination end -->
<script>
$(function(){
	$('.mod-tab-hemai .iTips, .mod-tab-item[data-action=user] .total-prize-money em:first').html(<?php echo $num['num']?>);
	$('.mod-tab-item[data-action=user] .total-prize-money em:last').html('<?php echo number_format(ParseUnit($num['money'], 1))?>')
})
</script>