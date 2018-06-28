<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/help.min.css'); ?>"/>
<div class="wrap help-container">
    <div class="l-frame">
        <?php echo $this->load->view('v1.1/elements/help/left'); ?>
        <?php if ($b == 4 && $i == 1): ?>
            <div class="l-frame-cnt">
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
                                <span>兑奖提现</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php $help_data = $help_center[$b][(empty($s) ? '0' : $s - 1)]; ?>
        <div class="l-frame-cnt">
            <div class="help-content">
                <h1><?php echo empty($help_data['name']) ? $help_center_type[$b] : $help_data['name']; ?> <div class="hr"></div></h1>
                <div class="p-content">
                    <?php foreach ($help_data['question_list'] as $k => $v): ?>
                        <div class="qs_box">
                            <a class="qs_link" href="javascript:;" target="_self"><?php echo ($k + 1) . "." . $v['title']; ?></a>
                            <div class="detail_bg_info" id="question_list<?php echo $k + 1; ?>" <?php 
                                if ($f == ($k + 1) || 
                                    (in_array($b, $help_empty) && $f == 1 && $k == 0) || 
                                    (!in_array($b, $help_empty) && $s > 0 && $k == 0 && $f<=1) || 
                                    ($b == 4 && $i == 1 && $k == 0)) { 
                                    ?>style="display:block;"<?php 
                                    } else { 
                                    ?>style="display:none;"<?php } ?>>
                                <span class="lingxing">◆</span>
                                <span class="lingxing_zhe">◆</span>
                                <a class="close_x" title="关闭"><span>×</span></a>
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

