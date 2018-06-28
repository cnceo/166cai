<?php if (!empty($crowd)): ?>
<div class="userIinfo">
    <div class="userIinfoImg">
        <img src="<?php echo $fileUrl; ?>avatar/<?php echo $crowd['togetherOrderInf']['createrid']; ?>" alt="头像" width="126" height="126" />
    </div>
    <div class="userIinfoName">
        <p><?php echo htmlspecialchars($crowd['togetherOrderInf']['creatername']); ?></p>
        <p>&nbsp;</p>
        <p>总中奖金额：<?php if (!empty($crowd['togetherOrderInf']['bonus'])): ?><?php echo number_format($crowd['togetherOrderInf']['bonus'], 2); ?>元<?php else: ?>-<?php endif; ?></p>
        <p>奖金回报：<?php echo number_format($crowd['togetherOrderInf']['bonus'] / $crowd['togetherOrderInf']['totalNum'] * 100, 4) * 100; ?>%</p>
    </div>
    <div class="userIinfoMondy">
        <p>方案金额：<?php echo $crowd['togetherOrderInf']['allmoney']; ?>元</p>
        <p>佣金：<?php echo $crowd['togetherOrderInf']['commission']; ?>%</p>
        <p>参与人数：<?php echo $crowd['togetherOrderInf']['partNum']; ?>人</p>
        <p>方案进度：<?php echo number_format(min(($crowd['togetherOrderInf']['guaranteeNum'] + $crowd['togetherOrderInf']['haveBuyNum']) / $crowd['togetherOrderInf']['totalNum'], 1) * 100, 2); ?>%</p>
        <div class="lotteryJionColor">保底：<?php echo number_format($crowd['togetherOrderInf']['guaranteeNum'] * 100 / $crowd['togetherOrderInf']['totalNum'], 2); ?>%</div>
    </div>
    <div class="fr crowd-detail"
         data-id="<?php echo $crowd['togetherOrderInf']['orderId']; ?>"
         data-one="<?php echo $crowd['togetherOrderInf']['oneMoney']; ?>"
         data-max="<?php echo $crowd['togetherOrderInf']['totalNum'] - $crowd['togetherOrderInf']['haveBuyNum']; ?>">
        <div class="buyDetailForm">
            <input type="text" class="my-fraction" />
            <span>剩<?php echo ($crowd['togetherOrderInf']['totalNum'] - $crowd['togetherOrderInf']['haveBuyNum']) * $crowd['togetherOrderInf']['oneMoney']; ?>元</span>
        </div>
        <h4>&nbsp;</h4>

        <p>
            <?php if ($isLogin && !empty($order)): ?>
                <?php if (!empty($order['uid'])): ?>
                    <?php if ($order['status'] <= 200): ?>
                    <a style="margin-right:7px;" class="cancel-crowd">
                        <img src="images/btn/btnBuyBack.gif" alt="撤消合买" width="100" height="40" />
                    </a>
                    <?php else: ?>
                        <?php if (($crowd['togetherOrderInf']['guaranteeNum'] + $crowd['togetherOrderInf']['haveBuyNum']) / $crowd['togetherOrderInf']['totalNum'] < 0.8 && $order['status'] > 100 && $order['qsFlag'] == 0 && $crowd['togetherOrderInf']['isFull'] < 2): ?>
                        <a style="margin-right:7px;" class="cancel-crowd">
                            <img src="images/btn/btnBuyBack.gif" alt="撤消合买" width="100" height="40" />
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($isLogin): ?>
                <?php if (empty($order)): ?>
                <a class="join-crowd">
                    <img src="images/btn/btnBuyGo.gif" alt="立即购买" width="137" height="40" />
                </a>
                <?php else: ?>
                    <?php if ($order['status'] > 100 && $order['qsFlag'] == 0 && $crowd['togetherOrderInf']['isFull'] < 2): ?>
                    <a class="join-crowd">
                        <img src="images/btn/btnBuyGo.gif" alt="立即购买" width="137" height="40" />
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
            <a href="<?php echo $baseUrl; ?>passport">
                <img src="images/btn/btnBuyGo.gif" alt="立即购买" width="137" height="40" />
            </a>
            <?php endif; ?>
        </p>
    </div>
</div>
<?php endif; ?>
