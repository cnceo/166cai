<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jclq.js'); ?>"></script>
<script type="text/javascript">
    var type = '<?php echo $jclqType; ?>';
    var typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jclqType]["cnName"]; ?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jcFixed.js'); ?>"></script>
<!--容器-->
<div class="wrap mod-box jingcai" id="container">
    <?php echo $this->load->view('elements/lottery/info_panel', array('noIssue' => TRUE)); ?>
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
                        <th width="26%">客队VS主队</th>
                        <th width="6%">让分</th>
                        <th width="7%">主负</th>
                        <th width="7%">主胜</th>
                        <th width="9%">大分</th>
                        <th width="9%">小分</th>
                        <th width="8%" class="last">胜分差</th>
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
                            if ($match['sfGd'] OR $match['dxfGd'] OR $match['sfcGd'] OR $match['rfsfGd']) {
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
                            <td width="55%" colspan="6">&nbsp;</td>
                        </tr>
                        <?php foreach ($dateMatch as $match): ?>
                            <?php if ($match['sfGd'] OR $match['dxfGd'] OR $match['sfcGd'] OR $match['rfsfGd']): ?>
                                <tr
                                    class="match"
                                    data-mid="<?php echo $match['mid']; ?>"
                                    data-home="<?php echo $match['home']; ?>"
                                    data-away="<?php echo $match['awary']; ?>"
                                    data-let="<?php echo $match['let']; ?>"
                                    data-league="<?php echo $leagues[$match['name']]; ?>"
                                    data-wid="<?php echo $match['weekId']; ?>"
                                    data-jzdt="<?php echo date('Y-m-d H:i:s', $match['jzdt'] / 1000); ?>"
                                    data-sf_fu="<?php echo $match['sfFu']; ?>"
                                    data-rfsf_fu="<?php echo $match['rfsfFu']; ?>"
                                    data-dxf_fu="<?php echo $match['dxfFu']; ?>"
                                    data-sfc_fu="<?php echo $match['sfcFu']; ?>"
                                    data-prescore="<?php echo $match['preScore']; ?>">
                                    <td class="match-league" width="10%" rowspan="2"
                                        style="background: <?php echo $match['cl']; ?>;"><?php echo $match['name']; ?></td>
                                    <td width="6%" rowspan="2"><?php echo $match['weekId']; ?></td>
                                    <td width="12%">停售：<?php echo date('m-d H:i', $match['jzdt'] / 1000); ?></td>
                                    <td width="26%" rowspan="2"><em class="team"><?php echo $match['awary']; ?></em> VS
                                        <em class="team"><?php echo $match['home']; ?></em></td>
                                    <td width="6%">0</td>
                                    <td width="7%"
                                        class="bgNumGray <?php if ($match['sfGd']): ?>sf-option<?php endif; ?>"
                                        data-val="0"
                                        data-odd="<?php echo $match['sfHf']; ?>"
                                        ><div class="pos-r fix-posr"><?php if ($match['sfGd']) {
                                            echo $match['sfHf'];
                                        }
                                        else {
                                            echo '停售 ';
                                        } ?>
                                        <?php if ( ! empty($match['sfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                    <td width="7%"
                                        class="bgNumGray <?php if ($match['sfGd']): ?>sf-option<?php endif; ?>"
                                        data-val="3"
                                        data-odd="<?php echo $match['sfHs']; ?>"
                                        ><div class="pos-r fix-posr"><?php if ($match['sfGd']) {
                                            echo $match['sfHs'];
                                        }
                                        else {
                                            echo '停售 ';
                                        } ?>
                                        <?php if ( ! empty($match['sfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                    <td width="9%" rowspan="2"
                                        class="bgGray <?php if ($match['dxfGd']): ?>dxf-option<?php endif; ?>"
                                        data-val="3"
                                        data-odd="<?php echo $match['dxfBig']; ?>"
                                        >
                                        <div class="pos-r fix-posr"><?php if ($match['dxfGd']) {
                                            echo "总分&gt", $match['preScore'], "<br />", $match['dxfBig'];
                                        }
                                        else {
                                            echo '未开售';
                                        } ?>
                                        <?php if ( ! empty($match['dxfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                    <td width="9%" rowspan="2"
                                        class="bgGray <?php if ($match['dxfGd']): ?>dxf-option<?php endif; ?>"
                                        data-val="0"
                                        data-odd="<?php echo $match['dxfSmall']; ?>"
                                        >
                                        <div class="pos-r fix-posr"><?php if ($match['dxfGd']) {
                                            echo "总分&lt", $match['preScore'], "<br />", $match['dxfSmall'];
                                        }
                                        else {
                                            echo '未开售';
                                        } ?>
                                        <?php if ( ! empty($match['dxfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                    <td width="8%" rowspan="2" class="bgGray">
                                        <div class="pos-r">
                                        <div class="fold-name">
                                            <?php if ($match['sfcGd']): ?>
                                                <div class="arrow-pos"></div>
                                                <?php if ( ! empty($match['sfcFu'])): ?>
                                                    <div class="single-pos"></div>
                                                <?php endif; ?>
                                                <span class="open-sfc">展开投注</span>
                                            <?php else: ?>
                                                <span>未开售</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                                <tr class="rfsf-detail">
                                    <td>比赛：<?php echo date('m-d H:i', $match['dt'] / 1000); ?></td>
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
                                    <td class="bgNumGray <?php if ($match['rfsfGd']): ?>rfsf-option<?php endif; ?>"
                                        data-val="0"
                                        data-odd="<?php echo $match['rfsfHf']; ?>"
                                        ><div class="pos-r fix-posr"><?php if ($match['rfsfGd']) {
                                            echo $match['rfsfHf'];
                                        }
                                        else {
                                            echo '停售 ';
                                        } ?>
                                        <?php if ( ! empty($match['rfsfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                    <td class="bgNumGray <?php if ($match['rfsfGd']): ?>rfsf-option<?php endif; ?>"
                                        data-val="3"
                                        data-odd="<?php echo $match['rfsfHs']; ?>"
                                        ><div class="pos-r fix-posr"><?php if ($match['rfsfGd']) {
                                            echo $match['rfsfHs'];
                                        }
                                        else {
                                            echo '停售 ';
                                        } ?>
                                        <?php if ( ! empty($match['rfsfFu'])): ?>
                                            <div class="single-pos"></div><?php endif; ?>
                                    </div></td>
                                </tr>
                                <tr class="sfc-options hidden">
                                    <td colspan="10">
                                        <div class="fold-wrap">
                                            <p style="float:left; font-weight: bold;height:48px;line-height:48px;margin:0 10px 10px 0;">
                                                客队</p>
                                            <ul class="lotteryTfootUl">
                                                <?php $count = 1; ?>
                                                <?php foreach ($sfcOptions as $key => $value): ?>
                                                    <li class="sfc-option"
                                                        data-val="1<?php echo $count ++; ?>"
                                                        data-odd="<?php echo $match['sfcAs' . $key]; ?>">
                                                        <h4><?php echo $value; ?>分</h4>

                                                        <p><?php echo $match['sfcAs' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach ?>
                                            </ul>
                                            <p style="float:left; font-weight: bold;height:48px;line-height:48px;margin-right:10px;clear:both;">
                                                主队</p>
                                            <ul class="lotteryTfootUl">
                                                <?php $count = 1; ?>
                                                <?php foreach ($sfcOptions as $key => $value): ?>
                                                    <li class="sfc-option"
                                                        data-val="0<?php echo $count ++; ?>"
                                                        data-odd="<?php echo $match['sfcHs' . $key]; ?>">
                                                        <h4><?php echo $value; ?>分</h4>

                                                        <p><?php echo $match['sfcHs' . $key]; ?></p>
                                                    </li>
                                                <?php endforeach ?>
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
                <tr class='one'>
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
                        <!--<img class="clear-matches" src="images/btn/btnClear.gif" alt="清空" width="61" height="41" />-->
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
                        <?php $this->load->view('elements/jclq/gg_types'); ?>
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
                    <?php if ($lotteryConfig[JCLQ]['status']): ?>
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
