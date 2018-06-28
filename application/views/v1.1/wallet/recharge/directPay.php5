<div class="wrap">
    <div class="cp-box">
        <div class="cp-box-hd product-info">
        <?php $datetime = strtotime($data['created']);?>
            <h2 class="tit">商品信息：<?php echo BetCnName::getCnName($data['lid']);?><?php if($orderType!=5){ ?>第<?php echo $data['issue'];?>期<?php } ?></h2>
            <p class="buy-time">购买时间：<?php echo date('Y', $datetime)."年".date('m', $datetime)."月".date('d', $datetime)."日 ".date('H:i:s', $datetime);?></p>
            <p class="order-num">订单编号：<?php echo $data['orderId'];?></p>
            <span class="total-money"id="total_money" data-totalMoney='<?php echo ParseUnit($data['money'], 1);?>' >总金额：<b><?php echo ParseUnit($data['money'], 1);?></b>元</span>
        </div>
        <div class="cp-box-bd">
            <div class="recharge-form pay-form">
                <div class="balance">
                    <span class="pay-nm">支付 <em class="money"><?php echo ParseUnit($money, 1);?></em> 元</span>
                    <label for="checkbox-balance">
                        <input type="checkbox" class="ipt_checkbox" name="checkbox-balance" id="checkbox-balance" checked>余额支付
                    </label>
                    账户<strong class="account"><?php echo $this->uname;?></strong>余额 <em class="money" id="remain_money" data-balance='<?php echo ParseUnit($money, 1);?>'><?php echo ParseUnit($money, 1);?></em> 元
                </div>
                <form class="form">
                    <span class="recharge-form-side pay-nm">支付 <b class="money recharge_money" id="need_recharge"><?php echo ParseUnit($data['money']-$money, 1);?></b> 元</span>
                    <div class="form-item">
                        <label class="form-item-label" style="margin-top: 4px;">请选择支付方式：</label>
                        <div class="form-item-con">
                            <div class="tab-nav">
                                <ul>
                                    <li class="active"><a href="javascript:;"><span>微信支付</span></a></li>
                                    <li><a href="javascript:;"><span>支付宝支付</span></a></li>
                                    <li><a href="javascript:;"><span>快捷支付</span></a></li>
                                    <li><a href="javascript:;"><span>网上银行</span></a></li>
                                    <li><a href="javascript:;"><span>信用卡</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-item" style="display: block;">
                            <div class="form-item">
                                <label class="form-item-label" style="margin-top: 5px;">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list">
                                        <ul>
                                            <li class="selected"><img src="../caipiaoimg/v1.1/img/bank/wxzf.png" width="128" height="38" alt="微信支付"><i class="s_yes"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-item">
                            <!--<div class="form-item">
                                <label class="form-item-label" style="margin-top: 5px;">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list">
                                        <ul>
                                            <li class="selected"><img src="../caipiaoimg/v1.1/img/bank/zfbzf.png" width="128" height="38" alt="支付宝"><i class="s_yes"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>-->
                            <!--维护中-->
                            <div class="m-vc">
                                <div class="m-vc-l">
                                    <img src="../caipiaoimg/v1.1/images/icon-weihu.png" width="90" height="90">
                                </div>
                                <div class="m-vc-r">
                                    <p class="m-vc-title">微信支付系统升级维护中</p>
                                    <p>为不影响您购彩，请先使用其他支付方式</p>
                                </div>
                                <s></s>
                            </div>        
                        </div>
                        <div class="tab-item">
                            <div class="form-item">
                                <label class="form-item-label" style="margin-top: 5px;">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list">
                                        <ul>
                                            <li class="selected"><img src="../caipiaoimg/v1.1/img/bank/ybzf.png" width="128" height="38" alt="易宝支付"><i class="s_yes"></i></li>
                                            <li><img src="../caipiaoimg/v1.1/img/bank/ttf.png" width="117" height="38" alt="统统付"><i class="s_yes"></i></li>
                                            <li><img src="../caipiaoimg/v1.1/img/bank/llzf.png" width="132" height="38" alt="连连支付"><i class="s_yes"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-item">
                            <div class="form-item">
                                <label class="form-item-label">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list m-choose" data-rule='{"name": "银行", "num": 8}'>
                                        <ul>
                                            <li class="selected"><img title="中国工商银行" alt="中国工商银行" src="../caipiaoimg/v1.1/img/bank/gsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国邮政储蓄银行" src="../caipiaoimg/v1.1/img/bank/yzcxyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国农业银行" src="../caipiaoimg/v1.1/img/bank/nyyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="招商银行" src="../caipiaoimg/v1.1/img/bank/zsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国建设银行" src="../caipiaoimg/v1.1/img/bank/jsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国银行" src="../caipiaoimg/v1.1/img/bank/zgyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="交通银行" src="../caipiaoimg/v1.1/img/bank/jtyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国民生银行" src="../caipiaoimg/v1.1/img/bank/msyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="兴业银行" src="../caipiaoimg/v1.1/img/bank/xyyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="光大银行" src="../caipiaoimg/v1.1/img/bank/gdyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="华夏银行" src="../caipiaoimg/v1.1/img/bank/hxyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中信银行" src="../caipiaoimg/v1.1/img/bank/zxyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="广东发展银行" src="../caipiaoimg/v1.1/img/bank/gfyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="上海浦东发展银行" src="../caipiaoimg/v1.1/img/bank/shpdfzyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="北京银行" src="../caipiaoimg/v1.1/img/bank/bjyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="上海银行" src="../caipiaoimg/v1.1/img/bank/shyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="平安银行" src="../caipiaoimg/v1.1/img/bank/payh.png"><i class="s_yes"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-item">
                            <div class="form-item">
                                <label class="form-item-label">充值方式：</label>
                                <div class="form-item-con">
                                    <div class="bank_list m-choose" data-rule='{"name": "银行", "num": 8}'>
                                        <ul>
                                            <li class="selected"><img alt="工商银行" src="../caipiaoimg/v1.1/img/bank/gsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="招商银行" src="../caipiaoimg/v1.1/img/bank/zsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国建设银行" src="../caipiaoimg/v1.1/img/bank/jsyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国银行" src="../caipiaoimg/v1.1/img/bank/zgyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中国民生银行" src="../caipiaoimg/v1.1/img/bank/msyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="光大银行" src="../caipiaoimg/v1.1/img/bank/gdyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="上海银行" src="../caipiaoimg/v1.1/img/bank/shyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="平安银行" src="../caipiaoimg/v1.1/img/bank/payh.png"><i class="s_yes"></i></li>
                                            <li><img alt="交通银行" src="../caipiaoimg/v1.1/img/bank/jtyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="兴业银行" src="../caipiaoimg/v1.1/img/bank/xyyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="广东发展银行" src="../caipiaoimg/v1.1/img/bank/gfyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="上海浦东发展银行" src="../caipiaoimg/v1.1/img/bank/shpdfzyh.png"><i class="s_yes"></i></li>
                                            <li><img alt="中信银行" src="../caipiaoimg/v1.1/img/bank/zxyh.png"><i class="s_yes"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">充值金额：</label>
                        <div class="form-item-con">
                            <div class="type_list">
                                <ul>
                                    <li class="selected">10元<i class="s_yes"></i></li>
                                    <li>50元<i class="s_yes"></i></li>
                                    <li>100元<i class="s_yes"></i></li>
                                    <li>200元<i class="s_yes"></i></li>
                                    <li>500元<i class="s_yes"></i></li>
                                    <li>1000元<i class="s_yes"></i></li>
                                    <li>2000元<i class="s_yes"></i></li>
                                    <li>3000元<i class="s_yes"></i></li>
                                    <li>4000元<i class="s_yes"></i></li>
                                    <li>5000元<i class="s_yes"></i></li>
                                    <li>10000元<i class="s_yes"></i></li>
                                    <li>15000元<i class="s_yes"></i></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label">其他金额：</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt ipt-money" placeholder="请输入10元以上的整数"><span class="units">元</span>
                        </div>
                    </div>
                    <div class="form-item form-chooseRp">
                        <label class="form-item-label">选择红包：</label>
                        <div class="form-item-con">
                            <div class="hongbao-s m-choose" data-rule='{"name": "红包", "num": 5}'>
                                <ul>
                                    <li class="selected">
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                    <li class="selected">
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                    <li class="selected">
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                    <li>
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                    <li>
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                    <li>
                                        充20送 <b>2</b><span>30天后过期</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <a class="btn btn-main" href="javascript:;">确认预约</a>
                        <p class="btn-group-txt">如选择其他支付方式，确认预约会跳转到对应支付方式页面完成支付</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>