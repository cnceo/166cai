<?php
$this->load->view("templates/head");
function bonusComputed($status)
{
    $isComputed = ($status >= 1000);

    return $isComputed;
}
?>
<div class="path">您的位置：报表系统&nbsp;>&nbsp;<a href="/backend/Order">订单管理</a>&nbsp;>&nbsp;<a href="">详情</a></div>
<div class="data-table-log mt20">
    <table>
        <colgroup>
            <col width="112"/>
            <col width="262"/>
            <col width="112"/>
            <col width="209"/>
            <col width="112"/>
            <col/>
        </colgroup>
        <tbody>
        <tr class="title">
            <th colspan="6">订单信息</th>
        </tr>
        <tr>
            <th>订单编号：</th>
            <td><?php echo $order['orderId'] ?></td>
            <th>订单类型：</th>
            <td><?php echo $this->caipiao_type_cfg[$order['orderType']] ?></td>
            <th>投注方式：</th>
            <td><?php if ($order['betTnum'] > 1): echo "复式投注";
                else: echo "单式投注"; endif; ?></td>
        </tr>
        <tr>
            <th>用户名：</th>
            <td><?php echo $order['userName'] ?></td>
            <th>期次：</th>
            <td><?php echo $order['issue'] ?></td>
            <th>前台订单状态：</th>
            <td><?php if ($order['status'] == '2000'): echo $this->caipiao_ms_cfg['2000'][$order['my_status']][1];
                else: echo $this->caipiao_status_cfg[$order['status']][1]; endif; ?></td>
        </tr>
        <tr>
            <th>彩种：</th>
            <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
            <th>玩法类型：</th>
            <td><?php if ( ! empty($this->caipiao_cfg[$order['lid']]['play'])): echo print_playtype($order['lid'], $order['playType'], $this->caipiao_cfg[$order['lid']]['play']);
                else: echo "--";  endif; ?></td>
            <th>后台订单状态：</th>
            <td><?php if ($order['status'] == '2000'): echo $this->caipiao_ms_cfg['2000'][$order['my_status']][0];
                else: echo $this->caipiao_status_cfg[$order['status']][0]; endif; ?></td>
        </tr>
        <tr>
            <th>投注倍数：</th>
            <td><?php echo $order['multi'] ?></td>
            <th>投注注数：</th>
            <td><?php echo $order['betTnum'] ?></td>
            <th>创建时间：</th>
            <td><?php echo $order['created'] ?></td>
        </tr>
        <tr>
            <th>单注金额（元）：</th>
            <td><?php echo m_format($order['eachAmount']) ?></td>
            <th>订单总额（元）：</th>
            <td><?php echo m_format($order['money']) ?></td>
            <th>支付时间：</th>
            <td><?php echo $order['pay_time']; ?></td>
        </tr>
        <tr class="hr">
            <td colspan="6">
                <div class="hr-dashed"></div>
            </td>
        </tr>

        <tr class="title">
            <th colspan="6">算奖派奖</th>
        </tr>
        <tr>
            <th>开奖号码：</th>
            <td><?php echo $winning['lottery_num'] ?></td>
            <th>开奖时间：</th>
            <td><?php if (intval($winning['time']) > 0): echo date("Y-m-d H:i:s", $winning['time']); endif; ?></td>
            <th>理论奖金：</th>
            <td><?php echo m_format($winning['money']) ?></td>
        </tr>
        <tr>
            <th>中奖金额（税前）：</th>
            <td><?php echo m_format($order['bonus']) ?> </td>
            <th>中奖金额（税后）：</th>
            <td><?php echo m_format($order['margin']) ?> </td>
            <th>派奖时间：</th>
            <td><?php echo $order['sendprize_time'] ?> </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="tab-nav mt20">
    <ul class="clearfix">
        <li class="active load_info"><a
                href="javascript:stab('orderDetail', 'Order/order_detail/?id=<?php echo $order['id']; ?>');"><span>投注内容</span></a>
        </li>
        <li class="load_info"><a
                href="javascript:stab('splitDetail', 'Order/splitDetail/orderId=<?php echo $order['orderId']; ?>')"><span>出票明细</span></a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div class="item" style="display:block;" id="orderDetail" has_load='true'>
        <div class="data-table-log bd-width-item">
            <table>
                <colgroup>
                    <col width="112"/>
                    <col width="262"/>
                    <col width="112"/>
                    <col width="209"/>
                    <col width="112"/>
                    <col/>
                </colgroup>
                <tbody>
                <tr class="title">
                    <th colspan="6">出票信息</th>
                </tr>
                <tr>
                    <th>出票商ID：</th>
                    <td><?php echo $order['ticket_merchant_id'] ?></td>
                    <th>票号：</th>
                    <td><?php echo $order['ticket_id'] ?></td>
                    <th>出票时间：</th>
                    <td><?php echo $order['ticket_time'] ?></td>
                </tr>
                <tr>
                    <th align="top">投注内容：</th>
                    <td colspan="5">
                        <textarea class="textarea w830" rows="10" cols="30" id=""
                                  name=""><?php echo $order['codes'] ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="item" style="display: none" id="split_detail" has_load="false">
        <div class="data-table-list mt20">
            <table>
                <colgroup>
                    <col width="5%"/>
                    <col width="15%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col/>
                </colgroup>
                <tbody>
                <tr>
                    <th>序号</th>
                    <th>拆票内容</th>
                    <th>出票时间</th>
                    <th>拆票金额</th>
                    <th>中奖注数</th>
                    <th>计奖奖金</th>
                    <th>计奖税后</th>
                    <th>出票金额</th>
                    <th>票商税前奖金</th>
                    <th>票商税后奖金</th>
                </tr>
                <?php if ($subOrders): ?>
                    <?php foreach ($subOrders as $subOrder): ?>
                        <tr>
                            <td><?php echo ++ $i; ?></td>
                            <td><?php echo $subOrder['content']; ?></td>
                            <td><?php echo $subOrder['ticketTime']; ?></td>
                            <td><?php echo m_format($subOrder['money']) . '元'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? $subOrder['stakeNum'] : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['bonus']) . '元') : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['margin']) . '元') : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['ticketMoney']) . '元') : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['ticketBonus']) . '元') : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['ticketMargin']) . '元') : '--'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function () {
        $(".tab-nav li").bind("click", function () {
            var i = $(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
        });
    });
    function stab(ele, url) {
        if ($("#" + ele).attr("has_load") == 'false') {
            $("#" + ele).load("/backend/" + url + "&fromType=ajax", function () {
                $("#" + ele).attr("has_load", 'true')
            });
        }
    }
</script>
</body>
</html>