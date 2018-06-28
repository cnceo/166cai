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
                        <th width="21%">主队VS客队</th>
                        <th width="6%">让球</th>
                        <th width="21%">投注区</th>
                        <th width="8%">比分</th>
                        <th width="8%">总进球</th>
                        <th width="8%" class="last">半全场</th>
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
                            if ($match['spfGd'] OR $match['bfGd'] OR $match['jqsGd']
                                OR $match['bqcGd'] OR $match['rqspfGd']
                            ) {
                                $matchCount += 1;
                            }
                        }
                    }
                    ?>
                    <?php if ($matchCount): ?>
                        <tr class="one">
                            <td width="55%" colspan="5" class="date"><?php echo $dates[1] ?><em
                                    class="pipe">|</em><?php echo date('m', $datetime); ?>月<?php echo date('d',
                                    $datetime); ?>日<em class="pipe">|</em>共<em
                                    class="cRed"><?php echo $matchCount; ?></em>场
                            </td>
                            <td width="7%">胜</td>
                            <td width="7%">平</td>
                            <td width="7%">负</td>
                            <td width="24%" colspan="3">&nbsp;</td>
                        </tr>
                        <?php foreach ($dateMatch as $match): ?>
                            <?php if ($match['spfGd'] OR $match['bfGd'] OR $match['jqsGd']
                                OR $match['bqcGd'] OR $match['rqspfGd']
                            ): ?>
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
                                    <td width="12%">停售<?php echo date('m-d H:i', $match['jzdt'] / 1000); ?></td>
                                    <td width="21%" rowspan="2"><em class="team"><?php echo $match['home']; ?></em> VS
                                        <em class="team"><?php echo $match['awary']; ?></em></td>
                                    <td width="6%"><b>0</b></td>
                                    <td width="7%"
                                        class="bgNumGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>"
                                        data-val="3"
                                        data-odd="<?php echo $match['spfSp3']; ?>"><div class="pos-r fix-posr"><?php if ( ! empty($match['spfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?><?php if ($match['spfGd']) {
                                            echo $match['spfSp3'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?></div></td>
                                    <td width="7%"
                                        class="bgNumGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>"
                                        data-val="1"
                                        data-odd="<?php echo $match['spfSp1']; ?>"><div class="pos-r fix-posr"><?php if ( ! empty($match['spfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?><?php if ($match['spfGd']) {
                                            echo $match['spfSp1'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?></div></td>
                                    <td width="7%"
                                        class="bgNumGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>"
                                        data-val="0"
                                        data-odd="<?php echo $match['spfSp0']; ?>"><div class="pos-r fix-posr"><?php if ( ! empty($match['spfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?><?php if ($match['spfGd']) {
                                            echo $match['spfSp0'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?></div></td>
                                    <td width="8%" rowspan="2">
                                        <div class="fold-name">
                                            <?php if ($match['bfGd']): ?>
                                                <div class="arrow-pos"></div>
                                                <?php if ( ! empty($match['bfFu'])): ?>
                                                    <div class="single-pos"></div>
                                                <?php endif; ?>
                                                <span class="open-cbf">展开投注</span>
                                            <?php else: ?>
                                                <span>未开售</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td width="8%" rowspan="2">
                                        <div class="fold-name">
                                            <?php if ($match['jqsGd']): ?>
                                                <div class="arrow-pos"></div>
                                                <?php if ( ! empty($match['jqsFu'])): ?>
                                                    <div class="single-pos"></div>
                                                <?php endif; ?>
                                                <span class="open-jqs">展开投注</span>
                                            <?php else: ?>
                                                <span>未开售</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td width="8%" rowspan="2">
                                        <div class="fold-name">
                                            <?php if ($match['bqcGd']): ?>
                                                <div class="arrow-pos"></div>
                                                <?php if ( ! empty($match['bqcFu'])): ?>
                                                    <div class="single-pos"></div>
                                                <?php endif; ?>
                                                <span class="open-bqc">展开投注</span>
                                            <?php else: ?>
                                                <span>未开售</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="rqspf-detail">
                                    <td width="10%">比赛<?php echo date('m-d H:i', $match['dt'] / 1000); ?></td>
                                    <td>
                                        <?php
                                        $className = '';
                                        if ($match['let'] > 0) {
                                            $className = ' class="cRed"';
                                        }
                                        elseif ($match['let'] < 0) {
                                            $className = ' class="cBlue"';
                                        }
                                        echo '<b' . $className . '>' . $match['let'] . '</b>';
                                        ?>
                                    </td>
                                    <td class="bgNumGray <?php if ($match['rqspfGd']): ?>rqspf-option<?php endif; ?>"
                                        data-val="3" data-odd="<?php echo $match['rqspfSp3']; ?>"><div class="pos-r fix-posr">
                                        <?php if ( ! empty($match['rqspfFu'])): ?>
                                            <div class="single-pos"></div>
                                        <?php endif; ?>
                                        <?php if ($match['rqspfGd']) {
                                            echo $match['rqspfSp3'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?>
                                    </div></td>
                                    <td class="bgNumGray <?php if ($match['rqspfGd']): ?>rqspf-option<?php endif; ?>"
                                        data-val="1" data-odd="<?php echo $match['rqspfSp1']; ?>"><div class="pos-r fix-posr">
                                        <?php if ( ! empty($match['rqspfFu'])): ?>
                                            <div class="single-pos"></div>
                                        <?php endif; ?>
                                        <?php if ($match['rqspfGd']) {
                                            echo $match['rqspfSp1'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?>
                                    </div></td>
                                    <td class="bgNumGray <?php if ($match['rqspfGd']): ?>rqspf-option<?php endif; ?>"
                                        data-val="0" data-odd="<?php echo $match['rqspfSp0']; ?>"><div class="pos-r fix-posr">
                                        <?php if ( ! empty($match['rqspfFu'])): ?>
                                            <div class="single-pos"></div>
                                        <?php endif; ?>
                                        <?php if ($match['rqspfGd']) {
                                            echo $match['rqspfSp0'];
                                        }
                                        else {
                                            echo '停售';
                                        } ?>
                                    </div></td>
                                </tr>
                                <tr class="bqc-options hidden">
                                    <td colspan="11">
                                        <div class="fold-wrap">
                                            <ul class="lotteryTfootUl">
                                                <?php foreach ($bqcOptions as $key => $value): ?>
                                                    <li class="bqc-option" data-val="<?php echo $key; ?>"
                                                        data-odd="<?php echo $match['bqcSp' . $key]; ?>">
                                                        <h4><?php echo $value; ?></h4>

                                                        <p><?php echo $match['bqcSp' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="cbf-options hidden">
                                    <td colspan="11">
                                        <div class="fold-wrap">
                                            <ul class="lotteryTfootUl lotteryTfootUl-first">
                                                <?php foreach ($cbfWinOptions as $key => $value): ?>
                                                    <li class="cbf-option" data-val="<?php echo $key; ?>"
                                                        data-odd="<?php echo $match['bfSp' . $key]; ?>">
                                                        <h4><?php echo $value; ?></h4>

                                                        <p><?php echo $match['bfSp' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <ul class="lotteryTfootUl">
                                                <?php foreach ($cbfDrawOptions as $key => $value): ?>
                                                    <li class="cbf-option" data-val="<?php echo $key; ?>"
                                                        data-odd="<?php echo $match['bfSp' . $key]; ?>">
                                                        <h4><?php echo $value; ?></h4>

                                                        <p><?php echo $match['bfSp' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <ul class="lotteryTfootUl lotteryTfootUl-last">
                                                <?php foreach ($cbfLoseOptions as $key => $value): ?>
                                                    <li class="cbf-option" data-val="<?php echo $key; ?>"
                                                        data-odd="<?php echo $match['bfSp' . $key]; ?>">
                                                        <h4><?php echo $value; ?></h4>

                                                        <p><?php echo $match['bfSp' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="jqs-options hidden">
                                    <td colspan="11">
                                        <div class="fold-wrap">
                                            <ul class="lotteryTfootUl">
                                                <?php foreach ($jqsOptions as $key => $value): ?>
                                                    <li class="jqs-option" data-val="<?php echo $key; ?>"
                                                        data-odd="<?php echo $match['jqsSp' . $key]; ?>">
                                                        <h4><?php echo $value; ?></h4>

                                                        <p><?php echo $match['jqsSp' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </td>
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
                        <a class="clear-matches" href="javascript:;">清空</a>
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

<?php //$this->load->view('elements/jczq/calc_prize'); ?>
