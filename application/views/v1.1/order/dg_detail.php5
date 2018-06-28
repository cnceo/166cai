<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/dg_detail.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
<script type="text/javascript">
$(function() {
    var order = $.parseJSON('<?php echo json_encode($order); ?>');
    var buycontent = '<?php echo $order["codes"]; ?>';
    var award = $.parseJSON('<?php echo json_encode($award); ?>');
    // 实际出票信息
    var ticketData = $.parseJSON('<?php echo json_encode($orderDetail['ticketData']); ?>');
    $('.order-status').html(cx.Order.getStatus(order.status, order.returnFlag));

    jcDetail.renderOrderCast(buycontent, award, ticketData);

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
<div class="wrap_in">
<div class="detail-container jc-detail-container mod-box">
    	<!--彩票信息-->
    	<?php $this->load->view('v1.1/elements/lottery/detail_info_panel', array('noIssue' => false)); ?>
    <div class="mod-box-bd stage-detail">
    	<!--彩票信息end-->
		<div class="hd clearfix">
			<div class="plan-state"><em class="t">方案状态<i></i></em><span class="state"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></div>
			<span class="stage">第 <b class="spec"><?php echo $order['issue']; ?></b> 期<em class="bet-type">普通投注</em></span>
			<span class="order-num">订单编号：<?php echo $order['orderId']; ?></span>
		</div>
		<div class="stage-table">
			<table>
				<colgroup>
                	<col width="102">
                    <col>
                </colgroup>
				<tbody>
				<tr>
					<th class="s-th">方案进度</th>
					<td class="s-td process-td">

						<div class="process-order" id="process">
                        <?php $this->load->view('v1.1/elements/order/precess', array('status' => $order['status'], 'shopId' => $order['shopId'], 'created' => $order['created'], 'pay_time' => $order['pay_time'], 'ticket_time' => $order['ticket_time'])); ?>
            			</div>
					</td>
				</tr>
                <tr>
					<th class="s-th">发起人</th>
                    <td class="s-td">
                    	<div class="s-td-mocks">
                        	<span class="username"><?php echo $uname; ?></span>
                            <span class="s-th">方案金额</span>
                            <div class="money">
                            	<div class="s">
                                	<b class="main-color-s"><?php echo ParseUnit( $order['money'], 1 ); ?></b>元<em>（ <?php echo $order['betTnum']; ?>注）</em>
                                	<?php if($order['redpackId'] && $order['status'] >='40'):?>
                                    <span class="fcw"><i class="icon-font specil-color">&#xe645;</i>红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元</span>
                                    <?php endif;?>
                            	</div>
                                <s></s>
                         	</div>
                        </div>
                    </td>
				</tr>
				<tr>
					<th class="s-th">中奖信息</th>
					<td class="s-td">
						<span class="startup">
							<?php if ($order['status'] == 2000):?>
								<?php if($order['bonus'] != $order['margin']):?>
								税前奖金<b class="spec"> <?php echo number_format($order['bonus']/100, 2);?></b>元，税后奖金<b class="spec"> <?php echo number_format($order['margin']/100, 2);?></b>元<?php if($order['add_money'] > 0):?>，加奖<b class="spec"> <?php echo number_format($order['add_money']/100, 2);?></b>元<?php endif;?>
								<?php else: ?>
								<?php echo parse_order_status($order['status'], $order['my_status']);?>
								    <?php if($order['add_money'] > 0):?>
                                    <b class="spec"> <?php echo number_format(($order['margin'] + $order['add_money'])/100, 2);?></b>元
                                    <?php else: ?>
                                    <b class="spec"> <?php echo number_format($order['margin']/100, 2);?></b>元
                                    <?php endif;?>
								<?php endif;?>
							<?php else:?>
								<?php echo parse_order_status($order['status'], $order['my_status']);?>
							<?php endif;?>
						</span>
                        <!-- 加奖 -->
                        <?php if($order['status'] == 2000 && $order['bonus'] == $order['margin'] && $order['add_money'] > 0):?>
                        <span class="jiajiang-tips">奖金：<em><?php echo number_format($order['margin']/100, 2);?></em>元 + 加奖：<em><?php echo number_format($order['add_money']/100, 2);?></em>元</span>
                        <?php endif; ?>
                        <?php if($order['status']=='10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())):?>
							<a href="javascript:cx.castCb({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});" class="btn-ss btn-main">立即支付</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<th class="s-th">方案内容</th>
					<td class="s-td">
						<table class="jc-inTable">
							<colgroup>
                				<col width="70">
                				<col width="120">
                				<col width="140">
                				<col width="80">
                				<col width="100">
                				<col width="80">
                				<col width="130">
                    			<col>
                			</colgroup>
							<thead>
								<tr>
                                    <th>场次</th>
                                    <th>比赛时间</th>
                                    <th><div><span class="tar" style="float: left; width: 50%;">主队&nbsp;&nbsp;</span><span class="tal" style="float: left; width: 50%;">&nbsp;&nbsp;客队</span></div></th>
                                    <th>比分</th>
                                    <th>玩法</th>
                                    <th>彩果</th>
                                    <th>投注方案/出票赔率</th>
                                    <th>投注金额</th>
							    </tr>
							</thead>
							<tbody class="match-award">
						    </tbody>
                        </table>
						<div class="meth"><span>过关方式</span><span class="pass-way"></span><?php if($order['status'] >= 1000):?><span class="sub-lnk"><a href="/kaijiang/<?php echo ($lotteryId == JCLQ)?'jclq':'jczq'; ?>" style="color:#0c6ad4;" target="_blank">查看开奖详情</a></span><?php endif; ?></div>
					</td>
				</tr>
				<?php if($orderDetail['detail']): ?>
				<tr>
		            <th class="s-th">出票明细</th>
		            <td class="s-td">
		            	<div class="jc-inTable-scroll">
		            		<div>
		            			<div class="jc-inTable-scroll">
		            				<table class="jc-inTable">
                                        <colgroup>
                                            <col width="50">
                                            <col width="272">
                                            <col width="70">
                                            <col width="60">
                                            <col width="60">
                                            <col width="80">
                                            <col width="80">
                                            <col width="180">
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>场次</th>
                                            <th>过关方式</th>
                                            <th>注数</th>
                                            <th>倍数</th>
                                            <th>投注金额</th>
                                            <th>订单状态</th>
                                            <th>奖金</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    <div class="jc-inTable-scroll-body">
				                  		<table class="jc-inTable" id="jcInTableBody">
				                    		<colgroup>
						                    	<col width="50">
                                                <col width="272">
                                                <col width="70">
                                                <col width="60">
                                                <col width="60">
                                                <col width="80">
                                                <col width="80">
                                                <col width="163">
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
						                    		<td><?php if($orderdetail['status'] == 600){echo '出票失败';}else{echo '出票成功';} ?></td>
						                    		<?php if($orderdetail['status'] == '2000'): ?>
						                    		<td><span class="bingo"><?php echo number_format(ParseUnit($orderdetail['bonus'], 1), 2); ?></span></td>
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
		            		</div>
			            </div>
		            </td>
		        </tr>
		    	<?php endif; ?>
		    	<!-- 声明 -->
                <?php $this->load->view('v1.1/elements/lottery/statement'); ?>  
			</tbody></table>
		</div>
		<div class="stage-detail-tips" style="">
            <h5 style="color: #666;">温馨提示：</h5>
            <?php $this->load->view('v1.1/elements/lottery/tips'); ?>
        </div>
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

	    $('body').on('click', '.ptips-bd-close', function () {
            $(this).closest('.mod-tips-b').hide();
            return false;
	    });

        // hover效果
        $('.bubble-tip').mouseenter(function() {
            $.bubble({
                target: this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width: '240px',
                autoClose: false
            })
        }).mouseleave(function() {
            $('.bubble').hide();
        });
        $('.matches .bubble-tip').mouseenter(function() {
            $.bubble({
                target: this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width: 'auto',
                autoClose: false
            })
        }).mouseleave(function() {
            $('.bubble').hide();
        });
    })
</script>