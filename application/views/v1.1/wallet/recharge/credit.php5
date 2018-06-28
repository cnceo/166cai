<div class="wrap p-recharge">
    <?php $this->load->view('v1.1/wallet/recharge/userinfo'); ?>
    <div class="cp-box">
        <div class="cp-box-bd recharge-form">
            <form target="_blank" action="/wallet/recharge/processRecharge" method='post' class="form _recharge">
               <?php $this->load->view('v1.1/wallet/recharge/form_comm'); ?>
                <!--开启-->
                <?php if($is_show):?>
                <div class="tab-item">
                    <div class="form-item">
                        <label class="form-item-label">充值方式：</label>
                        <div class="form-item-con">
                            <div class="bank_list m-choose _mybankList" data-rule='{"name": "银行", "num": 8}'>
                                <ul>
                                    <li data-val='ICBC-NET-B2C' class="selected"><img title="工商银行" alt="工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='CMBCHINA-NET-B2C'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='CCB-NET-B2C'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='BOC-NET-B2C'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='CMBC-NET-B2C'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='CEB-NET-B2C'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='SHB-NET-B2C'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='PINGANBANK-NET-B2C'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/payh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='BOCO-NET-B2C'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='CIB-NET-B2C'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='GDB-NET-B2C'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='SPDB-NET-B2C'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
                                    <li data-val='ECITIC-NET-B2C'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $this->load->view('v1.1/wallet/recharge/comm'); ?>
                <!--开启结束-->
                <?php else: ?>
                <!--维护开始-->
                <?php $this->load->view('v1.1/wallet/recharge/default_wf'); ?>
                <!--维护结束-->
                <?php endif; ?>
                <div class="recharge-form-side"><a href="<?php echo $baseUrl;?>/mylottery/recharge" target="_blank">充值记录</a></div>
            </form>
        </div>
        <!--注意事件-->
        <?php $this->load->view('v1.1/wallet/recharge/mod_note'); ?>
    </div>
</div>