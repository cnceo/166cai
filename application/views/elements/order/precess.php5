                            <div class="node"><span class="pro-in ready"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">提交方案</li></ul></div>

                            <div class="proce"><span class="pro-in <?php echo $status < 40 ? 'doing' : 'ready';?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2"><?php echo $status < 40 ? ( $status != 20 && $status != 21 ? '待付款' :  '投注失败') : '';?></li></ul></div>
                            <div class="node"><span class="pro-in <?php echo $status == 40 ? 'ready' : ( $status > 40 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">已付款</li><li class="tx3" id="track_time_4"></li></ul></div>

                            <?php if( $status == 200 || $status == 240 ): ?>
                            <div class="proce"><span class="pro-in doing"></span><ul><li class="tx1"><?php echo $status == 240 ? '出票中' : '等待出票'?></li></ul></div>
                            <?php else: ?>
                            <div class="proce"><span class="pro-in <?php echo ($status < 200 ? 'wait' : ( $status > 240 ? 'ready' : 'wait' ));?>"></span><ul><li class="tx1"></li></ul></div>
                            <?php endif; ?>

                            <?php if( $status == 500 || $status == 510 || $status == 600 ): ?>

                                <?php if( $status == 500 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 500 ? 'ready' : ( $status > 500 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">出票成功<br/><a href="javascript:;" class="more" id="seeBetShop" shop-data="<?php echo $shopId; ?>">投注站信息<i>&raquo;</i></a></li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status == 500 ? 'doing' : ( $status > 500 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">等待开奖</li></ul></div>
                                <?php endif; ?>

                                <?php if( $status == 510 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 510 ? 'ready' : ( $status > 510 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">部分出票成功<br/><a href="javascript:;" class="more" id="seeBetShop" shop-data="<?php echo $shopId; ?>">投注站信息<i>&raquo;</i></a></li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status == 510 ? 'doing' : ( $status > 510 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">等待开奖</li></ul></div>
                                <?php endif; ?>
                                
                                <?php if( $status == 600 ): ?>
                                <div class="node"><span class="pro-in <?php echo $status == 600 ? 'ready' : ( $status > 600 ? 'ready' : 'wait' ) ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">出票失败</li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in wait"></span><ul><li class="tx1">&nbsp;</li></ul></div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="node"><span class="pro-in <?php echo $status > 600 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">出票成功</li><li class="tx3" id="track_time_1"></li></ul></div>
                                <div class="proce"><span class="pro-in <?php echo $status > 600 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li></ul></div>
                            <?php endif; ?>
                           
                            <div class="node"><span class="pro-in <?php echo $status == 1000 || $status == 2000 ? 'ready' : 'wait' ;?>"></span><ul><li class="tx1">&nbsp;</li><li class="tx2">已开奖</li><li class="tx3" id="track_time_5"></li></ul></div>