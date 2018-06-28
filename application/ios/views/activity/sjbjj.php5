<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>世界杯加奖</title>
    <link href="<?php echo getStaticFile('/caipiaoimg/static/css/active/plus-awards8.min.css');?>" rel="stylesheet" type="text/css" />
</head>

<body ontouchstart="">
    <div class="wrap p-plus-awards">
        <center style="padding-top: 100px;">加载中。。。</center>
    </div>
    <script id="eurojj" type="text/html">
        <a href="javascript:;" target="_blank" class="plus-awards-hd">
            <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/plus-awards8/{{dataInfo.header.bannerName}}');?>" alt="">
            <h1>{{dataInfo.header.title}}</h1>
            <p class="plus-awards-time">加奖时间：{{dataInfo.header.time[indexTime]}}</p>
        </a>
        <div class="plus-awards-bd">
            <div class="m-plus-awards">
                <div class="plus-awards-table">
                    <table>
                        <thead>
                            <tr>
                                {{each dataInfo.tableA.th.title}}
                                <th width="{{dataInfo.tableA.th.width[$index]}}">{{$value}}</th>
                                {{/each}}
                            </tr>
                        </thead>
                        <tbody>
                            {{each dataInfo.tableA.td}}
                            <tr class="{{$index % 2 == 0 ? 'eurojj-table-odd' : 'eurojj-table-even'}}">
                                {{each $value}}
                                <td class="eurojj-table-ftd">{{$value}}</td>
                                {{/each}}
                            </tr>
                            {{/each}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="plus-rule">
                <div class="plus-rule-hd">
                    <h2>活动规则</h2>
                </div>
                <ol>
              	{{each dataInfo.rule}}
              		{{if $index === 0}}
              		<li><span>{{$index + 1}}</span>、{{$value[indexTime]}}</li>
              		{{else}}
              		<li><span>{{$index + 1}}</span>、{{$value}}</li>
              		{{/if}}
              	{{/each}}
    			</ol>
                <div class="rule-arrow"><span></a>规则<i></i></span></div>
            </div>
            <div class="rule-bg"></div>

            <div class="btn-group">
                <a href="javascript:;" onclick="window.webkit.messageHandlers.doBet.postMessage({lid:'42'});" target="_blank" class="btn btn-special btn-join"><span>投注世界杯</span></a>
            </div>
        </div>
    </script>
    <script src="/caipiaoimg/static/js/lib/zepto.min.js"></script>
    <script src="<?php echo $this->config->item('pages_url'); ?>caipiaoimg/v1.1/js/template.js"></script>
    <script>
        $(function(){
			var newTime;
			$.get('/ajax/getTime', function(data){
				newTime = ~~(data) * 1000

				$.get('/source/js/sjbjj.js?20180608', function() {
	        		teamData.nowTime = newTime
	        		var year = new Date(teamData.nowTime).getFullYear();
	        		var arrTime = teamData.dataInfo.header.time;
	        		$(arrTime).each(function (i, item) {
	            		var itemTime = item.split('~');
	            		itemTime = $(itemTime).map(function (x, date) {
	                		return this.split('日')[0].split('月').join('/')
	            		})
	            		if (teamData.nowTime <= +new Date('2018/' + itemTime[1])) {
		                	teamData.indexTime = i
		                	return false;
		            	} else {
		                	teamData.indexTime = i
		            	}
		        	})
					if(teamData){
						$('.p-plus-awards').html(template('eurojj', teamData));
		          		$('.plus-rule').on('click', '.rule-arrow', function() {
		            		$(this).parents('.plus-rule').toggleClass('plus-rule-show');
		            		return false;
		          		})
		          		$('.plus-rule + .rule-bg').on('click', function () {
		            		$('.plus-rule').removeClass('plus-rule-show');
		          		}).on('touchmove', function (e) {
                            e.preventDefault()
                        })
				    }
				})
			})
		});

    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>

</html>