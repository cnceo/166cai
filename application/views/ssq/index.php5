<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/ssq.js');?>"></script>

<!--容器-->
<div class="wrap cp-box">
  <!--彩票信息-->
  <?php $this->load->view('elements/lottery/info_panelv11', array('noIssue' => true)); ?>
  <!--彩票信息end-->

  <div class="cp-box-bd bet">
    <div class="bet-main">
      <!-- 数字彩投注区 start -->
      <!-- <div class="userLottery"> -->
        <?php $this->load->view('elements/lottery/tabsv11', array('type' => 'cast')); ?>
        <div class="pick-area">
          <div class="pick-area-tips">
            <p>选号助手</p>
            <a href="javascript:;" class="ptips">遗漏<i></i>
              <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
            </a>
          </div>
          <div class="pick-area-red pre-box">          
            <div class="pick-area-hd">
                <em>红球区</em>至少选择6个红球
            </div>
            <ol class="pick-area-ball balls">
            <?php 
            $i = 0;
            list($red, $blue) = explode("|", $miss); 
            foreach (explode(",", $red) as $m)
            {
            	$i++;?>
            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i><?php echo $m?></i></li>
            <?php }?>
            </ol>
            <div>
                <select class="rand-count">
                    <?php for ($i = 6; $i <= 16; ++$i): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <a class="rand-select">机选红球</a>
                <a class="clear-balls">清空</a>
            </div>
          </div>
          <div class="pick-area-blue post-box">
            <div class="pick-area-hd">
                <em>蓝球区</em>至少选择1个蓝球
            </div>
            <ol class="pick-area-ball">
            <?php 
            $i = 0;
            foreach (explode(",", $blue) as $m)
            {
            	$i++;?>
            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i><?php echo $m?></i></li>
            <?php }?>
            </ol>
            <div>
                <select class="rand-count">
                    <?php for ($i = 1; $i <= 16; ++$i): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <a class="rand-select">机选蓝球</a>
                <a class="clear-balls">清空</a>
            </div>
          </div>
        </div>
      <!-- </div> -->
      <!-- 数字彩投注区 end -->

      <div class="cast-basket">
        <!--添加到投注列表-->
        <div class="bet-solutions box-collection">
          <p><span>您已选中了 <strong class="num-red">0</strong> 个红球， <strong class="num-blue">0</strong> 个蓝球，共 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
          <div class="btn-pools"><a class="add-basket disabled btn-add2bet">添加到投注区<i class="icon-font">&#xe60a;</i></a></div>
        </div>

        <!--投注列表-->
        <div class="bet-area">
            <div class="bet-area-box">
                <ul class="cast-list">
                </ul>
              <!-- <div class="history"><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/ssq">查看历史开奖号</a></div> -->
            </div>
            <div class="bet-area-qbtn">
                <a class="btn-q" href="javascript:;" data-amount="1">机选1注</a>
                <a class="btn-q" href="javascript:;" data-amount="5">机选5注</a>
                <a class="btn-q" href="javascript:;" data-amount="10">机选10注</a>
                <a class="btn-q clear-list" href="javascript:;">清空列表</a>
                <div class="ptips-bd-b">机选一注，试试手气<a href="javascript:;" class="ptips-bd-close">&times;</a><b></b><s></s></div>
            </div>
        </div>
        <div class="bet-area-txt">
          已选<strong class="num-multiple">0</strong>注，投
          <div class="multi-modifier">
            <span class="minus">-</span>
            <label><input class="multi number" type="text" value="1" autocomplete="off"  /></label>
            <span class="plus" data-max="50">+</span>
          </div>
          倍（最大50倍），共计<strong class="num-money">0</strong>元
        </div>
        <div class="btn-group">
          <!-- <a id="pd_ssq_buy" class="btn-betting submit <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a> --> <!-- btn-disabled -->
          <a id="pd_ssq_buy" class="btn-betting <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a>
          <p class="btn-group-txt">
            <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label>
            <a href="javascript:;" class='lottery_pro'>《用户委托投注协议》</a>
          </p>
        </div>
      </div>
    </div>

    <!-- 投注页侧边栏模块 -->
    <div class="bet-side">
      <div class="bet-side-item bet-side-jc">
        <h3>双色球奖池</h3>
        <?php $pool = explode('|', $info['current']['pool'])?>
        <p class="num-jc"><em><b><?php echo $pool[0]?></b>亿<b><?php echo $pool[1]?></b>万</em></p>
        <p>至少可开出<em><?php echo $pool[2]?></em>注500万大奖</p>
      </div>
      <div class="bet-side-item bet-side-notice">
        <h3>开奖公告</h3>
        <div class="ball-group-box">
          <p class="ball-group-title"><a href="<?php echo $baseUrl; ?>kaijiang/ssq" target="_blank" class="lnk-detail">详情></a>双色球第<b><?php echo $info['current']['issue']?></b>期</p>
          <?php $awardArr = explode(":", $info['current']['awardNum'])?>
          <div class="ball-group-s">
          <?php foreach (explode(',', $awardArr[0]) as $award)
          {?>
          	<span><?php echo $award?></span>
          <?php }?>
          <?php foreach (explode(',', $awardArr[1]) as $award)
          {?>
          	<span class="ball-blue"><?php echo $award?></span>
          <?php }?>
          </div>
        </div>
        <table class="table-kj">
          <thead>
            <tr>
              <th>期次</th>
              <th>开奖号码</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($info['kj'] as $kj)
          {
          $awardArr = explode("|", $kj['awardNum'])?>
          	<tr>
              <td><?php echo $kj['issue']?>期</td>
              <td>
              <div class="num-group">
              	<?php foreach (explode(',', $awardArr[0]) as $award){?><span><?php echo $award?></span><?php }?><?php foreach (explode(',', $awardArr[1]) as $award){?><span class="num-blue"><?php echo $award?></span><?php }?>
              </div>
              </td>
            </tr>
          <?php }?>
          </tbody>
        </table>
        <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqzonghe.html" target="_blank" class="lnk-more">更多></a>
      </div>
      <div class="bet-side-item ">
        <h3>走势图表</h3>
        <div class="lnk-chart">
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqzonghe.html" target="_blank">基本走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqhezhi.html" target="_blank">和值走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/jioushuangma.html" target="_blank">红球奇偶走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqdaxiao.html" target="_blank">大小走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqlanqiuzonghe.html" target="_blank">蓝球走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqsanfq.html" target="_blank">红球三区走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqwuxing.html" target="_blank">五行走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqweishudingwei.html" target="_blank">红球尾走势</a>
          <a href="http://caipiao2345.cjcp.com.cn/cjwssq/view/ssqzhihe.html" target="_blank">红球质合走势</a>
        </div>
      </div>
      <div class="bet-side-item">
        <h3>帮助中心</h3>
        <ol class="lnk-help">
        	<li><a href="http://caipiao.2345.com/help/index/b4-f1" target="_blank">1.忘记支付密码怎么办？</a></li>
        	<li><a href="http://caipiao.2345.com/help/index/b2-s1" target="_blank">2.购彩后多久会进行出票？</a></li>
        	<li><a href="http://caipiao.2345.com/help/index/b2-s1" target="_blank">3.是否可以拿到纸质票？</a></li>
          	<li><a href="http://caipiao.2345.com/help/index/b3-s2" target="_blank">4.中奖后如何兑奖？</a></li>
          	<li><a href="http://caipiao.2345.com/help/index/b4-i1" target="_blank">5.2345彩票如何保障购彩安全？</a></li>
         	<li><a href="http://caipiao.2345.com/help/index/b4-i1" target="_blank">6.2345彩票如何完成电话委托购彩？</a></li>
        </ol>
      </div>
    </div>

  </div>
</div>


<!-- 右侧悬浮 bar -->
<div class="bet-sup-bar">
  <a href="http://caipiao.2345.com/activity/kxzhuihao3" target="_blank"><i class="icon-font">&#xe610;</i>投注技巧</a>
  <a href="javascript:;"><i class="icon-font">&#xe60e;</i>意见反馈</a>
</div>
<!--容器end-->

<?php $this->load->view('elements/common/editballs');?>
