                <div class="form-item">
                    <label class="form-item-label">充值金额：</label>
                    <div class="form-item-con">
                        <div class="type_list _type_list" style="width: 500px;">
                            <ul>
                                <li data-val='10' >10元<i class="s_yes"></i></li>
                                <li class="selected" data-val='20' >20元<i class="s_yes"></i></li>
                                <li data-val='50' >50元<i class="s_yes"></i></li>
                                <li data-val='100' >100元<i class="s_yes"></i></li>
                                <li data-val='200' >200元<i class="s_yes"></i></li>
                                <li data-val='500' >500元<i class="s_yes"></i></li>
                                <li data-val='1000' >1000元<i class="s_yes"></i></li>
                                <li data-val='2000' >2000元<i class="s_yes"></i></li>
                                <?php if (!isset($isZfb)): ?>
                                <li data-val='3000' >3000元<i class="s_yes"></i></li>
                                <li data-val='5000' >5000元<i class="s_yes"></i></li>        
                                <?php endif ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--支付宝限额-->
                <script type="text/javascript">
                 <?php if (isset($isZfb)): ?>
                 var isZfb = 1;
                 <?php else: ?>
                 var isZfb = 0;
                <?php endif ?>                     
                </script>
                <div class="form-item">
                    <label class="form-item-label">其他金额：</label>
                    <div class="form-item-con">
                        <input type="text" class="form-item-ipt ipt-money other_money" placeholder="请输入10元以上的整数"><span class="units">元</span>
                    </div>
                </div>
                <?php if(!empty($redpackData)):?>
                <!-- 充值红包 start -->
                <div class="form-item form-chooseRp">
                    <label class="form-item-label">选择红包：</label>
                    <div class="form-item-con">
                        <div class="hongbao-s"  id="redpackInfo">
                            <ul>
                            <?php foreach( $redpackData as $key => $items ): ?>
                                <li  redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?> <?php echo ParseUnit($params['money_bar'], 1) ==20 ? 'selected':'' ;?>" id="redpackId-<?php echo $items['id']; ?>">
                                    <?php echo ParseDesc($items['use_desc']);?><span><?php echo ParseEnd($items['valid_end']);?></span>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- 充值红包 end -->
                <?php endif; ?>
                <div class="form-item btn-group">
                    <a href="javascript:;" class="btn btn-main submit<?php echo $showBind ? ' not-bind': '';?>">下一步</a>
                </div>
