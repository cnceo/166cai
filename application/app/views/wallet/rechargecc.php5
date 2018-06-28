<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <meta http-equiv="Pragma" content="no-cache">
    <title>充值</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
    <style>
    .test-transform {
        position: absolute;
        left: 50%;
        bottom: 50%;
        width: 280px;
        height: 40px;
        background: red;
        -webkit-transform: translate3d(-50%, -50%, 0);
    }
    </style>
</head>
<body>
    <div class="wrapper recharge">
        <section class="cp-box cp-box-b recharge-area">
            <!-- <div class="cp-box-hd">
                <h1 class="cp-box-title">账户余额：<?php echo $money;?>元</h1>
            </div> -->
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label for="rechargeNumIpt">充值金额</label>
                        <input name="rechargeMoney" type="tel" id="rechargeNumIpt" class="recharge-num-ipt" min="1" maxlength="8" placeholder="请至少充值2元" value="2"><span>元</span>
                    </div> 
                </li>
            </ul>
            
            <ul class="recharge-num">
                <li>10元</li><li>20元</li><li>50元</li><li>100元</li><li>200元</li>
            </ul>
        </section>
        <?php if(!empty($redpackData)):?>
        <!-- 红包详情 -->
        <section class="cp-box m-redPackets">
            <header class="cp-box-hd">
                <h1 class="cp-box-title">选择红包（可不选择）</h1>
                <span class="notes">您已选择<em id="rNum">0</em>个红包，共<em id="rMoney">0</em>元</span>
            </header>
            <?php if(!empty($redpackData)):?>
            <div class="m-redPackets-bd">
                <div class="m-redPackets-bd-inner">
                <ul>
                    <?php foreach( $redpackData as $key => $items ): ?>
                    <li redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1) . '#' . ParseUnit($items['money'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?>" id="redpackId-<?php echo $items['id']; ?>">
                        <p><?php echo ParseDesc($items['use_desc']);?></p>
                        <p><?php echo ParseEnd($items['valid_end']);?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
                </div>
            </div>
            <?php endif;?>
        </section>
        <input type='hidden' class='' name='redpackNum' value='<?php echo count($redpackData);?>'/>
        <?php endif;?>   
        <section class="cp-box recharge-way">
            <header class="cp-box-hd">
                <h1 class="cp-box-title">选择充值方式</h1>
            </header>
            <div class="recharge-way-group">
	            <ul class="cp-list">
	                <!-- 充值方式 start -->
	                <?php if(!empty($payConfig)):?>
	                <?php foreach( $payConfig as $key => $detail ): ?>
	                <li>
	                    <div class="cp-list-txt">
	                        <input type="radio" id="<?php echo $detail['idName']; ?>" name="rechargeWay" pay-data="<?php echo $detail['pay_type']; ?>" secredpack="<?php echo $detail['secredpack']?>"
	                        maxmoney="<?php echo $detail['maxmoney']?>" jsaction="<?php echo $detail['jsaction']?>" <?php echo ($key == 0)?'checked':''; ?>>
	                        <label for="<?php echo $detail['idName']; ?>" class="<?php echo $detail['className']; ?>">
	                            <b>
                                <?php 
                                    echo $detail['title'].($detail['bank_id'] ? "(尾号".substr($detail['bank_id'], -4).")" : ''); 
                                    if ($detail['pay_type'] == '1_13' || $detail['pay_type'] == '8_0') echo '<i>单单随机减，最高减188元</i>';
                                    if ($detail['pay_type'] == '4_0') echo '<i>200元起充</i>';
                                ?>
                                </b>
	                            <?php if ($detail['bank_id']) {?>
	                            <a href='javascript:;' id='change_bankid'>更换银行卡</a>
	                            <?php }else {?>
	                            <small><?php echo $detail['desc']; ?></small>
								<?php }?>
	                        </label>
	                    </div>
	                </li>
	                <?php endforeach; ?>
	                <?php endif; ?>
	                <!-- 充值方式 end -->
	            </ul>
            </div>
        </section>

        <div class="btn-group">
           <a href="javascript:void(0)" class="btn btn-block-confirm" id="btn-block-confirm">充值</a> 
        </div>
        <aside class="recharge-tips">
            <ol>
                <li>1.为防止恶意提现、洗钱等不法行为，信用卡充值不可提现，储蓄卡每笔充值至少50%需用于购彩；</li>
                <li>2.使用充值红包后的单笔充值本金与红包均不可提现；</li>
                <li>3.奖金可以提现，无限制；</li>
            </ol>
        </aside>

        <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
        <input type='hidden' class='' name='redirectPage' value='recharge'/>
        <input type='hidden' class='' name='appVersionCode' value="<?php echo $versionInfo['appVersionCode']?>" />

        <!-- 表单提交 -->
        <form id="doPayForm" action="/app/wallet/doPayForm" method="post">
            <input type='hidden' class='' name='uid' value=''/>
            <input type='hidden' class='' name='trade_no' value=''/>
            <input type='hidden' class='' name='money' value=''/>
            <input type='hidden' class='' name='ip' value=''/>
            <input type='hidden' class='' name='real_name' value=''/>
            <input type='hidden' class='' name='id_card' value=''/>
            <input type='hidden' class='' name='merId' value=''/>
            <input type='hidden' class='' name='configId' value=''/>
            <input type='hidden' class='' name='pay_type' value=''/>
            <input type='hidden' class='' name='token' value=''/>
            <input type='hidden' class='' name='change_bankid' value='0'/>
            <input type='hidden' class='' name='refer' value=''/>
        </form>
    </div>
    <!-- 红包提示 -->
    <div class="ui-alert" id="redpack-alert" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">提示</div>
            <div class="ui-alert-bd">
                <p>充值金额不满足红包使用条件</p>
                <p>请修改充值方案</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="redpack-alert-confirm">确认</a>
            </div>
        </div>
    </div>
    <!-- 红包后台失效提示 -->
    <div class="ui-alert" id="redpack-used" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">提示</div>
            <div class="ui-alert-bd">
                <p>红包已失效，请重新选择。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="redpack-used-confirm">确认</a>
            </div>
        </div>
    </div>
    <!-- 充值金额限制提示 -->
    <div class="ui-alert" id="recharge-limit" style="display: none;">
        <div class="ui-alert-inner">
            <!-- <div class="ui-alert-hd">提示</div> -->
            <div class="ui-alert-bd">
                <p>单笔充值限额<span id="limit-money"></span>元。大额充值请登录网页888.166cai.cn使用网上银行支付。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="recharge-limit-confirm">我知道了</a>
            </div>
        </div>
    </div>
    <!-- 充值是否完成提示 -->
    <div class="ui-confirm" id="recharge-completed" style="display: none;">
        <div class="ui-confirm-inner">
            <div class="ui-confirm-hd">提示</div>
            <div class="ui-confirm-bd" style="text-align:center">
                <p>是否已完成充值？</p>
            </div>
            <div class="ui-confirm-ft">
                <a href="javascript:;" class="" id="recharge-completed-cancel">取消</a>
                <a href="javascript:;" class="special-color" id="recharge-completed-confirm">确认</a>
            </div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/basic.js');?>"  type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/ui/tips/src/tips.js');?>"  type="text/javascript"></script>
    <script charset="utf-8" src="<?php echo getStaticFile('/caipiaoimg/static/js/ui/loading/src/loading.min.js');?>"  type="text/javascript"></script>
    <script charset="utf-8" src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"  type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/app-rechargecc.min.js');?>" ></script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>