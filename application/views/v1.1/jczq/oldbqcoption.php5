<?php
/**
 * @see JCZQ::bqc()
 * @var $leagues
 * @var $leaguesNew
 * @var $bqcOptions
 * @var $matches
 * @var $jczqType
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
$bqcOptions = array(
            '33' => '胜-胜',
            '31' => '胜-平',
            '30' => '胜-负',
            '13' => '平-胜',
            '11' => '平-平',
            '10' => '平-负',
            '03' => '负-胜',
            '01' => '负-平',
            '00' => '负-负',
        );
?>
<?php if (count($matches)): ?>
                <?php foreach ($matches as $date => $dateMatch): ?>
                    <?php
                    $matchCount = 0;
                    if ($dateMatch)
                    {
                        foreach ($dateMatch as $match)
                        {
                            if ($match['bqcGd'])
                            {
                                $matchCount += 1;
                            }
                        }
                    }
                    if ($matchCount):?>
                            <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                共<?php echo $matchCount; ?>场比赛
                            </div>
                            <div class="jc-box-bd">
                                <table class="jc-table matches">
                                    <colgroup>
                                        <col width="76">
				                        <col width="76">
				                        <col width="232">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
				                        <col width="66">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($dateMatch as $match): ?>
                                        <?php if ($match['bqcGd']): ?>
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
                                                <?php $count = 0; ?>
                                                <?php foreach ($bqcOptions as $key => $value): ?>
                                                    <?php $count ++; ?>
                                                    <td class="jc-option <?php if($match['full_score']) { echo "hasmatched";}?> <?php echo $count == count($bqcOptions) ? 'no-bd-r' : ''; ?>">
                                                        <a class="bqc-option <?php if(isset($match['result']) && $match['full_score'] && $match['m_status']!=1){
                                                               if($match['result']['bqc']==trim($value)){
                                                                   echo "bingo";
                                                               }
                                                           }?>" onselectstart="return false;"
                                                           style="-moz-user-select: none"
                                                           data-val="<?php echo $key; ?>"
                                                           data-odd="<?php if($match['m_status']==1){echo 1;}
                                                           elseif($match['result']['bqc']==trim($value) || !$match['full_score']){echo $match['bqcSp' . $key];}
                                                           else{ echo 0;} ?>">
                                                            <b><?php echo $match['bqcSp' . $key]; ?></b>
                                                        </a>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
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
                    <?php endif; 
                    endforeach; ?>
            <?php endif; ?>