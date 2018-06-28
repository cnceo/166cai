<?php $this->load->view('elements/user/menu'); ?>
<div class="article">
    <div class="tit-b">
        <h2>提款申请</h2>
    </div>
    <ul class="steps-bar clearfix">
        <li class="cur"><i>1</i><span class="des">提款申请</span></li>
        <li><i>2</i><span class="des">验证身份</span></li>
        <li class="last"><i>3</i><span class="des">申请完成</span></li>
    </ul>
    <form action="" class="form uc-form-list cash-form">
        <div class="form-item">
            <label class="form-item-label">可提款金额</label>
            <div class="form-item-con">
                <span class="n-money form-item-txt" id="withDrawMoney" data-money="<?php echo ParseUnit($withDrawMoney, 1); ?>"><?php echo number_format(ParseUnit($withDrawMoney, 1), 2); ?></span>元
            </div>
        </div>
        <div class="form-item">
            <label class="form-item-label">选择提款账户</label>
            <?php if($bankInfo):?>
            <div class="form-item-con">
                <dl class="simu-select-med mybank-box">
                    <dt>
                        <?php foreach ($bankInfo as $key => $bankList): ?>
                        <?php if($bankList['is_default'] == '1'):?>
                            <span class="_scontent" id="province" data-value="1025">
                                <img src="http://caipiao.2345.com/caipiaoimg/v1.0/img/bank/<?php echo BanksDetail($bankList['bank_type'],'img');?>" alt=""><?php echo BanksDetail($bankList['bank_type'],'name');?><small>***** ***** ***** ***** <?php echo substr($bankList['bank_id'],-3);?></small>
                            </span>
                            <i class="arrow"></i>
                            <input type="hidden" class="vcontent" name="bank_id" value="<?php echo $bankInfo[0]['id'];?>">
                        <?php endif;?>
                        <?php endforeach;?> 
                    </dt>
                    <dd class="select-opt">
                        <div class="bank-select-sp" data-name="bank_name">
                            <ul class="clearfix">
                                <?php foreach ($bankInfo as $key => $bankList): ?>
                                <li><a href="javascript:;" target="_self" data-value="<?php echo $bankList['id'];?>"><img src="http://caipiao.2345.com/caipiaoimg/v1.0/img/bank/<?php echo BanksDetail($bankList['bank_type'],'img');?>" alt=""><?php echo BanksDetail($bankList['bank_type'],'name');?><small>***** ***** ***** ***** <?php echo substr($bankList['bank_id'],-3);?></small></a></li>
                                <?php endforeach;?>                                 
                            </ul>                            
                        </div>
                    </dd>
                </dl>
                <?php if(count($bankInfo) < 5):?>
                <a href="/safe/bankcard" class="lnk-txt lnk-add-bank">添加银行卡</a>
                <?php endif;?>
            </div>           
            <?php else:?>
            <div class="form-item-con">
                <span class="form-item-txt">您尚未绑定过银行卡</span><a href="/safe/bankcard" class="lnk-txt lnk-add-bank form-sub-txt">添加银行卡</a>
            </div>   
            <?php endif;?>
        </div>
        <div class="form-item">
            <input type='hidden' class='vcontent' name='action' value='_1'>
            <label class="form-item-label">提款金额</label>
            <div class="form-item-con">
                <input type="text" autocomplete='off' class="form-item-ipt ipt-money vcontent" data-rule='withdraw_money' c-placeholder="请输入提款金额" id="withdraw" value="" name="withdraw">元
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con withdraw tip">请输入提款金额</span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item">
            <label class="form-item-label">真实姓名</label>
            <?php if($this->uinfo['real_name']):?>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" data-rule="chinese" data-ajaxcheck='1' value="" name="real_name">
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con real_name tip"></span>
                    <s></s>
                </div>
            </div>
            <?php else: ?>
            <div class="form-item-con"><span class="form-item-txt">您尚未进行实名认证</span><a href="/safe/idcard" class="lnk-add-idcard form-sub-txt">实名认证</a></div>        
            <?php endif; ?>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-confirm submit<?php echo $showBindBank ? ' not-bind-bank' : ''; ?><?php echo $isTodayWithdraw ? ' today-withdraw' : ''; ?><?php echo $isFreeze ? ' user-freeze' : ''; ?>">下一步</a>
            </div>
        </div>
    </form>
    <div class="warm-tip">
        <h3>温馨提示：</h3>
        <p><span class="cOrange">1.真实姓名是您提款时的重要依据，填写后不可更改（请保证身份证姓名与银行卡姓名保持一致，否则无法提款）。</span></p>
        <p><span class="cOrange">2.网站不向未满18周岁的青少年出售彩票。</span></p>
        <p><span class="cOrange">3.您的个人信息将被严格保密，不会用于任何第三方用途。</span></p>
    </div>
    <script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
    <script type="text/javascript">

        $(function(){

            $('.user-freeze').on('click', function(e){
                cx.Alert({
                    content:'您的账户已被冻结'
                });
                e.stopImmediatePropagation();
            });

            // $('.today-withdraw').on('click', function(e){
            //     cx.Alert({
            //         content:'您今天已经申请过提款'
            //     });
            //     e.stopImmediatePropagation();
            // });

            if($('.cash-form').find('li.btn_area a').hasClass('not-bind-bank')){
                cx.Alert({
                    content:'您尚未绑定银行卡',
                    confirm: '去绑银行卡',
                    addtion: '1',
                    confirmCb: function() {
                        location.href = '/safe/bankcard';
                    }
                });
            }

            $('.not-bind-bank').on('click', function (e){
                cx.Alert({
                    content:'您尚未绑定银行卡',
                    confirm: '去绑银行卡',
                    addtion: '1',
                    confirmCb: function() {
                        location.href = '/safe/bankcard';
                    }
                });
                e.stopImmediatePropagation();
            });

            new cx.vform('.cash-form', {
                renderTip: 'renderTips',
                submit: function(data) {
                    var self = this;

                    //检查最大值
                    if( $('#withdraw').val() > $('#withDrawMoney').data('money') ) {
                        cx.Alert({content:'您设置的提款金额超出了可提款数量额'});
                        return false;
                    }

                    $.ajax({
                        type: 'post',
                        url:  '/wallet/withdraw',
                        data: data,
                        success: function(response) {
                            if( response == 2 ){
                                cx.Alert({content:'您今天已经申请过提款'});
                            }else if (response == 3 ) {
                                cx.Alert({content:'您的账户已被冻结'});
                            }else if (response == 4 ) {
                                cx.Alert({content:'您设置的提款金额超出了可提款数量'});
                            }else if (response == 5 ) {
                                cx.Alert({content:'验证码为空'});
                            }else if (response == 6 ) {
                                cx.Alert({content:'验证码错误或超时'});
                            }else if (response == 7 ) {
                                cx.Alert({content:'网络错误'});
                            }else if (response == 9 ) {
                                cx.Alert({content:'提款金额不合法'});
                            }else if (response == 10 ) {
                                showBind();
                            }else if (response == 11 ){
                                cx.Alert({
                                    content:'您尚未绑定银行卡',
                                    confirm: '去绑银行卡',
                                    addtion: '1',
                                    confirmCb: function() {
                                        location.href = '/safe/bankcard';
                                    }
                                });
                                e.stopImmediatePropagation();
                            } else {
                                $('.article').html(response);
                            }
		                	
                        }
                    });
                }
            });

        })

        

    </script>
</div>
<?php $this->load->view('elements/user/menu_tail'); ?>