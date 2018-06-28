<?php
/**
 * @see JCLQ::sfc()
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
<div class="wrap cp-box bet-jc jclq-sfc">
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
                                <col width="320">
                                <col width="56">
                                <col width="75">
                                <col width="75">
                                <col width="75">
                                <col width="75">
                                <col width="75">
                                <col width="75">
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
                                    胜分差
                                    <u></u>
                                </th>
                                <th>
                                    1-5分
                                    <u></u>
                                </th>
                                <th>
                                    6-10分
                                    <u></u>
                                </th>
                                <th>
                                    11-15分
                                    <u></u>
                                </th>
                                <th>
                                    16-20分
                                    <u></u>
                                </th>
                                <th>
                                    21-25分
                                    <u></u>
                                </th>
                                <th>
                                    26+分
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            <div class="jc-table-filter">
              <label for="rmss"><input id="rmss" name="jcFilter" type="checkbox">热门赛事</label>
              <label for="xstsbs"><input id="xstsbs" name="jcFilter" checked="checked"  disabled="disabled" type="checkbox">显示已截止比赛(<i><?php echo $count['count'];?></i>场)</label>
              <label for="yxzbs"><input id="yxzbs" name="jcFilter" type="checkbox">已选择比赛</label>
              <div class="filter-date">
                <strong>赛事回查</strong>
                <dl class="simu-select select-small">
                    <dt><?php echo date('Y.m.d', strtotime($date)); ?><i class="arrow"></i></dt>
                    <dd class="select-opt">
                        <div class="select-opt-in">
                            <?php foreach ($dates as $key => $value): ?>
                                <a href="<?php echo $baseUrl; ?>jclq/sfc?date=<?php echo $value; ?>"><?php echo $key; ?></a>
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
                                if ($match['sfcGd'])
                                {
                                    $matchCount += 1;
                                }
                            }
                        }
                        if ($matchCount):
                        ?>
                        <div class="jc-box oldmatch cutoff">
                                <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                    <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                    共<?php echo $matchCount; ?>场比赛
                                </div>
                                <div class="jc-box-bd">
                                    <table class="jc-table matches">
                                        <colgroup>
                                            <col width="86">
                                            <col width="86">
                                            <col width="320">
                                            <col width="56">
                                            <col width="75">
                                            <col width="75">
                                            <col width="75">
                                            <col width="75">
                                            <col width="75">
                                            <col width="75">
                                        </colgroup>
                                        <tbody>
                                        <?php foreach ($dateMatch as $match): ?>
                                            <?php if ($match['sfcGd']): ?>
                                                <tr
                                                    class="match<?php echo $match['hot'] ? ' match-hot' : ''; ?> hasmatched"
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
                                                    <td class="bd-no-l<?php echo $match['hot'] ? ' hot-box' : ''; ?>">
                                                        <div
                                                            class="type"><?php echo $match['weekId']; ?>
                                                            <span
                                                                style="background: <?php echo $match['cl']; ?>;"
                                                            ><?php echo $match['name']; ?></span>
                                                            <?php if ($match['hot']): ?>
                                                                <i class="hot"></i>
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
                                                                        target="_blank">
                                                                        <?php echo $match['awary']; ?></a>
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
                                                                        target="_blank">
                                                                        <?php echo $match['home']; ?></a>
                                                                </em>
                                                                <s>
                                                                    <?php echo empty($match['hRank']) ? '' : ('[' . $match['hRank'] . ']') ?>
                                                                </s>
                                                                
                                                            </p>
                                                            <?php if($match['full_score']){ ?>
                                                            <?php $full_score=explode(':',$match['full_score']); ?>
                                                            <span class="bf"><?php echo $full_score[0];?><s>-</s><?php echo $full_score[1];?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td class="jc-table-rangqiu">
                                                        <div class="jc-table-rangqiu-item">客胜
                                                            <?php if ($match['sfcFu']): ?>
                                                                <div class="mod-sup">
                                                                    <i class="mod-sup-bg"></i><u>单</u>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="jc-table-rangqiu-item no-bd-b">主胜
                                                            <?php if ($match['sfcFu']): ?>
                                                                <div class="mod-sup">
                                                                    <i class="mod-sup-bg"></i><u>单</u>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <?php $count = 0; ?>
                                                    <?php foreach ($sfcOptions as $key => $value): ?>
                                                        <?php $count ++; ?>
                                                        <td class="hasmatched jc-option jc-option2<?php echo $count > count($sfcOptions) ? ' no-bd-r' : ''; ?>">
                                                            <a href="javascript:;" class="sfc-option <?php if($match['result']['sfc']==$count && $match['result']['sf']==3 && $match['full_score']){ echo "bingo";} ?>"
                                                               data-val="1<?php echo $count; ?>"
                                                               data-odd="<?php if($match['m_status']==1){echo 1;}
                                                               elseif(($match['result']['sfc']==$count && $match['result']['sf']==3) || !$match['full_score'])
                                                               { echo $match['sfcAs' . $key];}
                                                               else{ echo 0;} ?>">
                                                                <?php echo $match['sfcAs' . $key]; ?>
                                                            </a>
                                                            <a href="javascript:;" class="sfc-option no-bd-b <?php if($match['result']['sfc']==$count && $match['result']['sf']==0 && $match['full_score']){ echo "bingo";} ?>"
                                                               data-val="0<?php echo $count; ?>"
                                                               data-odd="<?php if($match['m_status']==1){echo 1;}
                                                               elseif(($match['result']['sfc']==$count && $match['result']['sf']==0) || !$match['full_score'])
                                                               { echo $match['sfcHs' . $key];}
                                                               else{ echo 0;} ?>">
                                                                <?php echo $match['sfcHs' . $key]; ?>
                                                            </a>
                                                        </td>
                                                    <?php endforeach ?>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    <?php endif;
                    endforeach; ?>
                    <form id="optimizeForm" method="post" action="/optimize" target="_blank">
                        <input id="optSource" type="hidden" name="source" value="<?php echo $jclqType?>"/>
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
                    <div class="jc-box">
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
                if (league == val) {
                    $match.toggle().find('td').toggle();
                }
            });
        });

       $("#xstsbs").click(function(){
            if($(this).is(':checked')){
              if($(".oldmatch").hasClass("hidden")){
                    $(".oldmatch").removeClass("hidden");
                }else{
                        $.get('/jclq/getOldMatch?type=sfc',function(data){
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
                   if($this.data("select")<1)
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
                   if($this.data("select")<1)
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
            $('.league').trigger('click');
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

            // 赛事信息筛选
            $leagueFilter.on({
                mouseover: function () {
                    $(this).addClass('league-filter-hover')
                },
                mouseout: function () {
                    $(this).removeClass('league-filter-hover')
                }
            });

            // 赛事时间筛选
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
            });

        }

        if (stakeStr) {
            var eachMatchAry = stakeStr.split(';');
            $('.match').each(function () {
                var $this = $(this),
                    $tr = $this.closest('tr'),
                    mid = $tr.data('mid');
                $.each(eachMatchAry, function (i, eachMatchStr) {
                    var midSplitAry = eachMatchStr.split('='),
                        eachMid = midSplitAry[0],
                        optionAry;
                    if (mid != eachMid) {
                        return true;
                    }
                    optionAry = midSplitAry[1].split(',');
                    $.each(optionAry, function (i, optionStr) {
                        var optionSplit = optionStr.split('/'),
                            type = optionSplit[0].toLowerCase(),
                            option = optionSplit[1];

                        $tr.find('.' + type + '-option').each(function () {
                            var $this = $(this);
                            if (option == $this.data('val')) {
                                $this.trigger('click');
                            }
                        });
                    });
                });
            });
        }

    })
</script>