<!--容器-->
<div class="wrap mod-box jingcai">
    <?php echo $this->load->view('elements/lottery/info_panel'); ?>

    <!--彩票-->
    <div class="userLottery mod-box-bd">
    	<?php $this->load->view('elements/lottery/tabs', array('type' => 'award')); ?>
      <div class="userLotteryBox clearfix">
        <div class="userLotteryOn clearfix">
          <strong>日期</strong>
          <dl class="simu-select">
            <dt><?php echo date('Y.m.d', strtotime( $date )); ?><i class="arrow"></i></dt>
            <dd class="select-opt">
              <div class="select-opt-in">
                <?php foreach ($dates as $key => $value): ?>
                <a href="<?php echo $baseUrl; ?>awards/jczq/<?php echo $value; ?>" ><?php echo $key; ?></a>
                <?php endforeach; ?>
              </div>
            </dd>
          </dl>
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
          </div>
          <table class="lotteryTable">
              <tr>
                <th width="8%">编号</th>
                <th width="10%">赛事</th>
                <th width="15%">比赛时间</th>
                <th width="12%">主队</th>
                <th width="8%">比分</th>
                <th width="12%">客队</th>
                <th width="4%">让球</th>
                <th width="12%">比赛结果</th>
                <th width="9%">进球</th>
                <th width="10%">半全场</th>
              </tr>
              <?php if(!empty($matches)):?>
              <?php foreach ($matches as $match): ?>
                <?php
                  $spfBg = 'bgBlue';
                  $rqspfBg = 'bgBlue';

                  if( $match['spf'] == '胜' ){
                    $spfBg = 'bgRed';
                  }elseif( $match['spf'] == '负' ){
                    $spfBg = 'bgGreen';
                  }else{
                    $spfBg = 'bgBlue';
                  }

                  if( $match['rqspf'] == '胜' ){
                    $rqspfBg = 'bgRed';
                  }elseif( $match['rqspf'] == '负' ){
                    $rqspfBg = 'bgGreen';
                  }else{
                    $rqspfBg = 'bgBlue';
                  }

                ?>
              <tr>
                <td rowspan="2"><?php echo $match['matchId']; ?></td>
                <td rowspan="2"><?php echo $match['name']; ?></td>
                <td rowspan="2"><?php echo $match['end_sale_time']; ?></td>
                <td rowspan="2"><?php echo $match['home']; ?></td>
                <td rowspan="2" class="cRed score"><?php echo $match['score']; ?></td>
                <td rowspan="2"><?php echo $match['awary']; ?></td>
                <td>0</td>
                <td class="<?php echo $spfBg; ?>"><?php echo $match['spf']; ?></td>
                <td rowspan="2" class="bgYellow"><?php echo $match['jqs']; ?></td>
                <td rowspan="2" class="bgYellow"><?php echo $match['bqc']; ?></td>
              </tr>
              <tr>
                <td><?php echo $match['let']; ?></td>
                <td class="<?php echo $rqspfBg; ?>"><?php echo $match['rqspf']; ?></td>
              </tr>
          	<?php endforeach; ?>
          	<?php else:?>
          	  <tr>
                <th width="100%" colSpan='10' class="lotteryTable-none">暂时无赛事</th>
              </tr>
          	<?php endif;?>
          </table>
        </div>
    </div>
    <!--彩票end-->


</div>
<!--容器end-->
