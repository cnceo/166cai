<title>我的银行卡-2345彩票网</title>
<?php $this->load->view('elements/user/menu'); ?>
<?php $isBind = $is_phone_bind && $is_id_bind && $is_pay_pwd; ?>
<div class="article">
    <div class="bank-card-box">
        <ul>
        <?php if($bankInfo):?>
            <?php foreach ($bankInfo as $key => $bankList): ?>
            <li>
                <div class="bank-card bank-card-gs <?php if($bankList['is_default'] == '1'):?>bank-card-default<?php endif;?>">
                    <div class="bank-card-hd"><img src="/caipiaoimg/v1.0/img/bank/<?php echo BanksDetail($bankList['bank_type'],'img');?>" alt=""><?php echo BanksDetail($bankList['bank_type'],'name');?><i class="icon-default"></i></div>
                    <div class="bank-card-bd"><?php echo '***************'.substr($bankList['bank_id'], -3);?></div>
                    <div class="bank-card-ft">
                        <div class="lnk-group">
                            <a href="javascript:;" data-value="<?php echo $bankList['id'];?>" data-bid="<?php echo substr($bankList['bank_id'], -3);?>" data-bname="<?php echo BanksDetail($bankList['bank_type'],'name');?>" class="lnk-modify">修改</a>
                            <?php if($bankList['is_default'] == '0'):?>
                            <a href="javascript:;" data-value="<?php echo $bankList['id'];?>" data-bid="<?php echo substr($bankList['bank_id'], -3);?>" data-bname="<?php echo BanksDetail($bankList['bank_type'],'name');?>" class="lnk-del">删除</a>
                            <a href="javascript:;" data-value="<?php echo $bankList['id'];?>" data-bid="<?php echo substr($bankList['bank_id'], -3);?>" data-bname="<?php echo BanksDetail($bankList['bank_type'],'name');?>" class="lnk-default">设为默认</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
            <li>
                <a href="javascript:;" class="bank-card-add">
                    <div class="icon-add">
                        <i class="icon-add-h"></i>
                        <i class="icon-add-s"></i>
                    </div>
                    添加银行卡
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="javascript:;" class="bank-card-add <?php if(!$isBind): ?>not-bind<?php endif;?>">
                    <div class="icon-add">
                        <i class="icon-add-h"></i>
                        <i class="icon-add-s"></i>
                    </div>
                    添加银行卡
                </a>
            </li>
        <?php endif;?>
        </ul>
    </div>
    <script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
    <script type="text/javascript">
        $(function () {

            // $('.not-bind').on('click', showBind);

            $(".bank-card-add").on('click', function(){

                // if ($(this).hasClass('not-bind')) {
                //     return false;
                // }

                $.ajax({
                    type: 'post',
                    url: '/safe/bankcard',
                    data: {'action':'_add'},
                    success: function (response) {
                        if( response == 2 ){
                            cx.Alert({content:'最多添加五张银行卡！'});
                        }else if(response == 3) {
                            showBind;
                        }else if(response) {
                            $('.article').html(response);
                        }else{
                            cx.Alert({content:'系统异常！'});
                        }
                    }
                });
            });

            $(".lnk-modify").on('click', function(){
                
                $.ajax({
                    type: 'post',
                    url: '/safe/bankcard',
                    data: {'action':'_modify','id':$(this).attr('data-value')},
                    success: function (response) {
                        if( response == 2 ){
                            cx.Alert({content:'最多添加五张银行卡！'});
                        }else if(response) {
                            $('.article').html(response);
                        }else{
                            cx.Alert({content:'系统异常！'});
                        }
                    }
                });
            });

            //弹出框
            cx.setDefault = (function() {
                var me = {};
                var $wrapper = $('.pop-bank-choose');

                $wrapper.find('.pop-close').click(function() {
                    $wrapper.hide();
                    cx.Mask.hide();
                });

                $('#pop-close').click(function() {
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

            //设置默认
            $('.lnk-default').click(function(){
                var id = $(this).attr('data-value');
                var bankid = $(this).attr('data-bid');
                var bname = $(this).attr('data-bname');
                $('#bankId').attr('value',id);
                $('#setbankid').text(bankid);
                $('#setbankname').text(bname);
                cx.setDefault.show();
            });

            //提交
            new cx.vform('.pop-bank-choose', {
                renderTip: 'renderTips',
                submit: function (data) {
                    $.ajax({
                        type: 'post',
                        url: '/safe/bankcard',
                        data: data,
                        success: function (response) {
                            if(response == 2){
                                location.reload();
                            }else{
                                cx.Alert({content:'系统异常！'});
                            }
                        }
                    });
                }
            });

            //弹出框
            cx.delBank = (function() {
                var me = {};
                var $wrapper = $('.pop-bank-del');

                $wrapper.find('.pop-close').click(function() {
                    $wrapper.hide();
                    cx.Mask.hide();
                });

                $('#del-pop-close').click(function() {
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

            //设置默认
            $('.lnk-del').click(function(){
                var id = $(this).attr('data-value');
                var bankid = $(this).attr('data-bid');
                var bname = $(this).attr('data-bname');
                $('#bankId_del').attr('value',id);
                $('#setbankid_del').text(bankid);
                $('#setbankname_del').text(bname);
                cx.delBank.show();
            });

            //提交
            new cx.vform('.pop-bank-del', {
                renderTip: 'renderTips',
                submit: function (data) {
                    $.ajax({
                        type: 'post',
                        url: '/safe/bankcard',
                        data: data,
                        success: function (response) {
                            if(response == 2){
                                location.reload();
                            }else{
                                cx.Alert({content:'系统异常！'});
                            }
                        }
                    });
                }
            });



        });

    </script>
</div>
<?php $this->load->view('elements/user/menu_tail'); ?>