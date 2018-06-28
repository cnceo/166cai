<!--header begin-->
<?php $levExt = array(1=>'qtby',2=>'qtby',3=>'qtby',4=>'hjbj',5=>'hjbj',6=>'zs');?>
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
            <a href="<?php echo $baseUrl; ?>point" class="cur">积分赚取</a>
            <a href="<?php echo $baseUrl; ?>point#jfdh" >积分兑换</a>
            <a href="<?php echo $baseUrl; ?>point/lists" >积分明细</a>
            <a href="<?php echo $baseUrl; ?>point/help">积分帮助</a>
        </div>
    </div>
  </div>
</div>
<!--header end-->
<div class="p-jifen task">
  <div class="wrap">
    <div class="task-hd">
      <div class="task-hd-l">
        <div class="user-info">
          <div class="avatar">  
          <img src="<?php echo $info['headimgurl']?$info['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>" width="80" height="80" alt="">
          </div>
          <div class="user-info-txt mod-tips">
            <span class="user-name" alt="<?php echo $this->uname;?>" title="<?php echo $this->uname;?>"><?php  echo mb_strlen($this->uname)>6 ? mb_substr($this->uname,0,6,'utf-8').'...' : $this->uname; ?><a href="<?php echo $baseUrl; ?>member" target="_blank"><i class="icon-lv v<?php echo $info['grade'];?>"></i></a></span>
            <div class="des">当前积分：<em><?php echo $info['points'] ;?></em> 分 <a href="<?php echo $baseUrl; ?>point/lists">明细</a></div>
            <?php if ($info['last_year_points'] && time()>=strtotime(date('Y').'-01-01') && time()<strtotime(date('Y').'-03-01') ): ?>
            <div class="ptips-bd ptips-bd-t">您有<?php echo $info['last_year_points'] ;?>积分将于3月1日过期<a href="javascript:;" class="ptips-bd-close">×</a><b></b><s></s></div>              
            <?php endif ?>
            <div><a href="<?php echo $baseUrl; ?>point#jfrw" class="btn-s btn-specail mr10">赚取积分</a><a href="<?php echo $baseUrl; ?>point#jfdh" class="btn-s btn-main">兑换礼包</a></div>
          </div>
        </div>

        <h2 class="title">帮助中心<span class="more"><a href="<?php echo $baseUrl; ?>point/help">更多帮助></a></span></h2>
        <div class="task-help">
            <a href="<?php echo $baseUrl; ?>point/help#smsjf">什么是积分？</a>
            <a href="<?php echo $baseUrl; ?>point/help#smsjf">如何获得积分？</a>
            <a href="<?php echo $baseUrl; ?>point/help#smsjf">积分有什么作用</a>
        </div>
      </div>
      <div class="slider">
        <div class="infor-mod-r infor-banner">
            <ul class="conList">
              <?php if ($jfbanner): ?>
              <?php foreach ($jfbanner as $k=> $v): ?>
                <?php $i =0;?>
                <li class="con" style="z-index: <?php echo 10 -$i; ?>; display: list-item; ">
                  <a href="<?php echo $v['url'] ;?>" target="_blank"><img src="<?php echo '//888.166cai.cn/uploads/infobanner/'.$v['path'] ;?>" alt="<?php echo $v['title'] ;?>"></a>
                  <p class="p-bg"></p>
                  <h3><a href="<?php echo $v['url'] ;?>" target="_blank" ><?php echo $v['title'] ;?></a></h3>
                </li>
              <?php endforeach ?>
              <?php endif ?>
            </ul>
            <span class="banner-btn banner-btn-l" style="display: none;">
              <i class="icon-font"></i>
            </span>
            <span class="banner-btn banner-btn-r" style="display: none;">
              <i class="icon-font"></i>
            </span>
            <div class="infor-banner-num"><?php $j =1;?><?php if ($jfbanner): ?><?php foreach ($jfbanner as $k=> $v): ?><i class="<?php $j==1 ? 'current' : '';?>"><?php echo $j;$j++?></i><?php endforeach ?><?php endif ?></div>
        </div>
      </div>
    </div>
    <!--btn-disabled 已完成 btn-main 可领取 btn-plain 去完成-->
    <h2 class="title" id="jfrw">推荐任务</h2>
    <div class="task-group">
      <ul>
        <?php foreach ($jobs as $k => $v): ?>
        <li class="task-item <?php echo $v['icon'] ; ?> <?php echo $v['hot'] ? 'tuijian' :'' ; ?>">
          <h3 class="title"><?php echo $v['title'] ; ?></h3>
          <p class="des"><?php echo $v['desc'] ; ?></p>
          <p class="result"><em>奖励积分 +<?php echo $v['value']; ?></em>限1次</p>
          <?php $index = $v['id']-1; ?>
          <?php if ($v['doStatus']==0): ?>
          <a target="_blank" data-href="<?php echo $baseUrl.$v['url'];?>" herf="javascript:" class="btn btn-main btn-plain" data-id="<?php echo $v['id'] ; ?>" data-type="<?php echo $v['type'] ; ?>">去完成</a>        
          <?php elseif($v['doStatus']==1): ?>
          <a  href="javascript:;" class="btn btn-main btn-lq" data-id="<?php echo $v['id'] ; ?>" data-type="<?php echo $v['type'] ; ?>" >可领取</a> 
          <?php else:?>
          <a  href="javascript:;" class="btn btn-disable" data-id="<?php echo $v['id'] ; ?>" >已完成</a> 
          <?php endif ?>
        </li>          
        <?php endforeach ?>
        <li class="task-item"></li>
      </ul>
    </div>
    <h2 class="title" id="jfdh">兑换专区<span class="more"><s>礼包每天00：00更新，每人每天有3次兑换机会</s><a href="<?php echo $baseUrl; ?>point/help">更多规则></a></span></h2>
    <div class="exchange-group">
      <ul>
        <?php foreach ($redpack as $k => $v): ?>
        <li class="exchange-item">
          <div class="rp-hd">
            <b>¥<?php echo m_format($v['money']);?></b><?php echo $v['p_name'];?>
            <i>剩<?php echo $cha = $v['today_out']- $v['already_out'];?>个</i>
            <?php $arr = json_decode($v['use_params'],true);?>
          </div>
          <div class="rp-bd">
            <p class="des">积分：<?php if ($arr['lv'.$info['grade']]!='--' && $arr['price']!= $arr['lv'.$info['grade']]): ?><del><?php echo $arr['price'];?></del><em><?php echo $arr['lv'.$info['grade']] ;?></em>
            <?php else: ?>
              <em><?php echo $arr['price'];?></em>
            <?php endif ?><i class="icon-font bubble-tip" tiptext="<?php if ($arr['lv2']!='--'): ?>青铜、白银彩民<?php echo $arr['lv2'];?>分可兑换<br/>黄金、铂金彩民<?php echo $arr['lv4'];?>分可兑换<br/>钻石彩民<?php echo $arr['lv6'];?>分可兑换<?php else: ?>黄金、铂金彩民<?php echo $arr['lv4'];?>分可兑换<br/>钻石彩民<?php echo $arr['lv6'];?>分可兑换<?php endif ?>
            ">&#xe613;</i></p>
            <a href="javascript:" class="btn <?php echo $cha==0 ? '' : 'btn-main';?> <?php if($cha) echo 'ljdh' ;?>" data-rid="<?php echo $v['rid'];?>" data-money="<?php echo ParseUnit($v['money'],1);?>" data-jf="<?php echo $arr['lv'.$info['grade']]=='--'?$arr['price']:$arr['lv'.$info['grade']]; ?>" ><?php echo $cha==0 ? '已兑完' : '立即兑换';?></a>
          </div>
        </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</div>

<script type="text/javascript" src='//888.166cai.cn/caipiaoimg/v1.1/js/slideFocus.js?v=221'></script>
<script type="text/javascript">
  $(function(){
      //轮播
      $(".infor-banner").slideFocusPlugin({
        arrowBtn: true,
        leftArrowBtnClass: 'banner-btn-l',
        rightArrowBtnClass: 'banner-btn-r',
        tabClassName: 'infor-banner-num',
        selectClass: "current",
        autoPlayTime:3000,
        stepNum: $('.slider').width,
        animateStyle: ["fade"]
      });
      //鼠标事件
      $('.infor-banner').hover(
          function(){
              $(this).find('.banner-btn').fadeIn(400)
          },
          function(){
              $(this).find('.banner-btn').fadeOut(400)
          }
      );
    //领取积分
    $('.btn-lq').click(function(){
      if ($('.not-login').length >0 || !$.cookie('name_ie')) {cx.PopAjax.login();return ;}
      var jid = $(this).attr('data-id');
      var type = $(this).attr('data-type');
      $.ajax({
          type:'post',
          data:{jid:jid},
          url:'point/getPoint',
          dataType:"json",
          success: function(data)
          {
            if(data.code==3) 
            {
              cx.PopAjax.login();return;
            }else{
              if(data.code==200)
              {
                cx.Alert({content: data.msg,confirmCb:function(){window.location.reload(true)},cancelCb: function(){window.location.reload(true)} });
              }else{
                cx.Alert({content: data.msg,confirmCb:function(){window.location.reload(true)},cancelCb: function(){window.location.reload(true)} });
              }
            }
          }, 
      });
    });
    //兑换
    $('.ljdh').click(function(){
        if ($('.not-login').length >0 || !$.cookie('name_ie')) {cx.PopAjax.login();return ;}
        var rid = $(this).data('rid');
        var money = $(this).data('money');
        var jf = $(this).data('jf');
        cx.Confirm({
            title: '确认兑换信息' ,
            content: '<p class="pop-help" style="font-size:14px;">红包金额：<span style="color:#f00;">'+money+'</span>&nbsp;元</p><p class="pop-help" style="font-size:14px;">适用范围：<span>通用彩金红包</span></p><p class="pop-help" style="font-size:14px;">消耗积分：<span style="color:#f00;">'+jf+'</span></p>',
            btns:[{type: 'confirm', href: 'javascript:;', txt: '确认兑换'}],
            cancelCb: function(){},
            confirmCb: function()
            {
                $.ajax({
                    type:'post',
                    data:{rid:rid},
                    url:'point/exchangeRedPack',
                    dataType:"json",
                    success: function(data)
                    {
                      if(data.code==3) 
                      {
                        cx.PopAjax.login();return;
                      }else{
                        if(data.code==200)
                        {
                          cx.Confirm({
                              title: '提示' ,
                              content: '<p class="pop-help" style="font-size:14px;margin:0 auto;text-align:center;">恭喜您，兑换<span style="color:#f00;">'+money+'</span>&nbsp;元红包成功</p>',
                              btns:[{type: 'confirm', href: '<?php echo $baseUrl.'mylottery/redpack'; ?>', txt: '立即查看',target:'_blank'}],
                              cancelCb: function(){window.location.reload(true)}
                              
                            });
                        }else{
                          cx.Alert({content: data.msg});
                        }
                      }
                    }, 
                });
            }
        });

    });


    //去完成
    $('.btn-plain').click(function(e){
      if ($('.not-login').length >0 || !$.cookie('name_ie')) {cx.PopAjax.login();return ;}
      var jid = $(this).attr('data-id');
      var type = $(this).attr('data-type');
      var href = $(this).attr('data-href');
      var flag = false;
      var _this = $(this);
        $.ajax({
            type:'post',
            data:{jid:jid,type:type},
            url:'point/checkJobStatus',
            dataType:"json",
            async: false,
            success: function(data)
            {
              if(data.code==3) 
              {
                cx.PopAjax.login();return;
              }else{
                if(data.code==200)
                {
                  if(data.status==0)
                  {
                    _this.attr('href',href );
                  }else if(data.status==1){
                    cx.Alert({content: data.msg,confirmCb:function(){window.location.reload(true)},cancelCb: function(){window.location.reload(true)} });
                    e.preventDefault();
                  }else{
                    cx.Alert({content: '您好，积分已经领取过啦',confirmCb:function(){window.location.reload(true)},cancelCb: function(){window.location.reload(true)} });
                    e.preventDefault();
                  }

                }else{
                  cx.Alert({content: data.msg,confirmCb:function(){window.location.reload(true)},cancelCb: function(){window.location.reload(true)} });
                  e.preventDefault();
                }
              }
            }, 
        });
    });
  //红包兑换描述
  $('.exchange-item .bubble-tip').mouseenter(function(){
            $.bubble({
                target:this,
                position: 'b',
                align: 'l',
                autoClose: false, 
                content: $(this).attr('tiptext'),
                width:'190px'
            })
        }).mouseleave(function(){
            $('.bubble').hide();
        });


  });


</script>