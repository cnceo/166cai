	<div class="ds-mod">
        <div class="ds-mod-hd clearfix">
          <h3>专家推荐</h3>
        </div>
        <div class="ds-mod-bd">
          <ul class="infor-mod-con clearfix">
          <?php foreach ($recomm as $recom) {?>
          	<li>
              <div class="zj">
                <div class="zj-pic"><img src="/caipiaoimg/v1.1/img/submitter/<?php echo $recom['submitter_id']?>.jpg" alt=""></div>
                <p class="zj-name"><?php echo $recom['submitter']?></p>
                <div class="zj-type"><?php echo $recom['category_id'] == 9 ? '足彩' : '篮彩'?>专家</div>
              </div>
              <div class="zj-con">
                <span class="sj"></span>
                <h5><a target="_blank" href="/info/zjtj<?php echo ($recom['category_id'] == 9 ? 'zq' : 'lq')?><?php echo '/'.$recom['id']?>"><?php echo $recom['title']?></a></h5>
                <p><?php echo mb_substr(strip_tags(htmlspecialchars_decode($recom['content'])), 0, 35, 'utf-8')?>…</p>
                <div class="zj-detail"><a target="_blank" href="/info/zjtj<?php echo ($recom['category_id'] == 9 ? 'zq' : 'lq')?>/<?php echo $recom['id']?>" class="btn-sup">详情</a></div>
              </div>
            </li>
          <?php }?>
          </ul>
        </div>
      </div>