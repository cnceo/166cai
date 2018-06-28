<a target="_blank" href="<?php echo $baseUrl; ?>crowd/detail/<?php echo $item['orderId']; ?>/<?php echo $item['lotType']; ?>" class="buyItemsPerson" style="color: #000; display: block;">
    <img src="<?php echo $fileUrl; ?>avatar/<?php echo $item['uid']; ?>" alt="头像" width="60" height="60" />
    <div>
        <h2><?php echo htmlspecialchars($item['userName']); ?></h2>
        <h3></h3>
    </div>
    <p></p>
</a>
<div class="buyItemsView clearfix">
    <div class="fl">
        <p>彩种：<?php echo $lotteryNames[$item['lotType']]; ?></p>
        <p>方案金额：<?php echo $item['oneMoney'] * $item['totalNum']; ?></p>
    </div>
    <div class="fr">
        <p>参与人数：<?php echo $item['partNum']; ?>人</p>
        <p>佣金：<?php echo $item['commission']; ?>%</p>
        <p>方案进度：<?php echo number_format($item['haveBuyNum'] / $item['totalNum'] * 100, 2); ?>%+<?php echo number_format($item['guaranteeNum'] / $item['totalNum'] * 100, 2); ?>%</p>
    </div>
</div>
<div class="buyItemsColor">
    <span style="width: <?php echo 300 * min(($item['guaranteeNum'] + $item['haveBuyNum']) / $item['totalNum'], 1); ?>px;"></span>
    <strong style="width: <?php echo 300 * $item['haveBuyNum'] / $item['totalNum']; ?>px"></strong>
</div>
<div class="buyItemsForm crowd-detail" data-id="<?php echo $item['orderId']; ?>" data-lid="<?php echo $item['lotType']; ?>" data-one="<?php echo $item['oneMoney']; ?>" data-max="<?php echo $item['totalNum'] - $item['haveBuyNum']; ?>">
    <input type="text" autocomplete="off" class="my-fraction gray" value="剩<?php echo ($item['totalNum'] - $item['haveBuyNum']) * $item['oneMoney']; ?>元" />
    <?php if ($isLogin): ?>
    <a class="join-crowd">购买</a>
    <?php else: ?>
    <a href="<?php echo $baseUrl; ?>passport">购买</a>
    <?php endif; ?>
</div>
