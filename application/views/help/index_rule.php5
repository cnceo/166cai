<base target="_blank" />
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>"/>
<div class="wrap_in help-container">
    <div class="help-section clearfix">
        <?php echo $this->load->view('elements/help/left'); ?>
        <div class="article">
            <div class="help-content">
                <?php
                if (empty($t)) {
                    echo str_replace(
                            array('#jclq#', '#syydj#','#baseUrl#','#b#','#s#'), 
                            array(
                                '<img src="/caipiaoimg/v1.0/images/jclq.png"/>', 
                                '<img src="/caipiaoimg/v1.0/images/syydj.png" type="image/png" style="cursor: default;cursor: default;"/>',
                                $baseUrl,
                                $b,
                                $s), 
                            $help_center[$b][empty($s)?'0':$s-1]['introduction'] );
                } else {
                    echo str_replace(
                        array('#jclq#', '#syydj#'), 
                        array(
                            '<img src="/caipiaoimg/v1.0/images/jclq.png"/>', 
                            '<img src="/caipiaoimg/v1.0/images/syydj.png" type="image/png" style="cursor: default;cursor: default;"/>'
                        ), 
                        $help_center[$b][empty($s)?'0':$s-1]['rule'] );
                }
                ?>
            </div>
        </div>
    </div>
