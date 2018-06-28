<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/lottery.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/buy.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
    $(function(){
        //合买彩票表单交互
        $(".buyItemsForm input").each(function(){
            var _this = $(this);
            var _val = _this.val();
            $(this).focus(function(){
                _this.removeClass("gray").val("");
            });
            $(this).blur(function(){
                if(_this.val() == ""){
                    _this.addClass("gray").val(_val);
                }
            });
        });
        var lotteryId = <?php echo $lotteryId; ?>;
        var currPage = <?php echo $currPage; ?>;
        var totalPage = <?php echo $totalPage; ?>;
        $('.prev-page').click(function() {
            if (currPage > 1) {
                location.href = baseUrl + 'crowd?pn=' + (currPage - 1) + '&lid=' + lotteryId;
            }
        });
        $('.next-page').click(function() {
            if (currPage < totalPage) {
                location.href = baseUrl + 'crowd?pn=' + (currPage + 1) + '&lid=' + lotteryId;
            }
        });
        $('.select-page').change(function() {
            var page = $(this).val();
            if (page != currPage) {
                location.href = baseUrl + 'crowd?pn=' + page + '&lid=' + lotteryId;
            }
        });
    });
</script>
<!--容器-->
<div class="wrap clearfix">
    <!--彩票信息-->
    <?php echo $this->load->view('elements/lottery/info_panel'); ?>
    <!--彩票信息end-->

    <!--彩票-->
    <div class="userLottery">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'crowd')); ?>
        <div class="userLotteryViews border clearfix">
        	<div class="buyItems clearfix">
                <ul>
                    <?php foreach ($items as $item): ?>
                    <li>
                    <?php $this->load->view('elements/crowd/item', array('item' => $item)); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!--翻页-->
            <div class="buyPages">
                <p>
                    <span class="prev-page">上一页</span>
                    <select class="select-page">
                        <?php for ($i = 1; $i <= $totalPage; ++$i): ?>
                        <option <?php if ($i == $currPage) echo 'selected="selected"';?> value="<?php echo $i; ?>"><?php echo $i; ?>/<?php echo $totalPage; ?></option>
                        <?php endfor; ?>
                    </select>
                    <span class="next-page">下一页</span>
                </p>
            </div>
        </div>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->
