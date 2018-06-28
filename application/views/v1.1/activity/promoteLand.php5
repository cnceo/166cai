<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>您的随身彩票店-现在注册还送188红包哦-166彩票官网</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
<style>
body {
  background: #eaf4f6;
}

.module_warp{ min-width: 1000px;padding: 40px 0 70px;}
.module_warp_white{background: #fff;}
.module_warp_gray{background: #f6f6f6;}
.module_warp_inner{position: relative;width: 1000px;margin: 0 auto;}
.module_warp .module_warp_inner .sTit{line-height: 48px;height: 48px;display: block;text-align: center;font-size: 28px;color: #333;}
.module_warp_banner,.module_warp_banner .module_warp_inner{height: 480px; overflow: hidden;background:url(/caipiaoimg/v1.1/img/active/promoteLand/bannerBg.jpg?1213) center center  no-repeat; padding: 0;}
.module_warp_banner .registeredBtn {background:url(/caipiaoimg/v1.1/img/active/promoteLand/promoteLandMap.png) 0 0  no-repeat;width: 240px;height: 80px;overflow: hidden;display: block;margin: 285px auto 0;text-align: center;line-height: 80px;color: #fff;text-decoration: none;font-size: 20px;}
.module_warp_banner .registeredBtn:hover{background-position: 0 -80px;}
.module_warp_step {text-align: center;}
.module_warp_step img {display: block;margin: 0 auto;padding-top: 50px;}
.module_warp_notice {padding-bottom: 50px;text-align: center;}
.module_warp_notice img { display: block;margin: 0 auto;padding-top: 35px;}
.module_warp_notice .sTit a { vertical-align: bottom; font-size: 14px; color: #0c6ad4; line-height: 36px;}


.module_warp_pay {padding-bottom: 50px;text-align: center;}
.module_warp_pay img { display: block;margin: 0 auto;padding-top: 35px;}

.module_warp_safety ul { padding: 30px 0 0 0; overflow: hidden;}
.module_warp_safety li { float: left; width: 200px; padding: 30px 15px 0 15px; background: #fff; overflow: hidden; height: 300px; margin-left: 16px; display: inline;}
.module_warp_safety li .sDes{ display: block; line-height: 36px; height: 36px; text-align: center; font-size: 20px; overflow: hidden; padding-top: 20px; color: #333; padding-bottom: 10px;}
.module_warp_safety li p { line-height: 24px; font-size: 14px; color: #666;}
.module_warp_safety li p em { color: #333; font-weight: 700;}
.module_warp_safety li p.pPic { width: 250px; padding: 3px 0 0 0;}
.module_warp_safety li p.pPic a { float: left; margin: 0 5px 9px 0; display: inline;}
.module_warp_safety li .sTips { display: block; color: #999; text-align: center; font-size: 12px; line-height: 22px; height: 22px; padding: 5px 0 0 0;}
.module_warp_safety .iIcon{ background:url(/caipiaoimg/v1.1/img/active/promoteLand/promoteLandMap.png) 0 0  no-repeat; width: 90px; height: 72px; overflow: hidden; margin: 0 auto; display: block;}
.module_warp_safety .iIconA { background-position: 0 -428px;}
.module_warp_safety .iIconB { background-position: -91px -428px;}
.module_warp_safety .iIconC { background-position: -182px -428px;}
.module_warp_safety .iIconD { background-position: -273px -428px;}

.module_warp_receive {}
.module_warp_receive .sTit{ padding-bottom: 40px}
.module_warp_receive dl.w330 { width: 330px; float: left;}
.module_warp_receive dl.w340 { width: 340px; float: right}
.module_warp_receive dl dt { font-size: 18px; line-height: 30px; height: 30px; color: #333; padding: 25px 0 0 0;}
.module_warp_receive dl dd { line-height: 24px; padding: 6px 0 0 0; font-size: 14px;}
.module_warp_receive .winningPic{ float: left; padding-left: 80px;}

.module_warp_registered { background: #2c2926; padding: 30px 0 50px;}
.module_warp_registered img { display: block; margin: 0 auto;}
.module_warp_registered .registeredBtn { display: block; background:url(/caipiaoimg/v1.1/img/active/promoteLand/promoteLandMap.png) 0 -161px  no-repeat; width: 180px; height: 48px; line-height: 48px; text-align: center; color: #fff; text-decoration: none; font-size: 20px; margin: 20px auto 0;}
.module_warp_registered .registeredBtn:hover{ background-position: 0 -210px; color: #fff;}

.side-menu a { text-align: center;}
.side-menu a span { display: block;}
.side-menu a .hover { display: none; line-height: 18px; padding: 13px 5px 0;}
.side-menu .icon-font { width: 100%;}
.side-menu a:hover .default { display: none;}
.side-menu a:hover .hover { display: block;}
</style>
</head>
<body>
    <!--top begin-->
    <?php if (empty($this->uid)): ?>
        <div class="top_bar">
        	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
        </div>
    <?php else: ?>
        <div class="top_bar">
            <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
        </div>
    <?php endif; ?>
    </div>
    <div class="module_warp module_warp_banner">
        <div class="module_warp_inner">
            <?php if(!$isLogin){ ?>
            <a href="/main/register?tc" target="_blank" class="registeredBtn">立即注册</a>
            <?php }else{ ?>
            <a href="/ssq" target="_blank" class="registeredBtn">来一注双色球</a>
            <?php } ?>
        </div>
    </div>

    <div class="module_warp module_warp_white module_warp_step">
        <div class="module_warp_inner">
            <span class="sTit">创造全新购彩模式</span>
            <img src="/caipiaoimg/v1.1/img/active/promoteLand/step.jpg?1213" alt="" title="" width="1000" height="174">
        </div>
    </div>
    
    <div class="module_warp module_warp_gray module_warp_pay">
        <div class="module_warp_inner">
            <span class="sTit">支付便捷 , 零手续费</span>
            <img src="/caipiaoimg/v1.1/img/active/promoteLand/payStylePic.jpg?v=20161121" alt="" title="" width="840" height="200">
        </div>
    </div>

    <div class="module_warp module_warp_white module_warp_notice">
        <div class="module_warp_inner">
            <span class="sTit">出票有通知，权益有保障&nbsp;<a href="/safe/bindEmail" target="_blank" id="bindEmail">点我绑定邮箱></a></span>
            <img src="/caipiaoimg/v1.1/img/active/promoteLand/noticePic.jpg?v=20161104" alt="" title="" width="500" height="350">
        </div>
    </div>

    <div class="module_warp module_warp_gray module_warp_safety">
        <div class="module_warp_inner">
            <span class="sTit">安全策略完备，为你保驾护航</span>
            <ul class="clearfix">
                <li>
                    <i class="iIcon iIconA"></i>
                    <span class="sDes">资质齐全，信息透明</span>
                    <p class="clearfix pPic" >
                        <a href="//www.sgs.gov.cn/lz/licenseLink.do?method=licenceView&entyId=20170609153148983" target="_blank"><img src="/caipiaoimg/v1.1/img/active/promoteLand/qualificationIcon1.jpg" alt="" title=""></a>
                        <a href="https://ss.knet.cn/verifyseal.dll?sn=e16072531011564232v0gb000000&ct=df&a=1&pa=0.13280558679252863" target="_blank"><img src="/caipiaoimg/v1.1/img/active/promoteLand/qualificationIcon2.jpg" alt="" title=""></a>
                        <a href="http://<?php echo $this->config->item('domain')?>/caipiaoimg/v1.1/img/jgxxdm.png" target="_blank" alt="" title=""><img src="/caipiaoimg/v1.1/img/active/promoteLand/qualificationIcon3.jpg" alt="" title=""></a>
                        <a href="https://www.sgs.gov.cn/notice/notice/view?uuid=9DfasM8QpxkrBIC.hd.hMnJ4EgrVT52R&tab=01" target="_blank"><img src="/caipiaoimg/v1.1/img/active/promoteLand/qualificationIcon4.jpg" alt="" title=""></a>
                    </p>
                    <span class="sTips">点击以上图标查看资质</span>
                </li>
                <li>
                    <i class="iIcon iIconB"></i>
                    <span class="sDes">购彩实名，永不弃奖</span>
                    <p>为保障用户的合法权益，避免在中奖时因用户注册资料与真实情况不符而发生纠纷，用户注册时均需按照真实、全面、准确的原则填写姓名和身份证号码。</p>
                </li>
                <li>
                    <i class="iIcon iIconC"></i>
                    <span class="sDes">彩店审核，流程正规</span>
                    <p>在投注站为彩民提供出票服务前，166彩票会提前审核投注站的代销合同、代销证及相关资质。在核实投注站资质真实、有效后，才可上架为彩民提供出票服务。</p>
                </li>
                <li>
                    <i class="iIcon iIconD"></i>
                    <span class="sDes">先行赔付，彩金安全</span>
                    <p><em>如果出票成功后中奖未兑奖造成彩民权益损失，损失金额由166彩票网先行全额赔付，让您无后顾之忧。</em>之后我们将会协同公安等执法机关继续对相关投注站追责、追款。</p>
                </li>
            </ul>
        </div>
    </div>

    <div class="module_warp module_warp_white module_warp_receive">
        <div class="module_warp_inner">
            <span class="sTit">中奖无忧 , 领奖很方便</span>
            <div class="clearfix">
                <dl class="w330">
                    <dt>若您中得小奖</dt>
                    <dd>均直接派奖到您注册的购彩帐户，您可以申请提现；</dd>
                </dl>
                <img src="/caipiaoimg/v1.1/img/active/promoteLand/winningPic.jpg?v=20161104" alt="" title="" width="180" height="180" class="winningPic">
                <dl class="w340">
                    <dt>若您中得大奖（单注奖金≥100万）</dt>
                    <dd>会有专人联系您并确定领取方案，您可选择领取纸质票后我们协助您兑奖，也可选择由166代领彩金后派奖到购彩帐户，然后申请提现。</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="module_warp module_warp_registered">
        <div class="module_warp_inner">
            <img src="/caipiaoimg/v1.1/img/active/promoteLand/titPic.jpg" alt="" title="" width="360" height="42" class="titPic">
            <?php if(!$isLogin){ ?>
            <a href="/main/register?tc" target="_blank" class="registeredBtn">立即注册</a>
            <?php }else{ ?>
            <a href="/ssq" target="_blank" class="registeredBtn">来一注双色球</a>
            <?php } ?>
        </div>
    </div>
    
    <div class="side-menu">
        <a href="javascript:;" target="_blank" title="">
            <span class="default"><i class="icon-font">&#xe633;</i>客服热线</span>
            <span class="hover">400-690-6760</span>
        </a>
        <a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" target="_self"><i class="icon-font">&#xe634;</i>在线客服</a>
    </div>
<?php $this->load->view('v1.1/elements/common/footer_academy');?>
</body>
<script>
var loginrfsh = 1;
$(function(){
	if (!$.cookie('name_ie')) {
		$("#bindEmail").attr({'href':'javascript:;', 'target':'_self'});
		$('body').on('click', '#bindEmail', function(){
			cx.PopAjax.login(1);
		})
	}
	var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
	window.easemobim = window.easemobim || {};
	easemobim.config = {visitor: visitor};
})
</script>
<script src='//kefu.easemob.com/webim/easemob.js'></script>
</html>