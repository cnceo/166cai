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
    <title>快乐扑克玩法说明</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/play-des.min.css');?>">
</head>
<body>
    <div class="wrapper play-des">
        <div class="pd30">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>开奖时间</th>
                        <td>10分钟一期，每天88期</td>
                    </tr>
                    <tr>
                        <th>玩法规则</th>
                        <td>从52张（黑桃，红桃，梅花，方块A~K各13张）扑克牌中的开出3张作为开奖号码</td>
                    </tr>
                    <tr>
                        <th>奖项设置</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table-bet">
            <colgroup>
                <col width="18%">
                <col width="65%">
                <col width="17%">
            </colgroup>
            <thead>
                <tr>
                    <th>玩法</th>
                    <th>中奖条件</th>
                    <th>奖金(元)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>对子包选</td>
                    <td>开奖号码有且只有2个数字相同（不分花色）</td>
                    <td>7</td>
                </tr>
                <tr>
                    <td>对子单选</td>
                    <td>开奖号码有且只有2个数字相同（不分花色）且与投注号码相同</td>
                    <td>88</td>
                </tr>
                <tr>
                    <td>同花包选</td>
                    <td>开奖号花色都相同</td>
                    <td>22</td>
                </tr>
                <tr>
                    <td>同花单选</td>
                    <td>开奖号花色都相同且与投注花色相同</td>
                    <td>90</td>
                </tr>
                <tr>
                    <td>顺子包选</td>
                    <td>开奖号的3个数字是连续的（不分花色）</td>
                    <td>33</td>
                </tr>
                <tr>
                    <td>顺子单选</td>
                    <td>开奖号的3个数字是连续的（不分花色）且与投注号相同</td>
                    <td>400</td>
                </tr>
                <tr>
                    <td>同花顺包选</td>
                    <td>开奖号的3个数字是连续的且花色都相同</td>
                    <td>535</td>
                </tr>
                <tr>
                    <td>同花顺单选</td>
                    <td>开奖号的3个数字是连续的且都是所投注的花色</td>
                    <td>2150</td>
                </tr>
                <tr>
                    <td>豹子包选</td>
                    <td>开奖号的3个数字都相同（不分花色）</td>
                    <td>500</td>
                </tr>
                <tr>
                    <td>豹子单选</td>
                    <td>开奖号的3个数字都相同（不分花色）且与投注号相同</td>
                    <td>6400</td>
                </tr>
                <tr>
                    <td>任选一</td>
                    <td>选1个号码投注且与开奖的任意1个相同（不分花色）</td>
                    <td>5</td>
                </tr>
                <tr>
                    <td>任选二</td>
                    <td>选2个号码投注且与开奖的任意2个相同（不分花色）</td>
                    <td>33</td>
                </tr>
                <tr>
                    <td>任选三</td>
                    <td>选3个号码投注，投注号码包含开奖号码（不分花色）</td>
                    <td>116</td>
                </tr>
                <tr>
                    <td>任选四</td>
                    <td>选4个号码投注，投注号码包含开奖号码（不分花色）</td>
                    <td>46</td>
                </tr>
                <tr>
                    <td>任选五</td>
                    <td>选5个号码投注，投注号码包含开奖号码（不分花色）</td>
                    <td>22</td>
                </tr>
                <tr>
                    <td>任选六</td>
                    <td>选6个号码投注，投注号码包含开奖号码（不分花色）</td>
                    <td>12</td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>