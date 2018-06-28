<base target="_blank" />
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<div class="wrap_in article-container">
    <ul class="article-list">
        <?php if ($result):
            foreach ($result as $v): ?>
                <li>
                    <i class="dian"></i><a href="<?php echo $baseUrl; ?>notice/detail/<?php echo $v['id']; ?>" class="p_content"><?php echo $v['title']; ?></a><span class="p_time"><?php echo date('Y年m月d日 H:i', $v['addTime']); ?></span>
                </li>
            <?php endforeach;
        endif; ?>
    </ul>
    <?php if($page_num > 1){echo $pagestr;} ?>
</div>