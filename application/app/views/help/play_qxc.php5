<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>七星彩玩法说明</title>
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
                        <td>每周二、五、日晚20:30开奖</td>
                    </tr>
                    <tr>
                        <th>玩法规则</th>
                        <td>每期开出一个7位数作为中奖号码，每位数字为0~9。</td>
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
                    <th>奖金(元)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>一等奖</td>
                    <td>投注号码与开奖号码完全相符且顺序<br>一致</td>
                    <td>最高500万</td>
                </tr>
                <tr>
                    <td>二等奖</td>
                    <td>连续6位号码与开奖号码相同位置的<br>连续6位号码相同</td>
                    <td>浮动</td>
                </tr>
                <tr>
                    <td>三等奖</td>
                    <td>连续5位号码与开奖号码相同位置的<br>连续5位号码相同</td>
                    <td>1800</td>
                </tr>
                <tr>
                    <td>四等奖</td>
                    <td>连续4位号码与开奖号码相同位置的<br>连续4位号码相同</td>
                    <td>300</td>
                </tr>
                <tr>
                    <td>五等奖</td>
                    <td>连续3位号码与开奖号码相同位置的<br>连续3位号码相同</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>六等奖</td>
                    <td>连续2位号码与开奖号码相同位置的<br>连续2位号码相同</td>
                    <td>5</td>
                </tr>
            </tbody>
        </table> 
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>