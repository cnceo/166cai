<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/help.min.css');?>"/>
<div class="wrap help-container">
    <div class="l-frame">
        <?php echo $this->load->view('v1.1/elements/help/left'); ?>
        <div class="l-frame-cnt">
            <div class="help-content">
                <?php
                if (empty($t)) {
                    echo str_replace(
                            array('#jclq#', '#syydj#','#baseUrl#','#b#','#s#'), 
                            array(
                                '<img src="/caipiaoimg/v1.1/images/jclq.png"/>', 
                                '<img src="/caipiaoimg/v1.1/images/syydj.png" type="image/png" style="cursor: default;cursor: default;"/>',
                                $baseUrl,
                                $b,
                                $s), 
                            $help_center[$b][empty($s)?'0':$s-1]['introduction'] );
                } else {
                    echo str_replace(
                        array('#jclq#', '#syydj#'), 
                        array(
                            '<img src="/caipiaoimg/v1.1/images/jclq.png"/>', 
                            '<img src="/caipiaoimg/v1.1/images/syydj.png" type="image/png" style="cursor: default;cursor: default;"/>'
                        ), 
                        $help_center[$b][empty($s)?'0':$s-1]['rule'] );
                }
                ?>
            </div>
        </div>
    </div>
