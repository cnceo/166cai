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
    <title>竞彩加奖</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/plus-awards7.min.css');?>">
</head>

<body ontouchstart="">
    <div class="wrap p-plus-awards">
        <center style="padding-top: 100px;">加载中。。。</center>
    </div>
    <script id="eurojj" type="text/html">
        <a href="javascript:;" target="_blank" class="plus-awards-hd">
            <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/plus-awards7/{{dataInfo.header.bannerName}}');?>" alt="">
            <h1>{{dataInfo.header.title}}</h1>
            <p class="plus-awards-time">{{dataInfo.header.time[indexTime]}}</p>
        </a>
        <div class="plus-awards-bd">
            <div class="m-plus-awards">
                <div class="plus-awards-table">
                    <table>
                        <thead>
                            <tr>
                                {{each dataInfo.tableA.th.title}}
                                <th width="{{dataInfo.tableA.th.width[$index]}}"><span>{{$value}}</span></th>
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
                <a href="javascript:;" onclick="betJz();" target="_blank" class="btn btn-special btn-join">投注竞彩足球</a>
                <a href="javascript:;" onclick="betJl();" target="_blank" class="btn btn-special btn-join">投注竞彩篮球</a>
            </div>
        </div>
    </script>
    <script src="/caipiaoimg/static/js/lib/zepto.min.js"></script>
    <script src="<?php echo $this->config->item('pages_url'); ?>caipiaoimg/v1.1/js/template.js"></script>
    <script>
    var ua = navigator.userAgent.toLowerCase()
    var betJz = function () {
        if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) {
        	bet.btnclick('42', 'jczq');
		} else {
			window.webkit.messageHandlers.doBet.postMessage({lid:'42'});
		}
	}
	var betJl = function () {
		if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) {
			bet.btnclick('43', 'jclq');
		} else {
			window.webkit.messageHandlers.doBet.postMessage({lid:'43'});
		}
	}
    
        $(function(){
			var newTime;
			$.get('/ajax/getTime', function(data){
				newTime = ~~(data) * 1000

				$.get('/source/js/zmjj.js?20180608', function() {
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
		          		})
				    }
				})
			})
		});

    </script>
  <script>
    !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
    gio('init','8d4b2106242d6858', {});
    //custom page code begin here
    //custom page code end here
    gio('send');
  </script>
</body>

</html>