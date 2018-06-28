<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<title>注册送3元彩金-2345彩票网</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/global.css'); ?>"/>

<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jquery-1.8.3.min.js'); ?>"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/shake.js'); ?>"></script>
<script type="text/javascript">
    var baseUrl = '<?php echo $baseUrl; ?>';
    var busiUrl = '<?php echo $busiUrl; ?>';
    var passUrl = '<?php echo $passUrl; ?>';
    var payUrl = '<?php echo $payUrl; ?>';
    var fileUrl = '<?php echo $fileUrl; ?>';
    var cmsUrl = '<?php echo $cmsUrl; ?>';
    var G = {
        busiUrl: busiUrl,
        passUrl: passUrl,
        payUrl: payUrl,
        cmsUrl: cmsUrl,
        fileUrl: fileUrl
    };
</script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js'); ?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>

<style>
    html {
        overflow-y: auto;
    }
    a {
        text-decoration: none;
        color: #ff9000;
    }
    em {
        font-style: normal;
    }
    body {
        background: #1e76be;
        /* overflow-x: hidden; */
        font: 14px/1.5  Arial, "微软雅黑", sans-serif;
    }
    .ac-step .ac-mod-cot-bd li b, .ac-step .ac-mod-cot-bd .btn-active i, .ac-arrow {
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/sprite-active-rmb3.png'); ?>) no-repeat;
    }
    .ac-wrap {
        overflow-x: hidden;
        min-height: 100%;
        background: #1e76be;
    }
    .ac-main {
        position: relative;
        z-index: 10;
        width: 1000px;
        margin: 0 auto;
    }
    .ac-header {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 470px;
        overflow: hidden;
        *zoom: 1;
        _top: -24px;
    }
    .ac-fixed-aside {
        height: 40px;
    }
    .ac-aside {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 82px;
        overflow: hidden;
        margin-top: -82px;
    }
    .ac-aside .ft-bg2 {
        left: -8px;
    }
    .ac-aside .ft-bg1, .ac-aside .ft-bg3 {
        left: 8px;
    }
    .plaxify {
        position: absolute;
        z-index: 1;
    }
    .ac-header .bg1, .ac-header .bg2 {
        left: 0;
        top: 0;
    }
    .ac-header h1, .ac-hongbao {
        position: absolute;
        left: 50%;
        top: 0;
        z-index: 1;
        width: 1000px;
        margin-left: -500px;
    }
    .ac-header h1 {
        height: 340px;
    }
    .ac-hongbao {
        height: 280px;
    }
    .ac-slogan {
        left: 100px;
        /* left: 50%;
        margin-left: -400px; */
    }
    .ac-mod-cot {
        position: relative;
        *zoom: 1;
        z-index: 10;
        width: 100%;
        height: 200px;
        margin-bottom: 26px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/mod-cot-bg.png'); ?>) center 0 no-repeat;
        color: #fff;
    }
    .ac-mod-cot-hd {
        position: absolute;
        left: 10px;
        top: -6px;
        line-height: 1;
        font-size: 30px;
        color: #ff9600;
        font-style: italic;
        text-shadow: 1px 1px 1px rgba(0,0,0,.2);
    }
    .ac-step {
        margin-top: -120px;
    }
    .ac-step .ac-mod-cot-bd {
        margin-left: 20px;
        padding-top: 50px;
        overflow: hidden;
        *zoom: 1;
    }
    .ac-step .ac-mod-cot-bd li {
        position: relative;
        *zoom: 1;
        float: left; 
        width: 210px;
        height: 105px;
        margin: 0 30px;
        padding: 30px 25px 0;
        background: #2a84ce;
        text-align: center;
        font-size: 16px;
        *display: inline;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        -o-border-radius: 8px;
        -ms-border-radius: 8px;
        border-radius: 8px;
    }
    .ac-step .ac-mod-cot-bd li b {
        /* display: inline-block; */
        position: absolute;
        left: 50%;
        top: -23px;
        width: 46px;
        height: 46px;
        margin-left: -23px;
        /* margin-bottom: 6px; */
        line-height: 46px;
        font-size: 12px;
        color: #ff9000;
        /* *display: inline;
        *zoom: 1; */
    }
    .ac-step .ac-mod-cot-bd .ac-step1 b {
        background-position: 0 -40px;
    }
    .ac-step .ac-mod-cot-bd .ac-step2 b {
        background-position: -46px -40px;
    }
    .ac-step .ac-mod-cot-bd .ac-step3 b {
        background-position: 0 -90px;
    }
    .ac-step .ac-mod-cot-bd li p {
        height: 48px;
        margin-bottom: 8px;
        overflow: hidden;
    }
    .ac-step li p span, .ac-step li p i {
        display: inline-block;
        vertical-align: middle;
        *display: inline;
        *zoom: 1;
    }
    .ac-step li p i {
        height: 100%;
    }
    .ac-step .ac-mod-cot-bd .btn {
        display: inline-block;
        width: 130px;
        height: 34px;
        line-height: 34px;
        background: #ff9000;
        text-align: center;
        font-size: 16px;
        color: #fff;
        *display: inline;
        *zoom: 1;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        -o-border-radius: 4px;
        -ms-border-radius: 4px;
        border-radius: 4px;
    }
    .ac-step .ac-mod-cot-bd .btn:hover {
        text-decoration: none;
        cursor: pointer;
    }
    .ac-step .ac-mod-cot-bd .btn-link {
        background: #a7a7a7;
        color: #d7d7d7;
        cursor: default;
    }
    .ac-step .ac-mod-cot-bd .btn-hover:hover {
        background: #ff9d1d;
    }
    .ac-step .ac-mod-cot-bd .btn-hover:active {
        background: #ff8211;
    }
    .ac-step .ac-mod-cot-bd .btn-active {
        background: #1567aa;
        color: #2a84ce;
        cursor: default;
    }
    .ac-step .ac-mod-cot-bd .btn-active i {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 8px;
        vertical-align: -4px;
        line-height: 20px;
        background-position: -60px -90px;
        *display: inline;
        *zoom: 1;
        _position: relative;
        _top: 6px;
        _vertical-align: 6px;
    }
    .ac-step .ac-arrow {
        position: absolute;
        right: -50px;
        top: 50%;
        z-index: -1;
        width: 46px;
        height: 30px;
        margin-top: -20px;
    }
    .ac-step .ac-step1 .ac-arrow {
        background-position: 0 0;
    }
    .ac-step .ac-step2 .ac-arrow {
        background-position: -46px 0;
    }
    .ac-rule .ac-mod-cot-bd {
        padding: 55px 20px 10px 86px;
        line-height: 1.8;
    }
    .ac-rule em {
        color: #fff43c;
    }
    .ac-rule a {
        padding: 0 3px;
        text-decoration: underline;
    }
    .pub-pop {
        font: 12px/1.5 Arial, SimSun,sans-serif;
    }

    .bg1 {
    width: 1920px;
    height: 470px;
    background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/bg-bottom.png'); ?>) no-repeat;
    }
    .bg2 {
        width: 1920px;
    height: 470px;
    background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/bg-top.png'); ?>) no-repeat;
    }
    .bg3 {
        width: 800;
    height: 340;
    background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/slogan.png'); ?>) no-repeat;
    }
    .ac-slogan {
        width: 800px;
        height: 340px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/slogan.png'); ?>) no-repeat;
    }
    .ac-hongbao .plaxify {
        width: 1000px;
        height: 280px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/hongbao.png'); ?>) no-repeat;
    }

    .ft-bg3 {
        width: 1920px;
        height: 82px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ft-bg3.png'); ?>) no-repeat;
    }
    .ft-bg2 {
        width: 1920px;
        height: 82px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ft-bg2.png'); ?>) no-repeat;
    }
    .ft-bg1 {
        width: 1920px;
        height: 82px;
        background: url(<?php echo getStaticFile('/caipiaoimg/v1.0/img/active/ft-bg1.png'); ?>) no-repeat;
    }

    .simu-select .select-opt .select-opt-in, .simu-select-med .select-opt .select-opt-in {
        border-top: 1px solid #c6ced6;
    }

    .simu-select-med .select-opt {
        top: 31px;
    }
    .simu-select .select-opt .select-opt-in, .simu-select-med .select-opt .select-opt-in {
        max-height: 130px;
        _height: expression(this.scrollHeight > 130 ? "130px" : "auto");
    }
</style>
</head>
<body>
<!--top begin-->
    <?php if (empty($this->uid)): ?>
        <div class="top_bar" style="display:block;">
          <div class="wrap_in">
            <p class="main_menu"><span class="stock">A股上市公司旗下网站，股票代码：002195</span></p>
            <p class="user_info"><span class="unlogin">欢迎来到2345彩票！&nbsp;&nbsp;<a class="login not-login" target="_self" href="javascript:void(0);">登录</a><i>|</i><a class="register not-register" target="_self" href="javascript:void(0);">注册</a></span></p>
          </div>
        </div>
    <?php else: ?>
        <div class="top_bar" style="display:block;">
            <div class="wrap_in">
                <p class="main_menu"><span class="stock">A股上市公司旗下网站，股票代码：002195</span></p>
                  <ul class="user_info">
                      <li>欢迎您，</li><li class="myaccount">
                          <a target="_blank" href="/mylottery/account" class="lotery_btn spec"><?php echo $this->uname;?><i class="my_lottery"></i></a><div class="drop_down">
                              <div class="row1">
                                  <a target="_blank" href="/wallet/withdraw">提款</a>
                                  <a target="_blank" href="/wallet/recharge" class="forg">充值</a>
                                  余额：<span><?php echo number_format(ParseUnit($this->uinfo['money'], 1), 2);?></span>&nbsp;元
                              </div>
                              <div class="row2">
                                  <a target="_blank" href="/mylottery/account">我的账户</a><i>|</i><a target="_blank" href="/mylottery/betlog">投注记录</a><i>|</i><a target="_blank" href="/mylottery/detail">交易明细</a>
                              </div>
                              <div class="row3">
                                  <ul class="save-info clearfix">
                                      <?php if($this->uinfo['phone']):?>
                                        <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-phone"></i>已绑定</a></li>
                                      <?php else:?>
                                        <li><i class="icon icon-phone"></i><a href="/safe/phone" target="_blank" >未绑定</a></li>
                                      <?php endif;?>
                                      <?php if($this->uinfo['id_card']):?>
                                        <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-idCard"></i>已绑定</a></li>
                                      <?php else:?>
                                        <li><i class="icon icon-idCard"></i><a href="/safe/idcard" target="_blank">未绑定</a></li>
                                      <?php endif;?>
                                      <?php if($this->uinfo['bank_id']):?>
                                        <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-bankCard"></i>已绑定</a></li>
                                      <?php else:?>
                                        <li><i class="icon icon-bankCard"></i><a href="/safe/bankcard" target="_blank">未绑定</a></li>
                                      <?php endif;?>
                                  </ul>
                              </div>
                          </div>
                      </li>
                      <li class="recharge-li"><a class="btn btn-orange-small" href="/wallet/recharge">充值</a></li>
                      <li><a href="/mynews?cpage=1" class="c666">我的消息 <b class="spec">
                        <?php $CI = &get_instance(); $CI->load->model('news_model'); $count = $CI->news_model->countUnreadList($this->uid); echo intval($count[0])?intval($count[0]):''?></b></a></li><li class="pipe">|</li>
                      <li><a href="/main/loginout" target="_self">退出登录</a></li>
                  </ul>
              </div>
        </div>
    <?php endif; ?>
    <!--top end-->
<div class="ac-wrap">
    
    <div class="ac-header">
        <div class="bg1 plaxify png_bg"></div>
        <div class="bg2 plaxify png_bg"></div>
        <h1><div class="ac-slogan plaxify png_bg"></div></h1>
        <div class="ac-hongbao"><div class="plaxify png_bg" data-xrange="30" data-yrange="0"></div></div>
    </div>
    <div class="ac-main">
        <div class="ac-mod-cot ac-step png_bg">
            <h2 class="ac-mod-cot-hd">活动攻略</h2>
            <div class="ac-mod-cot-bd">
            <?php
                $isReg = !empty( $this->uid);
                $isBind = $is_bank_bind && $is_phone_bind && $is_id_bind; // TODO
                // $isTakeMoney = true; // 领取否?
            ?>
                <ol>
                    <li class="ac-step1">
                        <b class="png_bg"></b>
                        <p><span>立即注册加入2345彩票</span><i></i></p>
                        <?php if( !$isReg ): ?>
                            <a href="javascript:void(0);" class="btn btn-hover not-register">立即注册</a>
                        <?php else: ?>
                            <a href="javascript:;" class="btn btn-active"><i class="png_bg"></i>已完成</a>
                        <?php endif; ?>
                        <div class="ac-arrow png_bg"></div>
                    </li>

                    <li class="ac-step2">
                        <b class="png_bg"></b>
                        <p><span>填写手机号、身份证、银行卡绑定信息</span><i></i></p>
                        <?php if( !$isReg ): ?>
                            <a href="javascript:;" class="btn btn-link"><i class="png_bg"></i>完善资料</a>
                        <?php else: ?>
                            <?php if(  !$isBind ): ?>
                            <a href="javascript:void(0);" class="btn btn-hover not-bind">完善资料</a>
                            <?php else: ?>
                            <a href="javascript:;" class="btn btn-active"><i class="png_bg"></i>已完成</a>
                            <?php endif; ?>
                        <?php endif;?>
                        <div class="ac-arrow png_bg"></div>
                    </li>

                    <li class="ac-step3">
                        <b class="png_bg"></b>
                        <p><span>完成绑定，领取彩金</span><i></i></p>
                        <?php if( !$isReg ): ?>
                            <a href="javascript:;" class="btn btn-link"><i class="png_bg"></i>领取3元彩金</a>
                        <?php else: ?>
                            <?php if(  !$isBind ): ?>
                            <a href="javascript:;" class="btn btn-link"><i class="png_bg"></i>领取3元彩金</a>
                            <?php else: ?>
                                <?php if(  $isTakeMoney ): ?>
                                <a href="javascript:;" class="btn btn-active"><i class="png_bg"></i>已完成</a>
                                <?php else: ?>
                                <a href="javascript:void(0);" class="btn btn-hover caijin" data-acid="<?php echo $activityId; ?>">领取3元彩金</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif;?>
                    </li>
                </ol>  
            </div>
        </div>
        <div class="ac-mod-cot ac-rule png_bg">
            <h2 class="ac-mod-cot-hd">活动规则</h2>
            <div class="ac-mod-cot-bd">
                <ol>
                    <li>1、2015年1月26日起在本活动完成注册的用户，活动期间内完成手机绑定、实名认证、绑定银行卡，即可领取3元彩金。（1月26日前完成注册及绑定的用户，可直接参与活动点击“领取3元彩金”。）</li>
                    <li>2、参与活动的用户绑定的手机号和实名认证信息须没有在本网站使用过，<em>同一身份信息及手机号只允许参加一次。</em></li>
                    <li>3、彩金只能用于购彩，不能提现。</li>
                    <li>4、本活动最终解释权归2345彩票；如有疑问请联系<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes" target="_blank">在线客服</a>或400-000-2345 转8 彩票业务。</li>
                </ol>  
            </div>
        </div>
    </div>
    <div class="ac-fixed-aside"></div>  
</div>
<div class="ac-aside">
    <div class="ft-bg3 plaxify png_bg" data-xrange="30" data-yrange="8"></div>
    <div class="ft-bg2 plaxify png_bg" data-xrange="20" data-yrange="6"></div>
    <div class="ft-bg1 plaxify png_bg" data-xrange="8" data-yrange="2"></div>
</div>


<div class="pop-mask hidden"></div>
<iframe src="about:blank" class="popIframe hidden"></iframe>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/comm.js');?>'></script>
<?php $this->load->view('elements/common/encrypt');?>
<div id="pop_register">
<?php if( empty( $this->uid ) ): ?>

    <!-- 登录 begin -->
    <div class="pub-pop loginPopWrap">
        <div class="pop-in">
            <div class="pop-head">
                <h2>登录</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body pop-login-form">
                <div class="mod_user">			
                    <form class="form form_login">
                        <div class="form-item">
                            <input class='vcontent' type='hidden' name='actions' value='1' />
                            <label class="form-item-label">2345账号</label>
                            <div class="form-item-con">
                                <input tabindex="1" type="text" class='form-item-ipt vcontent' autocomplete="off" name="username" value="" />
                                <a href="/safe/find_account" target="_blank" class="lnk-txt">忘记帐号？</a>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label">密码</label>
                            <div class="form-item-con">
                                <input  tabindex="2" type="password" class='form-item-ipt vcontent' autocomplete="off"  name="pword" data-encrypt='1' value="" />	
                                <a href="/safe/find_password" target="_blank" class="lnk-txt">忘记密码？</a>				
                            </div>
                        </div>
                        <div class="form-item yz_area form-vcode" style="display:none;" id="captcha_area">
                            <label class="form-item-label">验证码</label>
                            <div class="form-item-con">
                                <input tabindex="3" class='form-item-ipt vcontent inp_s' type="text" name="captcha" value="" /><img id='captcha' src="/mainajax/captcha" alt="" />
                                <a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha">换一张</a>
                            </div>
                        </div>
                        <li class="message" style="display:none;color:red"></li>
                        <div class="form-item btn-group">
                            <div class="form-item-con">
                                <a class="btn-confirm submit" href="javascript:;" target="_self">立即登录</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="pop-other-login">
                <div class="other-login">
                    <a class="btn btn-qq" target="_self" href="http://login.2345.com/qq?forward=<?php echo $this->baseUrl; ?>activity/caijin/<?php echo $activityId; ?>"><i class="icon icon-qq"></i>QQ账号登录</a>
                    <a class="btn btn-weibo" target="_self" href="http://login.2345.com/weibo?forward=<?php echo $this->baseUrl; ?>activity/caijin/<?php echo $activityId; ?>"><i class="icon icon-weibo"></i>微博登录</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 登录 end -->
    <script>

    function showCaptche(flag)
    {
        if(flag)
        {
            $('#captcha_area').show();
        }
        else{
            $('#captcha_area').hide();
        }
    }

    $(function() {

        $('.not-login').click(function(e) {
            var $this = $(this);
            if ($this.hasClass('not-login')) {
                cx.PopLogin.show();
                e.stopImmediatePropagation();
            }
            e.preventDefault();
        });	

        new cx.vform('.pop-login-form', {
            renderTip: 'renderTips',
            submit: function(data) {
                var self = this;
                $.ajax({
                    type: 'post',
                    url:  '/main/login',
                    data: data,
                    success: function(response) {
                        showCaptche($.cookie('needCaptcha'));
                        recaptcha();
                        if(response.werror == 1){
                            console.log(response);
                        }
                        else if(response.captcha == 1){
                            self.renderTip('验证码错误', $('.message'));
                            $('input[name="captcha"]').focus();
                        }
                        else if(response.code){
                            if(response.code == 1){
                                cx.PopLogin.hide();

                                location.href = location.href;

                            } else if( response.code == 3 ){
                                $('#captcha_area').show();
                            } else if( response.code == -1 ){
                                self.renderTip('2345账号或密码输入错误', $('.message'));
                                $('input[name="username"]').focus();
                            } else if( response.code == 4 ){
                                alert('非法ip');
                            } else if( response.code == 5 ){
                                alert('非法域名');
                            } else if( response.code == 6 ){
                                alert('登录太频繁，IP被限制');
                            }
                        }
                    }
                });
            }
        });

        $('#change_captcha').on('click', function(){
            recaptcha();
            return false;
        });
        showCaptche($.cookie('needCaptcha'));
    });

    </script>

    <!-- 注册 begin -->
    <div class="pub-pop registerPopWrap">
        <div class="pop-in">
            <div class="pop-head">
                <h2>注册</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body pop-register-form">
                <div class="mod_user">			
                    <form class="form form_login form_register">
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>2345账号</label>
                            <div class="form-item-con">
                                <input type="text" class='form-item-ipt vcontent' autocomplete="off" data-rule='username' data-ajaxcheck='1' name="username" value="" />
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con username tip"></span>
                                    <s></s>
                                </div>
                            </div>
                            <input class='vcontent' type='hidden' name='actions' value='1' />
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>创建密码</label>
                            <div class="form-item-con">
                                <input class='form-item-ipt vcontent' type="password" name="pword" data-rule="password" data-encrypt='1' value="" />
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con pword tip"></span>
                                    <s></s>
                                </div>							
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>确认密码</label>
                            <div class="form-item-con">
                                <input class='form-item-ipt vcontent' type="password" name="con_pword" data-rule="same" data-encrypt='1' data-with="pword" value="" />	
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con con_pword tip"></span>
                                    <s></s>
                                </div>					
                            </div>
                        </div>
                        <div class="form-item yz_area form-vcode">
                            <label class="form-item-label"><b>*</b>验证码</label>
                            <div class="form-item-con">
                                <input class='form-item-ipt inp_s vcontent' type="text" name="captcha" data-rule='checkcode' value="" />
                                <img id='captcha_reg' src="/mainajax/captcha" alt="" />
                                <a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha_reg">换一张</a>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con captcha tip"></span>
                                    <s></s>
                                </div>  
                            </div>
                        </div>
                        <div class="form-item btn-group">
                            <div class="form-item-con">
                                <a class="btn-confirm submit" target="_self" href="javascript:;">立即注册</a>
                            </div>
                        </div>
                        <div class="form-item form-agree">
                            <div class="form-item-con">
                                <input class="ipt_checkbox vcontent" type="checkbox" id="a" name='agreement' value='1' checked="checked"/><label for="a">我同意</label>
                                <a href="http://login.2345.com/licence.html" target='_blank'>《服务协议》</a>
                                <a href="http://login.2345.com/declare.html" target='_blank'>《隐私声明》</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="pop-other-login">
                <div class="other-login">
                    <a class="btn btn-qq" target="_self" href="http://login.2345.com/qq?forward=<?php echo $this->baseUrl; ?>activity/caijin/<?php echo $activityId; ?>"><i class="icon icon-qq"></i>QQ账号登录</a>
                    <a class="btn btn-weibo" target="_self" href="http://login.2345.com/weibo?forward=<?php echo $this->baseUrl;?>activity/caijin/<?php echo $activityId; ?>"><i class="icon icon-weibo"></i>微博登录</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 注册 end -->
    <script type="text/javascript">
    $(function() {

        $('.not-register').click(function() {
            var $this = $(this);
            if ($this.hasClass('not-register')) {
                cx.PopRegister.show();
            }
        });	

        new cx.vform('.form_register', {
            renderTip: 'renderTips',
            submit: function(data) {
                if(data.agreement != '1'){
                    cx.Alert({
                            content: '请同意2345服务协议'
                    });
                    return false;
                }
                var self = this;
                data.activity_id = '<?php echo $activityId; ?>';
                $.ajax({
                    type: 'post',
                    url:  '/main/register',
                    data: data,
                    success: function(response) {
                        recaptcha_reg();
                        if(response.code == '200.0'){
                            $('.not-login').removeClass('not-login');
                            
                            cx.PopRegister.hide();

                            $.get('/main/getTopBar', function(data){
                                $('.top_bar').html(data);
                            });

                            cx.Alert({
                                content: '您已登录，请点击完善资料参与活动。',
								confirmCb: function() {
									location.href = location.href;
								}
                            });
                            
                        }
                        else if(response.werror == 1){
                            console.log(response);
                        }
                        else if(response.captcha == 1){
                            self.renderTip('您输入的验证码有误，请重新输入', $('.captcha'));
                            $('input[name="captcha"]').focus();
                        }
                        else if(response.code == '300.6'){
                            self.renderTip('此帐号已被注册', $('.username'));
                        }
                        else if(response.code == '300.7'){
                            self.renderTip('此邮箱已被注册，请换一个', $('.email'));
                        }
                        else{
                            console.log(response);
                        }
                        $('input[name="captcha"]').val('');
                    }
                });
            }
        });

        $("#change_captcha_reg").on('click', function(){
            recaptcha_reg();
            return false;
        });
    })
    </script>
<?php endif; ?>
</div>

<div>
<?php if( !$isBind ): ?>
    <div class="pub-pop safe-center bind-form" >
        <div class="pop-in">
            <div class="pop-head">
                <h2>完善信息</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="mod_user">	
                    <form class="form uc-form-list">
                        <?php if( !$is_id_bind ): ?>
                        <fieldset >
                            <h3><span class="form-tip"><i class="icon-tip"></i>真实身份信息是您领奖提款的依据，请如实填写</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>真实姓名</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="real_name" data-rule="chinese">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con real_name tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>身份证号</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-ajaxcheck='1' data-rule="identification">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        </fieldset>
                        <?php endif; ?>

                        <?php if( !$is_phone_bind ): ?>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>手机号码</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" data-rule='phonenum' data-ajaxcheck='1' data-freeze='_timer' name="phone">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con phone tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-vcode">
                            <label class="form-item-label"><b>*</b>验证码</label>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkcode' value="" name="newphoneyzm">
                                <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' target="_self" href="javascript:;">获取语音验证码</a>
                                <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con newphoneyzm tip"></span>
                                    <s></s>
                                </div>
                                <div style="left: 0; top: 46px;" class="ui-poptip ui-poptip-yuyin">
                                  <div class="ui-poptip-container">
                                    <div class="ui-poptip-arrow-top"><i>◆</i><span>◆</span></div>
                                    系统将拨打您的手机语音播报验证码，请注意接听。<a style="color:blue;" target="_blank" href="/help/index/b0-f4">未收到验证码？</a>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if( !$is_bank_bind ): ?>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>开户银行</label>
                            <div class="form-item-con">
                                <dl class="simu-select-med mr15">
                                    <dt><span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='bank_type' value=''></dt>
                                    <dd class="select-opt">
                                        <div class="select-opt-in" data-name='bank_name'>
                                            <?php foreach($bankTypeList as $key => $val): ?>
                                            <a href="javascript:;" target="_self" data-value='<?php echo $key; ?>'><?php echo $val;?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </dd>
                                </dl>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con bank_name tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>银行卡号</label>
                            <div class="form-item-con">
                                <input type="text" class="ipt_text vcontent" value="" data-rule="bankcard" name="bank_id" >
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con bank_id tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-add">
                            <label class="form-item-label"><b>*</b>开户地区</label>
                            <div class="form-item-con">
                                <input type='hidden' class='vcontent' name='action' value='_1'>
                                <dl class="simu-select-med" data-target='city_list'>
                                    <dt><span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='province' value=''></dt>
                                    <dd class="select-opt">
                                        <div class="select-opt-in" data-name='province'>
                                            <?php foreach($provinceList as $row): ?>
                                            <a href="javascript:;" target="_self" data-value='<?php echo $row['province']?>'><?php echo $row['province']?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="simu-select-med city_list" style="width:154px">
                                    <dt><span class='_scontent' id='city' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='city' value=''></dt>
                                    <dd class="select-opt">
                                        <div class="select-opt-in" id='city-container' data-name='city'>
                                        </div>
                                    </dd>
                                </dl>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con bank_area tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-item btn-group">
                            <div class="form-item-con">
                                <a class="btn btn-confirm submit" target="_self" href="javascript:;">提交</a>
                                <a class="btn btn-default cancel" target="_self" href="javascript:;">取消</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){

            $('#btn-getYzm').click(function(){
                var phone = $('#phone').html() || $('input[name="phone"]').val();
                
                if( !phone.match(/^\d{11}$/) ){
                    cx.Alert({
                        content: '请填写正确的手机号码'
                    });
                    return false;
                }

                if( !$(this).hasClass('disabled') )
                {
                    timer();
                    $.ajax({
                       type: 'post',
                       url:  '/safe/getPhoneCode/newphoneyzm',
                       data: {'phone':phone},
                       success: function(response) {
                            if(!response) {
                                cx.Alert({
                                    content: '验证码发送失败，请联系我们的客服！'
                                });
                            }
                       }
                    });
                }
            });

            new cx.vform('.bind-form', {
                renderTip: 'renderTips',
                submit: function (data) {
                    var self = this;

                    var data = data || {};
                    $.ajax({
                        type: 'post',
                        url: '/safe/one',
                        data: data,
                        success: function (response) {
                                if( response == 2 ){
                                    self.renderTip('请选择开户地区', $('.bank_area'));
                                }else if (response == 3 ) {
                                    self.renderTip('请选择开户银行', $('.bank_name'));
                                }else if (response == 4 ) {
                                    self.renderTip('请输入正确的银行卡号', $('.bank_id'));

                                } else if (response == 5 ) {
                                    self.renderTip('身份证已绑定', $('.id_card'));
                                } else if (response == 6 ) {
                                    self.renderTip('身份证格式错误', $('.id_card'));
                                } else if (response == 7 ) {
                                    self.renderTip('请输入真实姓名', $('.real_name'));

                                } else if (response == 8 ) {
                                    self.renderTip('验证码错误', $('.newphoneyzm'));
                                } else if (response == 9 ) {
                                    self.renderTip('手机号码为空', $('.phone'));
                                } else if (response == 10 ) {
                                    self.renderTip('手机号码已绑定', $('.newphoneyzm'));
                                } else if (response == 11 ) {
                                    self.renderTip('支付密码为空或两次输入不一致', $('.pay_pwd'));

                                }else {
                                    cx.Alert({
                                        content: '您已完成活动要求，请点击领取彩金。',
                                        confirmCb: function() {
                                            location.href = location.href;
                                        }
                                    });
                                }
                        }
                    });
                }
            });
        });
    </script>
<?php endif; ?>
</div>

<?php if( $isReg && $isBind ):?>
    <script>
    $(function(){
        $('.caijin').on('click', function(e){
            $.ajax({
                type: 'post',
                url: '/activity/dispatchMoney',
                data: {'acid' : $('.caijin').data('acid') },
                success: function (response) {
                        var content, 
                            confirm,
                            cbfunction = function() {
                                location.href = location.href;
                            };
                        if( response == 2 ){
                            content = '请先注册账户, 并完善用户信息';
                        } else if( response == 3 ) {
                            content = '您已参加过此活动，感谢关注！';
                        } else if( response == 1 ) {
                            content = '您已成功领取彩金';
                            cbfunction = function() {
                                location.href = '/hall';
                            };
                            confirm = '前往购彩';
                        } else {
                            content = '彩金领取错误';
                        }
                        cx.Alert({
                            content: content,
                            confirmCb: cbfunction,
                            confirm : confirm
                        });
                }
            });
        });
    });
    </script>
<?php endif;?>

<!--[if IE 6]>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js'); ?>"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<script>
$(function(){
    // 插件调用
    var layers = $('.plaxify');
    $.each(layers, function(index, layer){
        $(layer).plaxify({
          xRange: $(layer).data('xrange') || 0,
          yRange: $(layer).data('yrange') || 0,
          invert: $(layer).data('invert') || false
        });
    });
    $.plax.enable();

    //pop里面的select下拉弹窗定位
    $('.simu-select-med', '.pub-pop').on('click', function(){
        var selectTop = $('.pub-pop').height() - ($(this).offset().top - $('.pub-pop').offset().top);
        var selectOpt =  $(this).find('.select-opt');
        console.log(selectOpt.height())
        if(selectTop < 180){
            selectOpt.css({'top': 'auto', 'bottom': '31px', 'z-index': 10});
        }
    });
})
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/analyticstracking.js');?>"></script>
<script type="text/javascript" src="http://union2.50bang.org/js/caipiao2345"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/50bang.js');?>"></script>
</body>
</html>