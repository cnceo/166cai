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
                    <div class="tab-content">
                        <div class="tab-item" style="display: block;">
                            <div class="form-item">
                                <label class="form-item-label" style="margin-top: 5px;">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list">
                                        <ul>
                                        <?php foreach($pay_way as $k => $v ): ?>
                                            <li class="<?php echo $k==0 ?'selected':''; ?>" data-value='<?php echo $v['mode'];?>'><img src="<?php echo getStaticFile($v['img_src']);?>" width="<?php echo $v['img_w'];?>" height="38" alt="<?php echo $v['img_alt'];?>"><i class="s_yes"></i></li>
                                        <?php endforeach; ?> 
                                        </ul>
                                    </div>
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