<?php
/**
 * @see JCLQ::hh()
 * @var $leagues
 * @var $leaguesNew
 * @var $sfcOptions
 * @var $matches
 * @var $jclqType
 * @var $cnName
 * @var $typeMAP
 * @var $lotteryConfig
 * @var $stakeStr
 */
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}

?>
<div class="wrap cp-box bet-jc jclq">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>

    <div class="cp-box-bd bet">
        <div class="bet-main bet-main-nobar">
            <div class="jc-table-box">
                <!--表格-->
                <div class="jc-table-hd-box">
                    <div class="jc-table-hd">
                        <table>
                            <colgroup>
                                <col width="86">
                                <col width="86">
                                <col width="170">
                                <col width="128">
                                <col width="128">
                                <col width="128">
                                <col width="100">
                                <col width="86">
                                <col width="86">
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
                                                <span class="select-five">NBA</span>
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
                                    客队 | 主队
                                    <u></u>
                                </th>
                                <th>
                                    胜负
                                    <u></u>
                                </th>
                                <th>
                                    让分胜负
                                    <u></u>
                                </th>
                                <th>
                                    大小分
                                    <u></u>
                                </th>
                                <th>胜分差</th>
                                <th>数据<u></u></th>
                                <th>平均欧赔</th>
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
                                <a href="<?php echo $baseUrl; ?>jclq/hh?date=<?php echo $value; ?>"><?php echo $key; ?></a>
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
                        if ($dateMatch)
                        {
                            foreach ($dateMatch as $match)
                            {
                                if ($match['sfGd'] OR $match['dxfGd'] OR $match['sfcGd'] OR $match['rfsfGd'])
                                {
                                    $matchCount += 1;
                                }
                            }
                        }
                        if ($matchCount):
                        ?>
                        <div class="jc-box">
                                <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                    <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                    共<?php echo $matchCount; ?>场比赛可投注
                                </div>
                                <div class="jc-box-bd">
                                    <table class="jc-table matches">
                                        <colgroup>
                                            <col width="86">
                                            <col width="86">
                                            <col width="170">
                                            <col width="128">
                                            <col width="128">
                                            <col width="128">
                                            <col width="100">
                                            <col width="86">
                                            <col width="86">
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($dateMatch as $match): ?>
                                            <?php if ($match['sfGd'] OR $match['dxfGd'] OR $match['sfcGd'] OR $match['rfsfGd']): ?>
                                                <tr
                                                    class="match<?php echo $match['hot'] ? ' match-hot' : ''; ?>"
                                                    data-mid="<?php echo $match['mid']; ?>"
                                                    data-home="<?php echo $match['home']; ?>"
                                                    data-away="<?php echo $match['awary']; ?>"
                                                    data-let="<?php echo $match['let']; ?>"
                                                    data-league="<?php echo $leagues[$match['name']]; ?>"
                                                    data-wid="<?php echo $match['weekId']; ?>"
                                                    data-dt="<?php echo date('Y-m-d H:i:s', $match['dt'] / 1000); ?>"
                                                    data-jzdt="<?php echo date('Y-m-d H:i:s',
                                                        $match['jzdt'] / 1000); ?>"
                                                    data-sf_fu="<?php echo $match['sfFu']; ?>"
                                                    data-rfsf_fu="<?php echo $match['rfsfFu']; ?>"
                                                    data-dxf_fu="<?php echo $match['dxfFu']; ?>"
                                                    data-sfc_fu="<?php echo $match['sfcFu']; ?>"
                                                    data-prescore="<?php echo $match['preScore']; ?>"
                                                    data-select="0"
                                                    data-cancel="<?php echo $match['m_status']==1?1:0 ?>"
                                                    data-old="<?php echo $match['full_score']?1:0 ?>">
                                                    <td class="bd-no-l">
                                                        <div
                                                            class="type<?php echo $match['hot'] ? ' hot-box' : ''; ?>"><?php echo $match['weekId']; ?>
                                                            <span
                                                                style="background: <?php echo $match['cl']; ?>;"
                                                            ><?php echo $match['name']; ?></span>
                                                            <?php if ($match['hot']): ?>
                                                                <div class="hot"></div>
                                                            <?php endif; ?>
                                                        </div>
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
                                                        <div class="name">
                                                            <p>客队：
                                                                <em>
                                                                    <a <?php if (empty($match['queryMId'])): ?>
                                                                        onclick="return false;"
                                                                    <?php else: ?>
                                                                        href="<?php echo $match['aDetail']; ?>"
                                                                    <?php endif; ?>
                                                                       target="_blank"><?php echo $match['awary']; ?></a>
                                                                </em>
                                                                <s>
                                                                    <?php echo empty($match['aRank']) ? '' : ('[' . $match['aRank'] . ']') ?>
                                                                </s>
                                                            </p>

                                                            <p>主队：
                                                                <em>
                                                                    <a <?php if (empty($match['queryMId'])): ?>
                                                                        onclick="return false;"
                                                                    <?php else: ?>
                                                                        href="<?php echo $match['hDetail']; ?>"
                                                                    <?php endif; ?>
                                                                       target="_blank"><?php echo $match['home']; ?></a>
                                                                </em>
                                                                <s>
                                                                    <?php echo empty($match['hRank']) ? '' : ('[' . $match['hRank'] . ']') ?>
                                                                </s>
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <?php foreach (array('sf', 'rfsf', 'dxf') as $pt): ?>
                                                        <td class="jc-option jc-option2">
                                                            <a class="<?php echo empty($match[$pt . 'Gd']) ? 'stop-selling' : ($pt . '-option'); ?> <?php echo $pt . 'f' ?>"
                                                               onselectstart="return false;"
                                                               style="-moz-user-select: none"
                                                               data-val="<?php echo in_array($pt,
                                                                   array('dxf')) ? 3 : 0; ?>"
                                                               data-odd="<?php echo in_array($pt,
                                                                   array('dxf')) ? $match['dxfBig'] : $match[$pt . 'Hf']; ?>"
                                                               data-option="<?php echo $pt?>">
                                                                <?php if (empty($match[$pt . 'Gd'])) : ?>
                                                                     	未开售
                                                                <?php else: ?>
                                                                    <?php if ($pt == 'sf'): ?>
                                                                        <em><?php echo $match['sfHf']; ?></em><?php echo $wenan['jlsf']['0']?>
                                                                    <?php elseif ($pt == 'rfsf'): ?>
                                                                        <em><?php echo $match['rfsfHf']; ?></em>
                                                                    <?php elseif ($pt == 'dxf'): ?>
                                                                        <em><?php echo $match['dxfBig']; ?></em>大<?php echo $match['preScore']; ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                                <?php if ($match[$pt . 'Fu']): ?>
                                                                    <div class="mod-sup">
                                                                        <i class="mod-sup-bg"></i><u>单</u>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </a>
                                                            <a class="no-bd-b <?php echo empty($match[$pt . 'Gd']) ? 'stop-selling' : ($pt . '-option'); ?> <?php echo $pt . 's' ?>"
                                                               onselectstart="return false;"
                                                               style="-moz-user-select: none"
                                                               data-val="<?php echo in_array($pt,
                                                                   array('dxf')) ? 0 : 3; ?>"
                                                               data-odd="<?php echo in_array($pt,
                                                                   array('dxf')) ? $match['dxfSmall'] : $match[$pt . 'Hs']; ?>"
                                                               data-option="<?php echo $pt?>">
                                                                <?php if (empty($match[$pt . 'Gd'])) : ?>
                                                                    未开售
                                                                <?php else: ?>
                                                                    <?php if ($pt == 'sf'): ?>
                                                                        <em><?php echo $match['sfHs']; ?></em><?php echo $wenan['jlsf']['3']?>
                                                                    <?php elseif ($pt == 'rfsf'): ?>
                                                                        <em><?php echo $match['rfsfHs']; ?></em><?php echo $match['let']; ?>
                                                                    <?php elseif ($pt == 'dxf'): ?>
                                                                        <em><?php echo $match['dxfSmall']; ?></em>小<?php echo $match['preScore']; ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                                <?php if ($match[$pt . 'Fu']): ?>
                                                                    <div class="mod-sup">
                                                                        <i class="mod-sup-bg"></i><u>单</u>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </a>
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <?php if (empty($match['sfcGd'])) : ?>
                                                        <td>
                                                            <div class="jc-table-action">
                                                                	未开售
                                                            </div>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="open-attach">
                                                            <div class="jc-table-action">
                                                                <a href="javascript:;">胜分差<i class="arrow"></i>
                                                                    <?php if ($match['sfcFu']): ?>
                                                                        <div class="mod-sup">
                                                                            <i class="mod-sup-bg"></i><u>单</u>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="oyx">
                                                        <?php if (empty($match['queryMId'])): ?>
                                                            <a onclick="return false" href="#">欧</a><a onclick="return false" href="#">析</a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $match['oddsUrl'] . 'basketball/europe/' . $match['queryMId'] ?>"
                                                               target="_blank">欧</a><a
                                                                href="<?php echo $match['oddsUrl'] . 'basketball/analyze/' . $match['queryMId'] ?>"
                                                                target="_blank">析</a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="pjop">
                                                        <div class="op-oa"><?php echo $match['oa']; ?></div><div class="op-oh"><?php echo $match['oh']; ?></div>
                                                    </td>
                                                </tr>
                                                <tr class="attach-options" data-select="0"></tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    <?php endif;
                    endforeach; ?>
                    <form id="optimizeForm" method="post" action="/optimize" target="_blank">
                        <input id="optSource" type="hidden" name="source" value="<?php echo $jclqType ?>"/>
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
                <?php else: ?>
                    <div class="jc-box nomatch">
                        <div class="no-data">
                            <div class="no-data-bd">

                                <p>亲，今日无竞彩篮球赛事</p>

                                <p>去<a href="/jczq/">竞彩足球</a>看看吧！</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo $this->load->view('v1.1/elements/jclq/cast_panel', array('hoverInfo' => $hoverInfo)); ?>
<?php echo $this->load->view('v1.1/elements/jclq/commentary'); ?>

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jclq.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczqFixed.js'); ?>"></script>
<script type="text/javascript">
    var type = '<?php echo $jclqType; ?>',
        typeCnName = '<?php echo $cnName . ", " . $typeMAP[$jclqType]["cnName"]; ?>',
        typeHmName = '<?php echo $cnName . ", 发起合买, " . $typeMAP[$jclqType]["cnName"]; ?>',
        fiveLeague = <?php echo '[' . implode(', ',
                array_intersect_key($leagues, array_fill_keys(array('美职篮'), 0))) . ']'; ?>,
        saleStatus = <?php echo $lotteryConfig[JCLQ]['status']; ?>,
        midp = '<?php echo $idp;?>',
        multi = '<?php echo $multiple;?>',
        rfsf = '<?php echo $rfsf;?>',
        sf = '<?php echo $sf?>',
        dxf = '<?php echo $dxf?>',
        midn = '<?php echo $idn;?>',
        rfsfn = '<?php echo $rfsfn;?>',
        stakeStr = '<?php echo $stakeStr ?>',
        jclqType = '<?php echo $jclqType; ?>',
        ahead = '<?php echo $lotteryConfig[JCLQ]['ahead']?>',
        hmahead = '<?php echo $lotteryConfig[JCLQ]['united_ahead']?>';
</script>
<script>
    $(function () {
        <?php if ($tips) {?>
        cx.Confirm({
            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>&nbsp;&nbsp;您好，夜间不出票，投注警惕盘口变化！</div><p class="pop-help" style="padding: 0 30px;"><?php echo $tips?></p>', 
            btns: [{type: 'confirm',txt: '确定', href: 'javascript:;'}]
        });
        <?php }?>
        var openedClass = 'opened',
            $leagueFilter = $('.league-filter');
        $leagueFilter.mouseover(function () {
            $(this).addClass('league-filter-hover');
        }).mouseout(function () {
            $(this).removeClass('league-filter-hover');
        });
        if (sf !== '') {
            sfArr = sf.split('');
            $.each(sfArr, function(i, e){
            	$("tr[data-mid="+midp+"]").find('.sf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
            })
        }
        if (rfsf !== '') {
        	rfsfArr = rfsf.split('');
        	$.each(rfsfArr, function(i, e){
        		$("tr[data-mid="+midp+"]").find('.rfsf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
           	})
        }
        if (dxf !== '') {
        	dxfArr = dxf.split('');
        	$.each(dxfArr, function(i, e){
        		$("tr[data-mid="+midp+"]").find('.dxf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
            })
        }
        if (rfsfn !== '') {
        	rfsfnArr = rfsfn.split('');
        	$.each(rfsfnArr, function(i, e){
        		$("tr[data-mid="+midn+"]").find('.rfsf-option[data-val='+e+']').trigger('click');
            	$('.number').val(multi - 1);
                $('.plus').trigger('click');
            })
        }
        $leagueFilter.on('click', '.league', function () {
            var $this = $(this);
            var val = $this.val();
            $('.match').each(function (key, match) {
                var $match = $(match);
                var league = $match.data('league');
                var $matchAttach = $match.next();
                if (league == val) {
                    $('.attach-options .jc-table-action').trigger('click');
                    $match.toggle().find('td').toggle();
                    if (!$this.is(':checked')) {
                        $matchAttach.hide().removeClass(openedClass).find('td').hide();
                    }
                }
            });
        });
        
       var match;
       var hasload=0;
       $("#xstsbs").click(function(){
            if($(this).is(':checked')){
                if($(".oldmatch").hasClass("hidden") && match){
                    $(".oldmatch").removeClass("hidden");
                    $(".nomatch").addClass("hidden");
                }else{
                    if(hasload==0){
                        $.get('/jclq/getOldMatch?type=hh',function(data){
                            $(".oldmatch").html(data);
                            match=data;
                            hasload=1;
                            if(data){
                                $(".oldmatch").removeClass("hidden");
                                $(".nomatch").addClass("hidden");
                            }
                        });
                    }
                }
            }else{
              $(".oldmatch").addClass("hidden");
              $(".nomatch").removeClass("hidden");
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

        // IE6 悬停弹出提示、下拉框
        var ie6 = !-[1,] && !window.XMLHttpRequest;
        if (ie6) {
            $('.league-filter').on('mouseover', function () {
                $(this).addClass('hover');
            }).on('mouseout', function () {
                $(this).removeClass('hover');
            });

            $('.table-option').on({
                mouseover: function () {
                    $(this).addClass('table-option-hover')
                },
                mouseout: function () {
                    $(this).removeClass('table-option-hover')
                }
            });

            $('.mod-tips').on('mouseover', function () {
                $(this).addClass('mod-tips-hover');
            }).on('mouseout', function () {
                $(this).removeClass('mod-tips-hover');
            })
        }

        $('.match').on('click', '.jc-table-action a', function () {
            $(this).parents('.match').next().show().find('td').show();
        });

        $('.attach-options').on('click', '.jc-table-action', function () {
            $(this).parents('.attach-options').hide().find('td').hide();
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
                        if ($.inArray(type, ['sf', 'rfsf', 'dxf']) > -1) {
                            $tr.find('.' + type + '-option').each(function () {
                                var $this = $(this);
                                if (option == $this.data('val')) {
                                    $this.trigger('click');
                                }
                            });
                        } else {
                            if (!(type in loadOptions)) {
                                loadOptions[type] = [];
                            }
                            loadOptions[type].push(option);
                        }
                    });
                    $nextLn.load('ajax/attachOptionJL',
                        {
                            playType: jclqType,
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