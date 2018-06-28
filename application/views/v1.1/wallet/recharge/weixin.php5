<div class="wrap p-recharge">
    <?php $this->load->view('v1.1/wallet/recharge/userinfo'); ?>
    <div class="cp-box">
        <div class="cp-box-bd recharge-form">
            <form target="_blank" action="/wallet/recharge/processRecharge" method='post' class="form _recharge">
               <?php $this->load->view('v1.1/wallet/recharge/form_comm'); ?>
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