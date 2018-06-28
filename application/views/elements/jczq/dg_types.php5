<div class="userLboxItems">
    <ul class="nav">
      <?php foreach ($typeMAP as $key => $value): ?>
      <li <?php if ($key == $jczqType): ?>class="selected"<?php endif; ?> >
        <a href="<?php echo $baseUrl; ?>jczq/<?php echo $key; ?>">
            <p><?php echo $value['cnName']; ?></p>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <div class="lotteryChoose">
        <ul class="seleFiveInfo bet-chip">
            <li class="yuan yuan-2 selected" data-chip="1"><span>2</span></li>
            <li class="yuan yuan-10" data-chip="5"><span>10</span></li>
            <li class="yuan yuan-50" data-chip="25"><span>50</span></li>
            <li class="yuan yuan-100" data-chip="50"><span>100</span></li>
            <li class="yuan yuan-500" data-chip="250"><span>500</span></li>
            <li class="yuan yuan-1000" data-chip="500"><span>1000</span></li>
            <li class="yuan yuan-5000" data-chip="2500"><span>5000</span></li>
        </ul>
        <div class="seleFiveBtn">
        	<h3>
            	<a class="start-detail fr">投注明细&gt;&gt;</a>
                <p class="fr">
                    <strong>预测奖金：</strong>
                    <span class="wordRed">
                        <span class="min-money">0.00</span>
                        -
                        <span class="max-money">0.00</span>
                    </span>
                    <strong>元。</strong>
                </p>
            </h3>
            <p class="clear"></p>
            <p>
                <a class="seleViewYellow start-crowd <?php if (!$isLogin) echo 'not-login'; ?>">发起合买</a>
                <a id="pd_jczq_buy" class="seleViewRed submit <?php if (!$isLogin) echo 'not-login'; ?>">共 <span class="bet-money">0</span>元 立即投注</a>
            </p>
		</div>
        <p class="clear"></p>
    </div>
</div>
