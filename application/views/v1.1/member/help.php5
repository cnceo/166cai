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
            <a href="<?php echo $baseUrl; ?>member#wdcz" >我的成长</a>
            <a href="<?php echo $baseUrl; ?>member#wdtq">我的特权</a>
            <a href="javascript:;" class="cur" >会员帮助</a>
        </div>
    </div>
  </div>
</div>
<!--header end-->
<div class="p-jifen help">
  <div class="wrap">
    <dl>
      <dt>一、会员成长体系</dt>
      <dd>
        <p>用户通过注册后均自动加入会员成长体系。体系共包含6个阶段，具体成长阶段取决于会员近一年内累积的成长值，当您最近一年累积的成长值达到上一级（或多级）要求即可升级。 <a href="<?php echo $baseUrl; ?>member/help#sjbj">查看升、降、保级规则></a></p>
        <table style="width: 100%; margin-top: 20px;">
          <tbody>
            <tr>
              <td><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/rank_tubiao.png'); ?>" width="540" height="304" alt=""></td>
              <td class="tac">
                <div class="mod-tableA">
                  <table>
                    <thead>
                      <tr>
                        <th width="78">等级</th>
                        <th width="120">称号</th>
                        <th width="158">成长值</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($level as $k => $v): ?>
                      <tr>
                        <td>V<?php echo $v['grade'];?></td>
                        <td><?php echo $v['grade_name'];?></td>
                        <td><?php echo $v['value_end']==0 ? $v['value_start'].'及以上' : $v['value_start'].'~'.$v['value_end'];?></td>
                      </tr>
                    <?php endforeach ;?>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </dd>
      <dt>二、成长值获得</dt>
      <dd>
        <p>成长值是依据彩民购彩金额和活跃程度来计算经验值，具体如下：</p>
        <div class="mod-tableA">
          <table>
            <thead>
              <tr>
                <th width="160">用户行为</th>
                <th width="140">成长值</th>
                <th width="240">说明</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>购彩实付1元</td>
                <td style="color: #f00;">+1</td>
                <td>每日上限5000</td>
              </tr>
              <tr>
                <td>每日首次登录</td>
                <td style="color: #f00;">+5</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="strong"><strong>1元=1成长值，细则如下：</strong></p>
        <ol>
          <li>1.自购方案在出票成功后发放成长值；</li>
          <li>2.追号方案分期发放成长值，在追号方案出票后发放，如中途停追的期数不给成长值；</li>
          <li>3.合买方案在方案出票后根据实际认购金额发放对应的成长值；</li>
          <li>4.合买发起人以实际保底金额发放成长值；</li>
          <li>5.使用红包以实付金额发放成长值，彩金消费发放成长值；</li>
          <li>6.成长值均为正整数，如出现小数则退位取整。</li>
        </ol>
      </dd>
      <dt>三、会员特权</dt>
      <dd>
          <div class="mod-tableA">
            <table>
              <thead>
                <tr>
                  <th width="8%">特权/等级</th>
                  <?php foreach ($level as $k => $v): ?>
                  <th width="8%"><?php echo $v['grade_name'];?></th>
                  <?php endforeach ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>身份勋章</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td><i class="icon-font">&#xe6cb;</i></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>提现特权</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td><?php echo $v['privilege']['withdraw'];?>次/日</td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>积分兑换</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td <?php if($k==3) { echo 'width="10%"';};?> ><i class="icon-font"><?php echo $v['privilege']['exchange'] ? '&#xe6cb;' : '' ;?></i></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>升级礼包</td>
                  <?php foreach ($level as $k => $v): ?>
                    <td <?php if($k==4) { echo 'width="10%"';};?> ><?php echo implode('<br/>', $v['privilege']['upgrade']);?></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>生日红包</td>
                  <?php foreach ($level as $k => $v): ?>
                    <td <?php if($k==5) { echo 'width="10%"';};?> ><?php echo implode('<br/>', $v['privilege']['birthday']);?></td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td>购彩积分双倍</td>
                  <?php foreach ($level as $k => $v): ?>
                  <td><i class="icon-font"><?php echo $v['privilege']['double'] ? '&#xe6cb;' : '' ;?></i></td>
                  <?php endforeach ?>
                </tr>
              </tbody>
            </table>
          </div>
      </dd>
      <dt id='sjbj'>四、升、降、保级规则</dt>
      <dd>
        <ul>
          <li>
            <p class="strong"><strong>会员升级</strong></p>
            <p>当您最近一年内累积的成长值达到上一级别（或者多级别）要求时即可升级，同时按照升级日开始计算下一个年度成长值，升级后超出当前等级最低要求的成长值将顺延至下一个年度。</p>
          </li>
          <li>
            <p class="strong"><strong>会员保级</strong></p>
            <p>当您最近一年内累积的成长值未能达到升级要求，但只要您达到当前等级的最低成长值，您的级别即可顺延一年，同时按照保级日开始计算下一个年度成长值，保级后超出当前等级最低要求的成长值将顺延至下一个年度。</p>
          </li>
          <li>
            <p class="strong"><strong>会员降级</strong></p>
            <p>当您最近一年内累积的成长值不能满足升级或保级要求，您的级别将下调一个级别，同时按照降级日重新计算下一个年度成长值。</p>
          </li>
          <li>
            <p class="strong"><strong>重要提示</strong></p>
            <p>会员成长体系上线后，2018年1月23日前已注册的用户，自2018年1月23日起首次登录之日开始计算成长值，初始等级为新手彩民，初始成长值为0；2018年1月23日后注册的用户，自注册之日起开始计算成长值，初始等级为新手彩民，初始成长值为0。</p>
          </li>
        </ul>
      </dd>
    </dl>
  </div>
</div>


