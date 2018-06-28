<base target="_blank" />
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css'); ?>"/>
<div class="wrap_in help-container">
    <div class="help-section clearfix">
        <?php echo $this->load->view('elements/help/left'); ?>
        <?php if ($b == 4 && $i == 1): ?>
            <div class="article">
                <div class="question-top">
                    <h2>新手入门</h2>
                    <ul class="help-question-stemp">
                        <li>
                            <a href="<?php echo $baseUrl;?>help/index/b0-s1">
                                <i class="help-icon-zhuce png_bg"></i>
                                <span>注册登录</span>
                            </a>
                        </li>
                        <li class="split">
                            <em class="png_bg"></em>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl;?>help/index/b1-s1-f1">
                                <i class="help-icon-chongzhi png_bg"></i>
                                <span>充值</span>
                            </a>
                        </li>
                        <li class="split">
                            <em class="png_bg"></em>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl;?>help/index/b2">
                                <i class="help-icon-goucai png_bg"></i>
                                <span>购买彩票</span>
                            </a>
                        </li>
                        <li class="split">
                            <em class="png_bg"></em>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl;?>help/index/b3-s1">
                                <i class="help-icon-info png_bg"></i>
                                <span>开奖中奖</span>
                            </a>
                        </li>
                        <li class="split">
                            <em class="png_bg"></em>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl;?>help/index/b3-s2">
                                <i class="help-icon-tikuan png_bg"></i>
                                <span>兑奖提款</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php $help_data = $help_center[$b][(empty($s) ? '0' : $s - 1)]; ?>
        <div class="article">
            <div class="help-content">
                <h1><?php echo empty($help_data['name']) ? $help_center_type[$b] : $help_data['name']; ?> <div class="hr"></div></h1>
                <div class="p-content">
                    <?php foreach ($help_data['question_list'] as $k => $v): ?>
                        <div class="qs_box">
                            <a class="qs_link" onclick="clickOpen(<?php echo $k + 1; ?>);" href="javascript:;" target="_self"><?php echo ($k + 1) . "." . $v['title']; ?></a>
                            <div class="detail_bg_info" id="question_list<?php echo $k + 1; ?>" <?php 
                                if ($f == ($k + 1) || 
                                    (in_array($b, $help_empty) && $f == 1 && $k == 0) || 
                                    (!in_array($b, $help_empty) && $s > 0 && $k == 0) || 
                                    ($b == 4 && $i == 1 && $k == 0)) { 
                                    ?>style="display:block;"<?php 
                                    } else { 
                                    ?>style="display:none;"<?php } ?>>
                                <span class="lingxing">◆</span>
                                <span class="lingxing_zhe">◆</span>
                                <a class="close_x" onclick="clickClose(<?php echo $k + 1; ?>);" title="关闭"><span>x</span></a>
                                <div>
                                    <?php echo $v['content']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /**
     * 点击打开
     * @Author liusijia
     */
    function clickOpen(k){
        $('#question_list'+k).toggle();
    }
    
    /**
     * 点击关闭
     * @Author liusijia
     */
    function clickClose(k){
        $('#question_list'+k).hide();
    }
</script>


