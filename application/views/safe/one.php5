<?php $this->load->view('elements/user/menu');?>
<?php
    // 银行编号 映射 图标编号
    $bankIconMap = array(
        '1025'    => "1",
        '103'     => "2" ,
        '306'     => "4" ,
        '105'     => "5" ,
        '312'     => "6" ,
        '104'     => "7" ,
        '326'     => "8" ,
        '311'     => "9" ,
        '3080'    => "10",
        '301'     => "11",
        '314'     => "12",
        '309'     => "13",
        '3230'    => "14",
        '305'     => "15",
        '313'     => "16",
        '307'     => "17",
        '316'     => "18",
    );
?>
        <div class="article safe-center edit-form">
			<h2 class="tit">填写账户安全信息</h2>
			<div class="safeProfile-form"> 
				<form class="form uc-form-list">
                    <?php if( !$is_phone_bind ): ?>
                    <fieldset>
                        <h3><b>绑定手机号码</b><span class="form-tip">手机号码是您中奖的唯一联系方式，验证后还可用于自助找回登录密码。</span></h3>
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
                                <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkmsgcode' value="" name="newphoneyzm">
                                <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' target="_self" href="javascript:;">获取短信验证码</a>
                                <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con newphoneyzm tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php else: ?>
                    <fieldset>
                        <h3><b>绑定手机号码</b><span class="form-tip">手机号码是您中奖的唯一联系方式，验证后还可用于自助找回登录密码。</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>手机号码</label>
                            <div class="form-item-con" id="phone"><span class="form-item-txt"><?php echo $this->uinfo['phone'];?></span></div>
                        </div>
                        <!-- 图片验证码 -->
                        <div class="form-item form-vcode vcode-img">
                            <label for="" class="form-item-label"><b>*</b>图形验证码</label>
                            <div class="form-item-con">
                                <input class='form-item-ipt inp_s vcontent' type="text" name="imgCaptcha" data-rule='checkcode' value="" /><img id='imgCaptcha' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
                                <a class="lnk-txt" href="javascript:;" target="_self" id="change_imgCaptcha">换一张</a>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con tip imgCaptcha"></span>
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
                                    系统将拨打您的手机语音播报验证码，请注意接听。<a target="_blank" href="/help/index/b0-f4">未收到验证码？</a>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif;?>
                    <!-- 手机号 end -->
                    <?php if( !$is_pay_pwd ): ?>
                    <fieldset>
                        <h3><b>设置支付密码</b><span class="form-tip">支付密码用于您购买彩票付款时使用，不同于登录密码。</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>支付密码</label>
                            <div class="form-item-con">
                                <input type="password" class="form-item-ipt vcontent" value="" data-rule='password' data-ajaxcheck='1' name="pay_pwd">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con pay_pwd tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>重复一次</label>
                            <div class="form-item-con">
                                <input type="password" class="form-item-ipt vcontent" data-rule='same' data-with='pay_pwd' value="" name="conpay_pwd">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con conpay_pwd tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>
                    <!-- 支付密码 end -->
                    <fieldset>
                        <h3><b>绑定真实身份</b><span class="form-tip">实名信息是领奖、提款时核对提款人身份的重要信息</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>真实姓名</label>
                            <?php if( !$is_id_bind ):?>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="real_name" data-rule="chinese">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con real_name tip"></span>
                                    <s></s>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="form-item-con">
                                <strong class="form-item-txt"><?php echo cutstr($this->uinfo['real_name'], 0, 1);?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>身份证号</label>
                            <?php if( !$is_id_bind ):?>
                            <div class="form-item-con">
                                <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-ajaxcheck='1' data-rule="identification">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="form-item-con">
                                <strong class="form-item-txt"><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </fieldset>  
                <?php
                    if($this->bankInfo)
                    {
                        $bankInfo = $this->bankInfo;
                    }
                ?>
                <fieldset>
                    <h3><b>绑定银行卡号</b><span class="form-tip">绑定银行卡是您提款时的唯一用卡，是资金提取的安全保证。</span></h3>
                    <div class="form-item">
                        <label class="form-item-label">开户银行</label>
                        <?php if( !$is_bank_bind ): ?>
                        <div class="form-item-con">
                            <dl class="simu-select-med bank-select">
                                <dt><span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='bank_type' value=''></dt>
                                <dd class="select-opt bank-select-opt">
                                    <div class="bank-select-sp" data-name='bank_name'>
                                        <div class="bank-group-item">
                                            <h3>1小时内到账:</h3>
                                            <ul class="clearfix">
                                                <li><a href="javascript:;" target="_self" data-value='1025'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon1.png');?>" alt="">中国工商银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='103'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon2.png');?>" alt="">中国农业银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='306'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon4.png');?>" alt="">广发银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='105'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon5.png');?>" alt="">中国建设银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='312'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon6.png');?>" alt="">中国光大银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='104'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon7.png');?>" alt="">中国银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='326'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon8.png');?>" alt="">上海银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='311'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon9.png');?>" alt="">华夏银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='3080'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon10.png');?>" alt="">招商银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='301'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon11.png');?>" alt="">交通银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='314'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon12.png');?>" alt="">上海浦东发展银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='309'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon13.png');?>" alt="">兴业银行</a></li>
                                            </ul>
                                        </div>
                                        <div class="bank-group-item">
                                            <h3>24小时内到账:</h3>
                                            <ul class="clearfix">
                                                <li><a href="javascript:;" target="_self" data-value='3230'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon14.png');?>" alt="">中国邮政储蓄银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='305'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon15.png');?>" alt="">中国民生银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='313'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon16.png');?>" alt="">中信银行</a></li>
                                                <li><a href="javascript:;" target="_self" data-value='307'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon17.png');?>" alt="">平安银行</a></li>
                                            </ul>
                                        </div>
                                        <div class="bank-group-item last">
                                            <h3>2个工作日内到账:</h3>
                                            <ul class="clearfix">
                                                <li><a href="javascript:;" target="_self" data-value='316'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon18.png');?>" alt="">南京银行</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con bank_name tip"></span>
                                <s></s>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="form-item-con">
                                <span class="form-item-txt"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon'.$bankIconMap[$bankInfo[0]['bank_type']].'.png');?>" alt=""><?php echo $bankTypeList[$bankInfo[0]['bank_type']]; ?></span>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">银行卡号</label>
                        <?php if( !$is_bank_bind ): ?>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" data-rule="bankcard" name="bank_id" >
                                <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con bank_id tip"></span>
                                <s></s>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="form-item-con">
                            <strong class="form-item-txt"><?php echo cutstr($bankInfo[0]['bank_id'], 0, 12);?></strong>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-item form-add">
                        <label class="form-item-label">开户地区</label>
                        <?php if( !$is_bank_bind ): ?>
                        <div class="form-item-con">
                            <input type='hidden' class="vcontent" data-target='/safe/one' data-default='' name='action' value=''>
                            <dl class="simu-select-med" data-target='city_list'>
                                <dt>
                                    <span class='_scontent' id='province' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='province' value=''>
                                </dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in" data-name='province'>
                                        <?php foreach ($provinceList as $row): ?>
                                            <a href="javascript:;" data-value='<?php echo $row['province'] ?>'><?php echo $row['province'] ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="simu-select-med city_list">
                                <dt>
                                    <span class="_scontent" id='city' data-value=''>请选择</span><i class="arrow"></i><input type="hidden" class="vcontent" name='city' value=''>
                                </dt>
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
                        <?php else:?>
                        <input type='hidden' class="vcontent" data-target='/safe/one' data-default='' name='action' value=''>
                        <div class="form-item-con">
                            <span class="form-item-txt"><?php echo $bankInfo[0]['bank_province']; ?></span>&nbsp;&nbsp;<span class="form-item-txt"><?php echo $bankInfo[0]['bank_city']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </fieldset>

				<div class="form-item btn-group">
                    <div class="form-item-con">
                        <a class="btn btn-confirm submit" href="javascript:;">提交</a>
                        <a class="btn btn-default" target="_self" href="/safe/">取消</a>
                    </div>
                </div>
            </form>
			</div>
		</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<?php if( !$is_phone_bind ): ?>
        <script type="text/javascript">
            $(function () {
                $('#btn-getYzm').click(function(){
                    var phone = $('#phone').text() || $('input[name="phone"]').val();
                    if( !phone.match(/^\d{11}$/) ){
                        cx.Alert({
                            content: '请填写正确的手机号码'
                        });
                        return false;
                    }

                    if( !$(this).hasClass('disabled') )
                    {                        
                        $.ajax({
                           type: 'post',
                           url:  'http://login.2345.com/webapi/phone/sendCode',
                           data: {'phone':phone,'mid':'CP','action':'bind'},
                           dataType: 'jsonp',
                           success: function(response) 
                           {
                        	   if(response.code == '200.0')
                               {
                        		   timer();
                                   //cx.Alert({content:'验证码已发送你的手机！'});
                               }
                               else
                               {
                                    cx.Alert({content:response.msg});
                               }
                           }
                        });
                    }
                });

                new cx.vform('.edit-form', {
                    renderTip: 'renderTips',
                    submit: function (data) {
                        var self = this;

                        var data = data || {};
                        $.ajax({
                            type: 'post',
                            url: '/safe/one',
                            data: data,
                            success: function (response) 
                            {
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
                                    }else if (response == 12 ) {
                                        self.renderTip('手机号码绑定失败', $('.phone'));
                                    }else {
                                        location = location.href;
                                    }
                            }
                        });
                    }
                });
            });
        </script>
<?php else:?>
    <script type="text/javascript">
            $(function () {
                $("#change_imgCaptcha").on('click', function(){
                    $('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
                    return false;
                });
                $('#btn-getYzm').click(function(){
                    var phone = $('#phone').text() || $('input[name="phone"]').val();
                    if( !phone.match(/^\d{11}$/) ){
                        cx.Alert({
                            content: '请填写正确的手机号码'
                        });
                        return false;
                    }

                    if( !$(this).hasClass('disabled') )
                    {
                        var code = $('input[name="imgCaptcha"]').val() || false;
                        if(!code){
                            $('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                            $('.imgCaptcha').show().html('请输入图形验证码');
                            return false;
                        }
                        
                        $.ajax({
                           type: 'post',
                           url:  '/safe/getPhoneCode/newphoneyzm',
                           data: {'phone':phone,'code':code},
                           dataType: 'json',
                           success: function(response) {
                               if(response.status)
                               {
                                   timer();
                                   //cx.Alert({content:'验证码已发送你的手机！'});
                               }
                               else
                               {
                                    if(response.msg){
                                        $('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                                        $('input[name="imgCaptcha"]').val('');
                                        $('.imgCaptcha').show().html(response.msg);
                                        $('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
                                    }else{
                                        $('.imgCaptcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                                        cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                                    }
                               }
                           }
                        });
                    }
                });

                new cx.vform('.edit-form', {
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
                                        location = location.href;
                                    }
                            }
                        });
                    }
                });
            });
        </script>
<?php endif;?>
<?php $this->load->view('elements/user/menu_tail');?>