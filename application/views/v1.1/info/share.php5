<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/simple-share.min.js'); ?>?>"></script>
<script>
    var share = new SimpleShare({
        url: location.href,
        title: '《<?php echo $result["title"]; ?>》',
        content: '<?php echo mb_substr(strip_tags(htmlspecialchars_decode(preg_replace('/\s/', ' ', $result["content"]))), 0,
                20) . "..."; ?>',
        pic: '<?php echo $this->config->item('pages_url')?>/caipiaoimg/v1.1/img/logo/logo-square.png'    
    });
</script>
<div class="share">
    <span>分享到：</span>
    <a href="javascript:share.weibo();"
       class="wx"
       title="分享到新浪微博"></a>
    <a href="javascript:share.qzone();"
       class="wb"
       title="分享到QQ空间"></a>
    <a href="javascript:share.tqq();"
       class="kj"
       title="分享到腾讯微博"></a>
</div>