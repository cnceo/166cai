<?php
// 银行编号 映射 图标编号
$bankIconMap = array(
    '1025' => "1",
    '103' => "2",
    '306' => "4",
    '105' => "5",
    '312' => "6",
    '104' => "7",
    '326' => "8",
    '311' => "9",
    '3080' => "10",
    '301' => "11",
    '314' => "12",
    '309' => "13",
    '3230' => "14",
    '305' => "15",
    '313' => "16",
    '307' => "17",
    '316' => "18",
);
?>
<title>我的银行卡-166彩票网</title>	
<div class="tit-b">
    <h2>添加提现银行卡</h2>
    <p class="tip cOrange">绑定银行卡可用于快捷充值与提现</p>
</div>
<ul class="steps-bar clearfix">
    <li class="cur"><i>1</i><span class="des">填写实名信息和卡号</span></li>
    <li><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">添加成功</span></li>
</ul>
<div class="safe-item-box">
    <div class="form uc-form-list pl154 edit-form">
        <div class="form-item">
            <label class="form-item-label">真实姓名</label>
            <div class="form-item-con"><span class="name"><?php echo cutstr($this->uinfo['real_name'], 0, 1); ?></span></div>
        </div>
        <div class="form-item">
            <label class="form-item-label">开户银行</label>
            <div class="form-item-con">
                <dl class="simu-select-med bank-select">
                    <dt><span class='_scontent' id='province' data-value='<?php echo $bank_type; ?>'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon' . $bankIconMap[$bank_type] . '.png'); ?>" alt=""><?php echo $bankTypeList[$bank_type]; ?></span><i class="arrow"></i><input type="hidden" class="vcontent" name='bank_type' value='<?php echo $bank_type; ?>'></dt>
                    <dd class="select-opt bank-select-opt">
                        <div class="bank-select-sp" data-name='bank_name'>
                            <div class="bank-group-item">
                                <h3>1小时内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='1025'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon1.png'); ?>" alt="">中国工商银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='103'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon2.png'); ?>" alt="">中国农业银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='306'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon4.png'); ?>" alt="">广发银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='105'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon5.png'); ?>" alt="">中国建设银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='312'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon6.png'); ?>" alt="">中国光大银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='104'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon7.png'); ?>" alt="">中国银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='326'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon8.png'); ?>" alt="">上海银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='311'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon9.png'); ?>" alt="">华夏银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='3080'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon10.png'); ?>" alt="">招商银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='301'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon11.png'); ?>" alt="">交通银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='314'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon12.png'); ?>" alt="">上海浦东发展银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='309'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon13.png'); ?>" alt="">兴业银行</a></li>
                                </ul>
                            </div>
                            <div class="bank-group-item">
                                <h3>24小时内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='3230'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon14.png'); ?>" alt="">邮政储蓄</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='305'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon15.png'); ?>" alt="">中国民生银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='313'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon16.png'); ?>" alt="">中信银行</a></li>
                                    <li><a href="javascript:;" target="_self" data-value='307'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon17.png'); ?>" alt="">平安银行</a></li>
                                </ul>
                            </div>
                            <div class="bank-group-item last">
                                <h3>2个工作日内到账:</h3>
                                <ul class="clearfix">
                                    <li><a href="javascript:;" target="_self" data-value='316'><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bank-icon18.png'); ?>" alt="">南京银行</a></li>
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
        <div class="form-item">
            <label class="form-item-label">银行卡号</label>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" value="<?php echo $bank_id; ?>" data-rule="bankcard" name="bank_id" >
                <div class="form-tip" style="display:none;">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con bank_id tip" ></span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item form-add">
            <label class="form-item-label">开户地区</label>
            <div class="form-item-con">
                <input type='hidden' class='vcontent<?php echo $showBind ? ' not-bind' : ''; ?>' name='action' value='_1'>
                <dl class="simu-select-med" data-target='city_list'>
                    <dt><span class='_scontent' id='province' data-value='<?php echo $bank_province; ?>'><?php echo $bank_province; ?></span><i class="arrow"></i><input type="hidden" class="vcontent" name='province' value='<?php echo $bank_province; ?>'></dt>
                    <dd class="select-opt">
                        <div class="select-opt-in" data-name='province'>
                            <?php foreach ($provinceList as $row): ?>
                                <a href="javascript:;" data-value='<?php echo $row['province'] ?>'><?php echo $row['province'] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </dd>
                </dl>
                <dl class="simu-select-med city_list">
                    <dt><span class='_scontent' id='city' data-value='<?php echo $bank_city; ?>'><?php echo $bank_city; ?></span><i class="arrow"></i>
                        <input type="hidden" class="vcontent" name='city' value='<?php echo $bank_city; ?>'>
                    </dt>
                    <dd class="select-opt">
                        <div class="select-opt-in" id='city-container' data-name='city'></div>
                    </dd>
                </dl>
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con bank_area tip"></span>
                    <s></s>
                </div>
            </div>
        </div>

        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-main submit">下一步</a>
            </div>
        </div>
    </div>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>1.银行卡开户姓名必须与绑定的真实姓名一致，否则将提现失败。</p>
    <p>2.银行卡绑定后不可随意修改，特殊情况需修改，请联系客服。</p>
</div>
<script type="text/javascript">
    $(function () {

        $('.reedit').click(function(){
            $('.safe-item-box').find('input[name="action"]').val('_3');
            $('.safe-item-box').find('.submit').trigger('click');
        });

        $('.not-bind').on('click', showBind);

        new cx.vform('.edit-form', {
            renderTip: 'renderTips',
            submit: function (data) {
                var self = this;

                if (self.$submit.hasClass('not-bind')) {
                    return false;
                }

                var data = data || {};
                $.ajax({
                    type: 'post',
                    url: '/safe/bankcard',
                    data: data,
                    success: function (response) {
                        if( response == 2 ){
                            self.renderTip('请选择开户地区', $('.bank_area'));
                        }else if (response == 3 ) {
                            self.renderTip('请选择开户银行', $('.bank_name'));
                        }else if (response == 4 ) {
                            self.renderTip('请输入正确的银行卡号', $('.bank_id'));
                        }else if (response) {
                            $('.l-frame-cnt .uc-main').html(response);
                        } else {
                        }
                    }
                });
            }
        });
    });

</script>