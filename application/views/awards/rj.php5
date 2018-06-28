<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<!--容器-->
<div class="wrap detail-hist mod-box">
    <!--彩票信息-->
    <?php echo $this->load->view('elements/lottery/info_panel'); ?>
    <!--彩票信息end-->

    <!--彩票-->
    <div class="userLottery mod-box-bd">
        <?php $this->load->view('elements/lottery/tabs', array('type' => 'award')); ?>
        <div class="userLotteryBox clearfix">
            <div class="userLotteryOn clearfix">
            第
            <dl class="simu-select">
                <dt><?php echo $issue; ?><i class="arrow"></i></dt>
                <dd class="select-opt">
                    <div class="select-opt-in">
                        <?php foreach ($issueInfo as $key => $value): ?>
                        <a href="<?php echo $baseUrl; ?>awards/rj/<?php echo $value; ?>" ><?php echo $value; ?></a>
                        <?php endforeach; ?>
                    </div>
                </dd>
            </dl>
	        期
            <span>开奖日期：<b><?php echo $awardTime; ?></b></span>
            <span>本期销量：<b><?php echo $awardMoney; ?></b>元</span>
            <span>奖池滚存：<b><?php echo $awardPool; ?></b>元</span>
        </div>
        <div class="userLotteryTable clearfix">
        	<table class="jc-inTable">
                <tbody>
                    <tr class="th-bg-fix">
                        <th width="7%">场次</th>
                        <td width="6%">1</td>
                        <td width="6%">2</td>
                        <td width="6%">3</td>
                        <td width="6%">4</td>
                        <td width="6%">5</td>
                        <td width="6%">6</td>
                        <td width="6%">7</td>
                        <td width="6%">8</td>
                        <td width="6%">9</td>
                        <td width="6%">10</td>
                        <td width="6%">11</td>
                        <td width="6%">12</td>
                        <td width="6%">13</td>
                        <td width="6%" class="last">14</td>
                    </tr>
                    <tr class="text-vertical">
                        <th><span>主队</span></th>
                        <?php foreach ($matchInfo as $matchinfo): ?>
                            <td><span><?php echo $matchinfo['teamName1']; ?></span></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr class="text-vertical">
                        <th><span>客队</span></th>
                        <?php foreach ($matchInfo as $matchinfo): ?>
                            <td><span><?php echo $matchinfo['teamName2']; ?></span></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>彩果</th>
                        <?php foreach ($awardNumber as $key => $awardnumber): ?>
                            <td class="num-winners"><?php echo $awardnumber; ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="jc-inTable">
            <thead>
                <tr>
                    <th width="33%">奖项</th>
                    <th width="33%">中奖注数</th>
                    <th width="34%">单注奖金（元）</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($awards as $award): ?>
                <tr>
                    <th><?php echo $award['awardName']; ?></th>
                    <td><?php echo $award['prizeNumber']; ?></td>
                    <td><?php echo $award['prize']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="btn-group">
            <a href="/rj" class="btn btn-red-med">立即投注</a>  
        </div>
        </div>
    </div>
    <!--彩票end-->
</div>
<script>
    // select
    $("dl[class^='simu-select'] dt").bind('click', function() {
      _this = $(this);
      var dt = _this.parent();
      dt.addClass('selected');
      _this.siblings('.select-opt').find('a').on('click',function(){
        _this.html($(this).html() + '<i class="arrow"></i>');
        dt.removeClass('selected');
      });
    });
    $(document).bind("click", function(e){
      var menu = $(e.target).hasClass('simu-select') || ($(e.target).parents(".simu-select").length > 0);
      if (!menu){
        $(".simu-select").removeClass("selected");
      }
    });
</script>
<!--容器end-->
