<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/detail.min.css');?>">
<div class="wrap_in">
	<?php include('breadcrumb.php5') ?>
    <div class="infor-mod">
        <div class="infor-mod-l">
            <div class="infor-mod-bd">
                <ul class="infor-news">
                <?php foreach (array_slice($csxw, 0, 4) as $k => $val) {?>
                    <li class="<?php if ($k == 0){?>infor-news-top<?php }else {?>dian<?php }?>"><a href="/info/csxw/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
                <?php }?>
                </ul>
                <ul class="infor-news">
                <?php foreach (array_slice($csxw, 4, 4) as $k => $val) {?>
                    <li class="<?php if ($k == 0){?>infor-news-top<?php }else {?>dian<?php }?>"><a href="/info/csxw/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
                <?php }?>
                </ul>
            </div>
            <div class="infor-mod-ft">
                <a href="/info/lists/1?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
            </div>
        </div>
        <div class="infor-mod-r infor-banner">
            <ul class="conList">
            <?php foreach ($banner as $bn) {?>
            	<li class="con">
                    <a href="<?php echo $bn['url']?>" target="_blank"><img src="uploads/infobanner/<?php echo $bn['path']?>" alt="<?php echo $bn['title']?>"></a>
                    <p class="p-bg"></p>
                    <h3><a href="<?php echo $bn['url']?>"><?php echo $bn['title']?></a></h3>
                </li>
            <?php }?>
            </ul>
            <span class="banner-btn banner-btn-l">
                <i class="icon-font">&#xe623;</i>
            </span>
            <span class="banner-btn banner-btn-r">
                <i class="icon-font">&#xe629;</i>
            </span>
            <div class="infor-banner-num">
            </div>
        </div>
    </div>
    <div class="infor-mod">
        <div class="infor-mod-hd">
            <h2>数字彩</h2>
        </div>
        <div class="infor-mod-bd clearfix">
            <div class="infor-mod-item">
                <ul class="mod-tab clearfix">
                    <li class="current"><a href="/info/lists/2?cpage=1" target="_blank">双色球</a><i class='xian'></i></li>
                    <li><a href="/info/lists/3?cpage=1" target="_blank">其他福彩</a></li>
                </ul>
                <div class="mod-tab-con">
                    <div class="mod-tab-item" style="display: block;">
                        <ul class="infor-news">
                        <?php foreach (array_slice($ssq, 0, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/ssq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <ul class="infor-news">
                        <?php foreach (array_slice($ssq, 4, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/ssq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <a href="/info/lists/2?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                    </div>
                    <div class="mod-tab-item">
                        <ul class="infor-news">
                        <?php foreach (array_slice($qtfc, 0, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/qtfc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <ul class="infor-news">
                        <?php foreach (array_slice($qtfc, 4, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/qtfc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <a href="/info/lists/3?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                    </div>
                </div>
            </div>
            <div class="infor-mod-item">
                <ul class="mod-tab clearfix">
                    <li class="current"><a href="/info/lists/4?cpage=1" target="_blank">大乐透</a><i class='xian'></i></li>
                    <li><a href="/info/lists/5?cpage=1" target="_blank">其他体彩</a></li>
                </ul>
                <div class="mod-tab-con">
                    <div class="mod-tab-item" style="display: block;">
                        <ul class="infor-news">
                        <?php foreach (array_slice($dlt, 0, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/dlt/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <ul class="infor-news">
                        <?php foreach (array_slice($dlt, 4, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/dlt/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <a href="/info/lists/4?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                    </div>
                    <div class="mod-tab-item">
                        <ul class="infor-news">
                        <?php foreach (array_slice($qttc, 0, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/qttc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <ul class="infor-news">
                        <?php foreach (array_slice($qttc, 4, 4) as $k => $val) {?>
		                    <li><i class="dian"></i><a href="/info/qttc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
		                <?php }?>
                        </ul>
                        <a href="/info/lists/5?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                    </div>
                </div>
            </div>
            <div class="mod-item-side">
                <div class="item-side-con">
                    <h3>数字彩工具</h3>
                    <ul class="clearfix">
                        <li class="zst"><a href="/chart"><i></i>走势图</a></li>
                        <li class="jjzs"><a target="_blank" href="/tools/caculate/ssq"><i></i>奖金计算</a></li>
                    </ul>
                </div>
                <div class="item-side-con">
                    <h3>彩票客户端</h3>
                    <div class="item-2">
                        <a href="/activity/newmode">
                            <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/khd-ewm.png');?>" width="105" height="105"  alt="">
                            <h4>大奖就在手中</h4>
                            <p>扫描二维码下载<br>彩票APP</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="infor-mod">
        <div class="infor-mod-hd">
            <h2>竞技彩</h2>
        </div>
        <div class="infor-mod-bd clearfix">
            <div class="infor-mod-box clearfix">
                <div class="infor-mod-item">
                    <ul class="mod-tab clearfix">
                        <li class="current"><a href="/info/lists/6?cpage=1" target="_blank">竞彩足球</a><i class='xian'></i></li>
                        <li><a href="/info/lists/7?cpage=1" target="_blank">胜负彩</a></li>
                    </ul>
                    <div class="mod-tab-con">
                        <div class="mod-tab-item" style="display: block;">
                            <ul class="infor-news">
                            <?php foreach (array_slice($jczq, 0, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/jczq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <ul class="infor-news">
                            <?php foreach (array_slice($jczq, 4, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/jczq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <a href="/info/lists/6?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                        </div>
                        <div class="mod-tab-item">
                            <ul class="infor-news">
                            <?php foreach (array_slice($sfc, 0, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/sfc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <ul class="infor-news">
                            <?php foreach (array_slice($sfc, 4, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/sfc/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <a href="/info/lists/7?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                        </div>
                    </div>
                </div>
                <div class="infor-mod-item">
                    <ul class="mod-tab clearfix">
                        <li class="current"><a href="/info/lists/8?cpage=1" target="_blank">竞彩篮球</a></li>
                    </ul>
                    <div class="mod-tab-con">
                        <div class="mod-tab-item" style="display: block;">
                            <ul class="infor-news">
                            <?php foreach (array_slice($jclq, 0, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/jclq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <ul class="infor-news">
                            <?php foreach (array_slice($jclq, 4, 4) as $k => $val) {?>
			                    <li><i class="dian"></i><a href="/info/jclq/<?php echo $val['id']?>" target="_blank"><?php echo $val['title']?></a></li>
			                <?php }?>
                            </ul>
                            <a href="/info/lists/8?cpage=1" class="infor-more" target="_blank">更多&gt;</a>
                        </div>
                    </div>
                </div>
                <div class="infor-mod-item infor-mod-item-zj">
                    <h3>专家推荐</h3>
                    <ul class="infor-mod-con clearfix">
                    <?php foreach ($zjtj as $val) {?>
                        <li>
                            <div class="zj">
                                <div class="zj-pic"><img src="/caipiaoimg/v1.1/img/submitter/<?php echo $val['submitter_id']?>.jpg" alt=""></div>
                                <p class="zj-name"><?php echo $val['submitter']?></p>
                                <div class="zj-type"><?php echo $val['category_id'] == 9 ? '足彩' : '篮彩'?>专家</div>
                            </div>
                            <div class="zj-con">
                                <span class="sj"></span>
                                <h5><a target="_blank" href="/info/zjtj<?php echo ($val['category_id'] == 9 ? 'zq' : 'lq')?>/<?php echo $val['id']?>"><?php echo $val['title']?></a></h5>
                                <p><?php echo mb_substr(strip_tags(htmlspecialchars_decode($val['content'])), 0, 45, 'utf-8')?>…</p>
                                <div class="zj-detail"><a target="_blank" href="/info/zjtj<?php echo ($val['category_id'] == 9 ? 'zq' : 'lq')?>/<?php echo $val['id']?>" class="btn-sup">详情</a></div>
                            </div>
                        </li>
                    <?php }?>
                    </ul>
                </div>
            </div>
            
            <div class="mod-item-side">
                <div class="item-side-con">
                    <h3>竞技彩工具</h3>
                    <ul class="clearfix">
                        <li class="lszl"><a rel="nofollow" href="<?php echo $this->config->item('api_info')?>"><i></i>联赛资料</a></li>
                        <li class="bfzb"><a rel="nofollow" href="<?php echo $this->config->item('api_bf')?>"><i></i>比分直播</a></li>
                        <li class="sbbg"><a href="<?php echo $this->config->item('api_odds')?>complex/jc"><i></i><h5>赔率中心</h5><p>盘路在手，红单我有</p></a></li>
                    </ul>
                </div>
                <div class="item-side-con item-side-con-jcgl">
                    <h3>竞彩攻略</h3>
                    <a href="/activity/jczq" class="side-con-banner"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/jcgl.jpg');?>" alt=""></a>
                    <p class="side-con-list"><a href="/activity/jclq">手把手带你入门竞彩篮球</a></p>
                    <p class="side-con-list"><a href="/activity/dggp">带您全面解读单关固赔</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/slideFocus.js');?>'></script>
<script>
//资讯详情首页轮播
$(".infor-banner").slideFocusPlugin({
  arrowBtn: true,
  leftArrowBtnClass: 'banner-btn-l',
  rightArrowBtnClass: 'banner-btn-r',
  tabClassName: 'infor-banner-num',
  selectClass: "current",
  stepNum: 300,
  animateStyle: ["fade"]
});

$('.infor-banner').hover(
    function(){
        $(this).find('.banner-btn').fadeIn(400)
    },
    function(){
        $(this).find('.banner-btn').fadeOut(400)
    }
);
</script>