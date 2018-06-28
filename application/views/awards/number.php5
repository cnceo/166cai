<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
var lotteryId = <?php echo $lotteryId; ?>;
$(function() {
	$('.award-number').each(function(key, el) {
		var $el = $(el);
		var award = cx.Lottery.renderAward(lotteryId, $el.data('award'));
		$el.html(award)
	});
});
</script>
<!--容器-->
<div class="wrap mod-box">
    <!--彩票信息-->
    <?php echo $this->load->view('elements/lottery/info_panel'); ?>
    <!--彩票信息end-->

    <!--彩票-->
    <div class="userLottery mod-box-bd">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'award')); ?>
        <div class="userLotteryTable clearfix">
        	<div class="fl">
            	<table class="lotteryTable">
                	<tr>
                      <th width="20%">期次</th>
                      <th width="30%">开奖时间</th>
                      <th width="50%">开奖号码</th>
                    </tr>
                    <?php foreach ($awards as $key => $award): ?>
                    <?php if ($key <= ceil(count($awards) / 2) - 1): ?>
                    <tr>
                        <td><?php echo $award['seExpect']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $award['awardTime'] / 1000); ?></td>
                        <td class="award-number" data-award="<?php echo $award['awardNumber']; ?>"></td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="fr">
                <table class="lotteryTable">
                    <tr>
                      <th width="20%">期次</th>
                      <th width="30%">开奖时间</th>
                      <th width="50%">开奖号码</th>
                    </tr>
                    <?php foreach ($awards as $key => $award): ?>
                    <?php if ($key > ceil(count($awards) / 2) - 1): ?>
                    <tr>
                        <td><?php echo $award['seExpect']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $award['awardTime'] / 1000); ?></td>
                        <td class="award-number" data-award="<?php echo $award['awardNumber']; ?>"></td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="clear"></div>
            <div class="page">
                <?php $preUrl = $baseUrl . "awards/number/" . $lotteryId . "/"; ?>
                <a class="a_turn a_turn_left" target="_self" href="<?php echo ( $pageNumber - 1 > 0 ) ? $preUrl . ( $pageNumber - 1 ) : "javascript:void(0);"; ?>">上一页<i class="arrow arrow_left"></i></a>
                <span class="num">
                    <?php for( $i = 1; $i < 6; $i++ ): ?>
                        <?php if( $pageNumber == $i ): ?><span class="cur"><?php echo $i; ?></span>
                        <?php else: ?><a target="_self" href="<?php echo $preUrl . $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
                <a class="a_turn a_turn_right" href="<?php echo ( $pageNumber + 1 < 6 ) ? $preUrl . ( $pageNumber + 1 ) : "javascript:void(0);"; ?>">下一页<i class="arrow arrow_right"></i></a>
            </div>
        </div>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->
