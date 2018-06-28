<div class="fix-foot-wrap">
	<div class="top_bar">
		<div class="wrap_in">
	    hi，欢迎来到166彩票！
	   <!-- <a href="/mdownload" class="deskApp" target="_self"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/home-gif.gif');?>" width="106" height="26" alt="166彩票桌面版下载"></a> -->
	    <div class="user_info logged-in">
	      <div class="my-home">
	        <s class="split-line"></s>
	        <a href="/mylottery/betlog" target="_blank"><i class="icon-font">&#xe62d;</i>我的彩票<i class="icon-font icon-arrow">&#xe62a;</i></a>
	        <ul class="my-home-list">
	          <li><a href="/mylottery/betlog" target="_blank">投注记录</a></li>
	          <li><a href="/mylottery/detail" target="_blank">账户明细</a></li>
	          <li><a href="/safe/" target="_blank">安全中心</a></li>
              <li><a href="/point/" target="_blank">积分商城</a></li>
	          <li><a href="/mynews?cpage=1" target="_blank">我的消息<span class="num-corner"><i><?php $CI = &get_instance(); $CI->load->model('news_model'); $count = $CI->news_model->countUnreadList($this->uid); echo intval($count[0])?intval($count[0]):'0'?></i></span></a></li>
	          <li><a href="javascript:;" target="_self" class = "out">退出登录</a></li>
	        </ul>
	      </div>
          <a class="lnk-withdraw" target="_blank" href="/wallet/withdraw">提现</a>
	      <a class="btn-ss btn-specail btn-recharge" target="_blank" href="/wallet/recharge">充值</a>
          <div class="myaccount">
              <?php if(!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!='/ajax/topBar'):?>
	      	  <a href="/" class="back-index" target="_blank">首页</a>
              <?php endif; ?>
	          <span class="user-name"><a href="/mylottery/account" target="_blank" ><?php echo $this->uname;?></a><a target="_blank"  href="<?php echo $baseUrl; ?>member" class="icon-lv v<?php echo $this->uinfo['grade'];?>"></a></span>
	          <s class="split-line">|</s>
	          <span class="balance">余额：<em><?php if($this->uinfo['money_hide']) { echo '***'; } else { echo number_format(ParseUnit($this->uinfo['money'], 1), 2);}?></em> 元<a href="javascript:;" target="_self" id="hideMoney"><?php if($this->uinfo['money_hide']){ echo '显示';}else{ echo '隐藏';}?></a></span>
	          <s class="split-line">|</s>
	          <span class="view-hb">
	            <i class="icon-font main-color">&#xe645;</i>
	            <a href="/mylottery/redpack" target="_blank">查看红包</a>
	          </span>
	      </div>
	    </div>
	  </div>
	</div>
<script>
    $(function(){
        $('.out').click(function(){
            $.ajax({
                type:'post',
                url:'/mainajax/loginout',
                success:function(){
                	$.cookie('38338', null);
                    location.reload();
                }
            });

        });
        $('#hideMoney').click(function(){
            var value = $(this).html();
            var hide = 0;
            if(value == '隐藏'){
                hide = 1;
            }
            $.ajax({
                type:'post',
                url:'/mainajax/hideMoney',
                data: {uid:<?php echo $this->uid?>, hide:hide, version:version},
                success:function(response){
                    if(response.code == 0){
                    	$('.top_bar').html(response.data);
                    }else{
                    	cx.Alert({
                            content: '操作失败'
                        });
                    }
                }
            });

        });
    });
</script>