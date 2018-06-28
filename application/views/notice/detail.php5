<base target="_blank" />
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<div class="wrap_in article-container">
    <div class="article_tile">
        <h1><?php echo $result['title']; ?></h1>
        <span>时间：<?php echo date('Y-m-d H:i', $result['addTime']); ?>&nbsp;&nbsp;&nbsp;&nbsp;来源：<?php $noticeType = $this->config->item('noticeType');
echo $noticeType[$result['category']]; ?></span>
    </div>
    <?php echo htmlspecialchars_decode($result['content']); ?>
</div>