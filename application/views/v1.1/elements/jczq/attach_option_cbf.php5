<td colspan="4">
    <div class="jc-table-more">
        <a href="javascript:;" class="jc-table-action">收起<i class="arrow"></i></a>
        <div class="more-item cbf-options more-item1">
            <div class="more-item-hd">
                主队胜
                <?php if ($match['bfFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php foreach ($cbfWinOptions as $key => $value): ?>
                            <a
                                onselectstart="return false;"
                                style="-moz-user-select: none"
                                class="cbf-option <?php if(isset($match['result']) && $match['full_score']){
                                            if(in_array($match['result']['bf'],$cbfWinOptions))
                                            { echo ($match['result']['bf']==trim($value))?"bingo":""; }
                                            elseif($match['result']['spf']==3)
                                            { echo trim($value)=='胜其他'?"bingo":""; }
                                   }?>"
                                data-val="<?php echo $key; ?>"
                                data-odd="<?php 
                                if(in_array($match['result']['bf'],$cbfWinOptions))
                                { echo ($match['result']['bf']==trim($value))?$match['bfSp' . $key]:0; }
                                elseif($match['result']['spf']==3)
                                { echo trim($value)=='胜其他'?$match['bfSp' . $key]:0; }
                                elseif(!$match['full_score']){echo $match['bfSp' . $key];}else{ echo 0;}
                                ?>">
                                <?php echo $value; ?>
                                <s><?php echo $match['bfSp' . $key]; ?></s>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="javascript:;" class="pick-all">全包</a>
                </li>
            </ul>
        </div>
        <div class="more-item cbf-options more-item1">
            <div class="more-item-hd">
                平局
                <?php if ($match['bfFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>

            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php foreach ($cbfDrawOptions as $key => $value): ?>
                            <a
                                onselectstart="return false;"
                                style="-moz-user-select: none"
                                class="cbf-option <?php if(isset($match['result']) && $match['full_score']){
                                            if(in_array($match['result']['bf'],$cbfDrawOptions))
                                            { echo ($match['result']['bf']==trim($value))?"bingo":""; }
                                            elseif($match['result']['spf']==1)
                                            { echo trim($value)=='平其他'?"bingo":""; }
                                   }?>"
                                data-val="<?php echo $key; ?>"
                                    data-odd="<?php 
                                    if(in_array($match['result']['bf'],$cbfDrawOptions))
                                    { echo ($match['result']['bf']==trim($value))?$match['bfSp' . $key]:0; }
                                    elseif($match['result']['spf']==1)
                                    { echo trim($value)=='平其他'?$match['bfSp' . $key]:0; }
                                    elseif(!$match['full_score']){echo $match['bfSp' . $key];}else{ echo 0;}    
                                ?>">
                                <?php echo $value; ?>
                                <s><?php echo $match['bfSp' . $key]; ?></s>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="javascript:;" class="pick-all">全包</a>
                </li>
            </ul>
        </div>
        <div class="more-item cbf-options more-item1">
            <div class="more-item-hd">
                客队胜
                <?php if ($match['bfFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php foreach ($cbfLoseOptions as $key => $value): ?>
                            <a
                                onselectstart="return false;"
                                style="-moz-user-select: none"
                                class="cbf-option <?php if(isset($match['result']) && $match['full_score']){
                                            if(in_array($match['result']['bf'],$cbfLoseOptions))
                                            { echo ($match['result']['bf']==trim($value))?"bingo":""; }
                                            elseif($match['result']['spf']==0)
                                            { echo trim($value)=='负其他'?"bingo":""; }
                                   }?>"
                                data-val="<?php echo $key; ?>"
                                data-odd="<?php 
                                if(in_array($match['result']['bf'],$cbfLoseOptions))
                                { echo ($match['result']['bf']==trim($value))?$match['bfSp' . $key]:0; }
                                elseif($match['result']['spf']==0 && $match['full_score'])
                                { echo trim($value)=='负其他'?$match['bfSp' . $key]:0; }
                                elseif(!$match['full_score']){echo $match['bfSp' . $key];}else{ echo 0;}       
                                ?>">
                                <?php echo $value; ?>
                                <s><?php echo $match['bfSp' . $key]; ?></s>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="javascript:;" class="pick-all">全包</a>
                </li>
            </ul>
        </div>
    </div>
</td>