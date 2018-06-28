<!--header begin-->
<div class="header header-short header-jf">
  <div class="wrap header-inner">
    <div class="logo">
        <div class="logo-txt">
            <span class="logo-txt-name">166彩票</span>
        </div>
        <a href="<?php echo $baseUrl; ?>" class="logo-img">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo_white.png'); ?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo_white@2x.svg'); ?>" width="280" height="70" alt="">
        </a>
        <h1 class="header-title">会员中心</h1>
    </div>
    <div class="aside">
        <div class="header-nav-jf">
              <a href="<?php echo $baseUrl; ?>member#wdcz" class="cur">我的成长</a>
              <a href="<?php echo $baseUrl; ?>member#wdtq">我的特权</a>
              <a href="<?php echo $baseUrl; ?>member/help">会员帮助</a>
        </div>
    </div>
  </div>
</div>
<!--header end-->
<div class="p-jifen center">
  <div class="overview" id="wdcz">
    <div class="wrap">
      <h2 class="title">我的成长</h2>
      <div class="user-info">
        <div class="avatar">
        <img src="<?php echo $info['headimgurl']?$info['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>" width="50" height="50" alt="">
        </div>
        <div class="user-info-txt">
          <span class="user-name"><?php echo $this->uname;?><i class="icon-lv v<?php echo $info['grade'];?>"></i></span>
          <?php if ($info['grade']<=5): ?>
            <span>您已打败<em><?php echo $info['rank']/100; ?>%</em>用户，还差 <b><?php echo $info['next_grade_value'] -$info['grade_value'] ; ?></b> 成长值即可享受<?php echo $info['next_grade_name'] ; ?>特权&nbsp;&nbsp;<a href="<?php echo $baseUrl; ?>member/help" class='sub-color'>查看如何升级>></a></span>
          <?php else: ?> 
           <span>您已打败<em><?php echo $info['rank']/100; ?>%</em>用户&nbsp;&nbsp;<a href="<?php echo $baseUrl; ?>member/help" class='sub-color'>查看如何升级>></a></span> 
          <?php endif ?>
        </div>
      </div>
      <div class="progress">
        <div class="bar">
          <div class="bar-img">
            <div class="bar-img-inner"></div>
          </div>
          <ol class="bar-txt">
           <?php foreach ($level as $k => $v): ?>
            <li class="v<?php echo $v['grade'] ;?>"><?php echo $v['grade_name'];?><i></i></li>
           <?php endforeach ?>
<!--           <?php foreach ($level as $k => $v): ?>
            <li class="v<?php echo $v['grade'] ;?>"><?php echo $v['grade_name'];?><br><?php echo $v['value_end']==0 ? $v['value_start'] : $v['value_start'].'~'.$v['value_end'];?></li>
          <?php endforeach ?> -->
          </ol>
        </div>
        <div class="panel-l">
          <ul>
            <li>成长数值：<?php echo $info['grade_value'] ?></li>
            <li>成长天数：<?php echo $info['grade_days'];?></li>
            <li>成长周期：<?php echo date('Y-m-d',strtotime($info['cycle_start']));?>至<?php echo date('Y-m-d',strtotime($info['cycle_end']));?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="wrap" id='wdtq'>
      <dl>
        <dt>
          <h2 class="title">我的特权</h2>
        </dt>
        <dd>
          <div class="mod-tableA">
            <table>
              <thead>
                <tr>
                  <th width="10%">特权/等级</th>
                  <?php foreach ($level as $k => $v): ?>
                  <th width="10%" <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><?php echo $v['grade_name'];?></th>
                  <?php endforeach ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>身份勋章</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><i class="icon-font">&#xe6cb;</i></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>提现特权</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><?php echo $v['privilege']['withdraw'];?>次/日</td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>积分兑换</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><i class="icon-font"><?php echo $v['privilege']['exchange'] ? ($info['grade']== $v['grade'] ? "<a href='".$baseUrl.'point#jfdh'."' target='_blank'>兑换礼包></a>" : '&#xe6cb;')  : '' ;?></i></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>升级礼包</td>
                  <?php foreach ($level as $k => $v): ?>
                    <td <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><?php echo implode('<br/>', $v['privilege']['upgrade']);?></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>生日红包</td>
                  <?php foreach ($level as $k => $v): ?>
                    <td <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><?php echo implode('<br/>', $v['privilege']['birthday']);?></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>购彩积分双倍</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td  <?php echo $info['grade']== $v['grade'] ? 'class="cur"' : '' ;?> ><i class="icon-font"><?php echo $v['privilege']['double'] ? '&#xe6cb;' : '' ;?></i></td>
                  <?php endforeach ?>
                </tr>
              </tbody>
            </table>
          </div>
        </dd>
        <dt>
          <h2 class="title">全部特权</h2>
        </dt>
        <dd>
          <ul class="right">
            <li class="sfxz"><strong>身份勋章</strong><span>等级身份勋章彰显会员尊贵身份</span></li>
            <li class="txtq"><strong>提现特权</strong><span>会员提款免手续费，不同会员每天支持提现3/4/5/8次</span></li>
            <li class="jfdh"><strong>积分兑换</strong><span>青铜及以上会员享受积分商城超值兑换特权<a href="<?php echo $baseUrl; ?>point#jfdh" target="_blank" class='sub-color'>马上兑换礼包></a></span></li>
            <li class="sjlb"><strong>升级礼包</strong><span>白银及以上会员升级即可领取16元/66元/266元/1666元不等的升级礼包</span></li>
            <li class="srlb"><strong>生日礼包</strong><span>黄金及以上会员生日（以实名身份信息为准）当天会获得超值满100减20（通用）/满500减100（通用）/满1000减200（通用）不等的红包</span></li>
            <li class="jfsb"><strong>积分双倍</strong><span>钻石会员享受双倍购彩积分特权</span></li>
          </ul>
        </dd>
      </dl>
  </div>
</div>
<script>
!function (lv, num) {
var arr = [80, 200, 340, 500, 710, 960],
  numArr = [<?php foreach ($level as $k => $v): ?><?php echo $v['value_end']==0? 2000000 : $v['value_end'].',';?><?php endforeach ?>],
  elPanel = $('.progress').find('.panel-l'),
  elImg = $('.progress').find('.bar-img'),
  elImgInner = elImg.find('.bar-img-inner'),
  elImgWidth = elImg.width(),
  jl = 0,
  jlBg = 0,
  bgP = '';
if (lv > numArr.length) {
  lv = numArr.length;
}
var idx = lv - 1;
if (num > numArr[idx]) {
  num = numArr[idx];
}
if (idx === 0) {
  jl = Math.ceil(num / numArr[idx] * arr[idx]);
} else {
  jl = Math.ceil(num / numArr[idx] * (arr[idx] - arr[idx - 1]));
  jl += arr[idx - 1];
}

if (num / numArr[idx] < .1) {
  jl += 1;
}
var PB =  jl / elImgWidth;
if (PB > 0.35 && PB < 0.66) {
  bgP = '-266px -84px';
  jlBg = jl - 94;
} else if (PB > 0.66) {
  bgP = '-512px -84px';
  jlBg = jl - 188;
} else {
  bgP = '-20px -84px';
  jlBg = jl;
}

elPanel.css({
  'left': jlBg + 'px',
  'background-position': bgP
})
elImgInner.css({
  'width': jl + 'px'
})
}(<?php echo $info['grade'];?>, <?php echo $info['grade_value'];?>);
</script>

