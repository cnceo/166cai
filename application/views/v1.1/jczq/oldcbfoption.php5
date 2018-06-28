<?php
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}

?>
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
                            <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                共<?php echo $matchCount; ?>场比赛
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
                                                class="match <?php echo $match['hot'] ? ($match['hotid'] == 9 ? ' match-pa' :' match-hot') : ''; ?> hasmatched>"
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
                                                        <span class="bf"><?php echo $match['full_score']; ?></span>
                                                    </div>
                                                </td>
                                                <td class="open-attach jc-option no-bd-r <?php if($match['full_score']) { echo "hasmatched";}?>">
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
                                            <tr class="attach-options hasmatched"  data-select="0"></tr>
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
                        <?php endif; 
                    endforeach; ?>
            <?php endif; ?>