<?php
/**
 * @see RJ::index()
 * @var $targetIssue
 * @var $currIssue
 * @var $matches
 * @var $lotteryConfig
 * @var $showBind
 * @var $lotteryId
 * @var $nextIssueIds
 * @var $cnName
 */
$time = $targetIssue['seFsendtime'] / 1000 - time();
$hmselling = 0;
$hmendTime = $targetIssue['seFsendtime'] / 1000 - $lotteryConfig[SFC]['united_ahead'] * 60;
if(!$lotteryConfig[SFC]['status'] || ($currIssue['sale_time'] > time() * 1000) || ($currIssue['seExpect'] != $minCurrentId)) {
	$selling = 0;
}else {
	$selling = 1;
	if ($lotteryConfig[SFC]['united_status']) {
		$hmselling = 1;
	}
}?>
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
                                <col width="290">
                                <col width="70">
                                <col width="70">
                                <col width="70">
                                <col width="50">
                                <col width="86">
                                <col width="140">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>场次<u></u></th>
                                <th>联赛名称<u></u></th>
                                <th>比赛时间<u></u></th>
                                <th>
                                    <div class="name"><span class="name-l">主队</span><s></s><span
                                            class="name-r">客队</span></div>
                                    <u></u>
                                </th>
                                <th><?php echo $wenan['sfcsf']['3']?><u></u></th>
                                <th><?php echo $wenan['sfcsf']['1']?><u></u></th>
                                <th><?php echo $wenan['sfcsf']['0']?><u></u></th>
                                <th>全包<u></u></th>
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
                                <col width="290">
                                <col width="70">
                                <col width="70">
                                <col width="70">
                                <col width="50">
                                <col width="86">
                                <col width="140">
                            </colgroup>
                            <tbody>
                            <?php if ($matches) : ?>
                                <?php foreach ($matches as $match): ?>
                                    <tr
                                        class="match"
                                        data-index="<?php echo $match['orderId'] - 1; ?>"
                                        data-jzdt="<?php echo date('Y-m-d H:i:s', $match['gameTime'] / 1000); ?>">
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
                                        <td class="sfc-option jc-option"><a class="options" data-val="4"
                                                                            href="javascript:;">3</a></td>
                                        <td class="sfc-option jc-option"><a class="options" data-val="2"
                                                                            href="javascript:;">1</a></td>
                                        <td class="sfc-option jc-option"><a class="options" data-val="1"
                                                                            href="javascript:;">0</a></td>
                                        <td class="bd-r">
                                            <input type="checkbox" class="select-all">
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
            <?php if (($currIssue['sale_time'] > time() * 1000) || ($currIssue['seExpect'] != $minCurrentId)): ?>
                <div class="bet-type-area">
                    <div class="bet-solutions box-collection">
                        <p><span>您本次选择了 <strong class="num-red">0</strong> 注，共 <strong
                                    class="num-red">0</strong> 元</span>
                        </p>
                    </div>
                    <div class="btn-group">
                        <a id="pd_sfc_buy" class="btn btn-main btn-disabled">确认预约</a>

                        <p class="btn-group-txt">
                            <span class="num-red">（该期次处于预售期，暂不支持销售）</span>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="bet-type-area bet-type-danx">
                    <div class="bet-solutions box-collection">
                        <p>
                            <span>您本次选择了 <strong class="num-red current-bet-num">0</strong> 注，共 <strong
                                    class="num-red current-bet-money">0</strong> 元</span>
                        </p>
                    </div>
                    <div class="bet-area-txt">
                        已选<strong class="num-multiple bet-num">0</strong>注，投
                        <div class="multi-modifier">
                            <a href="javascript:;" class="minus">-</a>
                            <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                            <a href="javascript:;" class="plus" data-max="99">+</a>
                        </div>
                        倍（最大99倍），共计<strong class="num-money bet-money">0</strong>元
                        <a href="javascript:;" class="bet-type-more"><i>+</i>添加并选择更多方案</a>
                        <a href="javascript:;" class="btn-ss btn-ss-bet btn-hemai">发起合买</a>
                    </div>
                </div>
                <div class="bet-type-area bet-type-duox" style="display: none">
                    <div class="bet-solutions box-collection">
                        <p><span>您本次选择了 <strong class="num-red current-bet-num">0</strong> 注，共 <strong
                                    class="num-red current-bet-money">0</strong> 元</span>
                        </p>
                    </div>
                    <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i
                                class="icon-font"></i></a>
                    </div>
                    <div class="bet-area">
                        <div class="bet-area-box">
                            <div class="bet-area-box-hd">
                                <ol>
                                    <li>01</li>
                                    <li>02</li>
                                    <li>03</li>
                                    <li>04</li>
                                    <li>05</li>
                                    <li>06</li>
                                    <li>07</li>
                                    <li>08</li>
                                    <li>09</li>
                                    <li>10</li>
                                    <li>11</li>
                                    <li>12</li>
                                    <li>13</li>
                                    <li>14</li>
                                </ol>
                                <span>方案金额</span>
                            </div>
                            <div class="bet-area-box-bd">
                                <div class="inner">
                                    <ul class="cast-list">
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="bet-area-qbtn">
                            <a class="btn-sup rand-cast" href="javascript:;" data-volume="1">机选1注</a>
                            <a class="btn-sup rand-cast" href="javascript:;" data-volume="5">机选5注</a>
                            <a class="btn-sup rand-cast" href="javascript:;" data-volume="10">机选10注</a>
                            <a class="btn-sup clear-list" href="javascript:;">清空列表</a>
                        </div>
                    </div>
                    <div class="bet-area-txt">
                        已选<strong class="num-multiple bet-num">0</strong>注，投
                        <div class="multi-modifier">
                            <a href="javascript:;" class="minus">-</a>
                            <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                            <a href="javascript:;" class="plus" data-max="99">+</a>
                        </div>
                        倍（最大99倍），共计<strong class="num-money bet-money">0</strong>元
                    </div>
                    
                </div>
                <div class="buy-type tab-radio" style="display: none">
					<div class="buy-type-hd tab-radio-hd">
						<div class="chase-number-notes">由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i></span></div>
						<em>购买方式：</em>
                        <ul>
							<li><label for="ordertype0"><input type="radio" id="ordertype0" name="chaseNumberTab" checked>自购</label></li>
							<li><label for="ordertype1" class="main-color-s"><input type="radio" id="ordertype1" name="chaseNumberTab">发起合买</label></li>
						</ul>
					</div>
					<div class="buy-type-bd tab-radio-bd">
						<div class="tab-radio-inner hide" style="display: none;"></div>
						<div class="tab-radio-inner"><?php $this->load->view('v1.1/elements/lottery/hemai');?></div>
                    </div>
				</div>
				<div class="btn-group">
                        <a id="pd_sfc_buy" class="btn btn-main btn-betting
                        <?php echo $lotteryConfig[RJ]['status'] ? 'submit' : 'btn-disabled' ?>
                        <?php echo $showBind ? 'not-bind' : '' ?>"><?php if($lotteryConfig[RJ]['status']):?>确认预约<?php else:?>暂停预约<?php endif;?></a>

                        <p class="btn-group-txt">
                            <input class="ipt_checkbox agreement" type="checkbox" checked="checked"
                                   id="agreement2"><label
                                for="agreement2">我已阅读并同意</label>
                            <a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a>
                        </p>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js'); ?>"></script>
<script type="text/javascript">
var lotteryId = <?php echo $lotteryId?>, currIssue = '<?php echo $currIssue['seExpect'] ?>', nextIssue = '<?php echo $nextIssueIds[0] ?>', typeCnName = '<?php echo $cnName . ", 第" . $currIssue["seExpect"] . "期" ?>',
realTypeCnName = '任选九', typeEnName = 'rj', time = <?php echo $time?>, alertLastTime = 5, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), 
hmselling = '<?php echo $hmselling?>', realendTime = '<?php echo $currIssue['end_sale_time']?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lzc.js'); ?>"></script>