<td colspan="10">
    <div class="jc-table-more">
        <a href="javascript:;" class="jc-table-action">收起<i class="arrow"></i></a>

        <div class="more-item bqc-options more-item1">
            <div class="more-item-hd">半全场
                <?php if ($match['bqcFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php if ($match['bqcGd']): ?>
                            <?php foreach ($bqcOptions as $key => $value): ?>
                                <a onselectstart="return false;"
                                   style="-moz-user-select: none"
                                   class="bqc-option <?php if(isset($match['result']) && $match['full_score']){
                                       if($match['result']['bqc']==trim($value)){
                                           echo "bingo";
                                       }
                                   }?>"
                                   data-val="<?php echo $key; ?>"
                                   data-odd="<?php if($match['m_status']==1){echo 1;}
                                   elseif($match['result']['bqc']==trim($value) || !$match['full_score']){echo $match['bqcSp' . $key];}
                                   else{ echo 0;} ?>">
                                    <?php echo $value; ?>
                                    <s><?php echo $match['bqcSp' . $key]; ?></s>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="not-on-sale">未开售</p>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
        <div class="more-item cbf-options more-item<?php echo $match['bfGd'] ? 3 : 1?>">
            <div class="more-item-hd">比分
                <?php if ($match['bfFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>
            <ul>
                <?php if ($match['bfGd']): ?>
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
                                data-odd="<?php if($match['m_status']==1){echo 1;}
                                elseif(in_array($match['result']['bf'],$cbfWinOptions))
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
                                    data-odd="<?php if($match['m_status']==1){echo 1;}
                                    elseif(in_array($match['result']['bf'],$cbfDrawOptions))
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
                    <li>
                        <div class="more-item-bd">
                            <?php foreach ($cbfLoseOptions as $key => $value): ?>
                                <a onselectstart="return false;"
                                   style="-moz-user-select: none"
                                   class="cbf-option <?php if(isset($match['result'])  && $match['full_score']){
                                            if(in_array($match['result']['bf'],$cbfLoseOptions))
                                            { echo ($match['result']['bf']==trim($value))?"bingo":""; }
                                            elseif($match['result']['spf']==0)
                                            { echo trim($value)=='负其他'?"bingo":""; }
                                   }?>" 
                                   data-val="<?php echo $key; ?>"
                                data-odd="<?php if($match['m_status']==1){echo 1;}
                                elseif(in_array($match['result']['bf'],$cbfLoseOptions))
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
                <?php else: ?>
                    <li>
                        <div class="more-item-bd">
                            <p class="not-on-sale">未开售</p>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="more-item jqs-options more-item1">
            <div class="more-item-hd">总进球
                <?php if ($match['jqsFu']): ?>
                    <div class="mod-sup">
                        <i class="mod-sup-bg"></i><u>单</u>
                    </div>
                <?php endif; ?>
            </div>
            <ul>
                <li>
                    <div class="more-item-bd">
                        <?php if ($match['jqsGd']): ?>
                            <?php foreach ($jqsOptions as $key => $value): ?>
                                <a onselectstart="return false;"
                                   style="-moz-user-select: none"
                                   class="jqs-option <?php if(isset($match['result']) && $match['full_score']){
                                       if($match['result']['jqs']==$key || ($match['result']['jqs']>7 && $key==7)){
                                           echo "bingo";
                                       }
                                   }?>"
                                   data-val="<?php echo $key; ?>"
                                   data-odd="<?php if($match['m_status']==1){ echo 1;}elseif($match['result']['jqs']==$key || ($match['result']['jqs']>7 && $key==7) || !$match['full_score']){ echo  $match['jqsSp' . $key];}else{ echo 0;} ?>">
                                    <?php echo $value; ?>
                                    <s><?php echo $match['jqsSp' . $key]; ?></s>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="not-on-sale">未开售</p>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</td>