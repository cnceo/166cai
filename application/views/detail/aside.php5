		<div class="aside">
            <h1 class="help-home">开奖公告</h1>
            <dl class="help-side-nav">
                <dt class="lottery-detail-szc"><a href="#"><i></i>数字彩<em></em></a></dt>
                <dd <?php if ($this->router->method === "fc3d"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/fc3d">福彩3D<em></em></a></dd>
                <dd <?php if ($this->router->method === "ssq"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/ssq">双色球<em></em></a></dd>
                <dd <?php if ($this->router->method === "dlt"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/dlt">大乐透<em></em></a></dd>
                <dd <?php if ($this->router->method === "qlc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/qlc">七乐彩<em></em></a></dd>
                <dd <?php if ($this->router->method === "qxc"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/qxc">七星彩<em></em></a></dd>
                <dd <?php if ($this->router->method === "pl3"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/pl3">排列三<em></em></a></dd>
                <dd <?php if ($this->router->method === "pl5"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/pl5">排列五<em></em></a></dd>
                <dd <?php if ($this->router->method === "syxw"){?> class="current" <?php }?>><a href="<?php echo $baseUrl?>detail/syxw">老11选5<em></em></a></dd>
                <dt class="lottery-detail-jjc"><a href="###"><i></i>竞技彩<em></em></a></dt>
                <dd <?php if ($this->router->method === "rj"){?> class="current" <?php }?>><a href="###">任选九<em></em></a></dd>
                <dd <?php if ($this->router->method === "sfc"){?> class="current" <?php }?>><a href="###">胜负彩<em></em></a></dd>
                <dd <?php if ($this->router->method === "jczq"){?> class="current" <?php }?>><a href="###">竞彩足球<em></em></a></dd>
                <dd <?php if ($this->router->method === "jclq"){?> class="current" <?php }?>><a href="###">竞彩篮球<em></em></a></dd>
            </dl>
        </div>