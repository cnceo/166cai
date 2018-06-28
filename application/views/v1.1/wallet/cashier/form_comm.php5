 <form target="_blank" action="/wallet/recharge/processRecharge" method='post'  name='_cashier' class="form _cashier">
     <input type='hidden' class='vcontent ipt_fee' name='p3_Amt' value='<?php echo ParseUnit($data['money']-$money, 1);?>'> 
     <input type='hidden' class='vcontent' name='pd_FrpId' value=''> 
     <input type='hidden' class='vcontent' name='orderId' value='<?php echo $orderId;?>'>
     <input type='hidden' class='vcontent' name='mode' value='<?php echo isset($pay_way[0]) ? $pay_way[0]['mode'] : '';?>'>
     <input type='hidden' class='vcontent' name='orderType' value='<?php echo $orderType;?>'>
     <input type='hidden' class='vcontent' name='redpack' value=''>
     <span class="recharge-form-side pay-nm">支付 <b class="money recharge_money" id="need_recharge" data-val="<?php echo ParseUnit($data['money']-$money, 1);?>"><?php echo ParseUnit($data['money']-$money, 1);?></b> 元</span>
     <div class="form-item">
         <div class="form-item-con">
             <div class="tab-nav">
                 <ul>
                    <!--隐藏-->
                    <?php if(ParseUnit($data['money']-$money, 1)>2000){ unset($pay_list['1_4']); }echo $money;?>
                    <?php if(ParseUnit($data['money']-$money, 1)<10){ unset($pay_list['1_4']); }?>
                    <?php foreach($pay_list as $k => $v ): ?>
                        <li <?php echo $k == $mode_str ? 'class="active" ' : ''; ?> ><a href="<?php echo $v['url'] ?>"><span><?php echo $v['name'] ?></span></a><?php if($v['guide']){ ?><div class="icon-bank-intro"><b><s><?php echo $v['guide'] ?></s></b></div><?php } ?></li>
                    <?php endforeach; ?>
                 </ul>
             </div>
         </div>
     </div>