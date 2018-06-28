<?php $this->load->view("templates/head") ?>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tag.min.css">
<div id="app" v-cloak>
    <div class="path mb20">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">用户标签管理</a></div>

    <table class="filter-table">
        <colgroup><col width="70"><col width="140"><col width="90"><col width="200"><col width="70"></colgroup>
        <tbody>
            <tr>
                <td>标签信息：</td>
                <td>
                    <i-input v-model="tagName" placeholder="标签名（模糊查询）" style="width: 130px;"></i-input>
                </td>
                <td class="tar">标签建立时间：</td>
                <td>
                    <Date-picker type="datetimerange" format="yyyy-MM-dd" v-model="tag.filter.datevalue" placeholder="选择日期和时间" @on-change="datePickFormat" style="width: 280px;"></Date-picker>
                </td>
                <td class="tar">标签维度：</td>
                <td>
                    <i-select v-model="tag.filter.value" style="width: 130px;">
                        <i-option v-for="item in tag.filter.select" :value="item.value" :key="item">{{ item.label }}</i-option>
                    </i-select>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <div style="margin-top: 20px;">
                        <i-Button type="primary" @click="query">查询</i-Button>
                        <a class="ivu-btn ivu-btn-primary" href="/backend/tag/add">新建标签</a>
                        <i-Button type="primary">标签回查</i-Button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <i-table border ref="selection" :context="self" :columns="tag.columns" :data="tag.data" loading="true"></i-table>
    <Page v-if="tag.page.total > 20" :total="tag.page.total" :page-size="tag.page.size" :current="tag.page.current" show-total @on-change="pageChange" class="mb20"></Page>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    Vue.component('tag-info', {
        template: '<ul class="showList">\
                <li><b>标签名称：</b>{{ data.tag_name }}</li>\
                <li><b>标签解释：</b>{{ data.tag_desc }}</li>\
                <li v-if="data.filter"><b>标签基本条件：</b>\
                    <span class="logic-ctrl">\
                        <span v-for="(v, k) in data.filter" v-if="v.val" :key="k">\
                            <em class="logic-and">且</em>\
                            <span v-for="(it, idx) in v.val.split(\',\')" :key="idx">\
                                <em v-if="idx !== 0">{{ v.logic | logicMap }}</em>\
                                <span v-if="k === \'channel\'">{{ it }}</span>\
                                <span v-else>{{ map[k][it] }}</span>\
                            </span>\
                        </span>\
                    </span>\
                </li>\
                <li v-if="data.having"><b>标签复合条件：</b>\
                    <span class="logic-ctrl">\
                        <span v-for="(v, k) in data.having" v-if="v !== \'0\'" :key="k">\
                            <em class="logic-and">且</em>\
                            <span>{{ map.having[k] }}[{{ v }}] </span>\
                        </span>\
                    </span>\
                </li>\
                <li><b>购彩时间条件：</b>\
                    <span v-for="(v, k) in data.date" :key="k">\
                        <template v-if="v">\
                            <span v-if="k === \'range\'">{{ map[k][v]}}</span>\
                            <span v-else>{{ v }}</span>\
                        </template>\
                    </span>\
                </li>\
            </ul>',
        data () {
            return {
                api: {
                    getLid: '/backend/tag/get_lids'
                },
                map: {
                    lid: {},
                    orderType: {
                        '0': '自购',
                        '1': '追号',
                        '2': '发起合买',
                        '3': '参与合买'
                    },
                    platform: {
                        '0': '网页',
                        '1': 'Android',
                        '2': 'iOS',
                        '3': 'M版',
                    },
                    having: {
                        last_buy_time: '最后购彩时间',
                        total_buy_num: '购彩订单总数',
                        total_money: '购彩累计金额',
                        total_day: '购彩累计天数'
                    },
                    last_buy_time: {
                        '1': '1天',
                        '3': '3天',
                        '7': '7天', 
                        '30': '1个月',
                        '90': '3个月'
                    },
                    range: {
                        '1': '最近1天',
                        '7': '最近7天',
                        '30': '最近30天',
                        '90': '最近90天'
                    }
                }
            }
        },
        props: {
            data: {
                type: Object,
                default: {}
            }
        },
        created: function () {
            axios.get(this.api.getLid).then(function (res) {
                res.data.forEach(function (it) {
                    this.$set(this.map.lid, it.id + '', it.name)
                }.bind(this))
            }.bind(this)).catch(function (err) {
                console.log(err)
                this.$Message.error('彩种请求失败，请刷新重新获取');
            }.bind(this))
        },
        filters: {
            logicMap: function (val) {
                if (val === 'and') {
                    return '且'
                } else if (val === 'or') {
                    return '或'
                } else {
                    return ''
                }
            }
        }
    });

    new Vue({
        el: '#app',
        data () {
            return {
                api: {
                    getData: '/backend/tag/get_list',
                    getSingleData: '/backend/tag/get_data',
                    delSingleData: '/backend/tag/del_data',
                    getScopes: '/backend/tag/get_scopes'
                },
                self: this,
                tableLoading: '加载中...',
                tagName: '',
                tagNote: '',
                tag: {               
                    columns: [
                        {
                            type: 'selection',
                            width: 40,
                            align: 'center'
                        },
                        {
                            title: '序号',
                            key: 'id',
                            width: 50
                        },
                        {
                            title: '标签名称',
                            key: 'tag_name',
                            align: 'left'
                        },
                        {
                            title: '标签维度',
                            key: 'weidu',
                            width: 200,
                            align: 'left'
                        },
                        {
                            title: '标签对应用户数',
                            key: 'ucount',
                            width: 100,
                        },
                        {
                            title: '创建时间',
                            key: 'created',
                            width: 140,
                        },
                        {
                            title: '详细',
                            key: 'detail',
                            width: 180,
                            render: function (row, column, index) {
                                return '\
                                    <i-button type="primary" size="small" @click="showDetail(row)">查看条件</i-button>\
                                    <i-button type="primary" size="small" @click="exportTable(row)">导出</i-button>\
                                    <i-button type="primary" size="small" @click="delTr(row)">删除</i-button>\
                                ';
                            }
                        }
                    ],
                    data: [],
                    filter: {
                        select: [],
                        value: 0,
                        datevalue: [],
                    },
                    page: {
                        total: 0,
                        size: 20,
                        current: 1
                    }
                }
            }
        },
        created: function () {
            this.getData()
            axios.get(this.api.getScopes).then(function (res) {
                for(var k in res.data) {
                    this.tag.filter.select.push({
                        value: parseInt(k, 10),
                        label: res.data[k]
                    })
                }
            }.bind(this)).catch(function (err) {
                console.log(err)
                this.$Message.error('维度请求失败，请刷新重新获取');
            }.bind(this))
        },
        methods: {
            datePickFormat: function (val) {
                this.tag.filter.datevalue = val.split(' - ');
            },
            query: function () {
                if (this.tagName || this.tag.filter.datevalue || this.this.tag.filter.value) {
                    this.getData()
                } else {
                    this.$Message.error('请选择查询信息');
                }
            },
            pageChange: function (index) {
                this.getData(index)
            },
            getData: function (pageNum) {
                var _this = this;
                axios.get(this.api.getData, {
                    params: {
                        name: this.tagName,
                        data: this.tag.filter.datevalue,
                        scope: this.tag.filter.value,
                        num: this.tag.page.size,
                        pageNum: pageNum ? pageNum : 1
                    }
                }).then(function (res) {
                    if (res.status === 200) {
                        if (!res.data.data.length) {
                            this.tableLoading = '暂无数据';
                        }
                        _this.tag.data = res.data.data;
                        _this.tag.page.total = res.data.total;
                    } else {
                        _this.$Message.error(res.msg);
                    }
                }).catch(function (e) {
                    console.log(e)
                    _this.noteTxt = '';
                })
            },
            showDetail: function (row) {
                var _this = this;
                axios.get(this.api.getSingleData + '/' + row.id).then(function (res) {
                    console.log(res)
                    _this.$Modal.info({
                        title: '<div class="tac">查看条件</div>',
                        render: function (h) {
                            return h('tag-info', {
                               props: {
                                   data: res.data
                               } 
                            })
                        }
                    })
                }).catch(function (err) {
                    _this.$Message.error(err);
                })
            },
            delTr: function (row) {
                var _this = this;
                this.$Modal.confirm({
                    title: '提示',
                    content: '确认删除这个标签',
                    onOk: function () {
                        axios.post(_this.api.delSingleData, {
                            id: row.id
                        }, {
                            transformRequest: [function (data) {
                                var enData = ''
                                for (var i in data) {
                                    enData += encodeURIComponent(i) + '=' + encodeURIComponent(data[i]) + '&'
                                }
                                return enData.slice(0, enData.length - 1)
                            }],
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function (res) {
                            var res = res.data;
                            if (res.status === '200') {
                                _this.tag.data.splice(row._index, 1);
                                _this.$Message.success(res.msg);
                            } else {
                                _this.$Message.error(res.msg);
                            }
                        }).catch(function (err) {
                            _this.$Message.error('删除请求失败');
                            console.log(err)
                        })
                    }
                })
            },
            exportTable: function (row) {
                location.href = '/backend/tag/export/' + row.id;
            }
        }
    })
  </script>