<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript">
$(function() {
	cx.closeCount = true;
    var order = $.parseJSON('<?php echo json_encode($order); ?>');
    var award = $.parseJSON('<?php echo json_encode($award); ?>');
    var crowd = $.parseJSON('<?php echo json_encode($crowd); ?>');

    if (!$.isArray(order)) {
        var awardNumber = null;
        var lotteryId = order.lid;
        if (lotteryId == 54) {
            lotteryId = 21406;
        }
        if ('awardNumber' in award) {
            awardNumber = award.awardNumber;
        }
        var awardTpl = cx.Lottery.renderAward(lotteryId, awardNumber);

        var castStr = order.codes;
        var casts = castStr.split(';');
        var cast;
        var castHtml;
        var castTpl = '';

        var tpl = '';
        var playType;
        for (var i = 0; i < casts.length; ++i) {
            /*
            cast = casts[i];
            //playType = cx.Lottery.getPlayTypeName(lotteryId, cast.split(':')[1]);
            castHtml = cx.Lottery.renderCast(lotteryId, cast);
            castTpl += castHtml.preTpl;
            if (castHtml.postTpl) {
                castTpl += '<em>+</em>' + castHtml.postTpl;
            }
            //castTpl += '<br />';
            */
            castTpl = '';
            cast = casts[i];
            //playType = cx.Lottery.getPlayTypeName(lotteryId, cast.split(':')[1]);
            castHtml = cx.Lottery.renderCast(lotteryId, cast, awardNumber);
            castTpl += castHtml.preTpl;
            if (castHtml.postTpl) {
                castTpl += '<em>+</em>' + castHtml.postTpl;
            }
            tpl += renderTr(/*order, playType, awardTpl, */ castTpl);
        }
        $('#award-nums').html(awardTpl);
        $('#cast-nums').html( tpl );

        $('.order-code').html(order.orderCode);
        $('.order-table').find('tbody').append(tpl);


        //$('.order-status').html(cx.Order.getStatus(order.status, order.returnFlag));
        $('.lottery-name').html(cx.Lottery.getCnName(order.lotType));
    }

    function renderTr(castTpl) {
        return '<li>' + castTpl + '</li>';
    }

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
				<tr>
					<th class="s-th">方案进度</th>
					<td colspan="3" class="process-td s-td">
						<div class="process-order" id="process">
                        <?php $this->load->view('elements/order/precess', array('status' => $order['status'])); ?>
            			</div>
					</td>
				</tr>
				<tr>
					<th class="s-th">发起人</th>
					<td class="s-td"><span class="username"><?php echo $uname; ?></span></td>
					<th class="s-th">方案金额</th>
					<td class="s-td"><span class="money"><b class="spec"><?php echo ParseUnit( $order['money'], 1 ); ?></b>元<em>（<?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍 ）</em></span></td>
				</tr>
				<tr>
					<th class="s-th">本期开奖结果</th>
					<td class="s-td" colspan="3">
						<div class="award-nums" id="award-nums"></div>
					</td>
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
            <?php if($matchInfo): ?>
				<tr>
					<th class="s-th">方案内容</th>
					<td class="s-td" colspan="3">
                        <!-- 方案内容 strat -->
                        <table class="jc-inTable">
                            <tbody>
                                <tr class="th-bg-fix">
                                    <th width="7%">场次</th>
                                    <td width="6%">1</td>
                                    <td width="6%">2</td>
                                    <td width="6%">3</td>
                                    <td width="6%">4</td>
                                    <td width="6%">5</td>
                                    <td width="6%">6</td>
                                    <td width="6%">7</td>
                                    <td width="6%">8</td>
                                    <td width="6%">9</td>
                                    <td width="6%">10</td>
                                    <td width="6%">11</td>
                                    <td width="6%">12</td>
                                    <td width="6%">13</td>
                                    <td width="6%" class="last">14</td>
                                </tr>
                                <tr class="text-vertical">
                                    <th><span>主队</span></th>
                                    <?php foreach ($matchInfo as $matchinfo): ?>
                                        <td><span><?php echo $matchinfo['teamName1']; ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="text-vertical">
                                    <th><span>客队</span></th>
                                    <?php foreach ($matchInfo as $matchinfo): ?>
                                        <td><span><?php echo $matchinfo['teamName2']; ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <th>投注方案</th>
                                    <?php foreach ($betStr as $betarry): ?>
                                        <td>
                                            <?php if($betarry): ?>
                                            <?php foreach ($betarry as $betstr): ?>
                                                <b <?php if($betstr['is_win']=='1'): ?>class="num-winners"<?php endif; ?>><?php echo $betstr['bet']; ?></b>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            -
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <!-- <td class="num-winners">3,1</td> -->
                                </tr>
                            </tbody>
                        </table>
                        <!-- 方案内容 end -->
					</td>
				</tr>
            <?php endif; ?>
			</table>
		</div>
        <div class="stage-detail-tips" style="">
            <h5 style="color: #666;">温馨提示：</h5>
            <?php $this->load->view('elements/lottery/tips'); ?>
        </div>
	</div>
</div>
<!-- 订单详情end -->
