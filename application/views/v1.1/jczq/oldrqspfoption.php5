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

?>
<?php if (count($matches)): ?>
                <?php foreach ($matches as $date => $dateMatch): ?>
                    <?php
                    $matchCount = 0;
                    if ($dateMatch)
                    {
                        foreach ($dateMatch as $match)
                        {
                            if ($match['rqspfGd'])
                            {
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
				                        <col width="261">
				                        <col width="49">
				                        <col width="102">
				                        <col width="102">
				                        <col width="102">
				                        <col width="86">
				                        <col width="140">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($dateMatch as $match): ?>
                                        <?php if ($match['rqspfGd']): ?>
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
                                                    <td class="jc-option <?php if($match['full_score']) { echo "hasmatched";}?> <?php echo $r ? '' : 'no-bd-r' ?>">
                                                            <a class="rqspf-option
                                                               <?php if($match['result']['rqspf']==$r && $match['full_score'] && $match['m_status']!=1){ echo "bingo";} ?>"
                                                               onselectstart="return false;"
                                                           onselectstart="return false;"
                                                           style="-moz-user-select: none"
                                                           data-val="<?php echo $r; ?>"
                                                           data-odd="<?php if($match['m_status']==1){echo 1;}
                                                           elseif($match['result']['rqspf']==$r || !$match['full_score'])
                                                           {echo $match['rqspfSp' . $r];}else{ echo 0;} ?>">
                                                                <b><?php echo $match['rqspfSp' . $r]; ?></b>
                                                            </a>
                                                    </td>
                                                <?php endforeach; ?>
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
                                            <tr class="attach-options"></tr>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>