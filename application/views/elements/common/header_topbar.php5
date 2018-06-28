<div class="wrap_in">
  <p class="main_menu"><span class="stock">A股上市公司旗下网站，股票代码：002195</span></p>
  <div class="user_info">
      <div class="myaccount">
        <div class="lotery_btn">
          <i class="welcome-txt">欢迎您，</i>
          <a target="_blank" href="/mylottery/account" class="spec"><?php echo $this->uname;?></a>
          <i class="my_lottery"></i>
        </div>
        <div class="drop_down">
            <div class="row1">
                <a target="_blank" href="/wallet/withdraw">提款</a>
                <a target="_blank" href="/wallet/recharge" class="forg">充值</a>
                余额：<span><?php echo number_format(ParseUnit($this->uinfo['money'], 1), 2);?></span>&nbsp;元
            </div>
            <div class="row2">
              <div class="lnk-group">
                <a target="_blank" href="/mylottery/account">我的账户</a><a target="_blank" href="/mylottery/betlog">投注记录</a><a target="_blank" href="/mylottery/detail">交易明细</a>
              </div>
            </div>
            <div class="row3">
                <ul class="save-info clearfix">
                    <?php if($this->uinfo['phone']):?>
                      <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-phone"></i>已绑定</a></li>
                    <?php else:?>
                      <li><i class="icon icon-phone"></i><a href="http://my.2345.com/member/bindPhone?forward=http://caipiao.2345.com/safe/" target="_blank" >未绑定</a></li>
                    <?php endif;?>
                    <?php if($this->uinfo['id_card']):?>
                      <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-idCard"></i>已绑定</a></li>
                    <?php else:?>
                      <li><i class="icon icon-idCard"></i><a href="/safe/idcard" target="_blank">未绑定</a></li>
                    <?php endif;?>
                    <?php if($this->bankInfo):?>
                      <li class="binded"><a target="_blank" href="/safe/"><i class="icon icon-bankCard"></i>已绑定</a></li>
                    <?php else:?>
                      <li><i class="icon icon-bankCard"></i><a href="/safe/bankcard" target="_blank">未绑定</a></li>
                    <?php endif;?>
                </ul>
            </div>
          </div>
      </div>
      	<a class="btn btn-recharge" href="/wallet/recharge">充值</a><span class="lnk-group login-fix">
        <a href="/mynews?cpage=1">我的消息
          <b class="spec"><?php $CI = &get_instance(); $CI->load->model('news_model'); $count = $CI->news_model->countUnreadList($this->uid); echo intval($count[0])?intval($count[0]):''?></b>
        </a><a href="/main/loginout" target="_self">退出登录</a>
      </span>
  </div>
</div>
