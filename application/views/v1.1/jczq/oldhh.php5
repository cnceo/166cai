<?php
/**
 * @see JCZQ::hh()
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
 * @var $issueStr
 */
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}

$spfHash = array(
    3 => 's',
    1 => 'p',
    0 => 'f',
);

?>

<!--容器-->
<div class="wrap cp-box bet-jc jczq-hh">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>

    <div class="cp-box-bd bet">
        <div class="bet-main bet-main-nobar">
            <!--表格-->
            <div class="jc-table-hd-box">
                <div class="jc-table-hd">
                    <table>
                        <colgroup>
                            <col width="76">
	                        <col width="76">
	                        <col width="270">
	                        <col width="49">
	                        <col width="198">
	                        <col width="142">
	                        <col width="77">
	                        <col width="110">
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
                            <th class="name">
                                主队<span class="spacing"></span>客队
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
                            <th>数据<u></u></th>
                            <th>
                                <div class="table-option pjop-filter">
                                    <span class="table-option-title" id="op-name">99家平均<i class="table-option-arrow"></i></span>
                                    <ul class="table-option-list">
                                        <li><a class="odds-refer selected" href="javascript:void(0);" data-cid="0">99家平均</a></li>
                                        <li><a class="odds-refer" href="javascript:void(0);" data-cid="1">威廉希尔</a></li>
                                        <li><a class="odds-refer" href="javascript:void(0);" data-cid="3">立博</a></li>
                                        <li><a class="odds-refer" href="javascript:void(0);" data-cid="4">bet365</a></li>
                                        <li><a class="odds-refer" href="javascript:void(0);" data-cid="8">bwin</a></li>
                                        <li><a class="odds-refer" href="javascript:void(0);" data-cid="2">澳门</a></li>
                                    </ul>
                                </div>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="jc-table-filter">
              <label for="rmss"><input id="rmss" name="jcFilter" type="checkbox">热门赛事</label>
              <label for="xstsbs"><input id="xstsbs" name="jcFilter" checked="checked"   disabled="disabled" type="checkbox">显示已截止比赛(<i><?php echo $count['count'];?></i>场)</label>
              <label for="yxzbs"><input id="yxzbs" name="jcFilter" type="checkbox">已选择比赛</label>
              <div class="filter-date">
                <strong>赛事回查</strong>
                <dl class="simu-select select-small">
                    <dt><?php echo date('Y.m.d', strtotime($date)); ?><i class="arrow"></i></dt>
                    <dd class="select-opt">
                        <div class="select-opt-in">
                            <?php foreach ($dates as $key => $value): ?>
                                <a href="<?php echo $baseUrl; ?>jczq/hh?date=<?php echo $value; ?>"><?php echo $key; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </dd>
                </dl>
              </div>
          </div>
            <!--表格循环-->
            <?php if (count($matches)): ?>
                <?php foreach ($matches as $date => $dateMatch): ?>
                    <?php
                    $matchCount = 0;
                    if ($dateMatch)
                    {
                        foreach ($dateMatch as $match)
                        {
                            if ($match['bqcGd'] OR $match['bfGd'] OR $match['spfGd'] OR $match['rqspfGd'] OR $match['jqsGd'])
                            {
                                $matchCount += 1;
                            }
                        }
                    }
                    if ($matchCount): ?>
            <div class="jc-box oldmatch cutoff">
                    <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                共<?php echo $matchCount; ?>场比赛
                            </div>
                            <div class="jc-box-bd">
                                <table class="jc-table matches">
                                    <colgroup>
                                        <col width="76">
					                    <col width="76">
					                    <col width="270">
					                    <col width="49">
					                    <col width="66">
					                    <col width="66">
					                    <col width="66">
					                    <col width="142">
					                    <col width="77">
					                    <col width="110">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($dateMatch as $match): ?>
                                        <?php if ($match['bqcGd'] OR $match['bfGd'] OR $match['spfGd'] OR $match['rqspfGd'] OR $match['jqsGd']): ?>
                                            <tr
                                                class="match<?php echo $match['hot'] ? ($match['hotid'] == 9 ? ' match-pa' :' match-hot') : ''; ?> hasmatched"
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
                                                        <span class="bf"><?php echo $match['full_score']; ?></span>
                                                    </div>
                                                </td>
                                                <td class="jc-table-rangqiu">
                                                    <div class="jc-table-rangqiu-item">
                                                        0
                                                        <?php if ($match['spfFu']): ?>
                                                            <div class="mod-sup">
                                                                <i class="mod-sup-bg"></i><u>单</u>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="jc-table-rangqiu-item
                                                            <?php if ($match['let'] > 0): ?>
                                                            num-red
                                                            <?php elseif ($match['let'] < 0): ?>
                                                            num-blue
                                                            <?php endif; ?>
                                                            no-bd-b">
                                                        <?php echo $match['let']; ?>
                                                        <?php if ($match['rqspfFu']): ?>
                                                            <div class="mod-sup">
                                                                <i class="mod-sup-bg"></i><u>单</u>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <?php foreach (array(3, 1, 0) as $r): ?>
                                                    <td class="jc-option jc-option2 <?php if($match['full_score']) { echo "hasmatched";}?>">
                                                        <?php foreach (array('spf', 'rqspf') as $pt): ?>
                                                            <a class="<?php echo ($match[$pt . 'Gd'] ? ($pt . '-option ' . ($pt == 'spf' ? ($pt . $spfHash[$r]) : '')) : 'stop-selling') . ($pt == 'rqspf' ? ' no-bd-b' : ''); ?>
                                                               <?php if(($match['result'][$pt]==$r) && !empty($match[$pt . 'Gd']) && $match['full_score']  && $match['m_status']!=1){ echo "bingo";} ?>"
                                                               onselectstart="return false;"
                                                               style="-moz-user-select: none"
                                                               data-val="<?php echo $r; ?>"
                                                               data-option="<?php echo $pt; ?>"
                                                               data-odd="<?php if($match['m_status']==1){ echo 1;}
                                                               elseif($match['result'][$pt]==$r || !$match['full_score']){echo $match[$pt . 'Sp' . $r];}
                                                               else{ echo 0;} ?>">
                                                                <?php echo empty($match[$pt . 'Gd']) ? '未开售' : ('<b>' . $match[$pt . 'Sp' . $r] . '</b>'); ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="open-attach <?php if($match['full_score']) { echo "hasmatched";}?>">
                                                    <div class="jc-table-action">
                                                        <a href="javascript:;">半全场/比分/总进球<i class="arrow"></i>
                                                            <?php if ($match['bqcFu'] OR $match['bfFu'] OR $match['jqsFu']): ?>
                                                                <div class="mod-sup">
                                                                    <i class="mod-sup-bg"></i><u>单</u>
                                                                </div>
                                                            <?php endif; ?>
                                                        </a>
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
                                                <td class="pjop">
                                                    <span class="op-oh"><?php echo $match['oh']; ?></span>
                                                    <span class="op-od"><?php echo $match['od']; ?></span>
                                                    <span class="op-oa"><?php echo $match['oa']; ?></span>
                                                </td>
                                            </tr>
                                            <tr class="attach-options hasmatched" data-select="0"></tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <form id="optimizeForm" method="post" action="/optimize" target="_blank">
                                    <input id="optSource" type="hidden" name="source" value="<?php echo $jczqType ?>"/>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $this->load->view('v1.1/elements/jczq/cast_panel', array('hoverInfo' => $hoverInfo)); ?>
<?php echo $this->load->view('v1.1/elements/jczq/commentary'); ?>

<div class="pop-table" data-mid="0" data-option="spf"></div>

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczq.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczqFixed.js'); ?>"></script>
<script type="text/javascript">
    var type = '<?php echo $jczqType; ?>',
        jczqType = '<?php echo $jczqType; ?>',
        typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jczqType]["cnName"]; ?>',
        typeHmName = '<?php echo $cnName . ", 发起合买, " . $typeMAP[$jczqType]["cnName"]; ?>',
        fiveLeague = <?php echo '[' . implode(', ',
                array_intersect_key($leagues, array_fill_keys(array('西甲', '德甲', '意甲', '英超', '法甲'), 0))) . ']'; ?>,
        saleStatus = <?php echo $lotteryConfig[JCZQ]['status']; ?>,
        stakeStr = '<?php echo $stakeStr ?>',
        issueStr = '<?php echo $issueStr ?>',
        mid = '<?php echo $mid;?>',
        multi = '<?php echo $multiple;?>',
        spf = '<?php echo $spf;?>',
        rqspf = '<?php echo $rqspf;?>',
        ahead = '<?php echo $lotteryConfig[JCZQ]['ahead']?>',
        hmahead = '<?php echo $lotteryConfig[JCZQ]['united_ahead']?>';
</script>
<script>
    $(function () {
        var openedClass = 'opened';
        $('.league-filter').mouseover(function () {
            $(this).addClass('league-filter-hover');
        }).mouseout(function () {
            $(this).removeClass('league-filter-hover');
        }).on('click', '.league', function () {
            var $this = $(this),
                val = $this.val();
            $('.match').each(function (key, match) {
                var $match = $(match),
                    league = $match.data('league'),
                    $matchAttach = $match.next();
                if (league == val) {
                    $('.attach-options .jc-table-action').trigger('click');
                    $match.toggle().find('td').toggle();
                    if (!$this.is(':checked')) {
                        $matchAttach.hide().removeClass(openedClass).find('td').hide();
                    }
                }
            });
        });
 
       $("#xstsbs").click(function(){
            if($(this).is(':checked')){
              if($(".oldmatch").hasClass("hidden")){
                $(".oldmatch").removeClass("hidden");
              }else{
                $.get('/jczq/getOldMatch',function(data){
                    $(".oldmatch").html(data);
                });
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
                       $this.addClass("hidden").next('.attach-options').addClass("hidden");
                   }
                });
            }else{
                $('.match').each(function () {
                   var $this = $(this);
                   $this.removeClass("hidden").next('.attach-options').removeClass("hidden");
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
        $('.select-five').click(function () {
            $('.league').each(function () {
                var $this = $(this);
                if ($.inArray(parseInt($this.val(), 10), fiveLeague) == -1) {
                    if ($this.is(':checked')) {
                        $this.trigger('click');
                    }
                } else {
                    if (!$this.is(':checked')) {
                        $this.trigger('click');
                    }
                }
            });
        });
        $('.select-anti').click(function () {
            $('.attach-options .jc-table-action').trigger('click');
            $('.league').trigger('click');
            $('.match').next().hide().find('td').hide();
        });
        $('.select-all').click(function () {
            $('.league').each(function (key, league) {
                if (!$(league).is(':checked')) {
                    $(league).trigger('click');
                }
            });
        });
        $('.select-none').click(function () {
            $('.league').each(function (key, league) {
                if ($(league).is(':checked')) {
                    $(league).trigger('click');
                }
            });
        });

        var $showOddCheck = $('#showOdd');
        $showOddCheck.click(function () {
            $('#odd-tips').html(function () {
                return [$showOddCheck.is(':checked') ? '关闭' : '开启', '赔率展示浮层'].join('');
            });
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
            $popTable.data('mid', mid).data('option', option).html(popAry.join(''));
        });

        var timer = null;
        $(".jc-option a").on('mouseover', function () {
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

        var $match = $('.match');
        $match.on('click', '.jc-table-action a', function () {
            $(this).parents('.match').next().show().find('td').show();
        });

        $('.attach-options').on('click', '.jc-table-action', function () {
            $(this).parents('.attach-options').hide().find('td').hide();
        });

        if (spf !== '') {
            spfArr = spf.split('');
            $.each(spfArr, function(i, e){
            	$("tr[data-mid="+mid+"]").find('.spf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
            })
        }
        if (rqspf !== '') {
        	rqspfArr = rqspf.split('');
        	$.each(rqspfArr, function(i, e){
        		$("tr[data-mid="+mid+"]").find('.rqspf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
        	})
        }
        if (stakeStr) {
            var eachMatchAry = stakeStr.split(';');
            $match.each(function () {
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