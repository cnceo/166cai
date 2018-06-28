<?php 
$this->load->config('wenan');
$wenan = $this->config->item('wenan');
?>
<td colspan="9">
    <div class="jc-table-more">
        <a href="javascript:;" class="jc-table-action">收起<i
                class="arrow"></i></a>

        <div class="more-item more-item1">
            <div class="more-item-hd"><?php echo $wenan['jlsf']['0']?></div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php $count=0; ?>
                        <?php foreach ($sfcOptions as $key => $value): ?>
                        <?php $count++; ?>
                            <a class="sfc-option <?php if(isset($match['result']) && $match['full_score']){
                                       if($match['result']['sfc']==$count && $match['result']['sf']==3){
                                           echo "bingo";
                                       }
                                   }?>"
                               data-val="1<?php echo $count; ?>"
                               data-odd="<?php if($match['m_status']==1){echo 1;}
                                                elseif(($match['result']['sfc']==$count && $match['result']['sf']==3) || !$match['full_score']){echo $match['sfcAs' . $key];}
                                                else{echo 0;} ?>">
                                <b><?php echo $value; ?>分</b>
                                <s><?php echo $match['sfcAs' . $key]; ?></s>
                            </a>
                        <?php endforeach ?>
                    </div>
                </li>
            </ul>
        </div>
        <div class="more-item more-item1">
            <div class="more-item-hd"><?php echo $wenan['jlsf']['3']?></div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php $count=0; ?>
                        <?php foreach ($sfcOptions as $key => $value): ?>
                        <?php $count++; ?>
                            <a class="sfc-option <?php if(isset($match['result']) && $match['full_score']){
                                       if($match['result']['sfc']==$count && $match['result']['sf']==0){
                                           echo "bingo";
                                       }
                                   }?>"
                               data-val="0<?php echo $count; ?>"
                               data-odd="<?php if($match['m_status']==1){echo 1;}
                                                elseif(($match['result']['sfc']==$count && $match['result']['sf']==0) || !$match['full_score']){echo $match['sfcHs' . $key];}
                                                else{echo 0;} ?>">                               
                                <b><?php echo $value; ?>分</b>
                                <s><?php echo $match['sfcHs' . $key]; ?></s>
                            </a>
                        <?php endforeach ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</td>