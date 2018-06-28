
<!-- 我的参与 -->
<input type="hidden" name="action" class="vcontent" value="my">
<input type="hidden" name="orderId" class="vcontent" value="<?php echo $orderId?>">
<input class="submit" type="hidden" />
<table class="mod-tableA">
<?php if (empty($orders)) {?>
		<tbody><tr><td colspan="6" class="no-data">您尚未购买该方案</a></td></tr></tbody>
	<?php }else {?>
	<thead><tr><th width="8%">序号</th><th width="17%">用户名</th><th width="22%">认购时间</th><th width="20%">认购金额</th><th width="20%">税后奖金</th><th>&nbsp;</th></tr></thead>
	<tbody>
		<?php foreach ($orders as $k => $order) {?>
		<tr>
			<td><em class="fcw"><?php echo str_pad(($cpage -1) * $perPage + $k+1, 2, '0', STR_PAD_LEFT)?></em></td>
			<td><?php echo $this->uinfo['uname']?></td>
			<td><?php echo $order['created']?></td>
			<td><?php echo number_format(ParseUnit($order['buyMoney'], 1), 2)?>元</td>
			<td>
			<?php if ($order['status'] == 2000) {?>
			<em class="main-color-s"><?php echo number_format(ParseUnit($order['margin'], 1), 2)?></em>元
			<?php }elseif ($order['status'] == 1000) {echo '0.00';}else {echo '---';}?>
			</td>
			<td>&nbsp;</td>
		</tr>
	<?php }?>
	</tbody>
</table>
<!-- pagination -->
<?php echo $pagestr;
}?>
<!-- pagination end -->
<!-- 我的参与 -->
