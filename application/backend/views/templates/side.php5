<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>首页 - 彩票管理后台</title>
        <link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/admin.css">
        <script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery-1.8.3.min.js"></script>
       <script>
      // 侧栏导航
      $(document).ready(function(){
          $(".side-nav h3:not('.home')").bind("click", function () {
                      $(this).next(".sub-nav").eq(0).slideToggle();
          });  
      });
        </script>

    </head>
    <body>
        <div class="side-nav frame-column">
            <h3 class="home"><a href="/backend/main/right"  target="rightFrame" class="current"><i></i>首页<s></s></a></h3>
            <?php foreach($left as $key => $value):  ?>
            <h3><a href="<?php if(empty($value['child']) && !empty($value['url'])): echo $value['url']; else: echo 'javascript:;'; endif;?>" target="rightFrame"><i></i><?php echo $value['name']?><s></s></a></h3>
            <ul class="sub-nav" <?php if(!$value['isShow']): echo "style='display:none'"; endif;?> >
                <?php foreach($value['child'] as $key1 => $value1):  ?>
                <li><a href="<?php echo $value1; ?>" target="rightFrame"><?php echo $key1?><s></s></a></li>
                <?php endforeach; ?>
            </ul>
            <?php endforeach; ?>
        </div>
    </body>
</html>