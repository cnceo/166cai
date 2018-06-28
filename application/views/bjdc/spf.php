<link href="css/lotteryZQ.css?v=<?php echo VER_LOTTERY_ZQ_CSS; ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/math.js?v=<?php echo VER_MATH_JS; ?>"></script>
<script type="text/javascript" src="js/bjdc.js?v=<?php echo VER_BJDC_JS; ?>"></script>
<script type="text/javascript">
var type = '<?php echo $bjdcType; ?>';
</script>
<script type="text/javascript">
$(function(){
    //展开筛选
    $(".lotteryPlayWrap").hover(function(){
        $(this).find('h3').addClass("hover").next("div.lotteryPlayBox").show();
    }, function(){
        $(".lotteryPlayWrap h3").removeClass().next("div.lotteryPlayBox").hide();
    });

    $(".seleFiveTit").hover(function(){
            $(this).addClass("seleFiveTit2").next("div.seleFiveBox").show();
    });
    $(".seleFiveWrap").hover(function(){
    }, function(){
            $(".seleFiveTit").removeClass("seleFiveTit2").next("div.seleFiveBox").hide();
    });
    var $castPanel = $('.cast-panel');
    var $header = $('.lotteryTableTH');
    function onScroll() {
        var top = $('#container').height() + $('#container').offset().top;
        var headerTop = $('.userLotteryTab').height() + $('.userLotteryTab').offset().top;
        var scrollTop = $(document).scrollTop() + $(window).height();
        if (scrollTop >= top) {
            $castPanel.removeClass('fixed');
        } else {
            $castPanel.addClass('fixed');
        }
        if ($(document).scrollTop() >= headerTop) {
            $header.addClass('fixed');
        } else {
            $header.removeClass('fixed');
        }
    }
    $(window).scroll(onScroll);
    onScroll();

    
    $('.select-anti').click(function() {
        $('.league').trigger('click');
    });
    $('.select-all').click(function() {
        $('.league').each(function(key, league) {
            if ($(league).attr('checked') != 'checked') {
                $(league).trigger('click');
            }
        });
    });
    $('.select-none').click(function() {
        $('.league').each(function(key, league) {
            if ($(league).attr('checked') == 'checked') {
                $(league).trigger('click');
            }
        });
    });
});
</script>

<!--容器-->
<div class="wrap clearfix" id="container">
    <?php echo $this->load->view('elements/lottery/info_panel'); ?>
    <?php echo $this->load->view('elements/crowd/buy'); ?>
    <!--彩票-->
    <div class="userLottery">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'cast')); ?>
        <?php echo $this->load->view('elements/bjdc/types'); ?>
        <!--表格-->
        <div class="lotteryTableTH">
            <table class="lotteryTableTwo">
                <tr>
                    <th width="10%"></th>
                    <th width="15%">截止时间</th>
                    <th width="15%">主队</th>
                    <th width="15%">客队</th>
                    <th width="15%">主队胜</th>
                    <th width="15%">平</th>
                    <th width="15%">客队胜</th>
                </tr>
            </table>
            <div class="lotteryPlayWrap league-filter">
                <h3>赛事筛选</h3>
                <div class="lotteryPlayBox">
                    <ul class="clearfix">
                        <?php foreach ($leagues as $league => $key): ?>
                        <li>
                            <label><input class="league" type="checkbox" checked="checked" value="<?php echo $key; ?>" /> <?php echo $league; ?></label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <p>
                        <a class="select-all">全选</a>
                        <a class="select-anti">反选</a>
                        <a class="select-none wordGray">清空</a></p>
                </div>
            </div>
        </div>
        <!--表格循环-->
        <!--01-->

        <table class="lotteryTableCP matches">
            <tbody>
                <?php foreach($matches as $date => $dateMatch): ?>
                <tr>
                    <td colspan="7" style="text-align: left; padding-left: 10px; font-weight: bold;"><?php echo $date; ?></td>
                </tr>
                <?php foreach ($dateMatch as $match): ?>
                <tr
                    class="match"
                    data-mid="<?php echo $match['mid']; ?>"
                    data-home="<?php echo $match['home']; ?>"
                    data-away="<?php echo $match['awary']; ?>"
                    data-let="<?php echo $match['let']; ?>"
                    data-league="<?php echo $leagues[$match['name']]; ?>"
                    data-wid="<?php echo $match['weekId']; ?>" >
                    <td class="match-league" width="10%" rowspan="2" style="background: <?php echo $match['cl']; ?>;"><?php echo $match['name']; ?><br /><?php echo $match['weekId']; ?></td>
                    <td width="15%">停售：<?php echo date('m-d H:i', $match['jzdt'] / 1000); ?></td>
                    <td width="15%" rowspan="2"><?php echo $match['home']; ?></td>
                    <td width="15%" rowspan="2"><?php echo $match['awary']; ?></td>
                    <td rowspan="2" width="15%" class="bgGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>" data-val="3" data-odd="<?php echo $match['spfSp3']; ?>"><?php if ($match['spfGd']) echo $match['spfSp3']; else echo '停售 '; ?></td>
                    <td rowspan="2" width="15%" class="bgGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>" data-val="1" data-odd="<?php echo $match['spfSp1']; ?>"><?php if ($match['spfGd']) echo $match['spfSp1']; else echo '停售'; ?></td>
                    <td rowspan="2" width="15%" class="bgGray <?php if ($match['spfGd']): ?>spf-option<?php endif; ?>" data-val="0" data-odd="<?php echo $match['spfSp0']; ?>"><?php if ($match['spfGd']) echo $match['spfSp0']; else echo '停售' ?></td>
                </tr>
                <tr class="rqspf-detail">
                    <td width="10%">比赛：<?php echo date('m-d H:i', $match['dt'] / 1000); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!--彩票end-->
</div>
<!--容器end-->

<!--选择信息-->
<div class="lotteryZQbg cast-panel fixed">
        <div class="lotteryZQCenter">
            <!--已选5场-->
            <div class="seleFiveWrap">
                <div class="seleFiveTit">
                    <p>已选<span class="count-matches">0</span>场</p>
                </div>
                <div class="seleFiveBox">
                    <div class="seleFiveBoxTit">
                        <strong>比赛</strong>
                        <span>投注内容</span>
                        <!--<img class="clear-matches" src="images/btn/btnClear.gif" alt="清空" width="61" height="41" />-->
                        <a class="clear-matches" href="javascript:void(0)">清空</a>
                    </div>
                    <div class="seleFiveBoxScroll">
                        <table class="selected-matches">
                        </table>
                    </div>
                </div>
            </div>
            <div class="seleFiveInfo">
                <div style="width: 295px; float:left;">
                    <h3>过关方式：<strong class="fr">共 <span class="bet-num">0</span> 注</strong></h3>
                    <h3>
                        <span class="gg-type selected" data-type="1*1" style="visibility: hidden;">单关</span>
                        <span class="gg-type" data-type="2*1" style="visibility: hidden;">2串1</span>
                        <span class="gg-type" data-type="3*1" style="visibility: hidden;">3串1</span>
                        <span class="gg-type" data-type="4*1" style="visibility: hidden;">4串1</span>
                        <span class="gg-type" data-type="5*1" style="visibility: hidden;">5串1</span>
                        <span class="gg-type" data-type="6*1" style="visibility: hidden;">6串1</span>
                        <span class="gg-type" data-type="7*1" style="visibility: hidden;">7串1</span>
                        <span class="gg-type" data-type="8*1" style="visibility: hidden;">8串1</span>
                    </h3>
                </div>
                <div class="seleFiveBtn">
                    <ul class="fl">
                        <li class="multi-modifier">
                            <h3>投注倍数：</h3>
                            <span class="minus selem">-</span>
                            <p><input class="multi number" type="text" value="1" />倍</p>
                            <span class="plus selem">+</span>
                        </li>
                    </ul>
                    <div class="fr">
                        <p class="fl">
                            <strong>预测奖金：</strong>
                        <span class="wordRed">
                            <span class="min-money">0.00</span> - <span class="max-money">0.00</span>
                        </span>
                            <strong>元。</strong>
                        </p>
                        <p class="fr">
                            <a class="seleView start-detail">明细与优化&gt;&gt;</a>
                        </p>
                        <p class="clear">
                            <a id="pd_jczq_buy" class="seleViewRed submit <?php if (!$isLogin) echo 'not-login'; ?>">共 <span class="bet-money">0</span>元 立即投注</a>
                            <a class="seleViewYellow start-crowd <?php if (!$isLogin) echo 'not-login'; ?>">发起合买</a>
                        </p>
                    </div>
                </div>
            </div>

        </div>
</div>
<!--选择信息end-->

<?php $this->load->view('elements/jczq/calc_prize'); ?>
