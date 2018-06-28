<div class="aside">
    <dl class="help-side-nav">
        <dt class="help-home" onclick="window.location.href='<?php echo $baseUrl;?>help/index/b4-i1';" style="cursor: pointer">帮助首页</dt>
        <?php if (isset($help_center)):
            foreach ($help_center as $k => $v): ?>
        <input type="hidden" id="f_t<?php echo $k;?>" value="<?php echo ($b == $k)?1:0 ?>"/>
                <dt class="<?php echo (in_array($k, $help_empty) && ($b == $k) && ($i==0))?$help_center_type_logo[$k].' current':$help_center_type_logo[$k];?>" id="help_big_type<?php echo $k;?>"><a onclick="showAndHide(<?php echo $k; ?>,<?php echo (in_array($k, $help_empty)) ? 1 : 0; ?>,'<?php echo $help_center_type_logo[$k];?>','help_big_type',<?php echo $b;?>);"><i></i><?php echo $help_center_type[$k]; ?><em></em></a></dt>
                <span class="help_left_t" id="help_left<?php echo $k; ?>" <?php if ($b == $k) { ?>style="display: block;"<?php } else { ?>style="display:none;"<?php } ?>>
                    <?php foreach ($v as $ks => $vs):
                        if (!empty($vs['name'])): ?>
                            <dd <?php if (($b == $k) && $s == ($ks+1)): ?>class="current"<?php endif; ?>><a onclick="window.location.href='<?php echo $baseUrl; ?>help/index/b<?php echo $k; ?>-s<?php echo $ks+1; ?>';"><?php echo $vs['name']; ?><em></em></a></dd>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </span>
                <?php
            endforeach;
        endif;
        ?>
    </dl>
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
    function showAndHide(flag,key,logo,id_value,big_type_value){
        var f_t = $('#f_t'+flag).val();
        if(f_t == 0){
           $('#help_left'+flag).show(); 
           $('#f_t'+flag).val(1);
        }else{
            $('#help_left'+flag).hide(); 
            $('#f_t'+flag).val(0);
        }
        if(key == 1){
            window.location.href='<?php echo $baseUrl; ?>help/index/b'+flag+'-f1';
            $('#'+id_value+flag).attr('class',logo+' current');
        }
//        else{
//            $('#'+id_value+big_type_value).attr('class',logo);
//        }
    }
</script>