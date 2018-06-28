<?php $this->load->view('comm/header'); ?>
	<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper bet-detail">
    	<?php if($bonusInfo): ?>
        <table class="table-bet">
            <colgroup>
                <col width="10%">
                <col width="36%">
                <col width="18%">
                <col width="12%">
                <col width="24%">
            </colgroup>
            <thead>
                <tr>
                    <th>序号</th>
                    <th>场次</th>
                    <th>过关方式</th>
                    <th>倍数</th>
                    <th>中奖金额(元)</th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($bonusInfo as $key => $detail): ?>
            	<tr>
                    <td><?php echo $detail['index']; ?></td>
                    <td><?php echo $detail['matchInfo']; ?></td>
                    <td><?php echo $detail['type']; ?></td>
                    <td><?php echo $detail['multis']; ?></td>
                    <?php if($detail['status'] == $orderStatus['win']): ?>
					<td><span class="bingo"><?php echo ParseUnit($detail['bonus'],1); ?></span></td>
                    <?php elseif($detail['status'] == $orderStatus['notwin']): ?>
                    <td><span>未中奖</span></td>
                    <?php else :?>
                    <td><span><?php echo parse_order_status($detail['status'], 0);?></span></td>
                	<?php endif; ?>
                </tr>
            	<?php endforeach; ?>
            </tbody>
        </table>
    	<?php endif; ?>
    </div>
</body>
</html>