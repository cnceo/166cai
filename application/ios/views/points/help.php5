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
    <title>积分帮助</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
</head>
<body>
    <div class="wrapper p-jifen-help">
        <div class="jifen-help-mod">
            <h2>什么是积分</h2>
            <p>为了给用户带来更多实惠，您可以通过购彩和完成任务获得额外积分，积分可以在积分商城兑换各种红包；</p>
            <table class="jifen-table">
                <thead>
                    <tr>
                        <th>用户行为</th>
                        <th>积分</th>
                        <th>说明</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>购彩实付1元</td>
                        <td><em>+1</em></td>
                        <td>每日上限5000</td>
                    </tr>
                    <tr>
                        <td>完成任务</td>
                        <td>不等</td>
                        <td>
                            <a href="/ios/points/mall">马上做任务</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>1元=1积分，细则如下：</p>
            <ol>
                <li>1.自购方案在出票成功后发放积分；</li>
                <li>2.追号方案分期发放积分，在追号方案出票后发放，如中途停追的期数不给积分；</li>
                <li>3.合买方案在方案出票后根据实际认购金额发放对应的积分；</li>
                <li>4.合买发起人以实际保底金额发放积分；</li>
                <li>5.任务完成后将发放积分，用户可在积分商城领取积分；</li>
                <li>6.使用红包以实付金额发放成长值，彩金消费发放积分；</li>
                <li>7.积分均为正整数，如出现小数则退位取整。</li>
            </ol>
        </div>
        <div class="jifen-help-mod">
            <h2>商品介绍</h2>
            <p>积分是有有效期的。每年3月1日 00:00:00统一清除上一年度产生的积分，在限定时间内不兑换或消耗则全部清零。请在积分兑换截止日期前，及时兑换或使用，避免浪费积分。</p>
        </div>
        <div class="jifen-help-mod">
            <h2>积分兑换规则</h2>
            <ol>
                <li>1.积分兑换的是彩金红包，兑换成功后等额彩金将返至购彩账户，兑换彩金仅支持购彩消费，不支持提现；</li>
                <li>2.每人每天有3次兑换机会；</li>
                <li>3.若积分余额充足但兑换按钮为灰色，说明当天礼包已兑完，请于次日来兑换。</li>
            </ol>
        </div>
        <div class="jifen-help-mod">
            <h2>重要提示</h2>
            <p>自2018年1月23日起，购彩和完成任务可获得额外积分，请勿违规获取积分（包括但不限于虚假交易、套现、篡改数据、恶意退款等），如发现存在违规行为，166彩票有权取消获得积分的资格并收回违规获得的积分。</p>
        </div>
    </div>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>