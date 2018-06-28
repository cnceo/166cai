<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/news.css');?>" />
<script>
$(function() {
    $('.cate-list').click(function() {
        var $this = $(this);
        $this.find('li').toggleClass('selected');
        $this.parent().find('div').toggleClass('hidden');
    });
});
</script>
<div class="news clearfix">
    <div class="newsNav">
        <ul>
            <?php foreach ($categoryNames as $categoryId => $categoryName): ?>
                <?php if ($categoryId == $article['category']): ?>
                    <li class="selected">
                <?php else: ?>
                    <li>
                <?php endif; ?>
                <a href="<?php echo $baseUrl; ?>news/index/<?php echo $categoryId; ?>"><?php echo $categoryName; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="newsLeft border">
        <div class="manage">
            <div class="title">
                <h3><?php echo $article['title']; ?></h3>
                <p>
                    <span class="time"><?php echo date('Y-m-d', $article['createTime'] / 1000); ?></span>
                    <span class="author"><?php echo $article['author']; ?></span>
                </p>
            </div>
            <div class="content">
                <?php echo $article['content']; ?>
            </div>
        </div>
    </div>
    <div class="newsRight">
        <div class="cement border">
            <ul class="cate-list">
                <li class="selected"><a href="javascript:void(0)">公告</a></li>
                <li><a href="javascript:void(0)">活动</a></li>
            </ul>
            <div>
                <?php foreach ($relevant as $article): ?>
                <?php if ($article['category'] == Cms_Model::CATE_NOTICE): ?>
                <p>
                    <a href="<?php echo $baseUrl; ?>news/index/<?php echo Cms_Model::CATE_NOTICE; ?>">[公告]</a>
                    <a href="<?php echo $baseUrl; ?>news/detail/<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                </p>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="hidden">
                <?php foreach ($relevant as $article): ?>
                    <?php if ($article['category'] == Cms_Model::CATE_ACTIVITY): ?>
                        <p>
                            <a href="<?php echo $baseUrl; ?>news/index/<?php echo Cms_Model::CATE_ACTIVITY; ?>">[活动]</a>
                            <a href="<?php echo $baseUrl; ?>news/detail/<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                        </p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="advertising">
            <?php foreach ($relevant as $article): ?>
                <?php if ($article['category'] == Cms_Model::CATE_ADV): ?>
                <a href="<?php echo $article['link']; ?>">
                    <img src="<?php echo $article['img']; ?>" title="彩象彩票">
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
         </div>
    </div>
</div>