<!--header begin-->
<?php
 $ctype = array(0=>'购彩获得',1=>'任务获得',2=>'积分赠送',3=>'兑换红包',4=>'积分过期');
 $dates = array(1=>'一个月内',2=>'最近三个月',3=>'最近六个月',4=>'最近一年');
?>
<?php if(!$this->is_ajax):?>
<script type="text/javascript" src="/caipiaoimg/src/date/WdatePicker.js"></script>
<div class="header header-short header-jf">
  <div class="wrap header-inner">
    <div class="logo">
        <div class="logo-txt">
            <span class="logo-txt-name">166彩票</span>
        </div>
        <a href="/" class="logo-img">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo_white.png'); ?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo_white@2x.svg'); ?>" width="280" height="70" alt="">
        </a>
        <h1 class="header-title">积分商城</h1>
    </div>
    <div class="aside">
        <div class="header-nav-jf">
            <a href="<?php echo $baseUrl; ?>point">积分赚取</a>
            <a href="<?php echo $baseUrl; ?>point#jfdf" >积分兑换</a>
            <a href="<?php echo $baseUrl; ?>point/lists"  class="cur" >积分明细</a>
            <a href="<?php echo $baseUrl; ?>point/help">积分帮助</a>
        </div>
    </div>
  </div>
</div>
<!--header end-->
<div class="p-jifen shop point-form" >
  <div class="wrap">
    <div class="user-info">
      <div class="avatar">  
      <img src="<?php echo $this->uinfo['headimgurl']?$this->uinfo['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>" width="80" height="80" alt="">
      </div>
      <div class="user-info-txt">
        <span class="user-name"><?php echo $this->uname;?><a href="<?php echo $baseUrl; ?>member" target="_blank"><i class="icon-lv v<?php echo $this->uinfo['grade'];?>"></i></a></span>
        <span>当前积分：<em><?php echo $this->uinfo['points'];?></em> 分</span>
      </div>
    </div>
    <h2 class="title">积分明细</h2>
     <form action="" method="post" name='J_jifen'>
      <div class="filter-oper">
        <div class="lArea mr20">
          <!--   <span>交易时间：</span>
       <input class="Wdate vcontent start_time" id="startDate" type="text" value="<?php echo $search['created_s'];?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'endDate\',{y:-1})&&\'2014\'}',maxDate:'#F{$dp.$D(\'endDate\')||\'%y-%M-%d\';}',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="created_s"/>
              <span class="mlr10">至</span>
              <input class="Wdate vcontent end_time" id="endDate" type="text" value="<?php echo $search['created_e']; ?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'startDate\');}',maxDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="created_e"/> -->
            <dl class="simu-select select-small" data-target='submit'>
                <dt>
                  <span class='_scontent' data-value="<?php echo $search['date'];?>" ><?php echo $search['date']==1? '最近一个月': $dates[$search['date']]; ?></span><i class="arrow"></i>
                  <input type='hidden' name='date' class="vcontent" value='<?php echo $search['date'];?>' >
                </dt>
                <dd class="select-opt">
                  <div class="select-opt-in" data-name='date'>
                        <?php foreach ($dates as $k => $v): ?>
                        <a href="javascript:;" data-value="<?php echo $k;?>"><?php echo $v;?></a> 
                        <?php endforeach ?>
                     </div>
                </dd>
            </dl>
          </div>
        <div class="lArea">
          <span class="fl" >交易类型：</span>
          <dl class="simu-select select-small">
                  <dt>
                    <span class='_scontent' data-value="<?php echo $search['ctype'];?>"><?php echo $search['ctype']==''? '所有交易类型':$ctype[$search['ctype']]; ?></span><i class="arrow"></i>
                    <input type='hidden' name='ctype' class="vcontent" value='<?php echo $search['ctype'];?>' >
                  </dt>
                  <dd class="select-opt">
                    <div class="select-opt-in">
                      <a href="javascript:;" data-value="">所有交易类型</a>
                      <?php foreach ($ctype as $k => $v): ?>
                      <a href="javascript:;" data-value="<?php echo $k;?>"><?php echo $v;?></a> 
                      <?php endforeach ?>
                    </div>
                  </dd>
              </dl>
              <a href="javascript:;" class="btn-ss btn-specail submit fl chaxun">查询</a>
        </div>
      </div>  
     </form>
    <div id="container_betlog-form">
      <?php endif;?>
      <table class="mod-tableA">
        <thead>
          <tr>
            <th class="tal" width="16%">交易时间</th>
            <th class="tal" width="20%">交易编号</th>
            <th width="10%">交易类型</th>
            <th width="10%">收入</th>
            <th width="10%">支出</th>
            <th width="15%">当前积分</th>
            <th>备注</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list['res'] as $k => $v): ?>
          <tr>
            <td class="tal"><?php echo $v['created'];?></td>
            <td class="tal"><?php if ($v['ctype']==0): ?>
              <!--普通-->
              <?php if ($v['status']==0): ?>
              <a target="_blank" href="<?php echo $baseUrl; ?>orders/detail/<?php echo $v['orderId'];?>"><?php echo $v['trade_no'];?></a>
              <!--追号-->
              <?php elseif($v['status']==1): ?>
              <a target="_blank" href="<?php echo $baseUrl; ?>orders/detail/<?php echo $v['orderId'];?>"><?php echo $v['trade_no'];?></a>
              <?php elseif($v['status']==2): ?>
              <a target="_blank" href="<?php echo $baseUrl; ?>hemai/detail/hm<?php echo $v['orderId'];?>"><?php echo $v['trade_no'];?></a>
              <?php endif ?>
             <?php else: ?><?php echo $v['trade_no'];?><?php endif ?></td>
            <td><?php echo $ctype[$v['ctype']];?></td>
            <td <?php if ($v['mark'] ==1 && $v['value']>0): ?>class="main-color-s"<?php endif ?> ><?php echo $v['mark'] ==1 ? $v['value'] : 0;?></td>
            <td <?php if ($v['mark'] ==0 && $v['value']>0): ?>class="main-color-s"<?php endif ?>><?php echo $v['mark'] ==0 ? $v['value'] : 0;?></td>
            <td><?php echo  $v['uvalue'] ;?></td>
            <td><?php echo  $v['content'] ;?></td>
          </tr>
          <?php endforeach ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="7" class="tar">
              <div class="fl">
                <span class="mr20">收入总分：<em class="main-color-s"><?php echo isset($list['count'][1])?$list['count'][1]['s'] : 0;?></em>分</span><span>支出总分：<em class="green-color"><?php echo isset($list['count'][0])?$list['count'][0]['s']:0;?></em>分</span>
              </div>
              <div class="fr tar table-page">
                <span class="mlr10">本页<em class="mlr5"><?php echo $cpnum;?></em>条记录</span><span>共<em class="mlr5"><?php echo $pageNum;?></em>页</span>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
      <?php echo $page;?>

  </div>
  <?php if(!$this->is_ajax):?>
    <div class="warm-tip">
      <h3>温馨提示：</h3>
      <ol>
        <li>1、使用红包以实付金额发放成长值，彩金消费发放成长值；</li>
        <li>2、积分是有有效期的。每年03月01日清除上一年度产生的积分，在限定时间内不兑换或消耗则全部清零。</li>
      </ol>
    </div>
  <?php endif;?>
</div>
</div>
<?php if(!$this->is_ajax):?>
<script type="text/javascript">
var target = '/point/lists';
$(function(){
  var pagenum = 10;
  new cx.vform('.point-form', {
    checklogin: true,
        submit: function(data) {
        if(checkDate()){
          return ;
        };
        var self = this;
        $.ajax({
            type: 'post',
            url:  target,
            data: data,
            success: function(response) {
             $('#container_betlog-form').html(response);
            }
        });
      }
   });

  function getUri($obj)
  {
    jpage = $('#point-form_comm_page_').find('.skips').val();
    jpage = (isNaN(jpage) || !jpage) ? 1 : jpage;
    jpage = (jpage > pagenum)? pagenum : jpage;
    return $obj.href.replace(/cpage=\d+/i, 'cpage=' + jpage);
  }
    $(function(){
     $('.point-form').on('click', '#point-form_comm_page_ a', function(event) {
       if($(this).hasClass('page_skip'))
       {
         this.href = getUri(this);
       }
       var tar = target;
       target = this.href;
       $('.point-form').find('.submit').first().trigger('click');
       target = tar;
         return false;
     });
  })

});
</script>
 <?php endif;?>
