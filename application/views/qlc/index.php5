<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/math.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/qlc.js');?>"></script>

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
            <div class="fl pre-box" style="width:690px;">
                <strong class="tagtit tagtit1">选择号码</strong>
                <strong class="tagtit tagtit2" style="top:158px;">复式机选</strong>
                <div>
                    <span>至少选择7个球</span>
                </div>
                <ul class="balls clearfix">
                    <li>01</li>
                    <li>02</li>
                    <li>03</li>
                    <li>04</li>
                    <li>05</li>
                    <li>06</li>
                    <li>07</li>
                    <li>08</li>
                    <li>09</li>
                    <li>10</li>
                    <li>11</li>
                    <li>12</li>
                    <li>13</li>
                    <li>14</li>
                    <li>15</li>
                    <li>16</li>
                    <li>17</li>
                    <li>18</li>
                    <li>19</li>
                    <li>20</li>
                    <li>21</li>
                    <li>22</li>
                    <li>23</li>
                    <li>24</li>
                    <li>25</li>
                    <li>26</li>
                    <li>27</li>
                    <li>28</li>
                    <li>29</li>
                    <li>30</li>
                </ul>
                <div>
                    <select class="rand-count">
                        <?php for ($i = 7; $i <= 15; ++$i): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <a class="rand-select">机选号码</a>
                    <a class="clear-balls">清空</a>
                </div>
            </div>
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
              <div class="history"><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/qlc">查看历史开奖号</a></div>
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
              <span class="plus" data-max="50">+</span>
          </div>
          </dd>
        </dl>
        <dl>
          <dt>合计</dt>
          <dd class="lotteryMessage">
            <div class="lotteryMessageTwo">您选择了<strong class="total-bet-num">0</strong>注，总金额<strong class="total-bet-money">0</strong>元</div>
            <?php if($lotteryConfig[QLC]['status']):?>
            	<div class="lotteryMessageThree"><a id="pd_ssq_buy" class="btn btn-deepRed submit <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a></div>
            <?php else :?>
           		<div class="lotteryMessageThree"><a id="pd_ssq_buy" class="btn btn-disabled <?php if (!$isLogin) echo 'not-login'; ?><?php echo $showBind ? ' not-bind': '';?>">确认投注</a></div>
           	<?php endif;?>
            <div class="lotteryMessageFour"><input class="ipt_checkbox" type="checkbox"  checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label><a href="javascript:;" class='lottery_pro'>《用户委托投注协议》</a></div>
          </dd>
        </dl>
      </div>
    </div>
  </div>
</div>
<!--容器end-->
<?php $this->load->view('elements/common/editballs');?>
