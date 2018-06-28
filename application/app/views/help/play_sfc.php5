<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>胜负彩玩法说明</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css')?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/play-des.min.css')?>">
</head>
<body>
    <div class="wrapper play-des">
        <div class="pd30">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>玩法规则</th>
                        <td>竞猜全部14场比赛90分钟内的胜平负结果</td>
                    </tr>
                    <tr>
                        <th>奖项设置</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table-bet">
            <colgroup>
                <col width="15%">
                <col width="45%">
                <col width="40%">
            </colgroup>
            <thead>
                <tr>
                    <th>奖级</th>
                    <th>中奖条件</th>
                    <th>奖金（元）</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>一等奖</td>
                    <td>14场比赛胜平负结果全中</td>
                    <td>当期奖金总额的70%与奖池奖金之和除以中奖注数</td>
                </tr>
                <tr>
                    <td>二等奖</td>
                    <td>中任意13场比赛胜平负结果</td>
                    <td>当期奖金总额的30%除以中奖注数</td>
                </tr>
            </tbody>
        </table> 
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>