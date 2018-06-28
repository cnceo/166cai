<?php
/**
 * @see JCZQ::dg()
 * @var $leagues
 * @var $leaguesNew
 * @var $bqcOptions
 * @var $matches
 * @var $jczqType
 * @var $cnName
 * @var $typeMAP
 * @var $lotteryConfig
 * @var $stakeStr
 * @var $id
 * @var $multiple
 * @var $spfs
 * @var $spfp
 * @var $spff
 */
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}

?>

<!--容器-->
<div class="wrap cp-box bet-jc jczq-dg">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>

    <div class="cp-box-bd bet">
        <div class="bet-main">
            <div class="jc-table-box">
                <div class="jc-table-hd-box">
                    <div class="jc-table-hd">
                        <table>
                            <colgroup>
                                <col width="76">
		                        <col width="76">
		                        <col width="231">
		                        <col width="49">
		                        <col width="168">
		                        <col width="85">
		                        <col width="63">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>
                                    <div class="league-filter">
                                        <h3 class="league-filter-title">赛事信息<i class="arrow"></i></h3>

                                        <div class="league-filter-box">
                                            <ul>
                                                <?php foreach ($leagues as $league => $key): ?>
                                                    <li>
                                                        <label><input class="league" type="checkbox" checked="checked"
                                                                      value="<?php echo $key; ?>"/> <?php echo $leaguesNew[$league]; ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <div class="league-filter-action">
                                                <p class="fr">
                                                    <span class="select-all">全选</span>
                                                    <span class="select-anti">反选</span>
                                                    <span class="select-none">清空</span>
                                                </p>
                                                <span class="select-five">五大联赛</span>
                                            </div>
                                        </div>
                                    </div>
                                    <u></u>
                                </th>
                                <th>
                                    <div class="table-option">
                                    <span class="table-option-title" id="selectTime">停售时间<i
                                            class="table-option-arrow"></i></span>
                                        <ul class="table-option-list">
                                            <li class="list-item" data-field="dt"><a>比赛时间</a></li>
                                            <li class="list-item" data-field="jzdt"><a>停售时间</a></li>
                                        </ul>
                                    </div>
                                    <u></u>
                                </th>
                                <th>
                                    <div class="name"><span class="name-l">主队</span><s></s><span class="name-r">客队</span></div>
                                    <u></u>
                                </th>
                                <th>
                                    <div class="th-rangqiu">
                                        让球
                                        <div class="mod-tips th-rangqiu-ipt">
                                            <i class="icon-font">&#xe60d;</i>

                                            <div class="mod-tips-t">示例：让球胜平负 比赛结果2：1 让球：-1 彩果：让球平<b></b><s></s></div>
                                        </div>
                                    </div>
                                    <u></u>
                                </th>
                                <th>
                                    <div class="th-spf"><span><?php echo $wenan['jzspf']['3']?></span><span><?php echo $wenan['jzspf']['1']?></span><span><?php echo $wenan['jzspf']['0']?></span>

                                        <div target="_self" class="mod-tips th-spf-ipt">
                                            <input type="checkbox" id="showOdd" checked="checked">

                                            <div class="mod-tips-t" id="odd-tips">关闭赔率展示浮层<b></b><s></s></div>
                                        </div>
                                    </div>
                                    <u></u>
                                </th>
                                <th>其他玩法<u></u></th>
                                <th>数据</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            <div class="jc-table-filter">
              <label for="rmss"><input id="rmss" name="jcFilter" type="checkbox">热门赛事</label>
              <label for="xstsbs"><input id="xstsbs" name="jcFilter" type="checkbox">显示已截止比赛(<i><?php echo $count['count'];?></i>场)</label>
              <label for="yxzbs"><input id="yxzbs" name="jcFilter" type="checkbox">已选择比赛</label>
              <div class="filter-date">
                <strong>赛事回查</strong>
                <dl class="simu-select select-small">
                    <dt><?php echo date('Y.m.d', strtotime($date)); ?><i class="arrow"></i></dt>
                    <dd class="select-opt">
                        <div class="select-opt-in">
                            <?php foreach ($dates as $key => $value): ?>
                                <a href="<?php echo $baseUrl; ?>jczq/dg?date=<?php echo $value; ?>"><?php echo $key; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </dd>
                </dl>
              </div>
          </div>
            <div class="jc-box oldmatch cutoff hidden">
                
            </div>   
                <!--表格循环-->
                <?php if (count($matches)): ?>
                    <?php foreach ($matches as $date => $dateMatch): ?>
                        <?php
                        $matchCount = 0;
                        if ($dateMatch) {
                            foreach ($dateMatch as $match) {
                                if ($match['spfFu'] OR $match['rqspfFu']) {
                                    $matchCount += 1;
                                }
                            }
                        }
                        ?>
                        <?php if ($matchCount): ?>

                            <div class="jc-box">
                                <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                    <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                    共<?php echo $matchCount; ?>场比赛可投注
                                </div>
                                <div class="jc-box-bd">
                                    <table class="jc-table matches">
                                        <colgroup>
                                            <col width="76">
					                        <col width="76">
					                        <col width="231">
					                        <col width="49">
					                        <col width="56">
					                        <col width="56">
					                        <col width="56">
					                        <col width="85">
					                        <col width="63">
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($dateMatch as $match): ?>
                                            <?php if ($match['spfFu'] OR $match['rqspfFu']): ?>
                                                <tr
                                                    class="match<?php echo $match['hot'] ? ($match['hotid'] == 9 ? ' match-pa' :' match-hot') : ''; ?>"
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
                                                    data-dt="<?php echo date('Y-m-d H:i:s', $match['dt'] / 1000); ?>"
                                                    data-jzdt="<?php echo date('Y-m-d H:i:s',
                                                        $match['jzdt'] / 1000); ?>"
                                                    data-select="0"
                                                    data-cancel="<?php echo $match['m_status']==1?1:0 ?>"
                                                    data-old="<?php echo $match['full_score']?1:0 ?>">

                                                    <td class="bd-no-l<?php echo $match['hot'] ? ($match['hotid'] == 9 ? ' pa-box' :' hot-box') : ''; ?>">
                                                        <div
                                                            class="type"><?php echo $match['weekId']; ?>
                                                            <span
                                                                style="background: <?php echo $match['cl']; ?>;"
                                                            ><?php echo $match['name']; ?></span>
                                                        </div>
                                                        <?php if ($match['hot']): 
                                                        	if ($match['hotid'] == 9) {?>
                                                            <div class="pa"></div>
                                                            <?php }else {?>
                                                            <div class="hot"></div>
                                                            <?php }
                                                        endif; ?>
                                                    </td>
                                                    <td class="bd-no-l">
                                                        <div class="time mod-tips"><span
                                                                class="time-num"><?php echo date('H:i',
                                                                    $match['jzdt'] / 1000); ?></span>

                                                            <div
                                                                class="mod-tips-t">
                                                                停售时间：<?php echo date('Y-m-d H:i',
                                                                    $match['jzdt'] / 1000); ?><br/>
                                                                比赛时间：<?php echo date('Y-m-d H:i',
                                                                    $match['dt'] / 1000); ?>
                                                                <b></b><s></s></div>
                                                        </div>
                                                    </td>
                                                    <td class="bd-no-l">
                                                        <div
                                                            class="name"><span
                                                                class="name-l"><s><?php echo empty($match['hRank']) ? '' : ('[' . $match['hRank'] . ']') ?></s><a
                                                                    <?php if (empty($match['queryMId'])): ?>
                                                                        onclick="return false;"
                                                                    <?php else: ?>
                                                                        href="<?php echo $match['hDetail']; ?>"
                                                                    <?php endif; ?>
                                                                    target="_blank"><?php echo $match['home']; ?></a></span><span
                                                                class="name-r"><a
                                                                    <?php if (empty($match['queryMId'])): ?>
                                                                        onclick="return false;"
                                                                    <?php else: ?>
                                                                        href="<?php echo $match['aDetail']; ?>"
                                                                    <?php endif; ?>
                                                                    target="_blank"><?php echo $match['awary']; ?></a><s><?php echo empty($match['aRank']) ? '' : ('[' . $match['aRank'] . ']') ?></s></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="jc-table-rangqiu">
                                                            <div class="jc-table-rangqiu-item">0</div>
                                                            <?php if ($match['let'] > 0): ?>
                                                                <div
                                                                    class="num-red jc-table-rangqiu-item no-bd-b"><?php echo $match['let']; ?></div>
                                                            <?php elseif ($match['let'] < 0): ?>
                                                                <div
                                                                    class="num-blue jc-table-rangqiu-item no-bd-b"><?php echo $match['let']; ?></div>
                                                            <?php else: ?>
                                                                <div
                                                                    class="jc-table-rangqiu-item no-bd-b"><?php echo $match['let']; ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <?php foreach (array(3, 1, 0) as $r): ?>
                                                        <td class="jc-option jc-option2">
                                                            <?php foreach (array('spf', 'rqspf') as $pt): ?>
                                                                <a class="<?php echo (($match[$pt . 'Fu'] && $match[$pt . 'Gd']) ? ($pt . '-option') : 'stop-selling') . ($pt == 'rqspf' ? ' no-bd-b' : ''); ?>"
                                                                   onselectstart="return false;"
                                                                   style="-moz-user-select: none"
                                                                   data-val="<?php echo $r; ?>"
                                                                   data-option="<?php echo $pt; ?>"
                                                                   data-odd="<?php echo $match[$pt . 'Sp' . $r]; ?>">
                                                                    <?php echo (empty($match[$pt . 'Fu']) || empty($match[$pt . 'Gd'])) ? '未开售' : ('<b>' . $match[$pt . 'Sp' . $r] . '</b>'); ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td class="open-attach">
                                                        <div class="jc-table-action">
                                                            <a href="javascript:;">更多<i class="arrow"></i></a>
                                                        </div>
                                                    </td>
                                                    <td class="oyx">
                                                        <?php if (empty($match['queryMId'])): ?>
                                                            <a onclick="return false" href="#">欧</a><a
                                                                onclick="return false" href="#">亚</a><a
                                                                onclick="return false" href="#">析</a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $match['oddsUrl'] . 'match/europe/' . $match['queryMId'] . '?lotyid=6' ?>"
                                                               target="_blank">欧</a><a
                                                                href="<?php echo $match['oddsUrl'] . 'match/asia/' . $match['queryMId'] . '?lotyid=6' ?>"
                                                                target="_blank">亚</a><a
                                                                href="<?php echo $match['oddsUrl'] . 'match/analyze/' . $match['queryMId'] . '?lotyid=6' ?>"
                                                                target="_blank">析</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr class="attach-options" data-select="0"></tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <form id="optimizeForm" method="post" action="/optimize" target="_blank">
                                        <input id="optSource" type="hidden" name="source" value="<?php echo $jczqType?>"/>
                                        <input id="optLotteryId" type="hidden" name="lotteryId" value=""/>
                                        <input id="optBetNum" type="hidden" name="betNum" value=""/>
                                        <input id="optMidStr" type="hidden" name="midStr" value=""/>
                                        <input id="optBetStr" type="hidden" name="betStr" value=""/>
                                        <input id="optEndTime" type="hidden" name="endTime" value=""/>
                                        <input id="optIssue" type="hidden" name="issue" value=""/>
                                        <input id="optBetMoney" type="hidden" name="betMoney" value=""/>
                                        <input id="optMulti" type="hidden" name="multi" value=""/>
                                        <input id="optopenEndtime" type="hidden" name="openEndtime" value=""/>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="jc-box nomatch">
                    <div class="no-data">
                        <div class="no-data-bd">

                            <p>亲，今日无竞彩足球赛事</p>

                            <p>去<a href="/jclq/">竞彩篮球</a>看看吧！</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="jc-box bet-tip-box">
                <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>投注提示</div>
                <div class="jc-box-bd">
                    <ol>
                        <li><p>1、竞彩足球全部玩法过关投注、混合过关投注、及比分单关投注奖金为固定奖金，赛事选择界面显示仅为当前参考奖金。实际奖金以出票时刻固定奖金为准。</p></li>
                        <li>
                            <p>2、竞彩足球混合过关：胜平负玩法上限8关，总进球玩法支持上限6关，比分、半全场玩法支持上限4关。投注订单支持关数与所选的玩法中，串关数上限最低的那个玩法一致。</p>
                            <dl>
                                <dt>竞彩足球混合串关数限制规则：</dt>
                                <dd>混合过关方案包含胜平负（则混合过关方案最高8串1）</dd>
                                <dd>混合过关方案包含比分（则混合过关方案最高4串1）</dd>
                                <dd>混合过关方案包含总进球（则混合过关方案最高6串1）</dd>
                                <dd>混合过关方案包含半全场（则混合过关方案方案最高4串1）</dd>
                            </dl>
                        </li>
                        <li><p>3、让球符号含义，"+"为客让主，"-"为主让客。让球数含义，即（主队得分±让球数）减客队得分，大于0为胜，等于0为平，小于0为负。</li>
                        <li><p>4、竞彩足球的官方销售时间为：周一至周五09:00-00:00，周六至周日09:00-01:00。</p></li>
                        <li><p>5、2或3场过关投注，单注最高奖金限额为20万元；4或5场过关投注，单注最高奖金限额为50万元；6场过关投注，单注最高奖金限额100万元。</li>
                        <li><p>
                                6、竞彩足球彩果，以比赛90分钟内比分（含伤停补时）结果为准。其中投注赛事取消、中断或改期，官方比赛彩果公布或确认取消将延后36小时，对应场次奖金派发或退款将同步延后处理；取消比赛的任何结果都算对，固定奖金按照1计算。</p>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="bet-side bet-bar">
            <h3 class="bet-side-title">投注栏</h3>
            <a href="javascript:;" class="btn-clean">全清</a>

            <table id="no-matches">
                <thead>
                <tr>
                    <td><b>主队</b> VS <b>客队</b></td>
                    <td>场次</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="2"><p class="table-tips">请至少选择一场比赛</p></td>
                </tr>
                </tbody>
            </table>

            <div class="bet-data"></div>

            <p class="fcs zhushu"><span class="mr20">注数：<b><span class="bet-num">0</span></b>注</span><span>金额：<b><span
                            class="bet-money">0</span></b>元</span></p>

            <p class="fcs fanwei">预计奖金范围：<b class="min-money">0.00</b>-<b class="max-money">0.00</b>元</p>
            <p class="oldfcs hidden">奖金：<b class="old-max-money">0.00</b>元</p>
            <p class="jjyh" id="optimize" style="display: none;"><a class="seleView start-detail">奖金优化</a></p>
            <a href="javascript:;" class="btn-s btn-specail btn-hemai <?php echo $lotteryConfig[JCZQ]['united_status'] ? '' : 'btn-disabled' ?>">发起合买</a>
            <?php if ($lotteryConfig[JCZQ]['status']): ?>
                <a class="btn-s btn-main submit <?php echo $showBind ? ' not-bind' : ''; ?>">确认预约</a>
            <?php else : ?>
                <a class="btn-s btn-main btn-disabled <?php echo $showBind ? ' not-bind' : ''; ?>">确认预约</a>
            <?php endif; ?>
            <div class="hidden jjyc-imgs">
                <img src="/caipiaoimg/v1.1/images/bet-jjyc-hh-bg.png" width="224" height="70" alt="奖金预测">
            </div>    
            <p>实际奖金以出票SP为准</p>
            <p class="agree"><label for="hasRead">
                    <input type="checkbox" id="hasRead" checked="checked">我已阅读并同意<a href="javascript:void(0);"
                                                                                    class="lottery_pro">《用户委托投注协议》</a></label>
            </p>
        </div>

    </div>
</div>

<div class="pop-table" data-mid="0" data-option="spf"></div>
<!--容器end-->

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js'); ?>"></script>
<script type="text/javascript">
    var type = '<?php echo $jczqType; ?>',
        typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jczqType]["cnName"]; ?>',
        typeHmName = '<?php echo $cnName . ", 发起合买, " . $typeMAP[$jczqType]["cnName"]; ?>',
        fiveLeague = <?php echo '[' . implode(', ',
                array_intersect_key($leagues, array_fill_keys(array('西甲', '德甲', '意甲', '英超', '法甲'), 0))) . ']'; ?>,
        saleStatus = <?php echo $lotteryConfig[JCZQ]['status']; ?>,
        stakeStr = '<?php echo $stakeStr ?>',
        jczqType = '<?php echo $jczqType; ?>',
        ahead = '<?php echo $lotteryConfig[JCZQ]['ahead']?>',
        hmahead = '<?php echo $lotteryConfig[JCZQ]['united_ahead']?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczq_dg.js'); ?>"></script>
<script>
    $(function () {
        var $showOddCheck = $('#showOdd');
        $showOddCheck.click(function () {
            $('#odd-tips').html(function () {
                return [$showOddCheck.is(':checked') ? '关闭' : '开启', '赔率展示浮层'].join('');
            });
        });

       var match;
       var hasload=0;
       $("#xstsbs").click(function(){
            if($(this).is(':checked')){
                if($(".oldmatch").hasClass("hidden") && match){
                    $(".oldmatch").removeClass("hidden");
                }else{
                    if(hasload==0){
                        $.get('/jczq/getOldMatch?type=dg',function(data){
                            $(".oldmatch").html(data);
                            match=data;
                            hasload=1;
                            if(data){
                                $(".oldmatch").removeClass("hidden");
                            }
                        });
                    }
                }
            }else{
              $(".oldmatch").addClass("hidden");
            }
       });
       $("#rmss").click(function(){
            if($(this).is(':checked')){
                $('.match').each(function () {
                   var $this = $(this);
                   if(!$this.hasClass("match-hot"))
                   {
                       $this.addClass("hidden");
                   }
                });
            }else{
                $('.match').each(function () {
                   var $this = $(this);
                   $this.removeClass("hidden");
                });
            }
            if($("#yxzbs").is(':checked')){
                $('.match').each(function () {
                   var $this = $(this);
                   if($this.data("select")<1 &&　$this.next('tr').data("select")<1)
                   {
                       $this.addClass("hidden");
                       $this.next('tr').addClass("hidden");
                   }
                });
            }
       });
       $("#yxzbs").click(function(){
            if($(this).is(':checked')){
                $('.match').each(function () {
                   var $this = $(this);
                   if($this.data("select")<1 &&　$this.next('tr').data("select")<1)
                   {
                       $this.addClass("hidden");
                       $this.next('tr').addClass("hidden");
                   }
                });
            }else{
                $('.match').each(function () {
                   var $this = $(this);
                   $this.removeClass("hidden");
                   $this.next('tr').removeClass("hidden");
                });
            }
            if($("#rmss").is(':checked')){
                $('.match').each(function () {
                   var $this = $(this);
                   if(!$this.hasClass("match-hot"))
                   {
                       $this.addClass("hidden");
                   }
                });
            }
       }); 

        $('.jc-box').on('mouseover', '.jc-option a', function () {
            var $this = $(this),
                $tr = $this.closest('.match'),
                $popTable = $('.pop-table'),
                mid = $tr.data('mid'),
                option = $this.data('option'),
                hdAry,
                hdStr,
                popAry;
            if ($popTable.data('mid') == mid && $popTable.data('option') == option) {
                return;
            }

            hdAry = [$tr.data('wid'), $tr.data('home'), 'VS', $tr.data('away')];
            if (option == 'rqspf') {
                hdAry.push('(让球)');
            }
            hdStr = hdAry.join(' ');
            popAry = ['<div class="pop-table-hd">', hdStr, '</div>'];
            popAry.push('<div class="pop-table-bd"><table>',
                '<thead><tr><th width="93">胜</th><th width="93">平</th><th width="93">负</th></tr></thead>',
                '<tbody><tr>'
            );
            $tr.find('.jc-option a').each(function () {
                var item = $(this);
                if (item.data('option') == option) {
                    popAry.push('<td>', $(this).html(), '</td>');
                }
            });
            popAry.push('</tr></tbody></table></div>');
            $popTable.data('mid', mid);
            $popTable.data('option', option);
            $popTable.html(popAry.join(''));
        });

        var timer = null;
        $(".jc-option a").on('mouseover', function (e) {
            clearTimeout(timer);
            var showOddPop = $showOddCheck.is(':checked') && $.isNumeric($(this).text());
            timer = setTimeout(function () {
                if (showOddPop) {
                    $('.pop-table').show();
                }
            }, 800);
            $(this).on('mouseout', function () {
                clearTimeout(timer);
                $('.pop-table').hide();
            }).on('mousemove', function (e) {
                $(".pop-table").css({"top": (e.pageY + 12) + "px", "left": (e.pageX + 12) + "px"});
            });
        });

        var ie6 = !-[1,] && !window.XMLHttpRequest;
        if (ie6) {
            $('.league-filter').on('mouseover', function () {
                $(this).addClass('hover');
            }).on('mouseout', function () {
                $(this).removeClass('hover');
            });
            $('.mod-tips').on('mouseover', function () {
                $(this).addClass('mod-tips-hover');
            }).on('mouseout', function () {
                $(this).removeClass('mod-tips-hover');
            });
        }

        var jcTableHdTop = $('.jc-table-hd-box').offset().top;
        $(window).on('scroll', function () {
            var dScrollTop = $(document).scrollTop();

            if (dScrollTop > jcTableHdTop && dScrollTop < ($('.bet-main').offset().top + $('.bet-main').outerHeight() - $('.jc-table-hd').outerHeight())) {
                $('.jc-table-hd').addClass('jc-table-hd-fixed');
            } else {
                $('.jc-table-hd').removeClass('jc-table-hd-fixed');
            }

            var betMainHeight = $('.bet-main').outerHeight();
            var betBarHeight = $('.bet-bar').height();
            if (dScrollTop > 234 && !($('.bet-bar').height() >= $(window).height())) {
                $('.bet-bar').addClass('bet-bar-fixed');
                if (betMainHeight + $('.bet-main').offset().top - dScrollTop < betBarHeight) {
                    $('.bet-bar').css({
                        top: betMainHeight + $('.bet-main').offset().top - betBarHeight - dScrollTop + 'px'
                    })
                }
            } else {
                $('.bet-bar').removeClass('bet-bar-fixed');
            }
        });

        if (stakeStr) {
            var eachMatchAry = stakeStr.split(';');
            $('.match').each(function () {
                var $this = $(this),
                    $tr = $this.closest('tr'),
                    mid = $tr.data('mid'),
                    $nextLn = $tr.next();
                $.each(eachMatchAry, function (i, eachMatchStr) {
                    var midSplitAry = eachMatchStr.split('='),
                        eachMid = midSplitAry[0],
                        optionAry,
                        loadOptions = {};
                    if (mid != eachMid) {
                        return true;
                    }
                    optionAry = midSplitAry[1].split(',');
                    $.each(optionAry, function (i, optionStr) {
                        var optionSplit = optionStr.split('/'),
                            type = optionSplit[0].toLowerCase(),
                            option = optionSplit[1];
                        if ($.inArray(type, ['spf', 'rqspf']) > -1) {
                            $tr.find('.' + type + '-option').each(function () {
                                var $this = $(this);
                                if (type == $this.data('option') && option == $this.data('val')) {
                                    $this.trigger('click');
                                }
                            });
                        } else {
                            if (type == 'bqc') {
                                option = option.replace('-', '');
                            } else if (type == 'cbf') {
                                option = option.replace(':', '');
                                if (option == 90) {
                                    option = 93;
                                } else if (option == 99) {
                                    option = 91;
                                } else if (option == '09') {
                                    option = 90;
                                }
                            }
                            if (!(type in loadOptions)) {
                                loadOptions[type] = [];
                            }
                            loadOptions[type].push(option);
                        }
                    });
                    $nextLn.load('ajax/attachOption',
                        {
                            playType: jczqType,
                            mid: $tr.data('mid')
                        },
                        function () {
                            $nextLn.addClass('opened').show().find('td').show();
                            $.each(loadOptions, function (type, options) {
                                $.each(options, function (i, option) {
                                    $nextLn.find('.' + type + '-option')
                                        .each(function () {
                                            var $this = $(this);
                                            if (option == $this.data('val')) {
                                                $this.trigger('click');
                                            }
                                        });
                                });
                            });
                            $nextLn.find('.jc-table-action').trigger('click');
                        }
                    );
                });
            });
        }

    })
</script>