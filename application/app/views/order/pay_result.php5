<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>支付结果</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper pay-result" style="min-height:100%; background: #fff;">
        <div class="pay-result-hd mod-result<?php if($pay_status != 'true'){echo '-false';}?>">
            <div class="mod-result-hd">
                <?php if($orderType == 4){
                    $str=($ctype == 1 ? '参与' : '发起')."合买";
                }elseif($orderType == 5){
                    $str="定制跟单";
                }else{
                    $str="支付";
                } ?>
                <?php if($pay_status == 'true'): ?>
                <h1 class="pay-result-title mod-result-title"><?php echo $str?>成功</h1>
                <p>
                    <?php if($orderType != 5){ ?>
                    <?php echo ($orderType == 4) ? '合买满员将' : '正在'?>送往投注站出票...
                    <?php }else{ ?>
                    发起人发方案时，系统会按定制时间顺序去认购
                    <?php } ?>
                </p>
                <?php else: ?>
                <h1 class="pay-result-title mod-result-title"><?php echo $str?>失败</h1>
                <?php if($orderType != 5){ ?>
                <p>超过本期最后截止时间，如已支付，将退款至您的账户</p>
                <?php } ?>
                <?php endif; ?>
            </div>
            <div class="mod-result-bd"> 
                <ul class="cp-list">
                    <li>
                        <div class="cp-form-group">
                            <div class="cp-form-item">
                                <label for=""><?php echo ($orderType == 5) ? '定制彩种' : '预约彩种'?>:</label>
                                <span><?php echo $cnName; ?></span>
                            </div>
                            <div class="cp-form-item">
                                <label for="">支付金额:</label>
                                <span>
                                    <?php if($orderType != 5) {?>
                                    <?php echo number_format(ParseUnit($money, 1), 2); ?>元
                                    <?php  }else{ ?>
                                    <?php echo ($payType==0)?(number_format(ParseUnit($money, 1), 2)).'元':'无（实时扣款）'; ?>
                                    <?php } ?>
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="mod-result-ft">
                <?php
                $agentInfo = $_SERVER['HTTP_USER_AGENT'];
                preg_match("/#appVersionName:(.*?),appVersionCode:(\d+),channel:(.*?)#/is", $agentInfo, $match);
                $versionInfo = array(
                    'appVersionName' => $match[1] ? $match[1] : '1.0',
                    'appVersionCode' => $match[2] ? $match[2] : 0,
                    'channel' => $match[3] ? $match[3] : '',
                );
                ?>
                <?php if($versionInfo['appVersionCode'] < 40600){ ?>
                <div class="btn-group"> 
                    <?php if($pay_status == 'true'): ?>
                    <button class="btn btn-cancel" onClick="window.location.href='<?php echo $orderUrl; ?>';">查看订单详情</button>
                    <?php else: ?>
                    <button class="btn btn-cancel" onClick="android.goIntentUser();">查看账户详情</button>
                    <?php endif; ?>
                    <?php if ($orderType == 4 && $ctype == 1) {?>
                         <button class="btn btn-confirm" onclick="android.goHomeUnite(<?php echo $lid;?>);">再来一单</button>
                    <?php }elseif($orderType == 5) {?>
                         <button class="btn btn-confirm" onclick="android.goUniteRedUser(0);">继续定制</button>
                    <?php }else{ ?>     
                         <button class="btn btn-confirm" onclick="bet.btnclick('<?php echo $lid;?>', '<?php echo $enName;?>');">再来一单</button>
                    <?php }?>
                </div>
                <?php }else{ ?>
                <div class="btn-group">
                    <?php if ($orderType == 4 && $ctype == 1) {?>
                         <button class="btn btn-confirm" onclick="android.goHomeUnite(<?php echo $lid;?>);">再来一单</button>
                    <?php }elseif($orderType == 5) {?>
                         <button class="btn btn-confirm" onclick="android.goUniteRedUser(0);">继续定制</button>
                    <?php }else{ ?>     
                         <button class="btn btn-confirm" onclick="bet.btnclick('<?php echo $lid;?>', '<?php echo $enName;?>');">再来一单</button>
                    <?php }?>
                </div>
                <div class="btn-group">
                    <?php if($pay_status == 'true'): ?>
                    <button class="btn btn-cancel" onClick="" id="tz">查看订单详情</button>
                    <?php else: ?>
                    <button class="btn btn-cancel" onClick="android.goIntentUser();">查看账户详情</button>
                    <?php endif; ?>
                    <button class="btn btn-cancel" onClick="android.goMainActivity('tabHome')">返回首页</button>
                </div>
                <?php }?>
            </div>
        </div>    
        <?php if($pay_status == 'true' && !empty($banner)): ?>
        <!-- 支付成功Banner -->
        <aside class="img2active">
            <a href="javascript:;" class="img-link">
                <img src="<?php echo $banner['imgUrl']; ?>" alt="">
            </a>
        </aside>
        <?php endif; ?>
    </div> 
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

            var appAction = "<?php echo $banner['appAction']; ?>";
            var lid = "<?php echo $banner['tlid']; ?>";
            var enName = "<?php echo $banner['enName']; ?>";
            var webUrl = "<?php echo $banner['webUrl']; ?>";

            $('.img-link').on('click', function(){
                try{
                    // 点击事件
                    android.umengStatistic('webview_paysuccess_ad');
                }catch(e){
                    // ...
                }
                if(appAction == 'bet'){
                    bet.btnclick(lid, enName);
                }else if(appAction == 'email'){
                    android.goBindEmail('');
                }else if(appAction == 'unsupport'){
                    $.tips({
                        content:'请前往设置页面升级至最新版本！',
                        stayTime:2000
                    });
                }else if(appAction == 'ignore'){
                    $.tips({
                        content:'您已绑定过邮箱',
                        stayTime:2000
                    });
                }else{
                    window.location.href = webUrl;
                }
            });
            $('#tz').on('click', function(){
                android.goMainActivity("tabHome","<?php echo 'https:'.$orderUrl; ?>","<?php echo $lid?>");
            });
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>