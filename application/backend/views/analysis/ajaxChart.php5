<div id="<?php echo $ctype; ?>" style="height: 400px;">
    <!-- 此处放图表 -->
</div>
<script src="/source/echart/dist/echarts.js"></script>
<script type="text/javascript">
    // 路径配置
    require.config({
        paths: {
            echarts: '/source/echart/dist'
        }
    });
    // 使用 
    require(
        [
            'echarts',
            'echarts/chart/line',
            'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
            'echarts/chart/pie'
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('<?php echo $ctype; ?>')); 
            // 转化率
            <?php if($ctype == 'conversion'):?>       
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
                        "data":<?php echo $info['web'];?>
                    },
                    {
                        "name":"Android客户端",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $info['app'];?>
                    }
                ]
            };
            <?php elseif($ctype == 'validUser'):?>
            // 有效用户
            var option = {
                title : {
                    text: '有效用户数'
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
                        data : <?php echo $info['date']; ?>
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
                        axisLabel : {
                            formatter: '{value} 人'
                        }
                    }
                ],
                series : [
                    {
                        name:'网页',
                        type:'line',
                        data:<?php echo $info['data']['web']; ?>
                    },
                    {
                        name:'Android客户端',
                        type:'line',
                        data:<?php echo $info['data']['app']; ?>
                    },
                    {
                        name:'全部',
                        type:'line',
                        data:<?php echo $info['data']['all']; ?>
                    }
                ]
            };
            <?php elseif($ctype == 'allSale'):?>
            // 全国销量
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
                        data : <?php echo $info['date']; ?>
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
                        data:<?php echo $info['total']['web']; ?>
                    },
                    {
                        name:'Android客户端',
                        type:'line',
                        data:<?php echo $info['total']['app']; ?>
                    },
                    {
                        name:'全部',
                        type:'line',
                        data:<?php echo $info['total']['all']; ?>
                    }
                ]
            };
            <?php elseif($ctype == 'platformSale'):?>
            // 平台销量占比
            option = {
                title : {
                    text: '平台销量占比'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient : 'vertical',
                    data:<?php echo $info['title']; ?>
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {
                            show: true, 
                            type: ['pie', 'funnel'],
                            option: {
                                funnel: {
                                    x: '25%',
                                    width: '50%',
                                    funnelAlign: 'left',
                                    max: 1548
                                }
                            }
                        },
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                series : [
                    {
                        name:'访问来源',
                        type:'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        itemStyle: {
                            normal: { label : {show:true,position:'top',formatter:'{d} %'}}
                        },
                        data:<?php echo $info['data']; ?>
                    }
                ]
            };
            <?php elseif($ctype == 'lotterySale'):?>
            // 彩种销量占比
            var option = {
                title : {
                    text: '彩种销量占比'
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
                        data : <?php echo $info['lname']; ?>
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
                        axisLabel: {
                          show: true,
                          interval: 'auto',
                          formatter: '{value} %'
                        }
                    }
                ],
                series : [
                    {
                        "name":"网页",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $info['data']['web'];?>
                    },
                    {
                        "name":"Android客户端",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $info['data']['app'];?>
                    }
                ]
            };
            <?php elseif($ctype == 'lotteryAward'):?>
            // 彩种返奖率
            var option = {
                title : {
                    text: '彩种返奖率'
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
                    data:['彩种返奖率']
                },
                xAxis : [
                    {
                        type : 'category',
                        data : <?php echo $info['lname']; ?>
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
                        axisLabel: {
                          show: true,
                          interval: 'auto',
                          formatter: '{value} %'
                        }
                    }
                ],
                series : [
                    {
                        "name":"返奖率",
                        "type":"bar",
                        "itemStyle": {
                            normal: { label : {show:true,position:'top',formatter:'{c} %'}}
                        },
                        "data":<?php echo $info['data'];?>
                    }
                ]
            };
            <?php endif; ?>
            
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );
</script>
</body>
</html>