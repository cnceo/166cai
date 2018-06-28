<?php if(!$this->is_ajax):?>
<link rel="stylesheet" href="/caipiaoimg/v1.1/styles/detail.min.css">
<div class="wrap_in">
	<?php include('breadcrumb.php5') ?>
  <div class="detail-container clearfix">
    <div class="detail-container-l">
      <div class="mod-list-hd">
        <h1><?php echo $cname?></h1>
      </div>
<?php endif;?>
      <div class="mod-list-bd">
      <?php for ($i = 1; $i <= 4; $i++) {?>
        <ul class="mod-article-list">
        <?php if (!empty($data[($i-1)*5])) {
        foreach (array_slice($data, ($i-1)*5, 5) as $value) {?>
        	<li><i class="dian"></i><a target="_blank" href="/info/<?php echo $ename?>/<?php echo $value['id']?>"><?php echo $value['title']?></a><span><?php echo $value['show_time']?></span></li>
        <?php }
        }?>
        </ul>
      <?php }?>
      </div>
      <div class="mod-list-ft">
        <div class="stat">
          &gt;
          <span>总计<?php echo $num?>条</span>
          <span>共<?php echo $pagenum?>页</span>
        </div>
        <!-- pagination -->
        <?php echo $pagestr?>
        <!-- pagination end -->
      </div>
<?php if(!$this->is_ajax):?>
    </div>
    <div class="detail-side">
    <?php switch ($category){
    	case 1:
    	case 2:
    	case 3:
    	case 4:
    	case 5:
    		include('kaijiang.php5');
    		include('constellation.php5');
    		include('trend.php5');
    		break;
    	case 6:
    	case 8:
    		include('jc_mod_compet.php5');
    		include('jc_mod_recomed.php5');
    		include('jc_ad.php5');
    		break;    
    	case 7:
    		include('kaijiang.php5');
    		include('jc_mod_recomed.php5');
    		include('jc_ad.php5');
    		break;
    }?>
    </div>
  </div>
</div>
<?php endif;?>