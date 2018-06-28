
<!-- 合买成功 -->
<div class="tab-radio-inner" style="display:block">
    <table class="mod-tableA" id="history">
        <thead>
            <tr>
                <th width="17%">时间</th>
                <th width="20%">彩种期次</th>
                <th width="12%">方案金额</th>
                <th width="15%">税前奖金</th>
                <th width="12%">回报率</th>
                <th width="12%">战绩奖励</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unitedOrders as $unitOrder) { ?>
                <tr>
                    <td><?php echo $unitOrder['created']; ?></td>
                    <td><?php echo $unitOrder['lid']; ?></td>
                    <td><?php echo round($unitOrder['money']); ?>元</td>
                    <td><font style='color:#e60000'><?php echo $unitOrder['orderBonus']; ?></font>元</td>
                    <td><?php echo $unitOrder['returnRate']; ?></td>
                    <td><span class="level"><?php echo $unitOrder['award']; ?></span></td>
                    <td><a target='_blank' class='cBlue' href='/hemai/detail/hm<?php echo $unitOrder['orderId']; ?>'>详情</a></td>
                </tr>
            <?php } ?>
            <?php if (empty($unitedOrders)) { ?>
                <tr>
                    <td colspan="7">
                        <div class="noData"><p>暂无记录！赶紧<a href="/hall" target="_blank">发起合买</a></p></div>
                    </td>
                </tr>  
            <?php } ?>
        </tbody>
    </table>
    <div id="historyPage">
        <?php echo (count($unitedOrders) > 0) ? $pages : ''; ?>
    </div>
</div>
<!-- 合买成功 -->