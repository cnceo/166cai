<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/myLottery.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/order.js');?>"></script>
<script type="text/javascript">
$(function() {
    var pn = 1;
    var recent = 7;
    var lid = <?php echo $lotteryId; ?>;
    var type = -1;
    var $page = $('.orders-page');
    var status = -1;
    function requestOrders(cb) {
        $.ajax({
            url: baseUrl + 'orders/page',
            data: {
                pn: pn,
                recent: recent,
                lid: lid,
                type: type,
                status: status,
                t: (new Date()).valueOf()
            },
            success: function(response) {
                $page.html(response);
                if ($.isFunction(cb)) {
                    cb();
                }
            }
        });
    }
    requestOrders();
    $('.select-lottery').change(function() {
        var lotteryId = $(this).val();
        lid = lotteryId;
        pn = 1;
        requestOrders();
    });
    $('.select-type').click(function() {
        var $this = $(this);
        type = $(this).data('type');
        pn = 1;
        status = -1;
        requestOrders(function() {
            $this.addClass('selected').siblings().removeClass('selected');
        });
    });
    $('.select-status').click(function() {
        var $this = $(this);
        status = $this.data('status');
        pn = 1;
        type = -1;
        requestOrders(function() {
            $this.addClass('selected').siblings().removeClass('selected');
        });
    });
    $('.select-recent').click(function() {
        var $this = $(this);
        recent = $(this).data('recent');
        pn = 1;
        requestOrders(function() {
            $this.addClass('selected').siblings().removeClass('selected');
        });
    });
    $page.on('click', '.next-page', function() {
        var last = $(this).data('last');
        pn = parseInt(pn, 10);
        if (pn >= last) {
            return ;
        }

        pn += 1;
        requestOrders();
    });
    $page.on('click', '.prev-page', function() {
        if (pn == 1) {
            return ;
        }
        pn = parseInt(pn, 10);
        pn -= 1;
        requestOrders();
    });
    $page.on('change', '.select-page', function() {
        pn = $(this).val();
        requestOrders();
    });
    $page.on('click', '.pay-order', function() {
        var orderId = $(this).data('oid');
        new cx.Confirm({
            single: '继续支付该订单？',
            confirmCb: function() {
                cx.ajax.get({
                    url: cx.url.getBusiUrl('ticket/order/pay'),
                    data: {
                        orderId: orderId,
                        isToken: 1
                    },
                    success: function(response) {
                        if (response.code == 0) {
                            location.href = location.href;
                        } else {
                            new cx.Alert({
                                content: response.msg
                            });
                        }
                    }
                });
            }
        })
    });
});
</script>

<!--容器-->
<div class="wrap clearfix">
    <!--个人信息-->
    <?php $this->load->view('elements/account/basic_info'); ?>
    <!--个人信息end-->

    <!--彩票-->
    <div class="userLottery">
        <div class="userLotteryTab">
            <div class="fl">
                <a href="<?php echo $baseUrl; ?>orders" class="selected">彩票</a>
                <a href="<?php echo $baseUrl; ?>bills">账单</a>
                <a href="<?php echo $baseUrl; ?>account">账户信息</a>
            </div>
        </div>
        <div class="userLotteryBox">
            <!--筛选字段-->
            <div class="userLotterySer clearfix">
                <div class="fl">
                    <span class="selected select-type" data-type="-1">全部</span>
                    <span class="select-type" data-type="0">普通投注</span>
                    <span class="select-type" data-type="1">合买投注</span>
                    <span class="select-status" data-status="100">未付款</span>
                    <select class="select-lottery">
                        <option value="0">全部彩种</option>
                        <option <?php if ($lotteryId == DLT) echo 'selected="selected"'; ?> value="<?php echo DLT; ?>">大乐透</option>
                        <option <?php if ($lotteryId == SSQ) echo 'selected="selected"'; ?>value="<?php echo SSQ; ?>">双色球</option>
                        <option <?php if ($lotteryId == QXC) echo 'selected="selected"'; ?>value="<?php echo QXC; ?>">七星彩</option>
                        <option <?php if ($lotteryId == QLC) echo 'selected="selected"'; ?>value="<?php echo QLC; ?>">七乐彩</option>
                        <option <?php if ($lotteryId == PLS) echo 'selected="selected"'; ?>value="<?php echo PLS; ?>">排列3</option>
                        <option <?php if ($lotteryId == PLW) echo 'selected="selected"'; ?>value="<?php echo PLW; ?>">排列5</option>
                        <option <?php if ($lotteryId == FCSD) echo 'selected="selected"'; ?>value="<?php echo FCSD; ?>">福彩3D</option>
                        <option <?php if ($lotteryId == SYXW) echo 'selected="selected"'; ?>value="<?php echo SYXW; ?>">11运夺金</option>
                        <option <?php if ($lotteryId == SFC) echo 'selected="selected"'; ?>value="<?php echo SFC; ?>">胜负彩</option>
                        <option <?php if ($lotteryId == RJ) echo 'selected="selected"'; ?>value="<?php echo RJ; ?>">任选9</option>
                        <option <?php if ($lotteryId == JCZQ) echo 'selected="selected"'; ?>value="<?php echo JCZQ; ?>">竞彩足球</option>
                        <option <?php if ($lotteryId == JCLQ) echo 'selected="selected"'; ?>value="<?php echo JCLQ; ?>">竞彩篮球</option>
                    </select>
                </div>
                <div class="fr">
                    <span class="selected select-recent" data-recent="7">最近7天</span>
                    <span class="select-recent" data-recent="30">最近30天</span>
                    <span class="select-recent" data-recent="90">最近90天</span>
                </div>
            </div>
            <div class="orders-page"></div>
        </div>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->
