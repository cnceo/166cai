<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/recharge.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in p-pay-result bonus-optimization-error">
    <div class="mod-resulte resulte-success">
        <div class="mod-resulte-bd">
            <i class="icon-resulte"></i>
            <div class="resulte-txt">
                <h2 class="resulte-txt-title">投注列表中有场次已截止或玩法暂时不可投注，请重新选择</h2>
            </div>
        </div>
        <div class="btn-group">
            <a href="/hall" class="btn-continue">购彩大厅</a>
            <a href="/<?php echo $lotteryId == Lottery_Model::JCLQ ? 'jclq' : 'jczq'?>" class="btn-more">继续购彩</a>
        </div>
        <div class="result-side">
            <a href="/">166彩票APP助你走上人生巅峰<i></i></a>
        </div>
    </div>
</div>