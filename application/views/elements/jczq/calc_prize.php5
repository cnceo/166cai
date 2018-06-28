<div class="lotteryFlayer detail-panel">
    <h1>奖金明细 <a class="close"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/close12x12.gif');?>" alt="关闭" width="12" height="12" /></a></h1>
    <div class="lotteryFlayerBox">
        <div class="fl">
            <table class="lotteryFbTable prize-list">
                <tr class="hover">
                  <th width="25%">中2场</th>
                  <td width="75%"><h4>最小奖金：<em class="cRed">222</em></h4><h5>最小奖金：<em class="cRed">433</em></h5></td>
                </tr>
            </table>
        </div>
        <div class="fr">
            <?php if (!isset($noOptimization)): ?>
            <div>
                <a class="ave-optimize">平均优化</a>
                <a class="reset">还原</a>
                <a class="do-cast">立即投注</a>
            </div>
            <?php endif; ?>
            <table class="lotteryFbTable2">
                <tr>
                  <th width="170">投注内容</th>
                  <th width="70">倍数（倍）</th>
                  <th width="70">金额（元）</th>
                  <th width="80">奖金</th>
                  <th width="34">&nbsp;</th>
                </tr>
            </table>
            <div class="lotteryFbSroll">
                <table class="lotteryFbTable3 prize-detail">
                    <tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
