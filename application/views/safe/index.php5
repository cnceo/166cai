<?php $this->load->view('elements/user/menu');?>
        <div class="article safe-center">
            <div class="safe-hd">
				<h2 class="tit">安全中心 （近期由于业务信息调整，如您的个人信息有误，请及时与我们联系。）</h2>
                <?php if( !($is_bank_bind && $is_phone_bind && $is_id_bind && $is_pay_pwd) ): ?>
				<a href="/safe/one" target="_self" class="btn btn-blue-allBind">一键绑定</a>
                <?php endif; ?>
			</div>
			<ul class="safe-info">
				<?php if($this->uinfo['pay_pwd']):?>
				<li class="over">
					<div class="state"><i></i>已设置</div>
					<div class="title">支付密码</div>
					<div class="supply">保障您每一笔资金的安全使用</div>
					<div class="operate">已设置，<a href="/safe/paypwd">修改</a></div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未设置</div>
					<div class="title">支付密码</div>
					<!-- <div class="supply">为确保你的账户安全，请设置支付密码，用于购买彩票付款时输入。</div> -->
					<div class="supply">保障您每一笔资金的安全使用</div>
					<div class="operate"><a href="/safe/paypwd">设置</a></div>
				</li>
				<?php endif;?>
				<?php if($this->uinfo['phone']):?>
				<li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">手机号码</div>
					<div class="supply">您验证的手机：<strong><?php echo cutstr($this->uinfo['phone'], 0, 7);?></strong></div>
					<div class="operate">已绑定，<a target="_blank" href="http://my.2345.com/member/editPhone?forward=http://caipiao.2345.com/safe/">修改</a></div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">手机号码</div>
					<div class="supply">验证后,可用于快速找回登录密码，接收账户余额变动提醒。</div>
					<div class="operate"><a target="_blank" href="http://my.2345.com/member/bindPhone?forward=http://caipiao.2345.com/safe/">绑定</a></div>
				</li>
				<?php endif;?>
				<?php if($this->uinfo['id_card']):?>
				<li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">真实身份</div>
					<div class="supply">真实姓名：<strong><?php echo cutstr($this->uinfo['real_name'], 0, 1);?></strong>，身份证号码：<strong><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></strong></div>
					<div class="operate">已绑定，不能修改</div>
				</li>
				<?php else:?>
				<li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">真实身份</div>
					<div class="supply">实名信息是领奖、提款时核对提款人身份的重要信息,绑定后不能修改。</div>
					<div class="operate"><a href="/safe/idcard">绑定</a></div>
				</li>
				<?php endif;?>
				
				<?php if($this->uinfo['email']):?>
				  <li class="over">
					<div class="state"><i></i>已绑定</div>
					<div class="title">邮箱</div>
					<div class="supply">邮箱：<?php echo $hide_email; ?></div>
					<div class="operate">已绑定，<a target="_blank" href="http://my.2345.com/member/editEmail?forward=http://caipiao.2345.com/safe/">修改</a></div>
				</li>
				<?php else:?>
				  <li>
					<div class="state"><i></i>未绑定</div>
					<div class="title">邮箱</div>
					<div class="supply">用户找回密码，第一时间获得2345优惠消息</div>
					<div class="operate"><a target="_blank" href="http://my.2345.com/member/bindEmail?forward=http://caipiao.2345.com/safe/">绑定</a></div>
				</li>
				<?php endif;?>
			</ul>
			<div class="warm-tip mt30">
				<h3>温馨提示：</h3>
				<p>
					<em class="cOrange">中奖很重要，账户安全更重要，建议您绑定所有安全信息，以保障账户及资金安全。</em>
				</p>
			</div>
		</div> 
<?php $this->load->view('elements/user/menu_tail');?>