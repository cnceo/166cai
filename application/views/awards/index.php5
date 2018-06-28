<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/drawing.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
$(function() {
    var ssqAward = '<?php echo $awards[Lottery_Model::SSQ]['awardNumber']; ?>';
    var ssqHtml = cx.Lottery.renderAward(cx.Lottery.SSQ, ssqAward);
    $('.ssq-award-nums').html(ssqHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var syxwAward = '<?php echo $awards[Lottery_Model::SYYDJ]['awardNumber']; ?>';
    var syxwHtml = cx.Lottery.renderAward(cx.Lottery.SYXW, syxwAward);
    $('.syxw-award-nums').html(syxwHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var dltAward = '<?php echo $awards[Lottery_Model::DLT]['awardNumber']; ?>';
    var dltHtml = cx.Lottery.renderAward(cx.Lottery.DLT, dltAward);
    $('.dlt-award-nums').html(dltHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var fcsdAward = '<?php echo $awards[Lottery_Model::FCSD]['awardNumber']; ?>';
    var fcsdHtml = cx.Lottery.renderAward(cx.Lottery.FCSD, fcsdAward);
    $('.fcsd-award-nums').html(fcsdHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var qxcAward = '<?php echo $awards[Lottery_Model::QXC]['awardNumber']; ?>';
    var qxcHtml = cx.Lottery.renderAward(cx.Lottery.QXC, qxcAward);
    $('.qxc-award-nums').html(qxcHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var qlcAward = '<?php echo $awards[Lottery_Model::QLC]['awardNumber']; ?>';
    var qlcHtml = cx.Lottery.renderAward(cx.Lottery.QLC, qlcAward);
    $('.qlc-award-nums').html(qlcHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var plsAward = '<?php echo $awards[Lottery_Model::PLS]['awardNumber']; ?>';
    var plsHtml = cx.Lottery.renderAward(cx.Lottery.PLS, plsAward);
    $('.pls-award-nums').html(plsHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var plwAward = '<?php echo $awards[Lottery_Model::PLW]['awardNumber']; ?>';
    var plwHtml = cx.Lottery.renderAward(cx.Lottery.PLW, plwAward);
    $('.plw-award-nums').html(plwHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var sfcAward = '<?php echo $awards[Lottery_Model::SFC]['awardNumber']; ?>';
    var sfcHtml = cx.Lottery.renderAward(cx.Lottery.SFC, sfcAward);
    $('.sfc-award-nums').html(sfcHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));

    var rjAward = '<?php echo $awards[Lottery_Model::RJ]['awardNumber']; ?>';
    var rjHtml = cx.Lottery.renderAward(cx.Lottery.RJ, rjAward);
    $('.rj-award-nums').html(rjHtml.replace(/span/g, 'li').replace(/ball-red/g, 'numRed').replace(/ball-blue/g, 'numBlue'));
});
</script>
<!--容器-->
<div class="wrap clearfix">
    <div class="fl">
        <!--双色球-->
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="images/bg/indexSSQ.gif" alt="双色球" width="72" height="72" />
                <h3>双色球</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::SSQ]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>ssq">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::SSQ; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::SSQ; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="ssq-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::SSQ]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/index11.gif');?>" alt="11运夺金" width="72" height="72" />
                <h3>11运夺金</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::SYYDJ]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>syxw">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::SYYDJ; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::SYYDJ; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="syxw-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::SYYDJ]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexFCSD.gif');?>" alt="福彩3D" width="72" height="72" />
                <h3>福彩3D</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::FCSD]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>fcsd">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::FCSD; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::FCSD; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="fcsd-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::FCSD]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexQXC.gif');?>" alt="七星彩" width="72" height="72" />
                <h3>七星彩</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::QXC]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>qxc">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::QXC; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::QXC; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="qxc-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::QXC]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexPLS.gif');?>" alt="排列三" width="72" height="72" />
                <h3>排列三</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::PLS]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>pls">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::PLS; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::PLS; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="pls-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::PLS]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexSFC.gif');?>" alt="胜负彩" width="72" height="72" />
                <h3>胜负彩</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::SFC]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>sfc">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::SFC; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::SFC; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="sfc-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::SFC]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
    </div>
    <div class="fr">
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexDLT.gif');?>" alt="大乐透" width="72" height="72" />
                <h3>大乐透</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::DLT]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>dlt">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::DLT; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::DLT; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="dlt-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::DLT]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <!--竞彩足球-->
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexJCZQ.gif');?>" alt="竞彩足球" width="72" height="72" />
                <h3>竞彩足球</h3>
            </div>
            <div class="fr">
                <p>
                    <strong></strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>jczq/hh">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/jczq">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::JCZQ; ?>">中奖查询</a>
                    </span>
                </p>
                <dl>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd'); ?>" class="selected"><?php echo date('Y.m.d'); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd', strtotime('-1 day')); ?>"><?php echo date('Y.m.d', strtotime('-1 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd', strtotime('-2 day')); ?>"><?php echo date('Y.m.d', strtotime('-2 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd', strtotime('-3 day')); ?>"><?php echo date('Y.m.d', strtotime('-3 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd', strtotime('-4 day')); ?>"><?php echo date('Y.m.d', strtotime('-4 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jczq?date=<?php echo date('Ymd', strtotime('-5 day')); ?>"><?php echo date('Y.m.d', strtotime('-5 day')); ?></a></dd>
                </dl>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexJCLQ.gif');?>" alt="竞彩篮球" width="72" height="72" />
                <h3>竞彩篮球</h3>
            </div>
            <div class="fr">
                <p>
                    <strong></strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>jclq/hh">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/jclq">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::JCLQ; ?>">中奖查询</a>
                    </span>
                </p>
                <dl>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd'); ?>" class="selected"><?php echo date('Y.m.d'); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd', strtotime('-1 day')); ?>"><?php echo date('Y.m.d', strtotime('-1 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd', strtotime('-2 day')); ?>"><?php echo date('Y.m.d', strtotime('-2 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd', strtotime('-3 day')); ?>"><?php echo date('Y.m.d', strtotime('-3 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd', strtotime('-4 day')); ?>"><?php echo date('Y.m.d', strtotime('-4 day')); ?></a></dd>
                    <dd><a href="<?php echo $baseUrl; ?>awards/jclq?date=<?php echo date('Ymd', strtotime('-5 day')); ?>"><?php echo date('Y.m.d', strtotime('-5 day')); ?></a></dd>
                </dl>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexQLC.gif');?>" alt="七乐彩" width="72" height="72" />
                <h3>七乐彩</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::QLC]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>qlc">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::QLC; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::QLC; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="qlc-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::QLC]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexPLW.gif');?>" alt="排列五" width="72" height="72" />
                <h3>排列五</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::PLW]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>plw">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::PLW; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::PLW; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="plw-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::PLW]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
        <div class="drawingSSQ border">
            <div class="fl">
                <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexRJ.gif');?>" alt="任选九" width="72" height="72" />
                <h3>任选九</h3>
            </div>
            <div class="fr">
                <p>
                    <strong>第<?php echo $awards[Lottery_Model::RJ]['seExpect']; ?>期</strong>
                    <span>
                        <a href="<?php echo $baseUrl; ?>rj">立即投注</a>
                        <a href="<?php echo $baseUrl; ?>awards/number/<?php echo Lottery_Model::RJ; ?>">历史开奖</a>
                        <a href="<?php echo $baseUrl; ?>orders?lid=<?php echo Lottery_Model::RJ; ?>">中奖查询</a>
                    </span>
                </p>
                <ul class="rj-award-nums">
                </ul>
                <h4>开奖时间：<?php echo date('Y年m月d日H:i:s', $awards[Lottery_Model::RJ]['awardTime'] / 1000); ?></h4>
            </div>
        </div>
    </div>
</div>
<!--容器end-->
