<?php
$this->load->view("templates/head");
?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="">排行榜活动审核</a></div>
<div class="mod-tab mt20 mb20">
    <div class="mod-tab-bd">
        <ul>
            <li style="display: block;">
                <div class="data-table-filter mt20">
                    <table>
                        <tbody>
                            <tr>
                                <td width="320">活动期次：<span><?php echo $pissue; ?></span>期</td>
                                <td width="280">彩种系列：<span><?php echo $lname; ?></span></td>
                                <td width="320">活动时间：<span><?php echo $start_time . '至' . $end_time; ?></span></td>
                                <td width="220">参与彩种：<span><?php echo $lids; ?></span></td>
                            </tr>
                            <tr>
                                <td>用户统计：<span><?php echo $totalNum; ?></span></td>
                                <td>订单总额（元）：<span><?php echo ParseUnit($totalMoney, 1); ?></span></td>
                                <td>中奖总额（元）：<span><?php echo ParseUnit($totalMargin, 1); ?></span></td>
                                <td>加奖总额（元）：<span><?php echo ParseUnit($totalAdd, 1); ?></span></td>
                            </tr>
                            <tr>
                                <td>链接地址：<span><?php echo $url; ?></span></td>
                                <td>活动状态：<span><?php echo $statusMsg; ?></span></td>
                                <td>派奖状态：<span><?php echo $cstateMsg; ?></span></td>
                            </tr>
                        </tbody>  
                    </table>
                </div>
                <div class="mt10">
                    <img src="<?php echo $imgUrl; ?>" width="340" height="255">
                <div>
                <div class="mt10">
                    <table class="data-table-list">
                        <colgroup>
                            <col width="250">
                            <col width="300">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>名次</th>
                                <th>彩金奖励（元）</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($extra)):?>
                            <?php foreach ($extra as $items):?>
                            <tr>
                                <td><?php echo $items['min'] . '至' . $items['max'];?></td>
                                <td><?php echo ParseUnit($items['money'], 1);?></td>
                            </tr>
                            <?php endforeach;?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </li>
            <div class="mt10">
                <?php echo $rule; ?>
            </div>
        </ul>
    </div>
</div>
</body>
</html>