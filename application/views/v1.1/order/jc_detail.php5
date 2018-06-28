<?php if ($lotteryId == JCLQ): ?>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jclq_detail.min.js');?>"></script>
<?php elseif ($lotteryId == JCZQ): ?>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jc_detail.min.js');?>"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
<?php if (in_array($lotteryId, array(JCZQ, JCLQ))) {?>
<script type="text/javascript">
    $(function() {
        var order = $.parseJSON('<?php echo json_encode($order); ?>');
        var buycontent = '<?php echo $order["codes"]; ?>';
        var award = $.parseJSON('<?php echo json_encode($award); ?>');
        // 实际出票信息
        var ticketData = $.parseJSON('<?php echo json_encode($orderDetail['ticketData']); ?>');
        // var tickets = $.parseJSON('<?php echo json_encode($tickets); ?>');
        $('.order-status').html(cx.Order.getStatus(order.status, order.returnFlag));

        jcDetail.renderOrderCast(buycontent, award, ticketData);
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
<?php }?>
<!-- 订单详情 -->
<div class="wrap_in">
<div class="detail-container jc-detail-container mod-box">
    <!--彩票信息-->
    <?php $this->load->view('v1.1/elements/lottery/detail_info_panel', array('noIssue' => false)); ?>
    <!--彩票信息end-->
    <div class="stage-detail">
        <div class="hd clearfix">
            <div class="plan-state"><em class="t">方案状态<i></i></em><span class="state"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></div>
            <span class="stage">第 <b class="spec"><?php echo $order['issue']; ?></b> 期<em class="bet-type">普通投注</em></span>
            <span class="order-num">订单编号：<?php echo $order['orderId']; ?><?php if ($order['orderType'] == 3) {?><span class="baopei">不赚包赔</span><?php }?></span>
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
                                	<b class="main-color-s"><?php echo ParseUnit( $order['money'], 1 ); ?></b>元<em>（ <?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍）</em>
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
                    		<thead>
                            <tr>
                            <?php switch ($lotteryId) {
                            	case JCZQ:
                            	case JCLQ:?>
                                <th>场次</th>
                                <th>比赛时间</th>
                                <?php if ($lotteryId == JCLQ): ?>
                                    <th>客队</th>
                                    <th>主队</th>
                                <?php else:?>
                                    <th>主队</th>
                                    <th>客队</th>
                                <?php endif;?>
                                <th>出票盘口<i class="icon-font bubble-tip specil-color" tiptext="出票盘口即为出票时刻盘口和算奖依据,可在下方出票明细中查看详细出票信息"></i></th>
                                <th>比分</th>
                                <!-- <th>开奖结果</th> -->
                                <th class="last" width="140">投注方案/出票赔率</th>
                            <?php break;
                            default:?>
                    			<th>选项</th>
                                <th>球队组合<?php if (in_array($order['status'], array(500, 510, 1000, 1010, 2000))) {?>/出票赔率<?php }?></th>
                                <th>彩果</th>
                    		<?php break;
                            }?>
                    		</tr>
                            </thead>
                            <tbody class="match-award"><?php if (in_array($lotteryId, array(GJ, GYJ))) {echo $orderDetail[0];}?></tbody>
                    	</table>
                    	<?php if (in_array($lotteryId, array(JCLQ, JCZQ))) {?><div class="meth"><span>过关方式</span><span class="pass-way"></span><?php if($order['status'] >= 1000):?><span class="sub-lnk"><a href="/kaijiang/<?php echo ($lotteryId == JCLQ)?'jclq':'jczq'; ?>" style="color:#0c6ad4;" target="_blank">查看开奖详情</a></span><?php endif; ?></div><?php }?>
                        
                    </td>
                </tr>
                <?php if($orderDetail['detail']): ?>
                    <tr>
                        <th class="s-th">出票明细</th>
                        <td class="s-td">
                            <div>
                                <div class="jc-inTable-scroll">
                                    <table class="jc-inTable">
                                    <?php if (in_array($lotteryId, array(JCLQ, JCZQ))) {?>
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
                                    <?php }else {?>
                                    	<colgroup>
                                            <col width="50">
                                            <col width="242">
                                            <col width="80">
                                            <col width="80">
                                            <col width="100">
                                            <col width="100">
                                            <col width="180">
                                        </colgroup>
                                        <thead>
	                                   		<tr>
	                                            <th>序号</th>
	                                            <th>投注内容</th>
	                                            <th>注数</th>
	                                            <th>倍数</th>
	                                            <th>投注金额</th>
	                                            <th>订单状态</th>
	                                            <th>奖金</th>
	                                        </tr>
                                        </thead>
                                    <?php }?>
                                    </table>
                                    <div class="jc-inTable-scroll-body">
                                        <table class="jc-inTable" id="jcInTableBody">
                                        <?php if (in_array($lotteryId, array(JCLQ, JCZQ))) {?>
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
                                                    <?php elseif($orderdetail['status'] == '600'): ?>
                                                        <td><span class="wzj">---</span></td>
                                                    <?php else: ?>
                                                        <td><span class="ddkj">等待开奖</span></td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        <?php }else {?>
                                        	<colgroup><col width="50">
	                                            <col width="242">
	                                            <col width="80">
	                                            <col width="80">
	                                            <col width="100">
	                                            <col width="100">
	                                            <col width="163">
                                            </colgroup>
                                            <tbody>
                                            <?php $i = 0;
                                            foreach ($orderDetail['info'] as $key => $info): 
                                                foreach ($orderDetail['detail'] as $detail) {
                                            	$i++?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $info['mid'].$info['name'].(empty($orderDetail['pDetail']) ? '':'（'.$orderDetail['pDetail'][$info['mid']].'）'); ?></td>
                                                    <td><?php echo $detail['betTnum']/count($orderDetail['info']); ?></td>
                                                    <td><?php echo $detail['multi']; ?></td>
                                                    <td><b><?php echo number_format(ParseUnit($detail['money']/count($orderDetail['info']), 1), 2); ?></b></td>
                                                    <td><?php if($detail['status'] == 600){echo '出票失败';}else{echo '出票成功';} ?></td>
                                                    <?php if($info['status'] == 2 && $detail['status'] == 2000): ?>
                                                        <td><span class="bingo"><?php echo number_format(ParseUnit($detail['bonus'], 1), 2); ?></span></td>
                                                    <?php elseif(in_array($detail['status'], array(1000, 2000))): ?>
                                                        <td><span class="wzj">未中奖</span></td>
                                                    <?php elseif($detail['status'] == '600'): ?>
                                                        <td><span class="wzj">---</span></td>
                                                    <?php else: ?>
                                                        <td><span class="ddkj">等待开奖</span></td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php }
                                            endforeach; ?>
                                            </tbody>
                                        <?php }?>
                                        </table>
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
        $('table', '.jc-inTable-scroll').css({});
        $('th, td','.jc-inTable-scroll').each(function(i){
            w = $(this).width();

            $('th:eq('+i+'), td:eq('+i+')', th).css('width',w+'px');
            $('th:eq('+i+'), td:eq('+i+')', tb).css('width',w+'px');
        });

        $('body').on('click', '.ptips-bd-close', function () {
            $(this).closest('.mod-tips-b').hide();
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
