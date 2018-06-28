<?php
/**
 * @see optimize::index()
 * @var $lotteryId
 * @var $lotteryConfig
 * @var $source
 * @var $midAry
 * @var $matchInfoHash
 * @var $matchToOption
 * @var $parlayStr
 * @var $betNum
 * @var $betMoney
 * @var $castStrAry
 * @var $isLogin
 * @var $showBind
 * @var $minBetMoney
 */
$this->load->config('wenan');
$wenan = $this->config->item('wenan');
if ($lotteryId == Lottery_Model::JCZQ) {
    $lineVolume = array(
        'SPF'   => 3,
        'RQSPF' => 1,
        'BQC'   => 2,
        'CBF'   => 3,
        'JQS'   => 3,
    );
    $betTypeName = array(
        'SPF'   => array(
            '3' => $wenan['jzspf']['3'],
    		'1' => $wenan['jzspf']['1'],
            '0' => $wenan['jzspf']['0']
        ),
        'RQSPF' => array(
            '3' => $wenan['jzspf']['r3'],
    		'1' => $wenan['jzspf']['r1'],
            '0' => $wenan['jzspf']['r0']
        ),
        'BQC'   => array(
            '3-3' => '胜胜',
            '3-1' => '胜平',
            '3-0' => '胜负',
            '1-3' => '平胜',
            '1-1' => '平平',
            '1-0' => '平负',
            '0-3' => '负胜',
            '0-1' => '负平',
            '0-0' => '负负',
        ),
        'CBF'   => array(
            '9:0' => '胜其他',
            '9:9' => '平其他',
            '0:9' => '负其他',
        ),
        'JQS'   => array(
            '0' => '0球',
            '1' => '1球',
            '2' => '2球',
            '3' => '3球',
            '4' => '4球',
            '5' => '5球',
            '6' => '6球',
            '7' => '7+球',
        ),
    );
    $betTypeForMatch = array(
        'SPF'   => array(
            '3' => $wenan['jzspf']['3'],
    		'1' => $wenan['jzspf']['1'],
            '0' => $wenan['jzspf']['0']
        ),
        'RQSPF' => array(
            '3' => $wenan['jzspf']['r3'],
    		'1' => $wenan['jzspf']['r1'],
            '0' => $wenan['jzspf']['r0']
        ),
        'BQC'   => array(
            '3-3' => '胜胜',
            '3-1' => '胜平',
            '3-0' => '胜负',
            '1-3' => '平胜',
            '1-1' => '平平',
            '1-0' => '平负',
            '0-3' => '负胜',
            '0-1' => '负平',
            '0-0' => '负负',
        ),
        'CBF'   => array(
            '9:0' => '胜',
            '9:9' => '平',
            '0:9' => '负',
        ),
        'JQS'   => array(
            '0' => '0球',
            '1' => '1球',
            '2' => '2球',
            '3' => '3球',
            '4' => '4球',
            '5' => '5球',
            '6' => '6球',
            '7' => '7+球',
        ),
    );
    $title = '竞彩足球';
    $saleStatus = $lotteryConfig[JCZQ]['status'];
    $stakeUrl = '/jczq/' . $source;
}
else {
    $lineVolume = array(
        'SF'   => 3,
        'RFSF' => 1,
        'SFC'  => 2,
        'DXF'  => 3,
    );
    $betTypeName = array(
        'SF' => array(
    		'3' => $wenan['jlsf']['3'],
            '0' => $wenan['jlsf']['0']
        ),
        'RFSF' => array(
            '3' => $wenan['jlsf']['r3'],
            '0' => $wenan['jlsf']['r0']
        ),
        'DXF'  => array(
            '3' => '大分',
            '0' => '小分',
        ),
        'SFC'  => array(
            '11' => $wenan['jlsf']['0']."1-5",
            '12' => $wenan['jlsf']['0']."6-10",
            '13' => $wenan['jlsf']['0']."11-15",
            '14' => $wenan['jlsf']['0']."16-20",
            '15' => $wenan['jlsf']['0']."21-25",
            '16' => $wenan['jlsf']['0']."26+",
            '01' => $wenan['jlsf']['3']."1-5",
            '02' => $wenan['jlsf']['3']."6-10",
            '03' => $wenan['jlsf']['3']."11-15",
            '04' => $wenan['jlsf']['3']."16-20",
            '05' => $wenan['jlsf']['3']."21-25",
            '06' => $wenan['jlsf']['3']."26+",
        ),
    );
    $betTypeForMatch = array(
        'SF' => array(
    		'3' => $wenan['jlsf']['3'],
            '0' => $wenan['jlsf']['0']
        ),
        'RFSF' => array(
            '3' => $wenan['jlsf']['r3'],
            '0' => $wenan['jlsf']['r0']
        ),
        'DXF'  => array(
            '3' => '大分',
            '0' => '小分',
        ),
        'SFC'  => array(
            '11' => $wenan['jlsf']['0']."1-5",
            '12' => $wenan['jlsf']['0']."6-10",
            '13' => $wenan['jlsf']['0']."11-15",
            '14' => $wenan['jlsf']['0']."16-20",
            '15' => $wenan['jlsf']['0']."21-25",
            '16' => $wenan['jlsf']['0']."26+",
            '01' => $wenan['jlsf']['3']."1-5",
            '02' => $wenan['jlsf']['3']."6-10",
            '03' => $wenan['jlsf']['3']."11-15",
            '04' => $wenan['jlsf']['3']."16-20",
            '05' => $wenan['jlsf']['3']."21-25",
            '06' => $wenan['jlsf']['3']."26+",
        ),
    );
    $title = '竞彩篮球';
    $saleStatus = $lotteryConfig[JCLQ]['status'];
    $stakeUrl = '/jclq/' . $source;
}
?>
<div class="cp-box-wrap cp-box p-bonus">
    <div class="cp-box-hd">
        <h2 class="cp-box-title"><?php echo $title ?>奖金优化</h2>
    </div>
    <div class="cp-box-bd bonus-plan">
        <div class="bonus-plan-before">
            <div class="bonus-plan-title">投注方案</div>
            <div class="bonus-plan-scroll">
                <table>
                    <colgroup>
                        <col width="61">
                        <col width="64">
                        <col width="65">
                        <col width="196">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>场次</th>
                        <th class="tar">主队</th>
                        <th class="tal no-bd-l">客队</th>
                        <th class="tal">投注内容</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($midAry as $mid): ?>
                        <tr class="match"
                            data-mid="<?php echo $matchInfoHash[$mid]['mid'] ?>"
                            data-home="<?php echo $matchInfoHash[$mid]['homeSname'] ?>"
                            data-away="<?php echo $matchInfoHash[$mid]['awarySname'] ?>"
                            data-endtime="<?php echo date('Y-m-d H:i:s', $matchInfoHash[$mid]['jzdt'] / 1000); ?>">
                            <td><?php echo $matchInfoHash[$mid]['weekId'] ?></td>
                            <td class="tar"><?php echo $matchInfoHash[$mid]['homeSname'] ?></td>
                            <td class="tal no-bd-l"><?php echo $matchInfoHash[$mid]['awarySname'] ?></td>
                            <td class="tal">
                                <?php foreach ($matchToOption[$mid] as $betType => $betAry): ?>
                                    <?php $matchDescAry = array(); ?>
                                    <?php foreach ($betAry as $betOption => $betOdd): ?>
                                        <?php array_push($matchDescAry,
                                            '<span class = "wager" data-type =' . $betType . ' data-option =' . $betOption . ' data-odds =' . $betOdd . '>' . (array_key_exists($betOption,
                                                $betTypeName[$betType]) ? $betTypeName[$betType][$betOption] : $betOption) . ($betType == ($lotteryId == Lottery_Model::JCZQ ? 'RQSPF' : "RFSF") ? '(' . $matchInfoHash[$mid]['let'] . ')' : "") . '[' . $betOdd . ']' . '</span>'); ?>
                                    <?php endforeach; ?>
                                    <?php $matchChunks = array_chunk($matchDescAry, $lineVolume[$betType]); ?>
                                    <?php foreach ($matchChunks as $chunk): ?>
                                        <p><?php echo implode('&nbsp;&nbsp;', $chunk) ?></p>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4">
                            <div class="total">过关方式：<?php echo str_replace('*', '串', $parlayStr); ?>
                                (共<?php echo $betNum ?>注)
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        <div class="bonus-plan-after">
            <div class="bonus-plan-after-hd">
                <div class="bonus-plan-title">优化方案</div>
                <div class="inner">
                    <div class="bonus-plan-bar">
                        <div class="bonus-plan-label mod-tips">
                            <label for="plan">计划购买：</label>
                            <input type="text" value="<?php echo $betMoney ?>" id="plan">元
                            <div class="mod-tips-t">
                                为达到较好的优化效果，投注金<br>
                                额至少为原方案金额的2倍
                                <b></b><s></s>
                            </div>
                        </div>
                        <div class="yh-list">
                            <ul>
                                <li class="selected" id="avgOptimize">平均优化<i></i></li>
                                <li class="<?php echo isset($parlayStr{3}) ? 'disabled' : '' ?> mod-tips"
                                    id="hotOptimize">搏热优化<i></i>
                                    <?php if (isset($parlayStr{3})): ?>
                                        <div class="mod-tips-t">
                                            该优化类型不支持组合过关
                                            <b></b><s></s>
                                        </div>
                                    <?php endif; ?>
                                </li>
                                <li class="<?php echo isset($parlayStr{3}) ? 'disabled' : '' ?> mod-tips"
                                    id="coldOptimize">
                                    搏冷优化<i></i>
                                    <?php if (isset($parlayStr{3})): ?>
                                        <div class="mod-tips-t">
                                            该优化类型不支持组合过关
                                            <b></b><s></s>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <div class="yh-help mod-tips">
                                <i class="icon-font">&#xe613;</i>
                                <div class="mod-tips-t">
                                    <ol>
                                        <li><em>平均优化：</em>使所有单注奖金较为平均，一定程度上避免方案中奖却不盈利！</li>
                                        <li><em>博热优化：</em>在其它单注奖金保本的情况下，使概率最高的单注奖金最大化！</li>
                                        <li><em>博冷优化：</em>在其它单注奖金保本的情况下，使回报最高的单注奖金最大化！</li>
                                    </ol>
                                    <b></b><s></s>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table>
                        <colgroup>
                            <col width="52">
                            <col width="256">
                            <col width="80">
                            <col width="120">
                            <col width="100">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>单注组合</th>
                            <th>单注奖金</th>
                            <th>倍数</th>
                            <th>
                                预测奖金<i class="icon-font edit">&#xe61e;</i>
                                <div class="mod-tips-t edit-box">
                                    所有单注奖金接近：
                                    <div class="multi-modifier-s">
                                        <a href="javascript:;" class="minus stdMinus">-</a>
                                        <label><input class="multi number stdMulti" type="text" value="1"
                                                      autocomplete="off"></label>
                                        <a href="javascript:;" class="plus stdPlus" data-max="1000000">+</a>
                                    </div>
                                    元
                                    <div class="btn-group">
                                        <a href="javascript:;" class="btn-confirm btn" id="stdConfirm">确认</a><a
                                            href="javascript:;"
                                            class="btn-cancel btn" id="stdCancel">取消</a>
                                    </div>
                                    <b></b><s></s>
                                </div>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="bonus-plan-after-bd">
                <table>
                    <colgroup>
                        <col width="52">
                        <col width="256">
                        <col width="80">
                        <col width="120">
                        <col width="100">
                    </colgroup>
                    <tbody id="schemeTable">
                    <?php $prizeAry = array(); ?>
                    <?php foreach ($castStrAry as $lnIndex => $eachCast): ?>
                        <?php $castLn = $eachCast['cast'] ?>
                        <?php $countCastLn = count($castLn);
                        $dataForStr = ""; ?>
                        <?php for ($i = 0; $i < $countCastLn; $i ++): ?>
                            <?php $castLn[$i] = explode('/', $castLn[$i]); ?>
                            <?php
                            $dataString = $castLn[$i][1] . '>' . $castLn[$i][0] . '=' . $castLn[$i][2];
                            if (in_array($castLn[$i][1], array('RQSPF', 'RFSF'))) {
                                $dataString .= '{' . $matchInfoHash[$castLn[$i][0]]['let'] . '}';
                            }
                            $dataString .= '(' . $castLn[$i][3] . ')';
                            if ($castLn[$i][1] == 'DXF') {
                                $dataString .= '{' . $matchInfoHash[$castLn[$i][0]]['preScore'] . '}';
                            }
                            $dataString .= ',';
                            $dataForStr .= $dataString;
                            $dataStr = rtrim("$dataForStr", ",");
                            ?>
                        <?php endfor; ?>
                        <tr class="bet-list-item"
                            data-index="<?php echo $lnIndex; ?>"
                            data-odd="<?php echo round($eachCast['odd'] * 200) / 200 ?>"
                            data-multi="<?php echo $eachCast['multi'] ?>"
                            data-str="<?php echo $dataStr ?>"
                            data-parlay="<?php echo $countCastLn . "*1" ?>"
                        >
                            <td><span class="fcw"><?php echo str_pad($lnIndex + 1, 2, '0', STR_PAD_LEFT) ?></span></td>
                            <td class="tal">
                                <?php for ($i = 0; $i < $countCastLn; $i ++): ?>
                                    <div class="bonus-plan-item" data-mid="<?php echo $castLn[$i][0] ?>"
                                         data-type="<?php echo $castLn[$i][1] ?>"
                                         data-option="<?php echo $castLn[$i][2] ?>"
                                         data-odds="<?php echo $castLn[$i][3] ?>">
                                        <?php echo $matchInfoHash[$castLn[$i][0]]['homeSname'] ?>
                                        <span><?php echo (array_key_exists($castLn[$i][2],
                                                    $betTypeForMatch[$castLn[$i][1]]) ? $betTypeForMatch[$castLn[$i][1]][$castLn[$i][2]] : $castLn[$i][2]) . ($castLn[$i][1] == ($lotteryId == Lottery_Model::JCZQ ? 'RQSPF' : "RFSF") ? '(' . $matchInfoHash[$castLn[$i][0]]['let'] . ')' : ""); ?></span>
                                    </div>
                                <?php endfor; ?>
                            </td>
                            <td>
                                <?php echo number_format($eachCast['odd'] * 2, 2, '.', ''); ?>
                            </td>
                            <td>
                                <div class="multi-modifier-s">
                                    <a href="javascript:;" class="minus eachMinus">-</a>
                                    <label><input class="multi number eachMulti" type="text"
                                                  value="<?php echo $eachCast['multi'] ?>" autocomplete="off"></label>
                                    <a href="javascript:;" class="plus eachPlus">+</a>
                                </div>
                            </td>
                            <td>
                                <?php $eachPrize = number_format(round($eachCast['odd'] * 2,
                                        2) * $eachCast['multi'], 2,
                                    '.', ''); ?>
                                <?php array_push($prizeAry, $eachPrize) ?>
                                <span class="each-prize<?php echo $eachPrize > $betMoney ? ' main-color-s' : ''; ?>">
                                    <?php echo $eachPrize; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bonus-plan-after-ft">
                <div class="inner">
                    <table>
                        <colgroup>
                            <col width="112">
                            <col width="276">
                            <col width="220">
                        </colgroup>
                        <tfoot>
                        <tr>
                            <td class="tar">实际投注金额：</td>
                            <td><b id="bet-money" class="main-color-s"><?php echo $betMoney ?></b>元</td>
                            <td rowspan="2">
                            	<div class="btn-group-s">
	                            	<a href="javascript:;" class="btn-s btn-specail <?php echo ($lotteryConfig[$lotteryId]['united_status']) ? 'btn-hemai' : 'btn-disabled'?> 
	                            	<?php echo $showBind ? 'not-bind' : '' ?>">发起合买</a>
	                                <a href="javascript:;"
	                                   class="btn-s btn-main
	                                <?php echo $saleStatus ? 'submit submitOpt' : 'btn-disabled' ?>
	                <?php echo $isLogin ? '' : 'not-login' ?>
	                <?php echo $showBind ? 'not-bind' : '' ?>
	                                ">
	                                    <?php if($saleStatus):?>立即预约<?php else :?>暂停预约<?php endif;?>
	                                </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="tar">理论奖金：</td>
                            <td><b id="min-money" class="main-color-s"><?php echo min($prizeAry) ?></b>~<b
                                    id="max-money" class="main-color-s"><?php echo number_format(array_sum($prizeAry),
                                        2, '.',
                                        '') ?></b>元
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <form id="stakeForm" method="post" action="<?php echo $stakeUrl ?>" target="_blank">
                <input id="stakeStr" type="hidden" name="stakeStr" value=""/>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var lotteryId = <?php echo $lotteryId?>,
        typeCnName = '<?php echo $title?>',
        typeHmName = '<?php echo $title . ", 发起合买";?>',
        MIN_BET_MONEY = <?php echo $minBetMoney ?>,
        openEndtime = '<?php echo $openEndtime?>',
        endTime = '<?php echo date('Y-m-d H:i:s', strtotime($endTime) + $lotteryConfig[$lotteryId]['united_ahead'] * 60)?>',
        hmendTime = '<?php echo $endTime?>',
        expiredImgUrl = '<?php echo getStaticFile("/caipiaoimg/v1.1/img/pop-jjyh.png"); ?>',
        ahead = '<?php echo $lotteryConfig[$lotteryId]['ahead']?>',
        hmahead = '<?php echo $lotteryConfig[$lotteryId]['united_ahead']?>';
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/optimization.js'); ?>"></script>
<script>
    $(function () {
        var $BonusHd = $('.p-bonus .bonus-plan-after-hd'),
            $BonusFt = $('.p-bonus .bonus-plan-after-ft'),
            $bonus = $('.p-bonus'),
            castPanelTop = $bonus.height() + $bonus.offset().top,
            BonusHdTop = $BonusHd.offset().top + $('.bonus-plan-title').height(),
            beforePlanScroll = $('.bonus-plan-before').find('.bonus-plan-scroll');
        // var bonusPlanScrollHeight = $(window).height() - beforePlanScroll.offset().top;

        function onScroll() {
            var scrollTop = $(window).scrollTop() + $(window).height();
            if (scrollTop >= castPanelTop) {
                $BonusFt.find('.inner').removeClass('bonus-plan-ft-fixed');
                if (!-[1,] && !window.XMLHttpRequest) {
                    $BonusFt.css({'position': 'static'});
                }
            } else {
                $BonusFt.find('.inner').addClass('bonus-plan-ft-fixed');
                if (!-[1,] && !window.XMLHttpRequest) {
                    $BonusFt.css({
                        'position': 'absolute',
                        'bottom': 'auto',
                        'top': scrollTop - $BonusFt.height() + 'px'
                    });
                }
            }
            if ($(window).scrollTop() <= BonusHdTop) {
                $BonusHd.find('.inner').removeClass('bonus-plan-hd-fixed');
                beforePlanScroll.removeClass('bonus-plan-table-fixed');
                beforePlanScroll.css({
                    height: 'auto'
                });
                if (!-[1,] && !window.XMLHttpRequest) {
                    $BonusHd.css({'position': 'static'});
                }
            } else {
                $BonusHd.find('.inner').addClass('bonus-plan-hd-fixed');
                beforePlanScroll.addClass('bonus-plan-table-fixed');
                beforePlanScroll.css({
                    height: $(window).height() + 'px'
                });
                if ($(window).scrollTop() > $(document).height() - $('.footer').height() - $(window).height() - 10) {
                    beforePlanScroll.css({
                        height: $(document).height() - $(window).scrollTop() - $('.footer').height() -$('.m-qlink').parents('.wrap').height() - 32 + 'px'
                    })
                }
                if (!-[1,] && !window.XMLHttpRequest) {
                    $BonusHd.css({
                        'position': 'absolute',
                        'bottom': 'auto',
                        'top': $(document).scrollTop() + 'px'
                    });
                    beforePlanScroll.css({
                        'position': 'absolute',
                        'bottom': 'auto',
                        'top': $(document).scrollTop() + 'px'
                    });
                }
            }


            function noScroll() {
                return false;
            }

            if (beforePlanScroll.hasClass('bonus-plan-table-fixed')) {
                beforePlanScroll.on('mousewheel', noScroll);
                beforePlanScroll.on('DOMMouseScroll', noScroll);
            } else {
                beforePlanScroll.off('mousewheel', noScroll);
                beforePlanScroll.off('DOMMouseScroll', noScroll);
            }
        }

        var timer = null;
        $('.yh-list .disabled').on({
            'mouseover': function () {
                var that = $(this);
                clearTimeout(timer);
                timer = setTimeout(function () {
                    that.find('.mod-tips-t').show()
                }, 1000)
            },
            'mouseout': function () {
                clearTimeout(timer);
                $(this).find('.mod-tips-t').hide();
            }
        });

        var Throttle;
        $(window).scroll(function () {
            onScroll();
        });
        $(window).resize(function () {
            clearTimeout(Throttle);
            Throttle = setTimeout(function () {
                onScroll();
            }, 2)
        });
        onScroll();
        $('.edit').on('click', function () {
            $('.edit-box').show();
        });
        $('.edit-box').on('click', '.btn', function () {
            $(this).parents('.edit-box').hide();
        });

        $('.bet-list-item').on('mouseenter', function () {
            var $this = $(this),
                bonus = $this.find('.bonus-plan-item');
            bonus.each(function () {
                var $this = $(this),
                    dataMid = $this.data('mid'),
                    dataType = $this.data('type'),
                    dataOption = $this.data('option'),
                    dataOdds = $this.data('odds');
                $('.match').each(function () {
                    var $this = $(this),
                        matchMid = $this.data('mid'),
                        bet = $this.find('.wager');
                    if (dataMid == matchMid) {
                        bet.each(function () {
                            var $this = $(this),
                                type = $this.data('type'),
                                option = $this.data('option'),
                                odds = $this.data('odds');
                            $this.removeClass('main-color-s');
                            if (dataType == type && dataOption == option && dataOdds == odds) {
                                $this.addClass('main-color-s');
                            }
                        });
                    }
                });
            });
        }).on('mouseleave', function () {
            $('.wager').removeClass('main-color-s');
        });
    })
</script>