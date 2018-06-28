<?php if(!$this->is_ajax):?>
<?php $this->load->view('v1.1/elements/user/menu');?>
<script type="text/javascript" src="/caipiaoimg/src/date/WdatePicker.js"></script>
<div class="l-frame-cnt withdrawals-form">
<div class="uc-main">
    <input type='hidden' class='submit' data-target='/mylottery/withdrawals' name='action' value=''>
	<div class="tab-nav">
		<ul class="clearfix">
			<li><a href="/mylottery/detail"><span>交易明细</span></a></li>
			<li><a href="/mylottery/recharge"><span>充值记录</span></a></li>
			<li class="active"><a href="/mylottery/withdrawals"><span>提现记录</span></a></li>
		</ul>
	</div>
	<div class="tab-content">
		<div class="tab-item" style="display:block;">
			<!--表单筛选 begin-->
			<div class="filter-oper">
				<div class="lArea">
					<span>交易时间：</span>
                    <input class="Wdate vcontent start_time" id="startDate" type="text" value="<?php echo date('Y-m-d', strtotime( '-1 month' ));?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'endDate\',{y:-1})&&\'2014\'}',maxDate:'#F{$dp.$D(\'endDate\')||\'%y-%M-%d\';}',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_from"/>
			        <span class="mlr10">至</span>
                    <input class="Wdate vcontent end_time" id="endDate" type="text" value="<?php echo date('Y-m-d'); ?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'startDate\');}',maxDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_to"/>
				</div>
				<div class="rArea">
			        <a href="javascript:;" class="btn-ss btn-specail submit">查询</a>
				</div>
			</div>
			<!--表单筛选 end-->

<div id='container-withdrawals-form'>
<?php endif;?>

			<!--表格 begin-->
			<table class="mod-tableA">
				<thead>
					<tr>
						<th width="15%">交易时间</th>
						<th width="20%" class="tal">交易编号</th>
						<th width="15%" class="tar">提现金额(元)</th>
						<th width="15%" class="tar">订单状态</th>
						<th width="15%">提现银行卡</th>
						<th width="20%">备注</th>
					</tr>
				</thead>
				<tbody>
                    <?php foreach( $orders as $order ): ?>
                    <tr>
						<td><strong><?php echo date('m月d日', strtotime($order['created']));?></strong><br /><?php echo date('H:i:s', strtotime($order['created']));?></td>
						<td class="tal"><?php echo $order['trade_no']; ?></td>
						<td class="tar"><?php echo number_format(ParseUnit( $order['money'], 1 ), 2); ?></td>
						<td class="tar"><?php echo wallet_status($order['status']);?></td>
						<?php
							$additions = explode('@', $order['additions']);
							$bankCard = substr($additions[2], -4);
						?>
						<td>尾号<?php echo $bankCard;?></td>
						<td><?php if($order['content']){ echo $order['content'];}else{ echo '---';}?></td>
					</tr>
                    <?php endforeach; ?>
				</tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="tal">
                            <span>提现总额：<strong class="c_chu"><?php echo number_format(ParseUnit( $money, 1 ), 2);?></strong> 元</span>
                        </td>
                        <td class="tar table-page">
                            <span class="mlr10">本页<em class="mlr5"><?php echo $cpnum;?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
                        </td>
                    </tr>
                </tfoot>
			</table>
			<!--表格 end-->
			<!-- pagination -->
			<div id='container_forms'>
	      	<?php echo $pagestr;?>
	      	</div>
			<!-- pagination end -->

<?php if(!$this->is_ajax):?>

<script type="text/javascript">
var target = '/mylottery/withdrawals';
$(function(){
	new cx.vform('.withdrawals-form', {
		checklogin: true,
        submit: function(data) {
		 	if(checkDate()){
			 	return ;
		 	};
            var self = this;
            $.ajax({
                type: 'post',
                url:  target,
                data: data,
                success: function(response) {//alert(response);
            		$('#container-withdrawals-form').html(response);
                }
            });
        }
	 });
});
window.easemobim = window.easemobim || {};
easemobim.config = {visitor: visitor};
</script>
<script src='//kefu.easemob.com/webim/easemob.js'></script>

</div>
		</div>
	</div>
	<div class="warm-tip">
		<h3>温馨提示：</h3>
        <p>如对您的交易明细有疑问，请联系<a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" target="_self">在线客服</a>，或拨打客服电话400-690-6760&nbsp;</p>
	</div>
</div>
</div>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<?php endif;?>