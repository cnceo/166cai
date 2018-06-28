<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/user.css'); ?>">
<div class="wrap_in uc-container">
    <!--mod-account begin-->
    <div class="mod-account clearfix">
        <!-- 用户头像 -->
        <div class="avatar" id="J_uc-avatar">
            <a href="http://my.2345.com/member/avatar.php?forward=http://<?php echo DOMAIN;?>/<?php echo $this->con;?>/<?php echo $this->act;?>" target="_blank" class="avatar-modify" title="点击更换头像"></a>
            <img width="80px" height="80px" src="http://my.2345.com/member/avatar/<?php echo ceil($this->uinfo['passid'] / 3000); ?>/<?php echo $this->uinfo['passid']; ?>_middle.jpg?<?php echo time();?>" onerror="this.src='<?php echo getStaticFile('/caipiaoimg/v1.0/img/avatar/default-avatar.png'); ?>'" alt="">
            <span class="avatar-mask"></span>
            <span class="avatar-txt" style="display: none;">更换头像</span>
        </div>
        <!-- 用户头像 end -->
        <!-- 账户信息 -->
        <div class="infor">
            <div class="welcome">欢迎您，<span class="name"><?php echo $this->uname; ?></span> <?php if(in_array(mb_substr($this->uname, 0, 4), array('qq用户', '微博用户', '手机用户'))):?><a href="http://my.2345.com/member/editUser">完善用户名</a><?php endif;?></div>
            <div class="safety-level level-<?php echo $this->safe_level[0] ?>">安全等级：
                <span class="safety-progess"></span><b class="safety-status"><?php echo $this->safe_level[1] ?></b>
                <?php if (!($this->con == 'safe' && $this->act == 'index')): ?>
                    <a href="/safe">提升指数</a>
                <?php endif; ?>
            </div>
            <ul class="save-info clearfix">
                <?php if ($this->uinfo['phone']): ?>
                    <li class="binded"><a target="_self" href="/safe/"><i class="icon icon-phone"></i>已绑定手机</a></li>
                <?php else: ?>
                    <li><a href="http://my.2345.com/member/bindPhone?forward=http://caipiao.2345.com/safe/" target="_blank"><i class="icon icon-phone"></i>未绑定手机</a></li>
                <?php endif; ?>
                <?php if ($this->uinfo['id_card']): ?>
                    <li class="binded"><a target="_self" href="/safe/"><i class="icon icon-idCard"></i>已验证身份</a></li>
                <?php else: ?>
                    <li><a href="/safe/idcard" target="_self"><i class="icon icon-idCard"></i>未验证身份</a></li>
                <?php endif; ?>
                <?php if ($this->bankInfo): ?>
                    <li class="binded"><a target="_self" href="/safe/bankcard"><i class="icon icon-bankCard"></i>已绑定银行卡</a></li>
                <?php else: ?>
                    <li><a  href="/safe/bankcard" target="_self"><i class="icon icon-bankCard"></i>未绑定银行卡</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- 账户信息 end -->
        <!-- 账户资金 -->
        <div class="funds">
            <p class="first">账户余额：<b class="n-money"><?php echo number_format(ParseUnit($this->uinfo['money'], 1), 2) ?></b>元</p>
            <p class="mid">冻结资金：<b class="arial"><?php echo number_format(ParseUnit($this->uinfo['blocked'], 1), 2) ?></b>元<a href="javascript:;" id="rip-price-swich" class="icon q-tip"></a></p>
            <p class="btn-group"><a href="/wallet/recharge" class="btn btn-recharge">充值</a><a href="/wallet/withdraw" class="btn btn-withdraw">提款</a></p>
            <div style="left: 78px; top: 46px; display: none;" id="rip-price" class="ui-poptip">
                <div class="ui-poptip-container">
                    <div class="ui-poptip-arrow-top"> <i>◆</i> <span>◆</span> </div>
                    冻结资金是您申请使用中的资金。交易结束前，该笔资金不可用于其他交易。
                </div>
            </div>
        </div>
        <!-- 账户资金 end -->
        <!-- 网站公告 -->
        <div class="affiche">
            <h3><a href="<?php echo $baseUrl;?>notice/index" target="_blank">网站公告</a></h3>
            <?php
            $noticeInfo = $this->Notice_Model->noticeList(array('status' => 1), 0, 3);
            if ($noticeInfo) :
                foreach ($noticeInfo as $v) :
                    ?>
                    <p><i></i><a href="<?php echo $baseUrl; ?>notice/detail/<?php echo $v['id']; ?>" target="_blank"><?php echo $v['title']; ?></a></p>
                <?php endforeach;
            endif; ?>
        </div>
        <!-- 网站公告 end -->
    </div>
    <!--mod-account end-->
    <div class="uc-section clearfix">
        <div class="aside">
            <dl class="uc-side-nav">
                <dt class="my-lottery"><i></i>我的彩票</dt>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'account'): ?>class="current"<?php endif; ?>><a href="/mylottery/account">我的账户<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'betlog'): ?>class="current"<?php endif; ?>><a href="/mylottery/betlog">投注记录<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && in_array($this->act, array('detail', 'recharge', 'withdrawals'))): ?>class="current"<?php endif; ?>><a href="/mylottery/detail">账户明细<i></i></a></dd>
                <dt class="my-safety"><i></i>账户安全</dt>
                <dd <?php if ($this->con == 'safe' && $this->act == 'index'): ?>class="current"<?php endif; ?>><a href="/safe/">安全中心<i></i></a></dd>
                <dd <?php if ($this->con == 'safe' && $this->act == 'baseinfo'): ?>class="current"<?php endif; ?>><a href="/safe/baseinfo">基本资料<i></i></a></dd>
                <dd <?php if ($this->con == 'safe' && $this->act == 'paypwd'): ?>class="current"<?php endif; ?>><a href="/safe/paypwd/">密码管理<i></i></a></dd>
                <dd <?php if ($this->con == 'safe' && $this->act == 'bankcard'): ?>class="current"<?php endif; ?>><a href="/safe/bankcard/">银行卡管理<i></i></a></dd>
            </dl>
        </div>