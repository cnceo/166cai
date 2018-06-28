<div class="l-frame-menu m-menu">
	<h2 onclick="window.location.href='<?php echo $baseUrl;?>help/index/b4-i1';" class="m-menu-title">帮助首页</h2>
	<div class="m-menu-bd">
        <?php if (isset($help_center)):
        $logo = $this->config->item('help_center_type_logo');
            foreach ($help_center as $k => $v): ?>
                <dl id="help_big_type<?php echo $k;?>" <?php if ($b == $k) {?>class="show-dd"<?php } ?>>
                	<dt class="<?php if ($b == $k && count($v) == 1) {?>current<?php }?> <?php echo $logo[$k]?>">
	                	<a href="<?php echo count($v) > 1 ? 'javascript:;' : "/help/index/b".$k."-f1"?>" target="_self">
	                		<i class="icon"></i><?php echo $help_center_type[$k]; ?><i class="arrow"></i>
	                	</a>
                	</dt>
                    <?php foreach ($v as $ks => $vs):
                        if (!empty($vs['name'])): ?>
                            <dd <?php if ($b == $k && $ks+1 == $s) {?>class="current"<?php }?>>
	                            <a href="/help/index/b<?php echo $k; ?>-s<?php echo $ks+1; ?>" target="_self"><?php echo $vs['name']; ?>
	                            	<i class="arrow"></i>
	                            </a>
                            </dd>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </dl>
                <?php
            endforeach;
        endif;
        ?>
    </div>
<!--    <div class="other-contact">
        <a href="">问题反馈</a>
        <a href="">在线客服</a>
    </div>-->
</div>
<script>
    /**
     * 显示 和 隐藏 
     * @Author liusijia
     */
     $(document).ready(function(){
         $(".qs_link").click(function(){
            $(this).parents(".qs_box").find(".detail_bg_info").toggle();
         })
         $(".close_x").click(function(){
             $(this).parents(".detail_bg_info").hide();
         });


         $('.help-container').find('.m-menu').on('click', 'dt', function(){
             var $dl = $(this).parent('dl');
             if($dl.children('dd').length > 0){
                 $dl.toggleClass('show-dd');
             }
         })
     });
//     function showAndHide(flag,key,logo,id_value,big_type_value){
//         console.log(111);
//         var f_t = $('#f_t'+flag).val();
//         if(f_t == 0){
//            $('#help_left'+flag).addClass("show-dd"); 
//            $('#f_t'+flag).val(1);
//         }else{
//             $('#help_left'+flag).hide(); 
//             $('#f_t'+flag).val(0);
//         }
//         if(key == 1){
           // window.location.href='<?php // echo $baseUrl; ?>help/index/b'+flag+'-f1';
//             $('#'+id_value+flag).attr('class',logo+' current');
//         }
//        else{
//            $('#'+id_value+big_type_value).attr('class',logo);
//        }
//     }
</script>