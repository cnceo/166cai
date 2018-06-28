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
    <title>七乐彩玩法说明</title>
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
                        <td>每周一、三、五晚21:15开奖</td>
                    </tr>
                    <tr>
                        <th>玩法规则</th>
                        <td>从01~30中选择7个数字组成一注；每期开出7个基本号码和一个特别号码。</td>
                    </tr>
                    <tr>
                        <th>奖项设置</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table-bet">
            <colgroup>
                <col width="20%">
                <col width="50%">
                <col width="30%">
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
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                        </div>
                    </td>
                    <td>浮动</td>
                </tr>
                <tr>
                    <td>二等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="blue-ball"></span>
                        </div>
                    </td>
                    <td>浮动</td>
                </tr>
                <tr>
                    <td>三等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                        </div>
                    </td>
                    <td>浮动</td>
                </tr>
                <tr>
                    <td>四等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="blue-ball"></span>
                        </div>
                    </td>
                    <td>200</td>
                </tr>
                <tr>
                    <td>五等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                        </div>
                    </td>
                    <td>50</td>
                </tr>
                <tr>
                    <td>六等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="blue-ball"></span>
                        </div>
                    </td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>七等奖</td>
                    <td>
                        <div class="ball-group">
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                            <span class="red-ball"></span>
                        </div>
                    </td>
                    <td>5</td>
                </tr>
            </tbody>
        </table>
        <p class="table-tips">特别号码<span class="ball-group"><span class="blue-ball"></span></span>说明：特别号码仅做为二、四、六等奖的使用，即开出7个奖号后再从23个号码里面随机摇出一个就是特别号，只要跟你买的7个号码中的任意一个号码相符，就算中特别号。</p>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>