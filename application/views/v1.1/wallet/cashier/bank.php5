<div class="wrap">
    <div class="cp-box">
        <?php $this->load->view('v1.1/wallet/cashier/comm'); ?>
        <div class="cp-box-bd">
            <div class="recharge-form pay-form">
                <div class="balance">
                    <span class="pay-nm">支付 <em class="money"><?php echo ParseUnit($money, 1);?></em> 元</span>
                    <label for="checkbox-balance">
                        <input type="checkbox" class="ipt_checkbox" name="checkbox-balance" id="checkbox-balance" checked>余额支付
                    </label>
                    账户<strong class="account"><?php echo $this->uname;?></strong>余额 <em class="money" id="remain_money" data-balance='<?php echo ParseUnit($money, 1);?>'><?php echo ParseUnit($money, 1);?></em> 元
                </div>
                <?php $this->load->view('v1.1/wallet/cashier/form_comm'); ?>
                    <!--开启-->
                    <?php if($is_show):?>
                    <div class="tab-item">
                        <div class="form-item">
                            <label class="form-item-label">充值方式：</label>
                            <div class="form-item-con">
                                <div class="bank_list m-choose _mybankList" data-rule='{"name": "银行", "num": 8}'>
                                    <ul>
                                        <li data-val='ICBC-NET-B2C' class="selected"><img title="中国工商银行" alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='POST-NET-B2C'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='ABC-NET-B2C'><img title="中国农业银行" alt="中国农业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/nyyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='CMBCHINA-NET-B2C'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='CCB-NET-B2C'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='BOC-NET-B2C'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='BOCO-NET-B2C'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='CMBC-NET-B2C'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='CIB-NET-B2C'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='CEB-NET-B2C'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='HXB-NET-B2C'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='ECITIC-NET-B2C'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='GDB-NET-B2C'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='SPDB-NET-B2C'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='BCCB-NET-B2C'><img title="北京银行" alt="北京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bjyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='SHB-NET-B2C'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                        <li data-val='PINGANBANK-NET-B2C'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/payh.png');?>"><i class="s_yes"></i></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--开启结束-->
                    <!--提交按钮-->
                    <?php $this->load->view('v1.1/wallet/cashier/submit'); ?>
                    <?php else: ?>
                    <!--维护开始-->
                    <?php $this->load->view('v1.1/wallet/recharge/default_wf'); ?>
                    <!--维护结束-->
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>