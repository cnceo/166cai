<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/detail.min.css'); ?>"/>
<div class="wrap_in">
    <?php include('breadcrumb.php5') ?>
    <div class="detail-container clearfix">
        <div class="detail-container-l">
            <div class="article">
                <h1 class="article-title"><?php echo $result['title']; ?></h1>
				<div class="article-source"><i><?php echo substr($result['show_time'], 0, 16); ?></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>来源：<?php echo empty($result['submitter_id']) ? '转载' : '166彩票'?></i>
				</div>
				<?php echo htmlspecialchars_decode($result['content']); ?>
                <div class="newImg">
                    <a href="<?php echo $this->config->item('base_url')?>activity/newmode">
                        <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/newImg.jpg'); ?>" alt="">
                    </a>
                </div>
                <?php include('share.php5') ?>
            </div>
            <div class="mod-links-article">
		        <p><span>上一篇：<?php if (!empty($left['id'])) {?><a href="/info/<?php echo $this->act."/".$left['id']?>"><?php echo $left['title']?></a><?php }else {echo '无'; }?></span></p>
		        <p><span>下一篇：<?php if (!empty($right['id'])) {?><a href="/info/<?php echo $this->act."/".$right['id']?>"><?php echo $right['title']?></a><?php }else {echo '无'; }?></span></p>
		    </div>
		    <div class="related-article">
	        <h4>相关阅读</h4>
	        <ul class="mod-article-list">
	        <?php foreach ($xg as $val) {?>
	        	<li><i class="dian"></i><a href="/info/<?php echo $this->act."/".$val['id']?>"><?php echo $val['title']?></a><span><?php echo $val['created']?></span></li>
	        <?php }?>
	        </ul>
	      </div>
        </div>
        <div class="detail-side">
            <?php include('kaijiang.php5') ?>
            <?php include('constellation.php5') ?>
            <?php include('trend.php5'); ?>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.side-tab span').hover(function () {
            $(this).addClass('current').siblings().removeClass('current');
            var idx = $(this).index();
            $(this).parent().parent().siblings('.mod-bd').eq(idx).addClass('mod-bd-cur').siblings('.mod-bd').removeClass('mod-bd-cur');
        });

        $('body').on('click', '.lucky', function () {
            var $this = $(this),
                scheme = $this.data('scheme');
            $this.closest('.list-xz').find('.caption')
                .html('【' + $this.data('name') + '】今日幸运数字<span>' + $this.data('num')
                    + '</span>')
                .end().find('.current').removeClass('current');
            $this.addClass('current');
        }).on('click', '.obtain-luck', function () {
            var $this = $(this),
                $luckBody = $this.closest('.mod-bd-lucky');
            $luckBody.find('.lucky').each(function () {
                var $this = $(this);
                if ($this.hasClass('current')) {
                    var scheme = $this.data('scheme'),
                        schemeAry = scheme.split(',');
                    $luckBody.find('.lucky-num').children().each(function () {
                        var $this = $(this),
                            num = schemeAry.shift();
                        $this.html(num > 9 ? num : ('0' + num));
                    });
                    $luckBody.find('.cast-away').data('scheme', scheme);
                }
            });
            $luckBody.find(".cast-away").removeClass('btn-disabled');
        }).on('click', '.cast-away', function () {
            var $this = $(this),
                url = $this.data('url'),
                scheme = $this.data('scheme'),
                schemeAry = scheme.split(','),
                red = $this.data('red'),
                castAry = [],
                cast;
            if (!$this.hasClass('btn-disabled')) {
                for (var i = 0; i < red; i++) {
                    castAry.push(schemeAry.shift());
                }
                cast = castAry.join(',');
                if (schemeAry.length > 0) {
                    cast = cast + '|' + schemeAry.join(',');
                }
                cast += ':1:1';
                location.href = url + '?codes=' + encodeURIComponent(cast);
            }
        });
    });
</script>
