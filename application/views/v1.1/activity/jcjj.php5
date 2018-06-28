<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui" />
    <meta>
    <title>竞彩加奖</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>

    <style>
        body {
            background: #0d0d39;
            font-size: 14px;
            color: #ccebff;
        }
        
        .eurojj-wrap {
            overflow: hidden;
            background: #0045b8 url(<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/bg.jpg');?>) 50% 0 no-repeat;
            font-size: 14px;
        }
        
        .eurojj-hd {
            background-position: 50% 0;
            background-repeat: no-repeat;
        }
        
        .eurojj-hd .wrap {
            position: relative;
            display: block;
            height: 424px;
            background-position: 50% 0;
            background-repeat: no-repeat;
        }
        
        .eurojj-hd .wrap:hover {
            text-decoration: none;
        }
        
        .eurojj-hd .wrap h1 {
            text-indent: -150%;
            overflow: hidden;
            font-size: 0;
        }
        
        .eurojj-hd .wrap p {
            margin-top: 231px;
            text-align: center;
            font-size: 18px;
            color: #fff;
        }
        
        .eurojj-hd .wrap a {
            position: absolute;
            left: 506px;
            top: 252px;
            width: 144px;
            height: 42px;
        }
        
        .eurojj-wrap h2 {
            height: 28px;
            margin-bottom: 14px;
            line-height: 1.1;
            font-weight: bold;
            font-size: 24px;
            color: #fff;
        }

        .eurojj-bd {
            position: relative;
            z-index: 1;
            margin-top: -20px;
        }
        
        .eurojj-table {
            float: left;
            width: 480px;
            margin-bottom: 60px;
        }
        
        .eurojj-table h2 i {
            display: inline-block;
            vertical-align: top;
            *vertical-align: 0;
            width: 38px;
            height: 28px;
            background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/sprite-eurojj.png');?>) 0 0 no-repeat;
        }
        
        .eurojj-table .jc-inTable {
            width: 100%;
            height: 280px;
            border: 3px solid #171752;
        }
        
        .eurojj-table .jc-inTable th {
            height: 38px;
            background: #171752;
            text-align: center;
            border-bottom: 3px solid #171752;
            color: #fff;
        }
        
        .eurojj-table .jc-inTable td {
            height: 39px;
            background: #ffffee;
            text-align: center;
            color: #d17818;
        }
        
        .eurojj-table .jc-inTable .eurojj-table-ftd {
            color: #d17818;
        }
        .eurojj-table .jc-inTable .eurojj-table-ftd:first-child {
            color: #333;
        }
        
        .eurojj-table .eurojj-table-odd td {
            background: #fffad6;
        }
        
        .eurojj-match {
            float: right;
            width: 470px;
        }
        
        .eurojj-match h2 {
            overflow: hidden;
        }
        
        .eurojj-match h2 i {
            display: inline-block;
            vertical-align: top;
            *vertical-align: 0;
            width: 28px;
            height: 28px;
            background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/sprite-eurojj.png');?>) 0 -28px no-repeat;
        }
        
        .eurojj-match-bd {
            background: #171752;
        }
        
        .eurojj-match .no-match {
            height: 258px;
            background: #ffffef;
            border-radius: 7px;
            text-align: center;
            line-height: 258px;
            font-size: 16px;
            color: #333;
        }
        
        .eurojj-match .no-match a {
            color: #e60000;
        }
        
        .eurojj-match li a {
            float: right;
        }
        
        .eurojj-match ul {
            margin: -12px -20px 0;
        }
        
        .eurojj-match li {
            height: 60px;
            margin-bottom: 11px;
            padding: 12px 20px 12px 30px;
            background: #ffffef;
            color: #999;
        }
        
        .eurojj-match li strong {
            display: block;
            margin-top: 10px;
            line-height: 1.1;
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }
        
        .btn-main {
            margin-top: 10px;
        }
        
        .eurojj-rule {
        	color: #FFF;
        	margin-bottom: 60px;
            clear: both;
        }
        
        .eurojj-rule h2 {
            background-position: 0 -80px;
        }
        
        .eurojj-rule li {
            margin-bottom: 16px;
        }
        
        .eurojj-rule li span {
            float: left;
            width: 20px;
            height: 20px;
            margin: 0 0 0 -26px;
            /*background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/sprite-eurojj.png');?>) 0 -120px no-repeat;*/
            text-align: center;
            line-height: 20px;
            font-family: Arial;
        }
        
        .jj-table {
            width: 100%;
            height: 182px;
            font-size: 14px;
        }
        
        .jj-table th {
            height: 26px;
            border-bottom: 2px solid #00446e;
            text-align: center;
            font-weight: bold;
            color: #75caff;
        }
        .jj-table td {
            border-top: 1px solid #3278a3;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php if (empty($this->uid)): ?>
    <div class="top_bar">
       <?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
    </div>
    <?php else: ?>
    <div class="top_bar">
       <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
    </div>
    <?php endif; ?>
    <div class="eurojj-wrap">
        <center style="padding-top: 100px;">加载中。。。</center>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/template.js');?>"></script>
    <script id="eurojj" type="text/html">
        <div class="eurojj-hd">
            <a href="{{dataInfo.header.url}}" target="_blank" class="wrap" style="background-image: url(<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/{{dataInfo.header.bannerName}}');?>)">
                <h1>{{dataInfo.header.title}}</h1>
                <p>{{dataInfo.header.time[indexTime]}}</p>
            </a>
        </div>
        <div class="wrap eurojj-bd">
            <div class="eurojj-table">
                <h2><i></i>{{dataInfo.tableA.title}}</h2>
                <table class="jc-inTable" id="table">
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

            <div class="eurojj-match">
                <h2><i></i>{{dataInfo.tableB.title}}<s>({{dataInfo.tableB.note}})</s></h2>
                <div class="eurojj-match-bd">
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/active/zmjj2/{{dataInfo.tableB.appDlImg}}');?>" width="470" height="277" alt="{{dataInfo.tableB.appDlImgAlt}}">
                </div>
            </div>

            <div class="eurojj-rule">
                <h2>活动规则</h2>
                <ol>
                    {{each dataInfo.rule}}
                    {{if $index === 0}}
                    <li>{{$index + 1}}、{{$value[indexTime]}}</li>
                    {{else}}
                    <li>{{$index + 1}}、{{$value}}</li>
                    {{/if}}
                    {{/each}}
                </ol>
            </div>
        </div>
    </script>
    <script>
        $(function() {
        	$.getScript('/source/js/<?php echo $js?>.js?20180608', function(data, textStatus, XHR) {
                teamData.nowTime = new Date(XHR.getResponseHeader('Date'));
                var year = teamData.nowTime.getFullYear();
                var arrTime = teamData.dataInfo.header.time;
                $(arrTime).each(function (i, item) {
                    var itemTime = item.split('~');
                    itemTime = $(itemTime).map(function (x, date) {
                        return this.split('日')[0].split('月').join('/')
                    })
                    if (+teamData.nowTime <= +new Date(year + '/' + itemTime[1])) {
                        teamData.indexTime = i
                        return false;
                    } else {
                        teamData.indexTime = i
                    }
                })
                if (teamData) {
                    $('.eurojj-wrap').html(template('eurojj', teamData));
                }
            })
        });
    </script>
</body>

</html>