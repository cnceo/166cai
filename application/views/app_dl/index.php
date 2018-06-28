<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>彩象彩票网</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="www..51caixiang.com">
    <link rel="stylesheet" href="<?php echo $pagesUrl; ?>css/bootstrap.min.css">
    <style>
        .btn {
            width: 100%;
        }
        .download-tips {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            z-index: 100;
        }
        .download-prompt-img {
            width: 92%;
            margin-left: 4%;
            margin-right: 4%;
        }

        .shadow {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 10;
            opacity: 0.5;
            background: #000;
        }
    </style>
    <script src="<?php echo $pagesUrl; ?>js/jquery-1.7.2.min.js"></script>
    <script>
        $(function() {
            $('#download').click(function(event) {
                event.stopPropagation();
                var ua = $('#user_agent').val().toLowerCase();
                if (ua.indexOf('micromessenger') > 0) {
                    event.preventDefault();
                    $('#download_tips').removeClass('hidden');
                    $('#shadow').removeClass('hidden');
                }
            });

            $('body').click(function() {
                $('#download_tips').addClass('hidden');
                $('#shadow').addClass('hidden');
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="jumbotron">
            <div id="shadow" class="shadow hidden"></div>
            <div id="download_tips" class="hidden download-tips" >
                <img class="download-prompt-img" src="<?php echo $pagesUrl; ?>images/download_prompt.png?t=0618" />
                <input id="user_agent" type="hidden" value="<?php echo $_SERVER['HTTP_USER_AGENT']; ?>" />
            </div>
            <div>
                <h1>彩象彩票</h1>
                <h4>带给您不一样的惊喜</h4>
                <p class="lead" style="margin-top: 20px;">
                    彩象提供大乐透、双色球、11选5、竞彩足球等全国彩票的购买下注、开奖查询、订单跟踪的一站式服务，为广大彩民打造简单、便捷、专业、安全的购彩体验。
                </p>
                <p class="lead">
                    作为淘宝、支付宝的专业合作方，我们诚挚提供最优质的购彩服务，让您倍感放心与贴心！
                </p>
            </div>
            <p class="text-center clearfix ">
                <a id="download" style="margin-bottom: 20px;" class="btn btn-lg btn-success pull-left" href="<?php echo $dlUrl; ?>" role="button">APP下载</a>
                <a class="" style="display: block; color: #7777ff; font-size: .6em; margin-top: 20px;" href="http://<?php echo $channelName; ?>.51caixiang.com" role="button">进入网页(建议使用电脑浏览器打开)</a>
            </p>
        </div>
    </div>
</body>
</html>