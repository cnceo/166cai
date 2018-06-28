<?php $this->load->view('comm/header'); ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
<body>
    <div class="wrapper bet-detail">
        <!-- 未中奖的情况吧bet-winning改成bet-failed -->
        <div class="bet-detail-hd bet-winning">
            <div class="lottery-info ssq">
                <h1 class="lottery-info-name">竞彩足球</h1>
                <p>中奖500元</p> 
            </div>
        </div>
        <div class="bet-detail-bd cp-box">
            <div class="cp-box-hd">
                <h2 class="cp-box-title">投注方案<b>2串1</b><b>1注1倍</b><b>共200元</b></h2>
            </div>
            <div class="cp-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="17%">
                        <col width="17%">
                        <col width="26%">
                        <col width="18%">
                        <col width="22%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>场次</th>
                            <th>比赛时间</th>
                            <th>主队VS客队</th>
                            <th>玩法</th>
                            <th>投注方案<i>参考SP值</i></th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php echo $tpl;?>
                    </tbody>
                </table> 
            </div>
            <div class="pd30">
                <table class="table-info">
                    <tbody>
                        <tr>
                            <th>投注时间</th>
                            <td>2015-02-05 13:23</td>
                        </tr>
                        <tr>
                            <th>玩法</th>
                            <td>混合过关</td>
                        </tr>
                        <tr>
                            <th>订单类型</th>
                            <td>普通投注</td>
                        </tr>
                        <tr>
                            <th>订单编号</th>
                            <td>20150129181845024189</td>
                        </tr>
                    </tbody>
                </table>
            </div>  
        </div>
    </div>
    <script src="../ios/js/lib/require.js" data-main="../ios/js/ios"></script>
</body>
</html>