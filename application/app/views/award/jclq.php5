<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/lottery-notice.min.css');?>">
</head>
<body>
    <div class="wrapper lottery-detail lottery-detail-jclq">
        <table class="table-bet-hd lottery-detail-jclq-hd">
            <colgroup>
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
            </colgroup>
            <thead>
                <tr>
                    <th>赛事编号</th>
                    <th>胜负</th>
                    <th>让分胜负</th>
                    <th>大小分</th>
                    <th>胜分差</th>
                </tr>
            </thead>
        </table>
        <?php if($matches):?>
        <?php foreach ($matches as $date => $value):?>
        <dl class="lottery-result">
        <?php 
        	$matchDate = getWeekByTime(strtotime($date));
        	$week = str_replace('周', '星期', $matchDate);
        	$count = count($value);
        ?>
            <dt><?php echo $date . "  {$week}  {$count}场比赛已开奖";?><i></i></dt>
            <?php foreach ($value as $match):?>
            <dd>
               <table class="table-bet">
                    <colgroup>
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th rowspan="3"><?php echo $match["name"];?><b><?php echo $match["matchId"];?></b></th>
                            <td colspan="4">
                                <div class="lottery-result-num" id="minfo<?php echo $match['mid']; ?>">
                                    <span><?php echo $match["awary"];?></span><em class="special-color"><?php echo $match["score"];?></em><span><?php echo $match["home"];?>(<?php echo $match["let"];?>)</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><b><?php echo $match["sf"];?></b></td>
                            <td><b><?php echo $match["rfsf"];?></b></td>
                            <td><b><?php echo $match["dxf"];?></b></td>
                            <td><b><?php echo $match["sfc"];?></b></td>
                        </tr>
                        <tr>
                            <?php if(!empty($match["showDetail"])): ?>
                            <td colspan="4"><a href="javascript:;" class="btn-viewmore showDetails" data-index="<?php echo $match['mid']; ?>">更多详情</a></td>
                            <?php else: ?>
                            <td colspan="4"><span class="fcw">暂无</span></td>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                </table> 
            </dd>
            <?php endforeach;?>
        </dl>
        <?php endforeach;?>
        <?php else: ?>
        <div class="wrapper no-data" id="no-data3">
            <i class="logo-virtual"></i>
            <p>今日无比赛</p>
        </div>
        <?php endif;?>
        <?php if($channel): ?>
        <div class="fixed-bar">
            <div class="btn-group">
                <a href="<?php echo $this->config->item('pages_url'); ?>app/download?c=<?php echo $channel; ?>" target="_blank" class="btn btn-block-special">下载APP送188元红包</a>
            </div>
        </div>
        <?php endif; ?>
        <div class="ui-popup ui-alert odds-change-list odds-details" style="display:none;">
            <div class="ui-popup-inner">
                <div class="ui-popup-hd">
                </div>
                <div class="ui-popup-bd ui-scroller">
                </div>
                <div class="ui-popup-ft">
                    <a href="javascript:;" class="special-color hideDetail">朕知道了</a>
                </div>
            </div>
            <div class="mask"></div>
        </div>
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
          
            $('.lottery-result').find('dt').on('tap', function(){
                $(this).parent().toggleClass('lottery-result-hide');
            });

            // 查看详情
            var stop = true;
            $('.showDetails').on('tap', function(){
                if(stop){
                    stop = false;
                    var mid = $(this).data('index');
                    var info = $('#minfo' + mid).html();
                    // 去盘口
                    var reg = /\(.*?\)/g;
                    info = info.replace(reg, '');
                    $.ajax({
                        type: 'get',
                        url: '/app/awards/jclqDetail?mid=' + mid,
                        success: function (response) {
                            var response = $.parseJSON(response);
                            if(response.status == '1')
                            {
                                $('.ui-popup-hd').html(info);
                                $('.ui-popup-bd').html(response.data);
                                $('.odds-details').show();
                                try {
                                	android.setFresh('0')
                                } catch (e) {
            						console.log(e)
                                }
                            }else{
                                $.tips({
                                    content: '暂无详情',
                                    stayTime: 2000
                                })
                            }
                            stop = true;
                        },
                        error: function () {
                            stop = true;
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                }
            });

            $('.ui-popup').on('touchmove', '.mask', function (event) {
                event.preventDefault();
            })

            // 关闭
            $('body').on('tap', '.hideDetail', function () {
                var oParent = $(this).parents('.ui-popup');
                oParent.hide();
                try {
                	android.setFresh('1')
                } catch (e) {
					console.log(e)
                }
            })
        });
    </script>
</body>
</html>