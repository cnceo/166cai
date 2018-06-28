<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/news.css');?>" />
<script>
    $(function() {
        var categorId = parseInt('<?php echo $selectedCategoryId; ?>', 10);
        var currPage = parseInt('<?php echo $currPage; ?>', 10);
        var totalPage = parseInt('<?php echo $totalPage; ?>', 10);
        $('.cate-list').click(function() {
            var $this = $(this);
            $this.find('li').toggleClass('selected');
            $this.parent().find('div').toggleClass('hidden');
        });
        $('.select-page').change(function() {
            var $this = $(this);
            location.href = baseUrl + 'news/index/' + categorId + '?pn=' + $this.val();
        });
        $('.prev-page').click(function() {
            if (currPage > 1) {
                location.href = baseUrl + 'news/index/' + categorId + '?pn=' + (currPage - 1);
            }
        });
        $('.next-page').click(function() {
            if (currPage < totalPage) {
                location.href = baseUrl + 'news/index/' + categorId + '?pn=' + (currPage + 1);
            }
        });
    });
</script>
<div class="news clearfix">
    <div class="newsNav">
        <ul>
            <?php foreach ($categoryNames as $categoryId => $categoryName): ?>
            <?php if ($categoryId == $selectedCategoryId): ?>
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
        <!-- start -->
        <div class="newList">
            <ul>
                <?php foreach ($articles as $article): ?>
                <li>
                    <a href="<?php echo $baseUrl; ?>news/detail/<?php echo $article['id']; ?>">
                        <span class="content">
                            <strong>[<?php echo $categoryNames[$article['category']]; ?>]</strong>
                            <span><?php echo $article['title']; ?></span>
                        </span>
                        <span class="time"><?php echo date('Y-m-d', $article['createTime'] / 1000); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="page">
            <p>
                <span class="prev-page">上一页</span>
                <select class="select-page">
                    <?php for ($page = 1; $page <= $totalPage; ++$page): ?>
                    <option value="<?php echo $page; ?>" <?php if ($page == $currPage) echo 'selected="selected"'; ?>><?php echo $page; ?></option>
                    <?php endfor; ?>
                </select>
                <span class="next-page">下一页</span>
            </p>
        </div>
        <div style="clear:both; float:none;"></div>
        <!-- end -->
    </div>
    <div class="newsRight">
        <div class="cement border">
            <ul class="cate-list">
                <li class="selected">公告</li>
                <li >活动</li>
            </ul>
            <div>
                <?php foreach ($allArticles as $article): ?>
                <?php if ($article['category'] == Cms_Model::CATE_NOTICE): ?>
                <p>
                    <a href="<?php echo $baseUrl; ?>news/index/<?php echo Cms_Model::CATE_NOTICE; ?>">[公告]</a>
                    <a href="<?php echo $baseUrl; ?>news/detail/<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                </p>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="hidden">
                <?php foreach ($allArticles as $article): ?>
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
            <?php foreach ($allArticles as $article): ?>
            <?php if ($article['category'] == Cms_Model::CATE_ADV): ?>
            <a href="<?php echo $article['link']; ?>">
                <img src="<?php echo $article['img']; ?>" title="彩象彩票">
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
         </div>
    </div>
</div>