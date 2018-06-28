<?php $this->load->view("templates/head") ?>
<div id="app">
    <template>
        <Breadcrumb>
            <Breadcrumb-item>财务对账</Breadcrumb-item>
            <Breadcrumb-item href="/backend/Statistics/balance">对账查询</Breadcrumb-item>
            <Breadcrumb-item><?php echo $name;?> 差错详情页</Breadcrumb-item>
        </Breadcrumb>
    </template>

    <!--tab选项卡-->
    <i-table border :context="self" :columns="mistakeDetail.columns" :data="mistakeDetail.data">
        <template slot="header">
            <div class="ivu-table-header">
                <table>
                    <colgroup><col><col><col><col><col></colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2"><div class="ivu-table-cell">差错订单号</div></th>
                            <th colspan="2"><div class="ivu-table-cell"><?php if($tabId == 1):?>出票状态<?php else: ?>订单状态<?php endif;?></div></th>
                            <th colspan="2"><div class="ivu-table-cell">订单金额</div></th>
                        </tr>
                        <tr>
                            <th class=""><div class="ivu-table-cell">网站</div></th>
                            <th class=""><div class="ivu-table-cell"><?php if($tabId == 1):?>票商<?php else: ?>渠道<?php endif;?></div></th>
                            <th class=""><div class="ivu-table-cell">网站</div></th>
                            <th class=""><div class="ivu-table-cell"><?php if($tabId == 1):?>票商<?php else: ?>渠道<?php endif;?></div></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </template>
    </i-table>
    <i-Button type="primary" @click="exportTable">全部导出</i-Button>
</div>
<style>
    .ivu-breadcrumb {
        margin-bottom: 20px;
    }
    .ivu-tabs {
        min-height: 400px;
    }
    .ivu-table-wrapper {
        margin-bottom: 20px;
    }
    .ivu-table th, .ivu-table td {
        text-align: center;
    }
    .ivu-table-cell {
        padding-left: 4px;
        padding-right: 4px;
    }
    .ivu-table-title {
        height: auto;
        border-bottom: none;
        line-height: 1;
    }
    .ivu-table-title + .ivu-table-header {
        display: none;
    }
    .ivu-table-title table {
        width: 100%;
    }
    .ivu-table-title th {
        height: 36px;
    }
</style>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data () {
            return {
                api: {},
                self: this,
                // 差错详情页
                mistakeDetail: {               
                    columns: [
                        {
                            //title: '差错订单号',
                            key: 'trade_no'
                        },
                        {
                            //title: '出票状态网站',
                            key: 's_status'
                        },
                        {
                            //title: '出票状态票商',
                            key: 'o_status'
                        },
                        {
                            //title: '订单金额网站',
                            key: 's_money'
                        },
                        {
                            //title: '订单金额票商',
                            key: 'o_money'
                        }
                    ],
                    data: [
                      	<?php foreach ($datas as $value):?>
                        {
                        	trade_no: '<?php echo $value['trade_no'];?>',
                        	s_status: '<?php echo $value['s_status'];?>',
                        	o_status: '<?php echo $value['o_status'];?>',
                        	s_money: '<?php echo $value['s_money'];?>',
                        	o_money: '<?php echo $value['o_money'];?>'
                        },
                        <?php endforeach;?>
                    ]
                }
            }
        },
        ready: function () {
            this.getData()
        },
        methods: {
            exportTable: function () {
            	var url = '/backend/Statistics/exporterrorBalance';
            	var name = '<?php echo $tabId;?>';
            	var date = '<?php echo $date;?>';
            	var config_id = '<?php echo $config_id;?>';
            	url = url + '?name=' + name + '&config_id=' + config_id + '&date=' + date;
            	self.location = url; 
            }
        }
    })
  </script>