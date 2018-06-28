<div class="userLotteryItems">
    <div>
        <table class="userLotteryTable">
            <tr>
                <th width="20%">发生时间</th>
                <th width="10%">交易类型</th>
                <th width="20%">交易说明</th>
                <th width="10%">收入</th>
                <th width="10%">支出</th>
                <th width="10%">余额</th>
                <th width="20%">流水号</th>
            </tr>
            <?php foreach ((array) $bills['data']['transList'] as $bill): ?>
            <tr>
                <td><?php echo date('Y-m-d H:i:s', $bill['createdTime'] / 1000); ?></td>
                <td><?php if (isset($billTypes[$bill['transType']])) echo $billTypes[$bill['transType']]['name']; else echo '未知'; ?></td>
                <td><?php echo $bill['subject']; ?></td>
                <?php if (isset($billTypes[$bill['transType']]) && !$billTypes[$bill['transType']]['positive']): ?>
                <td style="color: #C9141B;">-</td>
                <td><?php echo number_format($bill['money'], 2); ?></td>
                <?php else: ?>
                <td style="color: #C9141B;"><?php echo number_format($bill['money'], 2); ?></td>
                <td>-</td>
                <?php endif; ?>
                <td><?php echo number_format($bill['balance'], 2); ?></td>
                <td><?php echo $bill['outTradeNo']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<div class="buyPages">
    <p>
        <span class="prev-page">上一页</span>
        <select class="select-page">
            <?php for ($i = 1; $i <= $bills['data']['totalPage']; ++$i): ?>
            <option <?php if ($i == $bills['data']['currentPageNo']) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo $i; ?>/<?php echo $bills['data']['totalPage']; ?></option>
            <?php endfor; ?>
        </select>
        <span class="next-page" data-last="<?php echo $bills['data']['totalPage']; ?>">下一页</span>
    </p>
</div>
