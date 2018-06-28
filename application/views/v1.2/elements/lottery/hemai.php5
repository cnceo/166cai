<div class="buy-together">
	<form class="form">
		<div class="form-item"><label for="" class="form-item-label">截止时间：</label><div class="form-item-con hmendTime"><span class="form-item-txt"></span></div></div>
		<div class="form-item">
			<label for="" class="form-item-label">投注信息：</label>
			<div class="form-item-con"><span class="form-item-txt"><em class="main-color-s betNum">0</em> 注，<em class="main-color-s Multi"><?php echo $multi?></em> 倍，共计 <em class="main-color-s betMoney">0</em> 元</span></div>
		</div>
		<div class="form-item">
			<label for="" class="form-item-label">盈利佣金：</label>
			<div class="form-item-con"><div class="commission"><ol><li class="cur" data-val='0'>无</li><?php for ($i = 1; $i <= 10; $i++) {?><li data-val='<?php echo $i?>'><?php echo $i?>%</li><?php }?></ol><s>若方案盈利，盈利佣金=税后奖金*佣金比例</s></div></div>
		</div>
		<div class="form-item">
			<label for="" class="form-item-label"><em class="main-color-s">*</em>我要认购：</label>
			<div class="form-item-con buyMoney"><input type="text" class="form-item-ipt" value='0'>元<span class="small">发起人至少认购<em class="main-color-s">5</em>%<u style="display: none">（已认购约<em class="main-color-s">5</em>%）</u></span></div>
		</div>
		<div class="form-item">
			<label for="" class="form-item-label">我要保底：</label>
			<div class="form-item-con guarantee">
				<input type="text" class="form-item-ipt" value='0'>元
				<span class="small">
					<label><input type="checkbox" class="form-item-checkbox guaranteeAll">全额保底</label>最多可保底
                    <em class="main-color-s">0</em>元<i class="icon-font bubble-tip" tiptext="<em>保底：</em>合买保底发起时，系统将会先从发起人帐<br>户内扣除相应的金额，若合买失败或者合买未全<br>部跟满，则会使用设置的保底金额自动购买未满<br>部分，以保证合买成功，若设置的保底金额还有<br>剩余资金，将自动退回到发起人的帐户内。">&#xe613;</i>
                    <u>（已保底约<em class="main-color-s">0</em>%）</u>
				</span>
			</div>
		</div>
		<div class="form-item">
			<label for="" class="form-item-label">保密设置：</label>
			<div class="form-item-con">
				<span class="form-item-txt bmsz">
					<label><input name="bmsz" type="radio" value="0" class="form-item-radio" checked>公开</label>
					<label><input name="bmsz" type="radio" value="1" class="form-item-radio">仅对跟单者公开</label>
					<label><input name="bmsz" type="radio" value="2" class="form-item-radio">截止后公开</label>
				</span>
			</div>
		</div>
	</form>
	<div class="buy-together-foot">您需支付：<span class='buy_txt'><em class="main-color-s">0</em> 元 <span>（认购0元+保底0元）</span></span><s>注：方案进度+保底>=95%，即可出票</s></div>
 </div>