<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/myLottery.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(function() {
    var pn = 1;
    var recent = 7;
    function requestBills(cb) {
        $.ajax({
            url: baseUrl + 'bills/page',
            data: {
                pn: pn,
                recent: recent,
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
    requestBills();
    var $page = $('.bills-page');
    $page.on('click', '.prev-page', function() {
        if (pn == 1) {
            return ;
        }
        pn = parseInt(pn, 10);
        pn -= 1;
        requestBills();
    });
    $page.on('click', '.next-page', function() {
        var last = $(this).data('last');
        pn = parseInt(pn, 10);
        if (pn >= last) {
            return ;
        }
        pn += 1;
        requestBills();
    });
    $page.on('change', '.select-page', function() {
        pn = $(this).val();
        requestBills();
    });
    $('.select-recent').click(function() {
        var self = this;
        recent = $(this).data('recent');
        requestBills(function() {
            $(self).addClass('selected').siblings().removeClass('selected');
        });
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
                <a href="<?php echo $baseUrl; ?>orders">彩票</a>
                <a href="<?php echo $baseUrl; ?>bills" class="selected">账单</a>
                <a href="<?php echo $baseUrl; ?>account">账户信息</a>
            </div>
        </div>
        <div class="userLotteryBox">
            <!--筛选字段-->
            <div class="userLotterySer clearfix">
                <div class="fl">
                    <span class="selected select-recent" data-recent="7">最近7天</span>
                    <span class="select-recent" data-recent="30">最近30天</span>
                    <span class="select-recent" data-recent="90">最近90天</span>
                </div>
            </div>
            <div class="bills-page"></div>
        </div>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->
