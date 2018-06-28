<!-- 订单详情 -->
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<div class="wrap mod-box jc-sg lsj-detail">
    <div class="mod-box-hd">
        <h1 class="mod-box-title">乐善奖详情</h1>
        <a href="/info/csxw/132122" target="_blank">查看规则&gt;</a>
        <span class="mod-box-note">派奖时间：乐善奖会在当期大乐透派奖时一并派奖</span>
    </div>
    <div class="mod-box-bd">
        <div class="table-box">
            <div class="table">
                <?php if(!empty($ticketDetail)): ?>
                <table>
                    <thead>
                        <tr>
                            <th width="40%">订单方案</th>
                            <th width="35%">对应乐善码</th>
                            <th width="25%">奖金</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ticketDetail as $ticket): ?>
                        <tr>
                            <td class="tal">
                                <ul class="lsj-format stage-detail">
                                    <?php foreach ($ticket['code'] as $code): ?>
                                    <li>
                                        <div class="number-game">
                                            <?php echo $code; ?>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <div class="award-nums">
                                    <?php echo $ticket['awardNum']; ?>
                                </div>
                            </td>
                            <td><?php echo $ticket['bonusStatus']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- 订单详情end -->