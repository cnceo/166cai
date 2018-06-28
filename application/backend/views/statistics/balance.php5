<?php $this->load->view("templates/head") ?>
<div id="app">
    <template>
        <Breadcrumb>
            <Breadcrumb-item>首页</Breadcrumb-item>
            <Breadcrumb-item>财务对账</Breadcrumb-item>
            <Breadcrumb-item>对账查询</Breadcrumb-item>
        </Breadcrumb>
    </template>

  <!--tab选项卡-->
    <template :animated="false">
        <Tabs v-model="tab" :animated="false">
            <Tab-pane label="票商对账" name="psdz">
                <table class="filter-table">
                    <colgroup><col width="300"><col width="50"><col width="120"><col width="100"><col width="180"><col></colgroup>
                    <tbody>
                        <tr>
                            <td><Date-picker type="datetimerange" v-model="psdz.filter.datevalue" placeholder="选择日期和时间" @on-change="datePickFormat"></Date-picker></td>
                            <td class="tac">票商</td>
                            <td>
                                <i-select v-model="psdz.filter.config_id">
                                    <i-option v-for="item in psdz.filter.select" :value="item.id" :key="item">{{ item.name }}</i-option>
                                </i-select>
                            </td>
                            <td class="tac"><i-Button type="primary" @click="query">查询</i-Button></td>
                            <td><span class="cRed">提示：每日12:00对账</span></td>
                            <td class="tar"><i-Button type="primary" @click="exportTable">全部导出</i-Button></td>
                        </tr>
                    </tbody>
                </table>
                <i-table border :context="self" :columns="psdz.columns" :data="psdz.data" :no-data-text="tablePlaceholder">
                    <template slot="header">
                        <div class="ivu-table-header">
                            <table>
                                <colgroup><col><col><col><col><col><col><col width="100"><col width="120"><col width="100"></colgroup>
                                <thead>
                                    <tr>
                                        <th rowspan="2"><div class="ivu-table-cell">票商</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">日期</div></th>
                                        <th colspan="2"><div class="ivu-table-cell">购彩</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差额</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">对账状态</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差错池</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">操作</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">备注</div></th>
                                    </tr>
                                    <tr>
                                        <th class=""><div class="ivu-table-cell">网站</div></th>
                                        <th class=""><div class="ivu-table-cell">票商</div></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </template>
                </i-table>

                <Page v-if="psdz.page.total > psdz.page.size" :total="psdz.page.total"  :current="psdz.page.current" :page-size="psdz.page.size" show-total @on-change="pageAjax" class="mb20"></Page>
            </Tab-pane>
            <Tab-pane label="充值渠道对账" name="czqd">
                <table class="filter-table">
                    <colgroup><col width="300"><col width="60"><col width="200"><col width="100"><col width="180"><col></colgroup>
                    <tbody>
                        <tr>
                            <td><Date-picker type="datetimerange" v-model="czqd.filter.datevalue" placeholder="选择日期和时间" @on-change="datePickFormat"></Date-picker></td>
                            <td class="tac">支付渠道</td>
                            <td>
                                <i-select v-model="czqd.filter.config_id">
                                    <i-option v-for="item in czqd.filter.select" :value="item.id" :key="item">{{ item.name }}</i-option>
                                </i-select>
                            </td>
                            <td class="tac"><i-Button type="primary" @click="query">查询</i-Button></td>
                            <td><span class="cRed">提示：每日14:00对账</span></td>
                            <td class="tar"><i-Button type="primary" @click="exportTable">全部导出</i-Button></td>
                        </tr>
                    </tbody>
                </table>
                <i-table border :context="self" :columns="czqd.columns" :data="czqd.data" :no-data-text="tablePlaceholder">
                    <template slot="header">
                        <div class="ivu-table-header">
                            <table>
                                <colgroup><col width="230"><col><col><col><col><col><col width="100"><col width="120"><col width="100"></colgroup>
                                <thead>
                                    <tr>
                                        <th rowspan="2"><div class="ivu-table-cell">支付渠道</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">日期</div></th>
                                        <th colspan="2"><div class="ivu-table-cell">充值</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差额</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">对账状态</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差错池</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">操作</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">备注</div></th>
                                    </tr>
                                    <tr>
                                        <th class=""><div class="ivu-table-cell">网站</div></th>
                                        <th class=""><div class="ivu-table-cell">渠道</div></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </template>
                </i-table>

                <Page v-if="czqd.page.total > czqd.page.size" :total="czqd.page.total" :current="czqd.page.current" :page-size="czqd.page.size" show-total @on-change="pageAjax" class="mb20"></Page>
            </Tab-pane>
            <Tab-pane label="提现渠道对账" name="txqd">
                <table class="filter-table">
                    <colgroup><col width="300"><col width="60"><col width="120"><col width="100"><col width="180"><col></colgroup>
                    <tbody>
                        <tr>
                            <td><Date-picker type="datetimerange" v-model="txqd.filter.datevalue" placeholder="选择日期和时间" @on-change="datePickFormat"></Date-picker></td>
                            <td class="tac">提现渠道</td>
                            <td>
                                <i-select v-model="txqd.filter.config_id">
                                    <i-option v-for="item in txqd.filter.select" :value="item.id" :key="item">{{ item.name }}</i-option>
                                </i-select>
                            </td>
                            <td class="tac"><i-Button type="primary" @click="query">查询</i-Button></td>
                            <td><span class="cRed">提示：每日14:00对账</span></td>
                            <td class="tar"><i-Button type="primary" @click="exportTable">全部导出</i-Button></td>
                        </tr>
                    </tbody>
                </table>
                <i-table border :context="self" :columns="txqd.columns" :data="txqd.data" :no-data-text="tablePlaceholder">
                    <template slot="header">
                        <div class="ivu-table-header">
                            <table>
                                <colgroup><col><col><col><col><col><col><col width="100"><col width="120"><col width="100"></colgroup>
                                <thead>
                                    <tr>
                                        <th rowspan="2"><div class="ivu-table-cell">提现渠道</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">日期</div></th>
                                        <th colspan="2"><div class="ivu-table-cell">提现</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差额</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">对账状态</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">差错池</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">操作</div></th>
                                        <th rowspan="2"><div class="ivu-table-cell">备注</div></th>
                                    </tr>
                                    <tr>
                                        <th class=""><div class="ivu-table-cell">网站</div></th>
                                        <th class=""><div class="ivu-table-cell">渠道</div></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </template>
                </i-table>

                <Page v-if="txqd.page.total > txqd.page.size" :total="txqd.page.total" :current="txqd.page.current" :page-size="txqd.page.size" show-total @on-change="pageAjax" class="mb20"></Page>
            </Tab-pane>
        </Tabs>
    </template>
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
    .filter-table {
        width: 100%;
        margin-bottom: 20px;
    }
    .ivu-modal-confirm-head {
        margin-bottom: 10px;
    }
    .ivu-modal-confirm-body textarea {
        resize: none;
    }
    .ivu-modal-confirm-footer {
        margin-top: 20px;
    }
</style>
<script src="/caipiaoimg/v1.0/js/axios.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data () {
            return {
                api: {
                    getData: '/backend/Statistics/selectBalance',
                    addNote: '/backend/Statistics/markBalance',
                    reGet: '/backend/Statistics/resetBalance',
                    exportTable: '/backend/Statistics/exportBalance'
                },
                tab: 'psdz',
                self: this,
                nameMap: ['psdz', 'czqd' ,'txqd'],
                tablePlaceholder: '加载中。。。',
                // 渠道管理
                psdz: {    
                    columns: [
                        {
                            title: '票商',
                            key: 'name'
                        },
                        {
                            title: '日期',
                            key: 'date'
                        },
                        {
                            title: '网站',
                            key: 's_money'
                        },
                        {
                            title: '票商',
                            key: 'o_money'
                        },
                        {
                            title: '差额',
                            key: 'difference'
                        },
                        {
                            title: '对账状态',
                            key: 'status'
                        },
                        {
                            title: '差错池',
                            key: 'mistake',
                            width: 100,
                            render (row, column, index) {
                                return (row.e_flag === '1') ? '<a href="/backend/Statistics/errorBalance?config_id='+ row.config_id +'&date='+ row.date +'&name=1" target="_blank" class="ivu-btn ivu-btn-primary ivu-btn-small">查看详情</a>' : '无';
                            }
                        },
                        {
                            title: '操作',
                            key: 'action',
                            width: 120,
                            render (row, column, index) {
                                return (row.reset === 0) ? `<i-button type="primary" size="small" @click="reGet(row)">重新获取对比</i-button>` : '-';
                            }
                        },
                        {
                            title: '备注',
                            key: 'note',
                            width: 100,
                            render (row, column, index) {
                                return row.mark ? row.mark : `<i-button type="primary" size="small" @click="note(row)">输入备注</i-button>`;
                            }
                        }
                    ],
                    data: [],
                    filter: {
                        select: [],
                        config_id: null,
                        datevalue: [],
                    },
                    page: {
                        total: 10,
                        size: 10,
                        current: 1
                    }
                },
                // 充值渠道对账
                czqd: {
                    columns: [
                        {
                            title: '支付渠道',
                            key: 'name',
                            width: 230
                        },
                        {
                            title: '日期',
                            key: 'date'
                        },
                        {
                            title: '网站',
                            key: 's_money'
                        },
                        {
                            title: '渠道',
                            key: 'o_money'
                        },
                        {
                            title: '差额',
                            key: 'difference'
                        },
                        {
                            title: '对账状态',
                            key: 'status'
                        },
                        {
                            title: '差错处理',
                            key: 'mistake',
                            width: 100,
                            render (row, column, index) {
                            	return (row.e_flag === '1') ? '<a href="/backend/Statistics/errorBalance?config_id='+ row.config_id +'&date='+ row.date +'&name=2" target="_blank" class="ivu-btn ivu-btn-primary ivu-btn-small">查看详情</a>' : '无';
                            }
                        },
                        {
                            title: '操作',
                            key: 'action',
                            width: 120,
                            render (row, column, index) {
                            	return (row.reset === 0) ? `<i-button type="primary" size="small" @click="reGet(row)">重新获取对比</i-button>` : '-';
                            }
                        },
                        {
                            title: '备注',
                            key: 'note',
                            width: 100,
                            render (row, column, index) {
                            	return row.mark ? row.mark : `<i-button type="primary" size="small" @click="note(row)">输入备注</i-button>`;
                            }
                        }
                    ],
                    data: [],
                    filter: {
                        select: [],
                        config_id: null,
                        datevalue: [],
                    },
                    page: {
                        total: 10,
                        size: 10,
                        current: 1
                    }
                },
                // 提现渠道对账
                txqd: {
                    columns: [
                        {
                            title: '提现渠道',
                            key: 'name'
                        },
                        {
                            title: '日期',
                            key: 'date'
                        },
                        {
                            title: '网站',
                            key: 's_money'
                        },
                        {
                            title: '渠道',
                            key: 'o_money'
                        },
                        {
                            title: '差额',
                            key: 'difference'
                        },
                        {
                            title: '对账状态',
                            key: 'status'
                        },
                        {
                            title: '差错处理',
                            key: 'mistake',
                            width: 100,
                            render (row, column, index) {
                            	return (row.e_flag === '1') ? '<a href="/backend/Statistics/errorBalance?config_id='+ row.config_id +'&date='+ row.date +'&name=3" target="_blank" class="ivu-btn ivu-btn-primary ivu-btn-small">查看详情</a>' : '无';
                            }
                        },
                        {
                            title: '操作',
                            key: 'action',
                            width: 120,
                            render (row, column, index) {
                                return (row.reset === 0) ? `<i-button type="primary" size="small" @click="reGet(row)">重新获取对比</i-button>` : '-';
                            }
                        },
                        {
                            title: '备注',
                            key: 'note',
                            width: 100,
                            render (row, column, index) {
                                return row.mark ? row.mark : `<i-button type="primary" size="small" @click="note(row)">输入备注</i-button>`;
                            }
                        }
                    ],
                    data: [],
                    filter: {
                        select: [],
                        config_id: null,
                        datevalue: [],
                    },
                    page: {
                        total: 10,
                        size: 10,
                        current: 1
                    }
                },
                noteTxt: ''
            }
        },
        watch: {
            'psdz.page.current' : {
                handler: function () {
                    this.getData(this[this.tab].page.current)
                },
                deep: true
            },
            'czqd.page.current' : {
                handler: function () {
                    this.getData(this[this.tab].page.current)
                },
                deep: true
            },
            'txqd.page.current' : {
                handler: function () {
                    this.getData(this[this.tab].page.current)
                },
                deep: true
            },
            tab: function (val) {
                if (!this[val].data.length) {
                    this.getData()
                }
            }
        },
        created: function () {
        	this.getData()
        },
        methods: {
        	datePickFormat: function (val) {
        		this[this.tab].filter.datevalue = val.split(' - ');
            },
            query: function () {
                if (this[this.tab].page.current === 1) {
                	this.getData()
                } else {
                	this[this.tab].page.current = 1;
                }
            },
            pageAjax: function (index) {
                this[this.tab].page.current = index;
            },
            getData: function (pageNum) {
                var _this = this;
                _this.tablePlaceholder = '加载中。。。';
                axios.get(this.api.getData, {
					params: {
	                	name: this.nameMap.indexOf(_this.tab) + 1,
	                    start_time: _this[_this.tab].filter.datevalue[0],
	                    end_time: _this[_this.tab].filter.datevalue[1],
	                    config_id: _this[_this.tab].filter.config_id,
	                    page: pageNum ? pageNum : 1,
	                
					}
                }).then(function (res) {
                    res = res.data;
                    if (res.status === 'y') {
                    	if (!res.info.datas.length) {
    						_this.tablePlaceholder = '暂无数据';
                        }
                        _this[_this.tab].data = res.info.datas;
                        _this[_this.tab].filter.select = res.info.names;
                        _this[_this.tab].filter.config_id = res.info.search.config_id;
                        _this[_this.tab].filter.datevalue = [];
                        _this[_this.tab].filter.datevalue.push(res.info.search.start_time);
                        _this[_this.tab].filter.datevalue.push(res.info.search.end_time);
                        _this[_this.tab].page.total = res.info.count;
                        _this[_this.tab].page.size = res.info.size;
                    } else {
                        _this.$Message.error(res.message);
                    }
                    this[this.tab].page.current++
                }).catch(function (e) {
                    //this.$Message.error(e.msg);
                    this.noteTxt = '';
                })
            },
            reGet: function (row) {
                var _this = this;
                this.$Modal.confirm({
                    content: '点击确认重新获取对比',
                    onOk: function () {
                    	axios.get(_this.api.reGet, {
                        	params: {
        						id: row.id,
        						name: _this.nameMap.indexOf(_this.tab) + 1,
        						flag: 1
                            }
                        }).then(function (res) {
                        	res = res.data;
                            if (res.status === 'y') {
                            	_this[_this.tab].data[row._index].reset = 1;
                            } else {
                            	_this.$Message.error(res.message);
                            }
                        }).catch(function (e) {
                            _this.$Message.error('操作失败');
                        })
                    }
                })
            },
            note: function (row) {
                var _this = this;
                this.$Modal.confirm({
                    title: '输入备注',
                    render: function (h) {
                        return h('Input', {
                            props: {
                                value: _this.noteTxt,
                                autofocus: true,
                                type: 'textarea',
                                rows: 4,
                                placeholder: '请输入您想备注的内容'
                            },
                            on: {
                                input: function (val) {
                                    _this.noteTxt = val;
                                }
                            }
                        })
                    },
                    onOk: function () {
                        axios.post(_this.api.addNote, {
                            id: row.id,
                            name: _this.nameMap.indexOf(_this.tab) + 1,
                            mark: _this.noteTxt
                        }, {
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            transformRequest: [function (data) {
                                var enData = []
                                for (var i in data) {
                                    enData.push(encodeURIComponent(i) + '=' + encodeURIComponent(data[i]))
                                }
                                return enData.join('&')
                            }],
                        }).then(function (res) {
                        	res = res.data;
                            if (res.status === 'y') {
                                _this[_this.tab].data[row._index].mark = _this.noteTxt;
                            } else {
                                _this.$Message.error(res.message);
                            }
                            _this.noteTxt = '';
                        }).catch(function (e) {
                            _this.$Message.error('操作失败');
                            _this.noteTxt = '';
                        })
                    },
                    onCancel: function () {
                         _this.noteTxt = '';
                    }
                })
            },
            exportTable: function () {
            	var _this = this;
            	var name = this.nameMap.indexOf(_this.tab) + 1;
            	var start_time = _this[_this.tab].filter.datevalue[0];
            	var end_time = _this[_this.tab].filter.datevalue[1];
            	var config_id = _this[_this.tab].filter.config_id;
            	var url = this.api.exportTable + '?name=' + name + '&start_time=' + start_time + '&end_time=' + end_time + '&config_id=' + config_id;
            	self.location = url; 
            }
        }
    })
  </script>