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
    <title>会员帮助</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
</head>
<body>
    <div class="wrapper p-jifen-help">
        <div class="jifen-help-mod">
            <h2>会员成长体系</h2>
            <p>用户通过注册后均自动加入会员成长体系。体系共包含6个阶段，具体成长阶段取决于会员近一年内累积的成长值，当您最近一年累积的成长值达到上一级（或多级）要求即可升级。</p>
            <div class="img">
                <img src="/caipiaoimg/static/images/member-help-img1.png" alt="">
            </div>
        </div>
        <div class="jifen-help-mod">
            <h2>会员特权</h2>
            <div class="img">
                <img src="/caipiaoimg/static/images/member-help-img2.png" alt="">
            </div>
        </div>
        <div class="jifen-help-mod">
            <h2>成长值获得</h2>
            <p>成长值是依据彩民购彩金额和活跃程度来计算经验值，具体如下：</p>
            <table class="jifen-table">
                <thead>
                    <tr>
                        <th>用户行为</th>
                        <th>成长值</th>
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
                        <td>每日首次登录</td>
                        <td><em>+5</em></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p>1元=1成长值，细则如下：</p>
            <ol>
                <li>1.自购方案在出票成功后发放成长值；</li>
                <li>2.追号方案分期发放成长值，在追号方案出票后发放，如中途停追的期数不给成长值；</li>
                <li>3.合买方案在方案出票后根据实际认购金额发放对应的成长值；</li>
                <li>4.合买发起人以实际保底金额发放成长值；</li>
                <li>5.使用红包以实付金额发放成长值，彩金消费发放成长值；</li>
                <li>6.成长值均为正整数，如出现小数则退位取整。</li>
            </ol>
        </div>
        <div class="jifen-help-mod">
            <h2>升降保级规则</h2>

            <h3>会员升级</h3>
            <p>当您最近一年内累积的成长值达到上一级别（或者多级别）要求时即可升级，同时按照升级日开始计算下一个年度成长值，升级后超出当前等级最低要求的成长值将顺延至下一个年度。</p>
        </div>
        <div class="jifen-help-mod">
            <h3>会员保级</h3>
            <p>当您最近一年内累积的成长值未能达到升级要求，但只要您达到当前等级的最低成长值，您的级别即可顺延一年，同时按照保级日开始计算下一个年度成长值，保级后超出当前等级最低要求的成长值将顺延至下一个年度。</p>
        </div>
        <div class="jifen-help-mod">
            <h3>会员降级</h3>
            <p>当您最近一年内累积的成长值不能满足升级或保级要求，您的级别将下调一个级别，同时按照降级日重新计算下一个年度成长值。</p>
        </div>
        <div class="jifen-help-mod">
            <h3>重要提示</h3>
            <p>会员成长体系上线后，2018年1月23日前已注册的用户，自2018年1月23日起首次登录之日开始计算成长值，初始等级为新手彩民，初始成长值为0；2018年1月23日后注册的用户，自注册之日起开始计算成长值，初始等级为新手彩民，初始成长值为0。</p>
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>