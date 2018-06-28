<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/qxc.js');?>"></script>
<script type="text/javascript">
var type = '<?php echo $qxcType; ?>';
var boxCount = <?php echo $boxCount; ?>;
</script>
<!--容器-->
<div class="wrap mod-box">
    <!--彩票信息-->
    <?php $this->load->view('elements/lottery/info_panel', array('noIssue' => true)); ?>
    <!--彩票信息end-->
  <div class="mod-box-bd">
    <!--彩票-->
    <div class="userLottery">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'cast')); ?>
        <div class="userLotteryBox clearfix">
            <?php if ($boxCount > 1): ?>
              <?php for ($i = 1; $i <= $boxCount; ++$i): ?>
                <div class="ball-box-<?php echo $i; ?> clearfix">
                  <div class="fl pre-box">
                      <?php if ($i == 1): ?>
                      <strong class="tagtit tagtit1">选择号码</strong>
                      <div><span>每位至少选择一个号码</span></div>
                      <?php endif; ?>
                      <?php if ($i == 1): ?>
                      <h3>第一位</h3>
                      <?php elseif ($i == 2): ?>
                      <h3>第二位</h3>
                      <?php elseif ($i == 3): ?>
                      <h3>第三位</h3>
                      <?php elseif ($i == 4): ?>
                      <h3>第四位</h3>
                      <?php elseif ($i == 5): ?>
                      <h3>第五位</h3>
                      <?php elseif ($i == 6): ?>
                      <h3>第六位</h3>
                      <?php else: ?>
                      <h3>第七位</h3>
                      <?php endif; ?>
                      <ul class="balls clearfix">
                      	<li>0</li>
                        <li>1</li>
                        <li>2</li>
                        <li>3</li>
                        <li>4</li>
                        <li>5</li>
                        <li>6</li>
                        <li>7</li>
                        <li>8</li>
                        <li>9</li>
                      </ul>
                  </div>
                  <div class="fr">
                      <?php if ($i == 1): ?>
                      <div></div>
                      <?php endif; ?>
                      <dl>
                        <dd>
                          <a class="filter filter-all">全</a>
                          <a class="filter filter-bigs">大</a>
                          <a class="filter filter-smalls">小</a>
                          <a class="filter filter-odds">奇</a>
                          <a class="filter filter-evens">偶</a>
                          <a class="clear-balls">清空</a>
                        </dd>
                      </dl>
                  </div>
                </div>
              <?php endfor; ?>
            <?php else: ?>
              <div class="ball-box-1 clearfix">
                <div class="fl pre-box" style="width:490px;">
                    <div><span><em>每位至少选择一个号码</em></span></div>
                    <ul class="balls clearfix">
                      <li>00</li>
                      <li>01</li>
                      <li>02</li>
                      <li>03</li>
                      <li>04</li>
                      <li>05</li>
                      <li>06</li>
                      <li>07</li>
                      <li>08</li>
                      <li>09</li>
                    </ul>
                </div>
                <div class="fr">
                    <div></div>
                    <dl>
                      <dd>
                        <a class="filter filter-all">全</a>
                        <a class="filter filter-bigs">大</a>
                        <a class="filter filter-smalls">小</a>
                        <a class="filter filter-odds">奇</a>
                        <a class="filter filter-evens">偶</a>
                        <a class="clear-balls">清空</a>
                      </dd>
                    </dl>
                </div>
              </div>
            <?php endif; ?>
        </div>
    </div>
    <!--彩票end-->
    <div class="cast-basket">
      <!--添加到投注列表-->
      <div class="lotteryAdd box-collection">
        <div class="text">
          <div class="hr"></div>
          <span>共<strong class="bet-num">0</strong>注，共<strong class="bet-money">0</strong>元</span>
        </div>
        <div class="btn-pools"><a class="add-basket disabled btn btn-add">添加到投注区<i></i></a></div>
      </div>

      <!--投注列表-->

      <div class="lotteryBetArea">
        <div class="arrTp"></div>
        <dl>
          <dt>投注区</dt>
          <dd class="lotteryList">
            <div class="fl border">
              <div class="scroll">
                <ul class="cast-list">
                </ul>
              </div>
              <div class="history"><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/qxc">查看历史开奖号</a></div>
            </div>
            <div class="fr">
                <a class="rand-cast" data-amount="1">机选1注</a>
                <a class="rand-cast" data-amount="5">机选5注</a>
                <a class="rand-cast" data-amount="10">机选10注</a>
                <a class="wordGray clear-list">清空列表</a>
            </div>
          </dd>
        </dl>
        <dl>
          <dt>倍数</dt>
          <dd class="lotteryMessage">
            <div class="lotteryMessageOne multi-modifier">
                <span class="minus">-</span>
                <p><input class="multi number" type="text" value="1" autocomplete="off"  /></p>
                <span class="plus">+</span>
            </div>
          </dd>
        </dl>
        <dl>
          <dt>合计</dt>
          <dd class="lotteryMessage">
            <div class="lotteryMessageTwo">您选择了<strong class="total-bet-num">0</strong>注，总金额<strong class="total-bet-money">0</strong>元</div>
            <?php if($lotteryConfig[QXC]['status']):?>
            	<div class="lotteryMessageThree"><a id="pd_syydj_buy" class="btn btn-deepRed submit <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a></div>
            <?php else :?>
           		<div class="lotteryMessageThree"><a id="pd_syydj_buy" class="btn btn-disabled <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a></div>
           	<?php endif;?>
            <div class="lotteryMessageFour"><input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment1"><label for="agreenment1">我已阅读并同意</label><a href="javascript:void(0);" class='lottery_pro'>《用户委托投注协议》</a><br><input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment2"><label for="agreenment2">我已阅读并同意</label><a href="javascript:void(0);" class='risk_pro'>《限号投注风险须知》</a></div>
          </dd>
        </dl>
      </div>
      <!--投注信息-->
    </div>
  </div>
</div>
<!--容器end-->
<?php $this->load->view('elements/common/editballs');?>
