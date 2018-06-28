<?php
/**
 * @see SFC::index()
 * @var $prevIssue
 * @var $matches
 * @var $lotteryId
 * @var $currIssue
 */
$oddIndex = array(
    3 => '1',
    1 => '2',
    0 => '3',
);
?>
<div class="wrap cp-box bet-jc sfc">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>
    <div class="cp-box-bd bet">
        <div class="bet-main">
            <?php echo $this->load->view('v1.1/elements/lzc/kj_info'); ?>
            <?php echo $this->load->view('v1.1/elements/lzc/filter_periods'); ?>
            <div class="jc-table-box">
                <div class="jc-table-hd-box">
                    <div class="jc-table-hd">
                        <table>
                            <colgroup>
                                <col width="50">
                                <col width="86">
                                <col width="86">
                                <col width="292">
                                <col width="86">
                                <col width="86">
                                <col width="86">
                                <col width="86">
                                <col width="140">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>场次<u></u></th>
                                <th>联赛名称<u></u></th>
                                <th>停售时间<u></u></th>
                                <th>
                                    <div class="name"><span class="name-l">主队</span><s></s><span
                                            class="name-r">客队</span></div>
                                    <u></u>
                                </th>
                                <th>全场比分<u></u></th>
                                <th>彩果赔率<u></u></th>
                                <th>彩果<u></u></th>
                                <th>数据<u></u></th>
                                <th>
                                    <div class="table-option pjop-filter">
                                        <span class="table-option-title" id="op-name">99家平均<i class="table-option-arrow"></i></span>
                                        <ul class="table-option-list">
                                            <li><a class="odds-refer selected" href="javascript:void(0);" data-cid="0">99家平均</a>
                                            </li>
                                            <li><a class="odds-refer" href="javascript:void(0);" data-cid="1">威廉希尔</a>
                                            </li>
                                            <li><a class="odds-refer" href="javascript:void(0);" data-cid="3">立博</a>
                                            </li>
                                            <li><a class="odds-refer" href="javascript:void(0);" data-cid="4">bet365</a>
                                            </li>
                                            <li><a class="odds-refer" href="javascript:void(0);" data-cid="8">bwin</a>
                                            </li>
                                            <li><a class="odds-refer" href="javascript:void(0);" data-cid="2">澳门</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="jc-box">
                    <div class="jc-box-bd">
                        <table class="jc-table">
                            <colgroup>
                                <col width="50">
                                <col width="86">
                                <col width="86">
                                <col width="292">
                                <col width="86">
                                <col width="86">
                                <col width="86">
                                <col width="86">
                                <col width="140">
                            </colgroup>
                            <tbody>
                            <?php $awardNumAry = explode(',', $prevIssue['result']); ?>
                            <?php if ($matches): ?>
                                <?php foreach ($matches as $match): ?>
                                    <tr class="match" data-index="<?php echo $match['orderId'] - 1; ?>">
                                        <td><?php echo str_pad($match['orderId'], 2, 0, STR_PAD_LEFT); ?></td>
                                        <td class="jc-table-title match-league"><?php echo $match['gameName']; ?></td>
                                        <td><?php echo date('m-d H:i', $match['gameTime'] / 1000); ?></td>
                                        <td>
                                            <div
                                                class="name"><span
                                                    class="name-l"><s><?php echo empty($match['hRank']) ? '' : ('[' . $match['hRank'] . ']') ?></s><a
                                                        <?php if (empty($match['queryMId'])): ?>
                                                            onclick="return false;"
                                                        <?php else: ?>
                                                            href="<?php echo $match['hDetail']; ?>"
                                                        <?php endif; ?>
                                                        target="_blank"><?php echo $match['teamName1']; ?></a></span><span
                                                    class="name-r"><a
                                                        <?php if (empty($match['queryMId'])): ?>
                                                            onclick="return false;"
                                                        <?php else: ?>
                                                            href="<?php echo $match['aDetail']; ?>"
                                                        <?php endif; ?>
                                                        target="_blank"><?php echo $match['teamName2']; ?></a><s><?php echo empty($match['aRank']) ? '' : ('[' . $match['aRank'] . ']') ?></s></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo empty($match['score']) ? '---' : $match['score']; ?>
                                        </td>
                                        <?php $scoreAry = explode(':', $match['score']) ?>
                                        <td class="cgodd"
                                            <?php if (count($scoreAry) > 1): ?>
                                                <?php if ($scoreAry[0] > $scoreAry[1]): 
                                                		echo "data-cg='3'>";
                                                		echo empty($match['oh']) ? '0.00' : $match['oh']; 
                                                    elseif ($scoreAry[0] == $scoreAry[1]): 
                                                    	echo "data-cg='1'>";
                                                		echo empty($match['od']) ? '0.00' : $match['od']; 
                                                    elseif ($scoreAry[0] < $scoreAry[1]): 
                                                    	echo "data-cg='0'>";
                                                		echo empty($match['oa']) ? '0.00' : $match['oa']; 
                                                	endif; ?>
                                            <?php else: ?>
                                                ---
                                            <?php endif; ?>
                                        </td>
                                        <td class="jc-option">
                                            <a class="selected" href="javascript:;" style="cursor: text">
                                                <?php if (count($scoreAry) > 1): ?>
                                                    <?php if ($scoreAry[0] > $scoreAry[1]): ?>
                                                        3
                                                    <?php elseif ($scoreAry[0] == $scoreAry[1]): ?>
                                                        1
                                                    <?php elseif ($scoreAry[0] < $scoreAry[1]): ?>
                                                        0
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    ---
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td class="oyx bd-r">
                                            <?php if (empty($match['queryMId'])): ?>
                                                <a onclick="return false" href="#">欧</a><a
                                                    onclick="return false" href="#">亚</a><a
                                                    onclick="return false" href="#">析</a>
                                            <?php else: ?>
                                                <a href="<?php echo $match['oddsUrl'] . 'match/europe/' . $match['queryMId'] . '?lotyid=1' ?>"
                                                   target="_blank">欧</a><a
                                                    href="<?php echo $match['oddsUrl'] . 'match/asia/' . $match['queryMId'] . '?lotyid=1' ?>"
                                                    target="_blank">亚</a><a
                                                    href="<?php echo $match['oddsUrl'] . 'match/analyze/' . $match['queryMId'] . '?lotyid=1' ?>"
                                                    target="_blank">析</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pjop">
                                            <span
                                                class="op-oh"><?php echo empty($match['oh']) ? '0.00' : $match['oh']; ?></span>
                                            <span
                                                class="op-od"><?php echo empty($match['od']) ? '0.00' : $match['od']; ?></span>
                                            <span
                                                class="op-oa"><?php echo empty($match['oa']) ? '0.00' : $match['oa']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="bet-type-area">
                <p class="not-on-sale">本期销售已截止</p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var lotteryId = <?php echo $lotteryId?>,
            currIssue = '<?php echo $currIssue['seExpect'] ?>',
            selectedClass = 'selected';
        $('body').on('click', '.odds-refer', function () {
            var $this = $(this),
                opName = $this.html();
            $.post('ajax/queryReferOdds', {
                lid: lotteryId,
                cid: $this.data('cid'),
                issue: currIssue
            }, function (data) {
                $this.closest('.table-option-list').find('.selected').removeClass(selectedClass)
                    .end().end().addClass(selectedClass);
                $('.match').each(function () {
                    var $this = $(this),
                        mid = $this.data('index') + 1;
                    if (!(mid in data)) {
                        data[mid] = {
                            oh: '0.00',
                            od: '0.00',
                            oa: '0.00'
                        };
                    }
                    $this.find('.pjop').children('.op-oh').html(parseFloat(data[mid]['oh']).toFixed(2)).end()
                        .children('.op-od').html(parseFloat(data[mid]['od']).toFixed(2)).end()
                        .children('.op-oa').html(parseFloat(data[mid]['oa']).toFixed(2)).end();
                    switch($this.find('.cgodd').data('cg')) {
	                	case 3:
	                		$this.find('.cgodd').html(parseFloat(data[mid]['oh']).toFixed(2));
	                		break;
	                	case 1:
	                		$this.find('.cgodd').html(parseFloat(data[mid]['od']).toFixed(2));
	                		break;
	                	case 0:
	                		$this.find('.cgodd').html(parseFloat(data[mid]['oa']).toFixed(2));
	                		break;
	                }
                });
                $('#op-name').html(opName + '<i class="table-option-arrow"></i>');
            }, 'json');
        }).on('click', '.filterPeriods', function () {
            location.assign([location.origin, location.pathname, '?issue=', $(this).data('issue')].join(''));
        });

        $('.table-option').on({
            mouseover: function(){
                $(this).addClass('table-option-hover')
            },
            mouseout: function(){
                $(this).removeClass('table-option-hover')
            }
        });
    });
</script>