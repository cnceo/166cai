<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">数据分析 </a>&nbsp;&gt;&nbsp;<a href="">概览</a></div>
<div class="data-table-filter mt10">
  <table>
    <colgroup>
        <col width="150">
        <col width="150">
        <col width="160">
    </colgroup>
    <tbody>
    <tr>
      <td colspan="2">
        <div class="filter" id="filter-days">时间：
          <a href="javascript:;" class="filter-options <?php if($days=='7'):?>selected<?php endif; ?>" data-value="7">过去7天</a>
          <a href="javascript:;" class="filter-options <?php if($days=='30'):?>selected<?php endif; ?>" data-value="30">过去30天</a>
          <a href="javascript:;" class="filter-options <?php if($days=='60'):?>selected<?php endif; ?>" data-value="60">过去60天</a>
        </div>
      </td>
      <td>
        <!-- <a href="javascript:;" class="btn-blue ml25">导出</a> -->
      </td>
    </tr>
    </tbody>
  </table>
  <!-- 查询条件 -->
  <input type='hidden' class='' name='days' value='<?php echo $days?>'/>
  <input type='hidden' class='' name='tab1' value='<?php echo $tab1?>'/>
  <input type='hidden' class='' name='tab2' value='<?php echo $tab2?>'/>
</div>

<div class="charts-tab">
    <div class="charts-tab-hd" id="charts-tab-hd-1">
        <a href="javascript:;" class="btn-b-white cur" data-type="conversion">转化率</a>
        <a href="javascript:;" class="btn-b-white" data-type="validUser">有效用户数</a>
    </div>
    <div class="charts-tab-hd" id="charts-tab-hd-date" style="display:none;">
        <a href="javascript:;" class="btn-b-white cur" data-type="day" data-value="7">日</a>
        <a href="javascript:;" class="btn-b-white" data-type="week" data-value="30">周</a>
    </div>
    <ul class="charts-tab-bd">
        <li class="cur" id="tab1">
            <div id="conversion" style="height: 400px;">
                <!-- 此处放图表 -->
            </div>
        </li>
    </ul>
</div>

<div class="charts-tab">
    <div class="charts-tab-hd" id="charts-tab-hd-2">
        <a href="javascript:;" class="btn-b-white cur" data-type="allSale">全国销量</a>
        <a href="javascript:;" class="btn-b-white" data-type="platformSale">平台销量占比</a>
        <a href="javascript:;" class="btn-b-white" data-type="lotterySale">彩种销量占比</a>
        <a href="javascript:;" class="btn-b-white" data-type="lotteryAward">彩种返奖率</a>
    </div>
    <div class="charts-tab-hd" id="charts-tab-hd2-date">
        <a href="javascript:;" class="btn-b-white cur" data-type="day" data-value="7">日</a>
        <a href="javascript:;" class="btn-b-white" data-type="week" data-value="30">周</a>
    </div>
    <ul class="charts-tab-bd">
        <li class="cur" id="tab2">
            <div id="allSale" style="height: 400px;">
                <!-- 此处放图表 -->
            </div>
        </li>
    </ul>
</div>
<script type="text/javascript">
    $(function(){
        var closeTag1 = true;
        var closeTag2 = true;
        $('#filter-days').find('a').on('click', function(){
            if(!$(this).hasClass('selected') && closeTag1 && closeTag2)
            {
                closeTag1 = false;
                closeTag2 = false;
                $('#filter-days a').removeClass('selected');
                $(this).addClass('selected');
                $('input[name="days"]').val($(this).attr('data-value'));
                var days = $('input[name="days"]').val();
                // Ajax 刷新查询 tab1
                var tab1 = $('input[name="tab1"]').val();
                // 有效用户 显示日周月
                if(tab1 == 'validUser'){
                    initDateTab();
                }else{
                    $('#charts-tab-hd-date').hide();
                }
                $.ajax({
                    type: "post",
                    url: '/backend/Analysis/ajaxChart',
                    data: {days:days,ctype:tab1},                   
                    success: function (response1) {
                        closeTag1 = true;
                        $('#tab1').html(response1);
                    },
                    error: function () {
                        closeTag1 = true;
                        alert('网络异常，请稍后再试');
                    }
                });
                // Ajax 刷新查询 tab2 
                var tab2 = $('input[name="tab2"]').val();
                // 全国销量 显示日周月
                if(tab2 == 'allSale'){
                    initDateTab2();
                }else{
                    $('#charts-tab-hd2-date').hide();
                }
                $.ajax({
                    type: "post",
                    url: '/backend/Analysis/ajaxChart',
                    data: {days:days,ctype:tab2},                   
                    success: function (response2) {
                        closeTag2 = true;
                        $('#tab2').html(response2);
                    },
                    error: function () {
                        closeTag2 = true;
                        alert('网络异常，请稍后再试');
                    }
                });
            }
        });
    });

    // Tab1切换
    var closeTag1 = true;
    $('#charts-tab-hd-1').find('a').on('click', function(){
        if(!$(this).hasClass('cur') && closeTag1)
        {
            closeTag1 = false;
            $('#charts-tab-hd-1 a').removeClass('cur');
            $(this).addClass('cur');
            var days = $('input[name="days"]').val();
            var ctype = $(this).attr('data-type');
            $('input[name="tab1"]').val(ctype);
            // 有效用户 显示日周月
            if(ctype == 'validUser'){
                initDateTab();
            }else{
                $('#charts-tab-hd-date').hide();
            }
            // Ajax 刷新查询
            $.ajax({
                type: "post",
                url: '/backend/Analysis/ajaxChart',
                data: {days:days,ctype:ctype},                   
                success: function (response) {
                    closeTag1 = true;
                    $('#tab1').html(response);
                },
                error: function () {
                    closeTag1 = true;
                    alert('网络异常，请稍后再试');
                }
            });  
        }
    });

    // Tab2切换
    var closeTag2 = true;
    $('#charts-tab-hd-2').find('a').on('click', function(){
        if(!$(this).hasClass('cur') && closeTag2)
        {
            closeTag2 = false;
            $('#charts-tab-hd-2 a').removeClass('cur');
            $(this).addClass('cur');
            var days = $('input[name="days"]').val();
            var ctype = $(this).attr('data-type');
            $('input[name="tab2"]').val(ctype);
            // 有效用户 显示日周月
            if(ctype == 'allSale'){
                initDateTab2();
            }else{
                $('#charts-tab-hd2-date').hide();
            }
            // Ajax 刷新查询
            $.ajax({
                type: "post",
                url: '/backend/Analysis/ajaxChart',
                data: {days:days,ctype:ctype},                   
                success: function (response) {
                    closeTag2 = true;
                    $('#tab2').html(response);
                },
                error: function () {
                    closeTag2 = true;
                    alert('网络异常，请稍后再试');
                }
            });  
        }
    });

    // 有效用户 星期tab切换
    var closeTag3 = true;
    $('#charts-tab-hd-date').find('a').on('click', function(){
        if(!$(this).hasClass('cur') && closeTag3)
        {   
            var dValue = $('input[name="days"]').val(); // 7 30 60
            var cValue = $(this).attr('data-value');    // 7 30 60
            if(dValue - cValue < 0)
            {
                alert("请重新选择时间！");
                return false;
            }
            closeTag3 = false;
            $('#charts-tab-hd-date a').removeClass('cur');
            $(this).addClass('cur');

            var days = $('input[name="days"]').val();
            var ctype = 'validUser';
            var stype = $(this).attr('data-type');
            // Ajax 刷新查询
            $.ajax({
                type: "post",
                url: '/backend/Analysis/ajaxChart',
                data: {days:days,ctype:ctype,stype:stype},                   
                success: function (response) {
                    closeTag3 = true;
                    $('#tab1').html(response);
                },
                error: function () {
                    closeTag3 = true;
                    alert('网络异常，请稍后再试');
                }
            }); 
        }
    });

    // 初始化日周月tab
    function initDateTab(){
        $('#charts-tab-hd-date a').removeClass('cur');
        $('#charts-tab-hd-date a:first').addClass('cur');
        $('#charts-tab-hd-date').show();
    }

    // 全国销量 星期tab切换
    var closeTag4 = true;
    $('#charts-tab-hd2-date').find('a').on('click', function(){
        if(!$(this).hasClass('cur') && closeTag4)
        {   
            var dValue = $('input[name="days"]').val(); // 7 30 60
            var cValue = $(this).attr('data-value');    // 7 30 60
            if(dValue - cValue < 0)
            {
                alert("请重新选择时间！");
                return false;
            }
            closeTag4 = false;
            $('#charts-tab-hd2-date a').removeClass('cur');
            $(this).addClass('cur');

            var days = $('input[name="days"]').val();
            var ctype = 'allSale';
            var stype = $(this).attr('data-type');
            // Ajax 刷新查询
            $.ajax({
                type: "post",
                url: '/backend/Analysis/ajaxChart',
                data: {days:days,ctype:ctype,stype:stype},                   
                success: function (response) {
                    closeTag4 = true;
                    $('#tab2').html(response);
                },
                error: function () {
                    closeTag4 = true;
                    alert('网络异常，请稍后再试');
                }
            }); 
        }
    });

    // 初始化日周月tab
    function initDateTab2(){
        $('#charts-tab-hd2-date a').removeClass('cur');
        $('#charts-tab-hd2-date a:first').addClass('cur');
        $('#charts-tab-hd2-date').show();
    }

</script>
<script src="/source/echart/dist/echarts.js"></script>
<script type="text/javascript">
    // 路径配置
    require.config({
        paths: {
            echarts: '/source/echart/dist'
        }
    });
    // 使用 转化率
    require(
        [
            'echarts',
            'echarts/chart/line',
            'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('conversion')); 
            
            var option = {
                title : {
                    text: '转化率'
                },
                tooltip: {
                    show: true
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar']},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                legend: {
                    data:['网页','Android客户端']
                },
                xAxis : [
                    {
                        type : 'category',
                        data : ["点击转化率","注册转化率","有效转化率","充值转化率","投注转化率"]
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        "name":"网页",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $conversion['web'];?>
                    },
                    {
                        "name":"Android客户端",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $conversion['app'];?>
                    }
                ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );
    // 使用 总销量
    require(
        [
            'echarts',
            'echarts/chart/line',
            'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('allSale')); 
            
            var option = {
                title : {
                    text: '全国销量'
                },
                tooltip : {
                    trigger: 'axis'
                },
                legend: {
                    data:['网页','Android客户端','全部']
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar']},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data : <?php echo $allSale['date']; ?>
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'网页',
                        type:'line',
                        data:<?php echo $allSale['total']['web']; ?>
                    },
                    {
                        name:'Android客户端',
                        type:'line',
                        data:<?php echo $allSale['total']['app']; ?>
                    },
                    {
                        name:'全部',
                        type:'line',
                        data:<?php echo $allSale['total']['all']; ?>
                    }
                ]
            };
                    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );
</script>
</body>
</html>