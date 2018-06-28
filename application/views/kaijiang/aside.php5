		<div class="aside">
            <h2 class="help-home">开奖公告</h2>
            <dl class="help-side-nav">
                <dt class="lottery-detail-szc"><a><i></i>数字彩</a></dt>
                <dd <?php if ($this->router->method === "ssq"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/ssq">双色球<?php if (in_array(date('w'), array(2, 4, 0))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "dlt"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/dlt">大乐透<?php if (in_array(date('w'), array(1, 3, 6))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "syxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/syxw">老11选5<em></em></a></dd>
                <dd <?php if ($this->router->method === "fc3d"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/fc3d">福彩3D<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "pl5"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/pl5">排列五<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "pl3"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/pl3">排列三<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "qlc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/qlc">七乐彩<?php if (in_array(date('w'), array(1, 3, 5))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "qxc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/qxc">七星彩<?php if (in_array(date('w'), array(2, 5, 0))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dt class="lottery-detail-jjc"><a><i></i>竞技彩</a></dt>
                <dd <?php if ($this->router->method === "sfc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/sfc">胜负彩<em></em></a></dd>
                <dd <?php if ($this->router->method === "rj"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/rj">任选九<em></em></a></dd>
                <dd <?php if ($this->router->method === "jczq"){?> class="current" <?php }?>><a target="_blank" href="<?php echo $baseUrl?>awards/jczq">竞彩足球<em></em></a></dd>
                <dd <?php if ($this->router->method === "jclq"){?> class="current" <?php }?>><a target="_blank" href="<?php echo $baseUrl?>awards/jclq">竞彩篮球<em></em></a></dd>
            </dl>
        </div>