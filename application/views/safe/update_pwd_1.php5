<?php if($loginType == 1): $this->load->view('elements/user/menu'); endif; ?>
<?php if($loginType == 1): ?>
<div class="article">    
    <div class="tab-nav">
        <ul class="clearfix">
            <li><a href="/safe/paypwd/"><span><?php if (empty($this->uinfo['pay_pwd'])): ?>修改支付密码<?php else: ?>设置支付密码<?php endif; ?></span></a>
            </li>
            <li class="active"><a href="javascript:;"><span>修改登录密码</span></a></li>
        </ul>
    </div>
    <div class="tab-content">
<?php endif; ?>
        <div class="tab-item pt20" style="display: block;">
            <ul class="steps-bar clearfix">
                <li><i>1</i><span class="des">验证身份</span></li>
                <li class="cur"><i>2</i><span class="des">设置密码</span></li>
                <li class="last"><i>3</i><span class="des">操作成功</span></li>
            </ul>
            <div class="safe-item-box">
                <form class="form uc-form-list pl154">
                    <div class="form-item">
                        <label class="form-item-label">新密码</label>
                        <div class="form-item-con">
                            <input type="password" class="form-item-ipt vcontent" value="" name="pword" data-rule="password" data-encrypt='1'>
                            <div class="form-tip hide form-tip-true">
                                <i class="icon-tip"></i>
                                <div class="form-tip-con pword tip" style="display:none;">
                                    <div>密码状态</div>
                                    <div class="pwd_streng pwd_streng_1"><i class="on"></i><i class="on"></i><i class="on"></i><i class="on"></i><em class="streng_field">极佳</em></div>
                                    <div>6-16字符，区分大小写。建议使用字母、数字和符号组合</div>
                                </div>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">再次输入</label>
                        <div class="form-item-con">
                            <input type="password" name="con_pword" data-rule="same" data-encrypt='1' data-with="pword" value="" class="form-item-ipt vcontent" >
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con con_pword tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a href="javascript:;" class="btn btn-confirm submit">下一步</a>
                        </div>
                    </div>
                    <input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_2'>
                </form>
            </div>
            <!-- <div class="warm-tip">
                <p>温馨提示：为了保障您的账户资金安全，建议您定期修改支付密码并牢记，以便安全购彩</p>
            </div> -->
        </div>

<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
<script type="text/javascript">
    $(function () {
        new cx.vform('.tab-content', {
            renderTip: 'renderTips',
            submit: function (data) {
                var self = this;
                $.ajax({
                    type: 'post',
                    url: '/safe/update_password',
                    data: data,
                    success: function (response) {
                        if(response == '001'){
                            cx.Alert({content:'验证身份信息异常'});
                        }
                        else if(response == '002'){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == '003'){
                            $('.pop-alert').show();
                        }
                        else if(response == '004'){
                            cx.Alert({content:'两次密码不匹配'});
                        }
                        else if(response == '005'){
                            cx.Alert({content:'登录密码不能与支付密码一致！'});
                        }
                        else if(response == '006'){
                            cx.Alert({content:'登录密码不正确！'});
                        }
                        else{
                            $('.tab-content').html(response);
                        }
                    }
                });
            }
        });
    })
</script>
<?php if($loginType == 1): ?>
    </div>
</div>
<?php endif; ?>
<?php if($loginType == 1): $this->load->view('elements/user/menu_tail'); endif; ?>
