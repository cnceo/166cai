<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<div class="l-frame-menu m-menu">
	<h2 class="m-menu-title">开奖公告</h2>
		<div class="m-menu-bd">
			<dl>
				<dt class="lottery-detail-szc"><i class="icon"></i>数字彩</dt>
				<dd <?php if ($this->router->method === "ssq"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/ssq">双色球<?php if (in_array(date('w'), array(2, 4, 0))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "dlt"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/dlt">大乐透<?php if (in_array(date('w'), array(1, 3, 6))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "syxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/syxw">老11选5<em></em></a></dd>
                <dd <?php if ($this->router->method === "jxsyxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/jxsyxw">新11选5<em></em></a></dd>
                <dd <?php if ($this->router->method === "fc3d"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/fc3d">福彩3D<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "pl5"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/pl5">排列五<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "pl3"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/pl3">排列三<span class="grayWords"><s class="arrowsIcon"></s>每日开奖</span><em></em></a></dd>
                <dd <?php if ($this->router->method === "qlc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/qlc">七乐彩<?php if (in_array(date('w'), array(1, 3, 5))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "qxc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/qxc">七星彩<?php if (in_array(date('w'), array(2, 5, 0))) {?><span class="grayWords"><s class="arrowsIcon"></s>今日开奖</span><?php } ?><em></em></a></dd>
                <dd <?php if ($this->router->method === "ks"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/ks">经典快3<em></em></a></dd>
                <dd <?php if ($this->router->method === "klpk"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/klpk">快乐扑克<em></em></a></dd>
                <dd <?php if ($this->router->method === "hbsyxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/hbsyxw">惊喜11选5<em></em></a></dd>
                <dd <?php if ($this->router->method === "jlks"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/jlks">易快3<em></em></a></dd>
                <dd <?php if ($this->router->method === "jxks"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/jxks">红快3<em></em></a></dd>
                <dd <?php if ($this->router->method === "gdsyxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/gdsyxw">乐11选5<em></em></a></dd>
            </dl>
            <dl>
                <dt class="lottery-detail-jjc"><i class="icon"></i>竞技彩</dt>
                <dd <?php if ($this->router->method === "sfc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/sfc">胜负彩<em></em></a></dd>
                <dd <?php if ($this->router->method === "rj"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>kaijiang/rj">任选九<em></em></a></dd>
                <dd <?php if ($this->router->method === "jczq"){?> class="current" <?php }?>><a target="_blank" href="<?php echo $baseUrl?>kaijiang/jczq">竞彩足球<em></em></a></dd>
                <dd <?php if ($this->router->method === "jclq"){?> class="current" <?php }?>><a target="_blank" href="<?php echo $baseUrl?>kaijiang/jclq">竞彩篮球<em></em></a></dd>
	            </dl>
            </div>
        </div>