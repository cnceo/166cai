<?php
function nextWeekday($date)
{
    $tmpAry = explode(' ', $date);
    $nextDay = date('w', strtotime('+1 day', strtotime($tmpAry[0])));
    $weekStrAry = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

    return $weekStrAry[$nextDay];
}
$sfcOptions = array(
    '15'   => '1-5',
    '610'  => '6-10',
    '1115' => '11-15',
    '1620' => '16-20',
    '2125' => '21-25',
    '26'   => '26+',
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
                                if ($match['sfcGd'])
                                {
                                    $matchCount += 1;
                                }
                            }
                        }
                        if ($matchCount):
                        ?>
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
                <?php endif; ?>