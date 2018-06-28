<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript">
    var fixedGGOptions = [14];
    var lotteryId = 11;
    var currIssue = '<?php echo $currIssue['seExpect']; ?>';
    var typeCnName = '<?php echo $cnName . ", 第" . $currIssue["seExpect"]  . "期" ; ?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lzc.js');?>"></script>
<script type="text/javascript">
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jcFixed.js');?>"></script>

<!--容器-->
<div class="wrap mod-box jingcai" id="container">
    <?php echo $this->load->view('elements/lottery/info_panel', array('currIssue' => $currIssue)); ?>
    <?php // echo $this->load->view('elements/crowd/buy'); ?>
    <!--彩票-->
    <div class="userLottery mod-box-bd">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'cast')); ?>
        <!--表格-->
        <div class="lotteryTableTH-fixed-box">   
            <div class="lotteryTableTH lottery-multi" style="border-bottom:none;">
                <table class="lotteryTableTwo">
                    <tr>
                        <th width="8%"></th>
                        <th width="14%">开赛时间</th>
                        <th width="14%">主队</th>
                        <th width="14%">客队</th>
                        <th width="12%">平均欧赔<div>胜&emsp;平&emsp;负</div></th>
                        <!-- <th width="8%">析</th> -->
                        <th width="10%">主队胜</th>
                        <th width="10%">平</th>
                        <th width="10%" class="last">客队胜</th>
                    </tr>
                </table>
                <div class="lotteryPlayWrap league-filter">
                    <h3 style="background: none;">联赛名称</h3>
                    </div>
                </div>
            </div>
        <!--表格循环-->
        <!--01-->

        <table class="lotteryTableCP matches lottery-multi" style="margin-left:-1px;width:999px;">
            <tbody>
            <?php foreach($matches as $match): ?>
                <tr
                    class="match"
                    data-mid="<?php echo $match['orderId']; ?>"
                    data-home="<?php echo $match['teamName1']; ?>"
                    data-away="<?php echo $match['teamName2']; ?>"
                    data-let="<?php echo 0; ?>"
                    data-league="<?php echo $match['gameName']; ?>"
                    data-wid="<?php //echo $match['weekId']; ?>" 
                    data-jzdt="<?php echo date('Y-m-d H:i:s', $match['gameTime'] / 1000); ?>" >
                    <td class="match-league" width="8%" style="background: #fff; color: #000;"><?php echo $match['gameName']; ?></td>
                    <td width="14%"><?php echo date('m-d H:i', $match['gameTime'] / 1000); ?></td>
                    <td width="14%"><?php echo $match['teamName1']; ?></td>
                    <td width="14%"><?php echo $match['teamName2']; ?></td>
                    <td width="12%" class="num-spf"><i><?php echo  number_format($match['odds1'],2); ?></i><i><?php echo number_format($match['odds2'],2); ?></i><i><?php echo number_format($match['odds3'],2); ?></i></td>
                    <!--<td width="8%" class="lnk-oyp"><a href="###">欧</a><a href="###">亚</a><a href="###">析</a></td>-->
                    <td width="10%" class="bgNumGray spf-option" data-val="3" data-odd="<?php echo $match['odds1']; ?>">3</td>
                    <td width="10%" class="bgNumGray spf-option" data-val="1" data-odd="<?php echo $match['odds2']; ?>">1</td>
                    <td width="10%" class="bgNumGray spf-option" data-val="0" data-odd="<?php echo $match['odds3']; ?>">0</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->

<!--选择信息-->
<div class="ele-fixed-box">
<div id="castPanel" class="lotteryZQbg cast-panel">
    <div class="lotteryZQCenter">
        <!--已选5场-->
        <div class="seleFiveWrap">
            <div class="seleFiveTit">
              <a class="btn btn-blue-bet">已选<span class="count-matches">0</span>场<i></i></a>
            </div>
            <div class="seleFiveBox">
                <div class="seleFiveBoxTit">
                    <strong>比赛</strong>
                    <span>投注内容</span>
                    <!--<img class="clear-matches" src="/caipiaoimg/v1.0/images/btn/btnClear.gif" alt="清空" width="61" height="41" />-->
                    <a class="clear-matches" href="javascript:void(0)">清空</a>
                </div>
                <div class="seleFiveBoxScroll"><table class="selected-matches"></table></div>
            </div>
        </div>
        <div class="seleFiveInfo">
          <ul class="clearfix">
            <li class="first">
              <div class="numbox"><b>共</b> <span class="bet-num">0</span> 注</div>
            </li>
            <li class="second">
              <div class="multi-modifier">
                  <strong class="fl">投注倍数：</strong>
                  <span class="minus selem">-</span>
                  <label><input class="multi number" type="text" value="1" autocomplete="off"  /></label>
                  <span class="plus selem">+</span>
              </div>
              <p><strong>投注金额：</strong><em class="bet-money cRed"></em> 元</p>
            </li>
            <li class="three">
              <!--
              <p><strong>预测奖金：</strong><span class="wordRed"><span class="min-money">0.00</span> - <span class="max-money">0.00</span></span><strong>元。</strong></p>
              <p><a class="seleView start-detail">奖金明细</a></p> -->
              <p class="agree"><input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我同意</label><a href="javascript:void(0);" class='lottery_pro'>《用户委托投注协议》</a></p>
            </li>
            <?php if($lotteryConfig[RJ]['status']):?>
            	<li class="last"><a id="pd_jczq_buy" class="btn btn-deepRed seleViewRed submit <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>"> 立即投注</a></li>
            <?php else :?>
            	<li class="last"><a id="pd_jczq_buy" class="btn btn-disabled seleViewRed <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>"> 立即投注</a></li>
            <?php endif;?>
          </ul>
        </div>

        </div>
    </div>
</div>
<!--选择信息end-->

<?php // $this->load->view('elements/jczq/calc_prize'); ?>
