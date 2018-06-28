<div class="tit-b">
    <h2>修改银行卡</h2>
    <p class="tip cOrange">为了保证你的快速提现，请保证银行卡开户姓名与绑定真实姓名一致</p>
</div>
<ul class="steps-bar clearfix">
    <li class="cur"><i>1</i><span class="des">填写银行信息</span></li>
    <li><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">验证完成</span></li>
</ul>
<div class="safe-item-box">
    <form class="form uc-form-list pl154 edit-form">
        <div class="form-item">
            <label class="form-item-label">真实姓名</label>
            <div class="form-item-con"><span class="form-item-txt name" id="realName"><?php echo cutstr($this->uinfo['real_name'], 0, 1);?></span><span class="name" id="realNameShow" style="display:none;"><?php echo $this->uinfo['real_name'];?></span></div>
        </div>
        <div class="form-item">
            <label class="form-item-label">身份证号</label>
            <div class="form-item-con"><span class="form-item-txt bankCard-num" id="idCardHide"><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></span><span class="bankCard-num" id="idCardShow" style="display:none;"><?php echo $this->uinfo['id_card'];?></span><a href="javascript:;" class="lnk-txt" id="btn-veiw-more">查看完整信息</a></div>
        </div>
        <div class="form-item">
            <label class="form-item-label">开户银行</label>
            <div class="form-item-con">
                <dl class="simu-select-med bank-select">
                    <dt>
                        <span class="_scontent" id="province" data-value="<?php echo $binfo['bank_type'];?>"><img src="http://caipiao.2345.com/caipiaoimg/v1.0/img/bank/<?php echo BanksDetail($binfo['bank_type'],'img');?>" alt=""><?php echo BanksDetail($binfo['bank_type'],'dname');?></span>
                        <i class="arrow"></i>
                        <input type="hidden" class="vcontent" name="bank_type" value="<?php echo $binfo['bank_type'];?>">
                    </dt>
                    <dd class="select-opt bank-select-opt">
                        <div class="bank-select-sp" data-name='bank_name'>
                            <div class="bank-group-item">
                                <h3>1小时内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='1025'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon1.png'); ?>" alt="">中国工商银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='103'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon2.png'); ?>" alt="">中国农业银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='306'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon4.png'); ?>" alt="">广发银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='105'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon5.png'); ?>" alt="">中国建设银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='312'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon6.png'); ?>" alt="">中国光大银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='104'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon7.png'); ?>" alt="">中国银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='326'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon8.png'); ?>" alt="">上海银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='311'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon9.png'); ?>" alt="">华夏银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='3080'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon10.png'); ?>" alt="">招商银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='301'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon11.png'); ?>" alt="">交通银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='314'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon12.png'); ?>" alt="">上海浦东发展银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='309'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon13.png'); ?>" alt="">兴业银行</a></li>
                                </ul>
                            </div>
                            <div class="bank-group-item">
                                <h3>24小时内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='3230'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon14.png'); ?>" alt="">中国邮政储蓄银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='305'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon15.png'); ?>" alt="">中国民生银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='313'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon16.png'); ?>" alt="">中信银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='307'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon17.png'); ?>" alt="">平安银行</a></li>
                                </ul>
                            </div>
                            <div class="bank-group-item last">
                                <h3>2个工作日内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='316'><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bank-icon18.png'); ?>" alt="">南京银行</a></li>
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
        </div>
        <div class="form-item form-add">
            <label class="form-item-label">开户地区</label>
            <div class="form-item-con">
                <input type='hidden' class='vcontent' name='action' value='_5'>
                <input type='hidden' class='vcontent' name='id' value='<?php echo $binfo['id'];?>'>
                <dl class="simu-select-med" data-target='city_list'>
                    <dt>
                        <span class="_scontent" id="province" data-value="<?php echo $binfo['bank_province'];?>"><?php echo $binfo['bank_province'];?></span>
                        <i class="arrow"></i>
                        <input type="hidden" class="vcontent" name="province" value="<?php echo $binfo['bank_province'];?>">
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
                        <span class='_scontent' id='city' data-value='<?php echo $binfo['bank_city'];?>'><?php echo $binfo['bank_city'];?></span>
                        <i class="arrow"></i>
                        <input type="hidden" class="vcontent" name='city' value='<?php echo $binfo['bank_city'];?>'>
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
        </div>
        <div class="form-item">
            <label class="form-item-label">银行卡号</label>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" value="<?php echo $binfo['bank_id'];?>" data-rule="bankcard" name="bank_id" >
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con bank_id tip"></span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-confirm submit">下一步</a>
            </div>
        </div>
    </form>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>提款账户开户姓名必须与您在2345填写的真实姓名一致，否则将提款失败。</p>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
<script>
    //查看完整信息
    cx.viewUinfo = (function() {
        var me = {};
        var $wrapper = $('.pop-bank-id');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        $('#bid-pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
            cx.Mask.show();
            $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();

    $('#btn-veiw-more').click(function(){
        cx.viewUinfo.show();
    })

    //提交
    new cx.vform('.edit-form', {
        renderTip: 'renderTips',
        submit: function (data) {
            $.ajax({
                type: 'post',
                url: '/safe/bankcard',
                data: data,
                success: function (response) {
                    if( response == 2 ){
                        cx.Alert({content:'开户地区不能为空'});
                    }else if (response == 3 ) {
                        cx.Alert({content:'开户行不能为空'});
                    }else if (response == 4 ) {
                        cx.Alert({content:'银行卡格式不正确'});
                    }else if (response == 5 ) {
                        cx.Alert({content:'银行卡格式不正确'});
                    }else if (response == 6 ) {
                        cx.Alert({content:'已绑定过此银行卡'});
                    }else if (response) {
                        $('.article').html(response);
                    } else {
                        cx.Alert({content:'系统异常'});
                    }
                }
            });
        }
    });

    //提交验证身份
    new cx.vform('#checkIdBank', {
        renderTip: 'renderTips',
        submit: function (data) {
            cx.viewUinfo.hide();
        }
    });

</script>

