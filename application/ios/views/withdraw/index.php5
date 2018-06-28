<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>提现</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/withdraw-money.min.css');?>">
</head>
<body>
    <div class="wrapper cp-box-b withdraw-money">
        <!-- 温馨提示 -->
        <div class="ui-notice"><span>因提现较多，工作日最迟24小时到账，节假日将顺延至工作日处理，请耐心等待</span></div>
        <div class="cp-box-hd">
            <h1 class="cp-box-title J-money-num" data-value="<?php echo ParseUnit($withDrawMoney, 1); ?>">可提现金额：<?php echo number_format(ParseUnit($withDrawMoney, 1), 2); ?>元</h1>
            <p>账户余额<?php echo number_format(ParseUnit($money['money'], 1), 2); ?>元，如需退款请联系客服</p>
        </div>
        <div class="cp-box-bd">
            <ul class="cp-list bankcard-box">
                <li>
                    <a href="javascript:;" onclick="window.location.href='<?php echo $this->config->item('pages_url'); ?>ios/withdraw/apply/<?php echo $token;?>';">
                        <div class="bankcard-name-<?php echo BanksDetail($bank_type,'st');?> <?php if($is_default == 1):?>bankcard-default<?php endif;?>">
                            <h2 class="bankcard-hd"><?php echo BanksDetail($bank_type,'name');?></h2>
                            <p class="bankcard-bd"><?php echo substr($bank_id,0,4);?> ***** ***** <?php echo substr($bank_id,-4);?></p>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <form class="cp-form form-hack" action="" autocomplete="off">
            <div class="form-group">
                <div class="form-item">
                    <label for="bankUserName">真实姓名</label>
                    <strong><?php echo $money['real_name']; ?></strong>
                    <s>若有误请联系客服</s>
                    <input name="uname" type="text" style="display:none;" value="<?php echo $money['real_name']; ?>" id="bankUserName" placeholder="请输入真实姓名" >
                </div>
                <div class="form-item">
                    <label for="moneyNum">提现金额</label>
                    <input name="moneyNum" type="number" id="moneyNum" class="recharge-num-ipt" placeholder="单笔提现金额最少10元" value="">
                    <a href="javascript:;" class="btn-allin">全部提现</a>
                </div>
            </div>
            <div class="btn-group">
                <a id="applyWithdraw1" class="btn btn-confirm btn-recharge" tabindex="1" href="javascript:void(0);">下一步</a>
                <ol>
                    <li>1.为防止恶意提款、洗钱等不法行为，信用卡每笔充值资金100%须用于购彩，储蓄卡每笔充值资金的50%须用于购彩，不可提现；</li>
                    <li>2.为保证账户资金安全，每天允许提现3次，单笔提现金额最少为10元；</li>
                    <li>3.参与网站活动领取的红包金额不可提现；</li>
                    <li>4.提现最快2分钟即可到账，最慢24小时内到账，请您耐心等待；</li>
                    <li>5.提现遇到问题请致电客服：400-690-6760</li>
                </ol>
            </div>
            <input type="submit" value="" class="hack-submit" />
            <input type='hidden' class='' name='withDrawMoney' value='<?php echo ParseUnit($withDrawMoney, 1);?>'/>
            <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
            <input type='hidden' class='' name='bankId' value='<?php echo $id;?>'/>
            <input type='hidden' class='' name='action' value='1'/>
        </form>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){


            $('.btn-allin').on('click', function () {
                $('input[name=moneyNum]').val($('.J-money-num').attr('data-value'))
            })
            // 虚拟键盘 hack
            $('.cp-form').on('submit', function(){
                $('#applyWithdraw1').trigger('tap');
                return false;
            })

            var closeTag = true;
            // 提现申请
            $('#applyWithdraw1').on('tap', function(){
                // 失焦
                $('.recharge-num-ipt').blur();
                $('#bankUserName').blur();

                var moneyNum = $('input[name="moneyNum"]').val();
                var uname = $('input[name="uname"]').val();
                var token = $('input[name="token"]').val();
                var bankId = $('input[name="bankId"]').val();
                var action = $('input[name="action"]').val();
                var withDrawMoney = $('input[name="withDrawMoney"]').val();

                if(moneyNum == '')
                {
                    $.tips({
                        content: '请输入提现金额',
                        stayTime: 2000
                    })
                    return false;
                }
                //  /^([1-9][0-9]*)?[0-9]\.[0-9]{2}$/
                if( /^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/.test(moneyNum) ) {
                    if( moneyNum <= 0 ) { 
                        $.tips({
                            content:'提现金额格式错误',
                            stayTime:2000
                        })
                        return false;
                    }
                    if( moneyNum < 10 ) { 
                        $.tips({
                            content:'单笔提现金额至少10元',
                            stayTime:2000
                        })
                        return false;
                    }
                }else{
                    $.tips({
                        content:'提现金额格式错误',
                        stayTime:2000
                    })
                    return false;
                }
                if(parseFloat(moneyNum) > parseFloat(withDrawMoney))
                {
                    $.tips({
                        content: '可提现余额不足',
                        stayTime: 2000
                    })
                    return false;
                }

                if(uname == '')
                {
                    $.tips({
                        content: '请输入真实姓名',
                        stayTime: 2000
                    })
                    return false;
                }
                if(closeTag)
                {
                    closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    $.ajax({
                        type: 'post',
                        url: '/ios/withdraw/applyWithdraw',
                        data: {moneyNum:moneyNum,uname:uname,token:token,bankId:bankId,action:action},
                        // beforeSend: loading,
                        success: function (response) {
                            showLoading.loading("hide");
                            var response = $.parseJSON(response);
                            if(response.status == 1)
                            {
                                closeTag = true;
                                window.location.href = response.data;
                            }
                            else
                            {
                                closeTag = true;
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }
                        },
                        error: function () {
                            showLoading.loading("hide");
                            closeTag = true;
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }             
            });

        });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>