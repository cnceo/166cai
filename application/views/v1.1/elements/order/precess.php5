                            <div class="node"><span class="pro-in ready"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">提交方案<span class="time-pointer"><?php echo $created; ?></span></li></ul></div>

                            <div class="proce"><span class="pro-in <?php echo $status < 40 ? 'doing' : 'ready';?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2"><?php echo $status < 40 ? ( $status != 20 && $status != 21 ? '待付款' :  '投注失败') : '';?></li></ul></div>
                            <div class="node"><span class="pro-in <?php echo $status == 40 ? 'ready' : ( $status > 40 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">已付款<span class="time-pointer"><?php echo $status >= 40 ? $pay_time : ''; ?></span></li><li class="tx3" id="track_time_4"></li></ul></div>

                            <?php if( $status == 200 || $status == 240 ): ?>
                            <div class="proce"><span class="pro-in doing"></span><ul><li class="tx1"><?php echo $status == 240 ? '分配彩站出票中' : '等待出票'?></li></ul></div>
                            <?php else: ?>
                            <div class="proce"><span class="pro-in <?php echo ($status < 200 ? 'wait' : ( $status > 240 ? 'ready' : 'wait' ));?>"></span><ul><li class="tx1"></li></ul></div>
                            <?php endif; ?>

                            <?php if( $status == 500 || $status == 510 || $status == 600): ?>

                                <?php if( $status == 500 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 500 ? 'ready' : ( $status > 500 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">出票成功<br/><span class="time-pointer"><?php echo $status >= 500 ? $ticket_time : ''; ?></span><span class="sub-lnk"><a href="javascript:;" class="more" id="seeBetShop" shop-data="<?php echo $shopId; ?>" style="<?php echo (!empty($this->uinfo['email']))?'padding-left:0;':''; ?>">投注站信息</a><?php if(empty($this->uinfo['email'])):?> | <a class="get-voucher" href="/safe/bindEmail" target="_blank">订阅出票通知服务</a><?php endif;?></span></li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status == 500 ? 'doing' : ( $status > 500 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">等待开奖</li></ul></div>
                                <?php endif; ?>

                                <?php if( $status == 510 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 510 ? 'ready' : ( $status > 510 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">部分出票成功<br/><span class="time-pointer"><?php echo $status >= 500 ? $ticket_time : ''; ?><br/></span><span class="sub-lnk"><a href="javascript:;" class="more" id="seeBetShop" shop-data="<?php echo $shopId; ?>" style="<?php echo (!empty($this->uinfo['email']))?'padding-left:0;':''; ?>">投注站信息</a><?php if(empty($this->uinfo['email'])):?> | <a class="get-voucher" href="/safe/bindEmail" target="_blank">订阅出票通知服务</a><?php endif;?></span></li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status == 510 ? 'doing' : ( $status > 510 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">等待开奖</li></ul></div>
                                <?php endif; ?>
                                
                                <?php if( $status == 600 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 600 ? 'ready' : ( $status > 600 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">出票失败</li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in wait"></span><ul><li class="tx1">&nbsp;</li></ul></div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="node"><span class="pro-in <?php echo $status > 600 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2"><?php if($failMoney > 0){ echo '部分出票成功';}else{ echo '出票成功';}?><br/><span class="time-pointer"><?php echo $status >= 500 ? $ticket_time : ''; ?></span><?php if($status >= 500):?><span class="sub-lnk"><a href="javascript:;" class="more" id="seeBetShop" shop-data="<?php echo $shopId; ?>" style="<?php echo (!empty($this->uinfo['email']))?'padding-left:0;':''; ?>">投注站信息</a><?php if(empty($this->uinfo['email'])):?> | <?php endif;?><?php endif; ?><?php if(empty($this->uinfo['email'])):?><a class="get-voucher" href="/safe/bindEmail" target="_blank">订阅出票通知服务</a><?php endif;?></span></li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status > 600 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li></ul></div>
                            <?php endif; ?>
                           
                            <div class="node"><span class="pro-in <?php echo $status == 1000 || $status == 2000 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">已开奖</li><li class="tx3" id="track_time_5"></li></ul></div>