<?php
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}

?>

<!--容器-->
<div class="wrap cp-box bet-jc jczq-bf">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>

    <div class="cp-box-bd bet">
        <div class="bet-main">
            <!--表格-->
            <div class="jc-table-hd-box">
                <div class="jc-table-hd">
                    <table>
                        <colgroup>
                            <col width="76">
	                        <col width="76">
	                        <col width="541">
	                        <col width="305">
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
                                <div class="name"><span class="name-l">主队</span><s></s><span class="name-r">客队</span>
                                </div>
                                <u></u>
                            </th>
                            <th>比分</th>
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
                                <a href="<?php echo $baseUrl; ?>jczq/cbf?date=<?php echo $value; ?>"><?php echo $key; ?></a>
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
                            if ($match['bfGd']) {
                                $matchCount += 1;
                            }
                        }
                    }
                    if ($matchCount): ?>
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
				                        <col width="541">
				                        <col width="305">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($dateMatch as $match): ?>
                                        <?php if ($match['bfGd']): ?>
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
                                                <td class="open-attach jc-option no-bd-r">
                                                    <div class="jc-table-action">
                                                        <a href="javascript:;">展开比分投注区<i class="arrow"></i>
                                                            <?php if ($match['bfFu']): ?>
                                                                <div class="mod-sup">
                                                                    <i class="mod-sup-bg"></i><u>单</u>
                                                                </div>
                                                            <?php endif; ?>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="attach-options" data-select="0"></tr>
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
                        <?php endif; 
                    endforeach; ?>
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
    </div>
</div>

<?php echo $this->load->view('v1.1/elements/jczq/cast_panel', array('hoverInfo' => $hoverInfo)); ?>
<?php echo $this->load->view('v1.1/elements/jczq/commentary'); ?>

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczq.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jczqFixed.js'); ?>"></script>
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
<script>
    $(function () {
        var openedClass = 'opened';
        var $leagueFilter = $('.league-filter');
        $leagueFilter.mouseover(function () {
            $(this).addClass('league-filter-hover');
        }).mouseout(function () {
            $(this).removeClass('league-filter-hover');
        });

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
                }else{
                    if(hasload==0){
                        $.get('/jczq/getOldMatch?type=cbf',function(data){
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