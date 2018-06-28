<?php if(!$this->is_ajax):?>
<?php $this->load->view('elements/user/menu');?>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<script type="text/javascript" src="/caipiaoimg/src/date/WdatePicker.js"></script>
<div class="article withdrawals-form">
    <input type='hidden' class='submit' data-target='/mylottery/withdrawals' name='action' value=''>
	<div class="tab-nav">
		<ul class="clearfix">
			<li><a href="/mylottery/detail"><span>交易明细</span></a></li>
			<li><a href="/mylottery/recharge"><span>充值记录</span></a></li>
			<li class="active"><a href="/mylottery/withdrawals"><span>提款记录</span></a></li>
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
			        <a href="javascript:;" class="btn btn-default-small submit">查询</a>
				</div>
			</div>
			<!--表单筛选 end-->

<div id='container-withdrawals-form'>
<?php endif;?>

			<!--表格 begin-->
			<table class="mod-tableA">
				<thead>
					<tr>
						<th width="11%">交易时间</th>
						<th width="23%" class="tal">交易编号</th>
						<th width="22%">交易类型</th>
						<th width="17%" class="tar">提款金额(元)</th>
						<th width="17%" class="tar">账户余额(元)</th>
						<th width="10%">订单状态</th>
					</tr>
				</thead>
				<tbody>
                    <?php foreach( $orders as $order ): ?>
                    <tr>
						<td><strong><?php echo date('m月d日', strtotime($order['created']));?></strong><br /><?php echo date('H:i:s', strtotime($order['created']));?></td>
						<td class="tal"><?php echo $order['trade_no']; ?></td>
						<td>
							<?php if($order['status'] <= 1):?>
								冻结预付款
							<?php elseif($order['status'] == 2):?>
								扣除预付款
							<?php else:?>
								解除冻结预付款
							<?php endif;?>
						</td>
						<td class="tar"><?php echo number_format(ParseUnit( $order['money'], 1 ), 2); ?></td>
						<td class="tar"><?php echo number_format(ParseUnit( $order['umoney'], 1 ), 2); ?></td>
						<td>
							<?php echo wallet_status($order['status']);?><br>
							<?php if($order['status']=='0'):?>
								<a href="javascript:void(0);" data-tradeid='<?php echo $order['trade_no']; ?>' class="cOrange conceal">提款撤销</a>
							<?php endif;?>
						</td>
					</tr>
                    <?php endforeach; ?>
				</tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="tal">
                            <span>提款总额：<strong class="c_chu"><?php echo number_format(ParseUnit( $umoney, 1 ), 2);?></strong> 元</span>
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
		 $('.conceal').on('click', function(){
			 var self = $(this);
			 trade_no = self.attr('data-tradeid');
			 var datas = {
	            action: 'withdraw_conceal',
	            trade_no: trade_no
	         };
	         new cx.Confirm({
	            title: '确认撤销',
	            single: '此操作将撤销提款申请，确认撤销？',
	            confirmCb: function() {
		        	 $.ajax({
		     	        type: "POST",
		     	        url: "/mylottery/withdrawConceal",
		     	        data: datas,
		     	        success: function(response){
		     				if(response == '1')
		     				{
		     					cx.Alert({
			     					content:'撤销成功！',
			     					confirmCb: function(){
	                                	location.href = location.href;
		     						}
				     			});
			     			}
		     				else
		     				{
		     					cx.Alert({
			     					content:'撤销失败！',
			     					confirmCb: function(){
	                                	location.href = location.href;
		     						}
				     			});
			     			}
		     			}
		             });
	            }
	        });
		 })
	});
</script>

</div>
		</div>
	</div>
	<div class="warm-tip">
		<h3>温馨提示：</h3>
        <p>如对您的交易明细有疑问，请联系<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2584565084&site=qq&menu=yes">在线客服</a>，或拨打客服电话400-000-2345转8彩票业务&nbsp;</p>
	</div>
</div>
<?php $this->load->view('elements/user/menu_tail');?>
<?php endif;?>