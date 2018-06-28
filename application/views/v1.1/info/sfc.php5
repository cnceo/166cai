<?php
function dashIfEmpty($value, $wrap = '')
{
    return ($value || in_array($value, array(0, '0'), TRUE))
        ? ($wrap ? ('<' . $wrap . '>' . $value . '</' . $wrap . '>') : $value)
        : '---';
}

?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/detail.min.css'); ?>"/>
<div class="wrap_in">
    <?php include ('breadcrumb.php5')?>
    <div class="detail-container clearfix">
        <div class="detail-container-l">
            <div class="article">
                <h1 class="article-title"><?php echo $result['title']; ?></h1>
				<div class="article-source"><i><?php echo substr($result['show_time'], 0, 16); ?></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>来源：<?php echo empty($result['submitter_id']) ? '转载' : '166彩票'?></i>
				</div>
				<?php echo htmlspecialchars_decode($result['content']); ?>
                <?php include ('share.php5')?>
            </div>
            <div class="compet-tz">
                <div class="tz-md-hd clearfix">
                    <h3>胜负彩快速投注</h3>
                </div>
                <div class="tz-md-bd">
                    <div class="mod-bet-hd">
                        <span>第<?php echo '20' . $sfcInfo['mid'] ?>期</span>
                        <span>开奖时间：<?php echo date('Y年m月d日', $sfcInfo['awardTime'] / 1000) ?></span>
                        <span>奖池奖金：<?php echo dashIfEmpty($sfcInfo['award'], 'em') ?>元</span>
                    </div>
                    <div class="mod-bet-bd">
                        <span class="table-des">开奖结果：</span>
                        <table class="table-tz">
                            <thead>
                            <tr>
                                <th>奖项</th>
                                <th>中奖注数</th>
                                <th>单住奖金（元）</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $data = $sfcInfo; ?>
                            <?php $award_detail = json_decode($data['award_detail'], TRUE) ?>
                            <tr>
                                <td>一等奖</td>
                                <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中' : $award_detail['1dj']['zs'] ?></td>
                                <td>
                                    <em class="main-color-s"><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中' : $award_detail['1dj']['dzjj'] ?></em>
                                </td>
                            </tr>
                            <tr>
                                <td>二等奖</td>
                                <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中' : $award_detail['2dj']['zs'] ?></td>
                                <td><?php echo ($data['status'] < 50 || $data['rstatus'] < 50) ? '统计中' : $award_detail['2dj']['dzjj'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <a href="/sfc" class="btn btn-main btn-center">立即预约</a>
                    </div>
                </div>
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
        <?php 
        include('jc_mod_ad.php5');
        include('jc_mod_recomed.php5');
        include('jc_ad.php5') ?>
        </div>
    </div>
</div>