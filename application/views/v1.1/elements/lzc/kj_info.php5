<?php
function dashIfEmpty($resultStatus, $value, $wrap = '')
{
    return $resultStatus > 50 && ($value || in_array($value, array(0, '0'), TRUE))
        ? ($wrap ? ('<' . $wrap . '>' . $value . '</' . $wrap . '>') : $value)
        : '---';
}

?>

<div class="bet-kj-info"><b><?php echo $awardIssue['seExpect']; ?></b>期开奖信息：
    <?php if ($lotteryId == SFC): ?>
        <span>销售额：<?php echo $awardIssue['sfcRStatus'] > 50 ? number_format($awardIssue['sfcSale']) : '---'; ?>
            元</span>
        <span>一等奖：<?php echo dashIfEmpty($awardIssue['sfcRStatus'], $awardIssue['awardInfo']['1dj']['zs'], 'em') ?>
            注
            <?php echo dashIfEmpty($awardIssue['sfcRStatus'], $awardIssue['awardInfo']['1dj']['dzjj'], 'em') ?>
            元</span>
        <span>二等奖：<?php echo dashIfEmpty($awardIssue['sfcRStatus'], $awardIssue['awardInfo']['2dj']['zs'], 'em') ?>
            注
            <?php echo dashIfEmpty($awardIssue['sfcRStatus'], $awardIssue['awardInfo']['2dj']['dzjj'], 'em') ?>
            元</span>
    <?php elseif ($lotteryId == RJ): ?>
        <span>销售额：<?php echo $awardIssue['rjRStatus'] > 50 ? number_format($awardIssue['rjSale']) : '---'; ?>
            元</span>
        <span>一等奖：<?php echo dashIfEmpty($awardIssue['rjRStatus'], $awardIssue['awardInfo']['rj']['zs'], 'em') ?>
            注
            <?php echo dashIfEmpty($awardIssue['rjRStatus'], $awardIssue['awardInfo']['rj']['dzjj'], 'em') ?>
            元</span>
    <?php endif; ?>
    滚存到下期：<?php echo dashIfEmpty($awardIssue['sfcRStatus'], $awardIssue['award'], 'em') ?>元
</div>

