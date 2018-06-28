<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<?php if ($lotteryId == JCLQ): ?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jclq_detail.js');?>"></script>
<?php else: ?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jc_detail.js');?>"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/order.js');?>"></script>
<script type="text/javascript">
$(function() {
    var order = $.parseJSON('<?php echo json_encode($order); ?>');
    var buycontent = '<?php echo $order["codes"]; ?>';
    var award = $.parseJSON('<?php echo json_encode($award); ?>');
    // var tickets = $.parseJSON('<?php echo json_encode($tickets); ?>');
    $('.order-status').html(cx.Order.getStatus(order.status, order.returnFlag));

    jcDetail.renderOrderCast(buycontent, award);
    // jcDetail.renderOrderTicket(tickets);

    //组件表单交互
    $(".buyDetailForm input").each(function(){
        var _this = $(this);
        $(this).focus(function(){
            _this.addClass("hover");
            _this.next("span").hide();
        });
        $(this).blur(function(){
            if(_this.val() == ""){
                _this.next("span").show();
            }
        });
        $(".buyDetailForm span").each(function(){
            var _this = $(this);
            $(this).click(function(){
                _this.prev("input").focus();
            });
        });
    });
});
</script>


<!-- 订单详情 -->
<div class="wrap_in detail-container mod-box">
    <!--彩票信息-->
    <?php $this->load->view('elements/lottery/detail_info_panel', array('noIssue' => false)); ?>
    <!--彩票信息end-->
    <div class="stage-detail">
		<div class="hd clearfix">
			<div class="plan-state"><em class="t">方案状态<i></i></em><span class="state"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></div>
			<span class="stage">第 <b class="spec"><?php echo $order['issue']; ?></b> 期<em class="bet-type">普通投注</em></span>
			<span class="order-num">订单编号：<?php echo $order['orderId']; ?></span>
		</div>
		<div class="stage-table">
			<table>
				<colgroup>
					<col width="12%">
					<col width="49%">
					<col width="12%">
					<col>
				</colgroup>
				<tbody>
				<tr>
					<th class="s-th">方案进度</th>
					<td colspan="3" class="s-td process-td">

						<div class="process-order" id="process">
                        <?php $this->load->view('elements/order/precess', array('status' => $order['status'])); ?>
            			</div>
					</td>
				</tr>
                <tr>
					<th class="s-th">发起人</th>
					<td class="s-td"><span class="username"><?php echo $uname; ?></span></td>
					<th class="s-th">方案金额</th>
					<td class="s-td"><span class="money"><b class="spec"><?php echo ParseUnit( $order['money'], 1 ); ?></b>元<em>（ <?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍）</em></span></td>
				</tr>
				<tr>
					<th class="s-th">中奖信息</th>
					<td class="s-td" colspan="3">
						<span class="startup">
							<?php echo parse_order_status($order['status'], $order['my_status']);?>
							<?php if ($order['status'] == 2000):?>
								<b class="spec"> <?php echo number_format($order['margin']/100, 2);?></b>元
							<?php endif;?>
						</span>
					</td>
				</tr>
				<tr>
					<th class="s-th">方案内容</th>
					<td class="s-td" colspan="3">
						<table class="jc-inTable">
							<thead>
								<tr>
                                    <th>场次</th>
                                    <th>比赛时间</th>
                                    <?php if ($lotteryId == JCLQ): ?>
                                    <th>客队</th>
                                    <th>主队</th>
                                    <?php else:?>
                                    <th>主队</th>
                                    <th>客队</th>
                                    <?php endif;?>
                                    <th>盘口</th>
                                    <th>比分</th>
                                    <!-- <th>开奖结果</th> -->
                                    <th class="last">投注方案/参考SP值</th>
							    </tr>
							</thead>
							<tbody class="match-award">
						    </tbody>
                        </table>
						<div class="meth"><span>过关方式</span><span class="pass-way"></span></div>
					</td>
				</tr>
				<?php if($orderDetail['detail']): ?>
				<tr>
		            <th class="s-th">出票明细</th>
		            <td class="s-td" colspan="3">
		            	<div class="jc-inTable-scroll">
			                <table class="jc-inTable" id="jcInTableHead" style="width: 850px;">
			                  <thead>
			                    <tr>
			                      <th width="5%" style="width: 26px;">序号</th>
			                      <th width="40%" style="width: 323px;">场次</th>
			                      <th width="11%" style="width: 76px;">过关方式</th>
			                      <th width="9%" style="width: 59px;">注数</th>
			                      <th width="9%" style="width: 59px;">倍数</th>
			                      <th width="13%" style="width: 93px;">投注金额</th>
			                      <th width="13%" class="last" style="width: 94px;">奖金</th>
			                    </tr>
			                  </thead>
			                </table>
			                <div class="jc-inTable-scroll-body">
			                  <table class="jc-inTable" id="jcInTableBody" style="width: 850px;">
			                    <colgroup>
			                      <col width="5%">
			                      <col width="40%">
			                      <col width="11%">
			                      <col width="9%">
			                      <col width="9%">
			                      <col width="13%">
			                      <col width="13%">
			                    </colgroup>
			                    <tbody>
			                    <?php foreach ($orderDetail['detail'] as $key => $orderdetail): ?>
	                				<tr>
					                    <td><?php echo $orderdetail['id']; ?></td>
					                    <td><p class="matches"><?php echo $orderdetail['matchInfo']; ?></p></td>
					                    <td><?php echo $orderdetail['type']; ?></td>
					                    <td><?php echo $orderdetail['pourNum']; ?></td>
					                    <td><?php echo $orderdetail['multis']; ?></td>
					                    <td><b><?php echo number_format(ParseUnit($orderdetail['money'], 1), 2); ?></b></td>
					                    <?php if($orderdetail['status'] == '2000'): ?>
					                    <td><span class="yzj"><i class="icon-gold"></i><?php echo number_format(ParseUnit($orderdetail['bonus'], 1), 2); ?></span></td>
					                	<?php elseif($orderdetail['status'] == '1000'): ?>
					                	<td><span class="wzj">未中奖</span></td>
					                	<?php else: ?>
					                	<td><span class="ddkj">等待开奖</span></td>
					                	<?php endif; ?>
			                  		</tr>
	                			<?php endforeach; ?>
			                    </tbody>
			                  </table>
			                </div>
			            </div>
		            </td>
		        </tr>
		    	<?php endif; ?>
			</tbody></table>
		</div>
		<div class="stage-detail-tips" style="">
            <h5 style="color: #666;">温馨提示：</h5>
            <?php $this->load->view('elements/lottery/tips'); ?>
        </div>
	</div>
</div>
<!-- 订单详情end -->
<script>
  $(function(){

      var w = 0;
      var th = $('thead tr:first', '#jcInTableHead');
      var tb = $('tbody tr:first', '#jcInTableBody');
      $('table', '.jc-inTable-scroll').css({'width': '850px'});
      $('th, td','.jc-inTable-scroll').each(function(i){
        w = $(this).width();

        $('th:eq('+i+'), td:eq('+i+')', th).css('width',w+'px');
        $('th:eq('+i+'), td:eq('+i+')', tb).css('width',w+'px');
      });
  })
</script>