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
    <title>福彩3D玩法说明</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css')?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/play-des.min.css')?>">
</head>
<body>
    <div class="wrapper play-des">
        <div class="pd30">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>开奖时间</th>
                        <td>每天21:15开奖</td>
                    </tr>
                    <tr>
                        <th>玩法规则</th>
                        <td>每期开出一个3位数作为中奖号码，百、十、个位每位号码取值范围为0-9</td>
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
                <col width="68%">
                <col width="17%">
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
                    <td>直选</td>
                    <td>与开奖号相同且顺序一致<br>
                        示例：选号123，开奖123，中奖
                    </td>
                    <td>1040</td>
                </tr>
                <tr>
                    <td>组三</td>
                    <td>开奖号码有2个相同数字和1个其他数字，全部猜中，顺序不限
                    <br>示例：组三单：选号188，开奖188，中奖
                    <br>组三复：选号18，开奖188或118，中奖
                    </td>
                    <td>346</td>
                </tr>
                <tr>
                    <td>组六</td>
                    <td>开奖号码3个数字全不同，全部猜中，顺序不限
                    <br>示例：选号123，开奖123，中奖
                    </td>
                    <td>173</td>
                </tr>
            </tbody>
        </table> 
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>