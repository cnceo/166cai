<script type="text/javascript">
$(function() {
    $('.lottery-name').each(function(k, ele) {
        $(ele).html(cx.Lottery.getCnName($(ele).data('lid')));
    });
     $('.order-status').each(function(k, ele) {
        $(ele).html(cx.Order.getStatus($(ele).data('status'), $(ele).data('rflag')));
    });
});
</script>
<div class="userLotteryItems">
    <!--普通投注table-->
    <div>
        <table class="userLotteryTable">
            <tr>
              <th width="20%">投注时间</th>
              <th width="15%">彩种</th>
              <th width="15%">投注金额</th>
              <th width="10%">状态</th>
              <th width="10%">奖金</th>
              <th width="30%">操作</th>
            </tr>
            <?php foreach ((array) $orders['data']['items'] as $item): ?>
            <tr>
                <td><?php echo date('Y-m-d H:i:s', $item['createTime'] / 1000); ?></td>
                <td class="lottery-name" data-lid="<?php echo $item['lotType']; ?>"></td>
                <td><?php echo number_format($item['allMoney'], 2); ?></td>
                <td class="order-status" data-status="<?php echo $item['status']; ?>"  data-rflag="<?php echo $item['returnFlag']; ?>"></td>
                <td><?php if (!empty($item['margin'])): ?><?php echo number_format($item['margin'], 2); ?><?php else: ?>-<?php endif; ?></td>
                <td>
                    <a target="_blank" href="<?php echo $baseUrl; ?>orders/detail/<?php echo $item['id']; ?>" class="userTextGreen">详情</a>
                    <?php if ($item['status'] <= 100): ?>
                        <?php if ($item['qsFlag'] > 0): ?>
                        <a class="userTextRed">已失效</a>
                        <?php else: ?>
                        <a class="userTextRed pay-order" data-oid="<?php echo $item['id']; ?>">付款</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<div class="buyPages">
    <p>
        <span class="prev-page">上一页</span>
        <select class="select-page">
            <?php for ($i = 1; $i <= $orders['data']['totalPage']; ++$i): ?>
            <option <?php if ($i == $orders['data']['currentPageNo']) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo $i; ?>/<?php echo $orders['data']['totalPage']; ?></option>
            <?php endfor; ?>
        </select>
        <span class="next-page" data-last="<?php echo $orders['data']['totalPage']; ?>">下一页</span>
    </p>
</div>
