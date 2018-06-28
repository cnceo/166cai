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
                                <div class="jc-box-hd"><span class="jc-box-action">隐藏</span>
                                    <?php echo $date; ?> 12:00 ~ <?php echo nextWeekday($date); ?> 12:00
                                    共<?php echo $matchCount; ?>场比赛
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
                                                            <?php if($match['full_score']){ ?>
                                                            <?php $full_score=explode(':',$match['full_score']); ?>
                                                            <span class="bf"><?php echo $full_score[0];?><s>-</s><?php echo $full_score[1];?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <?php foreach (array('sf', 'rfsf', 'dxf') as $pt): ?>
                                                        <td class="jc-option jc-option2 hasmatched">
                                                            <a class="<?php echo empty($match[$pt . 'Gd']) ? 'stop-selling' : ($pt . '-option'); ?> <?php echo $pt . 'f' ?>
                                                               <?php if(($match['result'][$pt]==3) && !empty($match[$pt . 'Gd']) && $match['full_score']){ echo "bingo";} ?>"
                                                               onselectstart="return false;"
                                                               style="-moz-user-select: none"
                                                               data-val="<?php echo in_array($pt,
                                                                   array('dxf')) ? 3 : 0; ?>"
                                                               data-odd="<?php if($match['m_status']==1){ echo 1;}
                                                               elseif($match['result'][$pt]==3 || !$match['full_score'])
                                                               {echo in_array($pt,array('dxf')) ? $match['dxfBig'] : $match[$pt . 'Hf'];}
                                                               else{ echo 0;}?>"
                                                               data-option="<?php echo $pt?>">
                                                                <?php if (empty($match[$pt . 'Gd'])) : ?>
                                                                     	未开售
                                                                <?php else: ?>
                                                                    <?php if ($pt == 'sf'): ?>
                                                                        <em class=""><?php echo $match['sfHf']; ?></em>客胜
                                                                    <?php elseif ($pt == 'rfsf'): ?>
                                                                        <em class=""><?php echo $match['rfsfHf']; ?></em>
                                                                    <?php elseif ($pt == 'dxf'): ?>
                                                                        <em class=""><?php echo $match['dxfBig']; ?></em>大<?php echo $match['preScore']; ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                                <?php if ($match[$pt . 'Fu']): ?>
                                                                    <div class="mod-sup">
                                                                        <i class="mod-sup-bg"></i><u>单</u>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </a>
                                                            <a class="no-bd-b <?php echo empty($match[$pt . 'Gd']) ? 'stop-selling' : ($pt . '-option'); ?> <?php echo $pt . 's' ?>
                                                               <?php if(($match['result'][$pt]==0) && !empty($match[$pt . 'Gd']) && $match['full_score'] && $match['m_status']!=1){ echo "bingo";} ?>"
                                                               onselectstart="return false;"
                                                               style="-moz-user-select: none"
                                                               data-val="<?php echo in_array($pt,
                                                                   array('dxf')) ? 0 : 3; ?>"
                                                               data-odd="<?php if($match['m_status']==1){ echo 1;}
                                                               elseif($match['result'][$pt]==0 || !$match['full_score'])
                                                                { echo in_array($pt,array('dxf')) ? $match['dxfSmall'] : $match[$pt . 'Hs'];}
                                                                else{ echo 0;}?>"
                                                               data-option="<?php echo $pt?>">
                                                                <?php if (empty($match[$pt . 'Gd'])) : ?>
                                                                    未开售
                                                                <?php else: ?>
                                                                    <?php if ($pt == 'sf'): ?>
                                                                        <em class=""><?php echo $match['sfHs']; ?></em>主胜
                                                                    <?php elseif ($pt == 'rfsf'): ?>
                                                                        <em class=""><?php echo $match['rfsfHs']; ?></em><?php echo $match['let']; ?>
                                                                    <?php elseif ($pt == 'dxf'): ?>
                                                                        <em class=""><?php echo $match['dxfSmall']; ?></em>小<?php echo $match['preScore']; ?>
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
                                                <tr class="attach-options hasmatched" data-select="0"></tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
                <?php endif; ?>
