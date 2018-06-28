<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jczq.js'); ?>"></script>
<script type="text/javascript">
    var type = '<?php echo $jczqType; ?>';
    var typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jczqType]["cnName"]; ?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jczqFixed.js'); ?>"></script>

<!--容器-->
<div class="wrap mod-box jingcai" id="container">
    <?php echo $this->load->view('elements/lottery/info_panel', array('noIssue' => TRUE)); ?>
    <?php //echo $this->load->view('elements/crowd/buy'); ?>
    <!--彩票-->
    <div class="userLottery mod-box-bd">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'cast')); ?>
        <!--表格-->
        <div class="lotteryTableTH-fixed-box">
            <div class="lotteryTableTH">
                <table class="lotteryTableTwo">
                    <tr>
                        <th width="10%"></th>
                        <th width="6%">编号</th>
                        <th width="12%">停售时间</th>
                        <th width="27%">主队VS客队</th>
                        <th width="5%">胜胜</th>
                        <th width="5%">胜平</th>
                        <th width="5%">胜负</th>
                        <th width="5%">平胜</th>
                        <th width="5%">平平</th>
                        <th width="5%">平负</th>
                        <th width="5%">负胜</th>
                        <th width="5%">负平</th>
                        <th width="5%" class="last">负负</th>
                    </tr>
                </table>
                <div class="lotteryPlayWrap league-filter">
                    <h3>赛事</h3>

                    <div class="lotteryPlayBox">
                        <ul class="clearfix">
                            <?php foreach ($leagues as $league => $key): ?>
                                <li>
                                    <label><input class="league" type="checkbox" checked="checked"
                                                  value="<?php echo $key; ?>"/> <?php echo $league; ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p>
                            <a class="select-all">全选</a>
                            <a class="select-anti">反选</a>
                            <a class="select-none">清空</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!--表格循环-->
        <!--01-->

        <table class="lotteryTableCP matches">
            <tbody>
            <?php if (count($matches) > 0): ?>
                <?php foreach ($matches as $date => $dateMatch): ?>
                    <?php
                    $dates = explode(' ', $date);
                    $datetime = strtotime($dates[0]);
                    $dataname = ($dates[0] == date('Y-m-d')) ? '今天' : '明天';
                    $matchCount = 0;
                    if ($dateMatch) {
                        foreach ($dateMatch as $match) {
                            if ($match['bqcGd']) {
                                $matchCount += 1;
                            }
                        }
                    }
                    ?>

                    <?php if ($matchCount): ?>
                        <tr class="one">
                            <td width="45%" colspan="5" class="date"><?php echo $dates[1] ?><em
                                    class="pipe">|</em><?php echo date('m', $datetime); ?>月<?php echo date('d',
                                    $datetime); ?>日<em class="pipe">|</em>共<em
                                    class="cRed"><?php echo $matchCount; ?></em>场
                            </td>
                            <td width="55%" colspan="9">&nbsp;</td>
                        </tr>
                        <?php foreach ($dateMatch as $match): ?>
                            <?php if ($match['bqcGd']): ?>
                                <tr
                                    class="match"
                                    data-mid="<?php echo $match['mid']; ?>"
                                    data-home="<?php echo $match['home']; ?>"
                                    data-away="<?php echo $match['awary']; ?>"
                                    data-let="<?php echo $match['let']; ?>"
                                    data-league="<?php echo $leagues[$match['name']]; ?>"
                                    data-spf_fu="<?php echo $match['spfFu']; ?>"
                                    data-rqspf_fu="<?php echo $match['rqspfFu']; ?>"
                                    data-jqs_fu="<?php echo $match['jqsFu']; ?>"
                                    data-cbf_fu="<?php echo $match['bfFu']; ?>"
                                    data-bqc_fu="<?php echo $match['bqcFu']; ?>"
                                    data-wid="<?php echo $match['weekId']; ?>"
                                    data-jzdt="<?php echo date('Y-m-d H:i:s', $match['jzdt'] / 1000); ?>">
                                    <td class="match-league" width="10%" rowspan="2"
                                        style="background: <?php echo $match['cl']; ?>;"><?php echo $match['name']; ?></td>
                                    <td width="6%" rowspan="2"><?php echo $match['weekId']; ?></td>
                                    <td width="12%">停售：<?php echo date('m-d H:i', $match['jzdt'] / 1000); ?></td>
                                    <td width="27%" rowspan="2"><em class="team"><?php echo $match['home']; ?></em> VS
                                        <em
                                            class="team"><?php echo $match['awary']; ?></em></td>
                                    <?php foreach ($bqcOptions as $key => $value): ?>
                                        <td width="5%" class="bgGray bqc-option" rowspan="2"
                                            data-val="<?php echo $key; ?>"
                                            data-odd="<?php echo $match['bqcSp' . $key]; ?>">
                                            <div class="pos-r"><?php if ( ! empty($match['bqcFu'])): ?>
                                                <div class="single-pos"></div>
                                            <?php endif; ?>
                                            <?php echo empty($match['bqcGd']) ? '停售' : $match['bqcSp' . $key]; ?>
                                        </div></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="rqspf-detail">
                                    <td width="10%">比赛：<?php echo date('m-d H:i', $match['dt'] / 1000); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class='one'>
                            <td width="100%" colspan="11" class="date">暂时无赛事</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="one">
                    <td width="100%" colspan="11" class="date">暂时无赛事</td>
                </tr>
            <?php endif; ?>
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
                    <div class="seleFiveBoxScroll">
                        <table class="selected-matches"></table>
                    </div>
                </div>
            </div>
            <div class="seleFiveInfo">
                <ul class="clearfix">
                    <li class="first">
                        <?php $this->load->view('elements/jczq/gg_types'); ?>
                        <div class="numbox"><b>共</b> <span class="bet-num">0</span> 注</div>
                    </li>
                    <li class="second">
                        <div class="multi-modifier">
                            <strong class="fl">投注倍数：</strong>
                            <span class="minus selem">-</span>
                            <label><input class="multi number" type="text" value="1" autocomplete="off"/></label>
                            <span class="plus selem">+</span>
                        </div>
                        <p><strong>投注金额：</strong><em class="bet-money cRed"></em> 元</p>
                    </li>
                    <li class="three">
                        <p><strong>预测奖金：</strong><span class="wordRed"><span class="min-money">0.00</span> - <span
                                    class="max-money">0.00</span></span><strong>元。</strong></p>
                        <!-- <p><a class="seleView start-detail">奖金明细</a></p> -->
                        <p class="agree"><input class="ipt_checkbox" type="checkbox" checked="checked"
                                                id="agreenment"><label for="agreenment">我同意</label><a
                                href="javascript:void(0);" class='lottery_pro'>《用户委托投注协议》</a></p>
                    </li>
                    <?php if ($lotteryConfig[JCZQ]['status']): ?>
                        <li class="last"><a id="pd_jczq_buy"
                                            class="btn btn-deepRed seleViewRed submit <?php if ( ! $isLogin) {
                                                echo 'not-login';
                                            } ?><?php echo $showBind ? ' not-bind' : ''; ?>"> 立即投注</a></li>
                    <?php else : ?>
                        <li class="last"><a id="pd_jczq_buy"
                                            class="btn btn-disabled seleViewRed <?php if ( ! $isLogin) {
                                                echo 'not-login';
                                            } ?><?php echo $showBind ? ' not-bind' : ''; ?>"> 立即投注</a></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>
</div>
<!--选择信息end-->

<?php $this->load->view('elements/jczq/calc_prize'); ?>
