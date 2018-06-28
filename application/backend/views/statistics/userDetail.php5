<?php $this->load->view("templates/head") ?>
<div class="">
    <div class="path">您的位置：<a href="/backend/Statistics/index?searchType=day">财务对账</a>&nbsp;&gt;&nbsp;<a href="/backend/Statistics/index?searchType=day">账户余额对账</a>&nbsp;&gt;&nbsp;明细</div>
    <div class="mod-tab mt20">
        <div class="mod-tab-bd">
            <ul>
                <li class="current" style="display: block;">
                    <div class="data-table-filter" style="width:100%">
                        <p class="pResult">
                            <b>充值 <span><?php echo m_format($tj['recharge']); ?></span></b>
                            <b>派奖 <span><?php echo m_format($tj['bonus']); ?></span></b>
                            <b>购彩 <span><?php echo m_format($tj['cost']); ?></span></b>
                            <b>购彩退款 <span><?php echo m_format($tj['refund']); ?></span></b>
                            <b>其他(+) <span><?php echo m_format($tj['oplus']); ?></span></b>
                            <b>其他(-) <span><?php echo m_format($tj['ominus']); ?></span></b>
                            <b>提现 <span><?php echo m_format($tj['withdraw']); ?></span></b>
                            <b>提现失败 <span><?php echo m_format($tj['withdraw_fail']); ?></span></b>
                            <b>送彩金 <span><?php echo m_format($tj['activity']); ?></span></b>
                            <b>返点 <span><?php echo m_format($tj['rebate']); ?></span></b>
                            <b>账户余额 <span><?php echo m_format($tj['money']); ?></span></b>
                        </p>
                    </div>
                    <div class="data-table-list mt10">
                        <table>
                            <colgroup>
                                <col width="10%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>日期</th>
                                    <th>充值（+）</th>
                                    <th>派奖（+）</th>
                                    <th>购彩（-）</th>
                                    <th>购彩退款（+）</th>
                                    <th>提现（-）</th>
                                    <th>提现失败（+）</th>
                                    <th>送彩金（+）</th>
                                    <th>返点（+）</th>
                                    <th>其他（+）</th>
                                    <th>其他（-）</th>
                                    <th>用户账户余额</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): ?>
                                <?php foreach ($list as $detail): ?>
                                <tr>
                                    <td><a href="" class="cBlue"><?php echo $detail['uname']; ?></a></td>
                                    <?php if($search['searchType'] == 'day'):?>
                                    <td><?php echo $search['date']; ?></td>
                                    <?php else: ?>
                                    <td><?php echo $detail['date']; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo m_format($detail['recharge']); ?></td>
                                    <td><?php echo m_format($detail['bonus']); ?></td>
                                    <td><?php echo m_format($detail['cost']); ?></td>
                                    <td><?php echo m_format($detail['refund']); ?></td>
                                    <td><?php echo m_format($detail['withdraw']); ?></td>
                                    <td><?php echo m_format($detail['withdraw_fail']); ?></td>
                                    <td><?php echo m_format($detail['activity']); ?></td>
                                    <td><?php echo m_format($detail['rebate']); ?></td>
                                    <td><?php echo m_format($detail['oplus']); ?></td>
                                    <td><?php echo m_format($detail['ominus']); ?></td>
                                    <td><?php echo m_format($detail['money']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="stat mt10">
                        <span class="ml20">本页共&nbsp;<?php echo $pages[2]; ?>&nbsp;条</span>
                        <span class="ml20">共&nbsp;<?php echo $pages[1]; ?>&nbsp;页</span>
                        <span class="ml20">总计&nbsp;<?php echo $pages[3]; ?>&nbsp;</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="page mt10">
         <?php echo $pages[0] ?>
    </div>
</div>
</body>
</html>
