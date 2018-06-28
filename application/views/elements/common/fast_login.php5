      	<?php if(!$this->uid):?>
        <div class="no-login btn-group">
          <a href="javascript:;"  target="_self" class="btn btn-login not-login">用户登录</a><a href="javascript:;"  target="_self" class="btn btn-register not-register">免费注册</a>
        </div>
        <?php else:?>
        <!-- 已登录 -->
        <div class="logined clearfix">
          <a id="lulogout" class="fr c666" target="_self" href="/main/loginout">退出</a>
          <span>欢迎您，<a href="/mylottery/account" class="spec" id="homeusername"><?php echo $this->uname;?></a></span>
        </div>
        <?php endif;?>