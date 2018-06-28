<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/user.min.css'); ?>">
<script type="text/javascript">
var rfshbind = <?php echo isset($rfshbind) ? $rfshbind : 0 ?>;
<?php if($this->uinfo['nick_name_modify_time'] == '0000-00-00 00:00:00'):?>
$(function(){
	//修改用户名
	var look = 0;
	$('.J-modifyName').on('click', function () {
		if (!look) {
    		look = 1;
    		$.ajax({
    	        type: 'post',
    	        url: '/pop/modifyName',
    	        data: {version: version},
    	        success: function (response) {
    	          	$('body').append(response);
    	            cx.PopCom.show('.J-popmodifyname');
    	            cx.PopCom.close('.J-popmodifyname');
    	            cx.PopCom.cancel('.J-popmodifyname');
                    look = 0;
    	        }
    	    });
    	}else {
    		$('.J-popmodifyname').show();
    	}
	})
});

<?php endif;?>
</script>
<div class="wrap_in uc-container">
<div class="l-frame">
    <!--mod-account begin-->
    
    <div class="mod-account clearfix">
        <!-- 用户头像 -->
        <div class="avatar" id="J_uc-avatar">
            <!-- <a href="#" target="_blank" class="avatar-modify" title="点击更换头像"></a> -->
            <img width="80px" height="80px" src="<?php echo $this->uinfo['headimgurl']?$this->uinfo['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>" onerror="this.src='<?php echo $this->uinfo['headimgurl']?$this->uinfo['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>'" alt="">
            <span class="avatar-mask"></span>
            <span class="avatar-txt" style="display: none;">更换头像</span>
        </div>
        <!-- 用户头像 end -->
        <!-- 账户信息 -->
        <div class="infor">
            <div class="welcome">
                <span class="user-name" alt="<?php echo $this->uname;?>" title="<?php echo $this->uname;?>"><?php  echo mb_strlen($this->uname)>7 ? mb_substr($this->uname,0,7,'utf-8').'...' : $this->uname; ?><a href="<?php echo $baseUrl; ?>member" target="_blank" class="icon-lv v<?php echo $this->uinfo['grade'];?>"></a></span>
                <span class="link-group">
                    <?php if($this->uinfo['nick_name_modify_time'] == '0000-00-00 00:00:00'):?>
                    <a href="javascript:" class="modify-name J-modifyName">修改用户名</a>
                    <?php endif;?>
                    <a href="<?php echo $baseUrl; ?>member#wdtq" target="_blank">查看特权></a>
                </span>
            </div>
            
            <div class="safety-level level-<?php echo $this->safe_level[0] ?>">安全等级：
                <span class="safety-progess"></span><b class="safety-status"><?php echo $this->safe_level[1] ?></b>
                <?php if (!($this->con == 'safe' && $this->act == 'index')): ?>
                    <!-- <a href="/safe">提升指数</a> -->
                <?php endif; ?>
            </div>
            <ul class="save-info">
                <?php if ($this->uinfo['phone']): ?>
                    <li class="binded"><a href="/safe/" target="_self"><i class="icon-font">&#xe663;</i><span>手机</span></a></li>
                <?php else: ?>
                    <li><a target="_blank" href="/safe/"><i class="icon-font">&#xe663;</i><span>手机</span></a></li>
                <?php endif; ?>
                <?php if ($this->uinfo['id_card']): ?>
                    <li class="binded"><a href="/safe/" target="_self"><i class="icon-font">&#xe665;</i><span>身份证</span></a></li>
                <?php else: ?>
                    <li><a href="/safe/" target="_self"><i class="icon-font">&#xe665;</i><span>身份证</span></a></li>
                <?php endif; ?>
                <?php if ($this->bankInfo): ?>
                    <li class="binded"><a href="/safe/" target="_self"><i class="icon-font">&#xe664;</i><span>银行卡</span></a></li>
                <?php else: ?>
                    <li><a  href="/safe/bankcard" target="_self"><i class="icon-font">&#xe664;</i><span>银行卡</span></a></li>
                <?php endif; ?>
                <?php if ($this->uinfo['email']): ?>
                    <li class="binded"><a href="/safe/" target="_self"><i class="icon-font">&#xe64b;</i><span>邮箱</span></a></li>
                <?php else: ?>
                    <li><a href="/safe/bindEmail" target="_self"><i class="icon-font">&#xe64b;</i><span>邮箱</span></a></li>
                <?php endif; ?>    
            </ul>
        </div>
        <!-- 账户信息 end -->
        <!-- 账户资金 -->
        <div class="funds">
            <p class="first">账户余额：<b class="n-money"><?php echo number_format(ParseUnit($this->uinfo['money'], 1), 2) ?></b>元</p>
            <p class="mid"></p>
             <p class="mid">当前积分：<b class="arial" style="color:#e60000;font-weight: 700;"><?php echo $this->uinfo['points']; ?></b>分<a href="<?php echo $this->baseUrl;?>point#jfdh" target="_blank">兑换礼包</a>
            <p class="btn-group"><a href="javascript:;" class="btn-ss btn-specail btn-recharge">充值</a><a href="/wallet/withdraw" class="btn-ss">提现</a></p>
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

        <div class="l-frame-menu m-menu">
        	<div class="m-menu-bd">
        
            <dl>
                <dt class="my-lottery"><i class="icon"></i>我的彩票</dt>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'betlog'): ?>class="current"<?php endif; ?>><a href="/mylottery/betlog">投注记录<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'chaselog'): ?>class="current"<?php endif; ?>><a href="/mylottery/chaselog">追号记录<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'gendanlog'): ?>class="current"<?php endif; ?>><a href="/mylottery/gendanlog">跟单记录<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && in_array($this->act, array('detail', 'recharge', 'withdrawals'))): ?>class="current"<?php endif; ?>><a href="/mylottery/detail">账户明细<i></i></a></dd>
                <dd <?php if ($this->con == 'mylottery' && $this->act == 'redpack'): ?>class="current"<?php endif; ?>><a href="/mylottery/redpack"><i class="icon-font main-color">&#xe645;</i>红包记录<i></i></a></dd>
                </dl>
                <dl>
                
                <dt class="my-safety"><i class="icon"></i>账户安全</dt>
                <dd <?php if ($this->con == 'safe' && $this->act == 'index'): ?>class="current"<?php endif; ?>><a href="/safe/">安全中心<i></i></a></dd>
                <dd <?php if ($this->con == 'safe' && $this->act == 'baseinfo'): ?>class="current"<?php endif; ?>><a href="/safe/baseinfo">基本资料<i></i></a></dd>
                <!-- <dd <?php if ($this->con == 'safe' && $this->act == 'paypwd'): ?>class="current"<?php endif; ?>><a href="/safe/paypwd/">密码管理<i></i></a></dd> -->
                <dd <?php if ($this->con == 'safe' && $this->act == 'bankcard'): ?>class="current"<?php endif; ?>><a href="/safe/bankcard/">提现银行卡<i></i></a></dd>
            </dl>
            <div class="other-contact" >
            	<?php if (in_array($this->uinfo['rebates_level'], array('1'))): ?>
               	<a href="/rebates/"><i class="icon-font main-color" style="margin-left: -1em;">&#xe618;</i>推广服务</a>
                <?php endif;?>
		    </div>
		    </div>
        </div>