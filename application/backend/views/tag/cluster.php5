<?php $this->load->view("templates/head") ?>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tag.min.css">
<div id="app" v-cloak>
    <div class="path mb20">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">用户群组管理</a></div>

  <!--tab选项卡-->
    <template :animated="false">
        <Tabs v-model="tab" :animated="false">
            <Tab-pane label="概览" name="gl">
                <table class="filter-table">
                    <colgroup><col width="70"><col width="140"><col width="90"><col width="290"><col width="70"></colgroup>
                    <tbody>
                        <tr>
                            <td>标签信息：</td>
                            <td>
                                <i-input v-model="tagName" placeholder="标签名（模糊查询）" style="width: 130px;"></i-input>
                            </td>
                            <td class="tar">标签建立时间：</td>
                            <td>
                                <Date-picker type="datetimerange" v-model="tag.filter.datevalue" placeholder="选择日期和时间" @on-change="datePickFormat" style="width: 280px;"></Date-picker>
                            </td>
                            <td class="tar">标签维度：</td>
                            <td>
                                <i-select v-model="tag.filter.wd">
                                    <i-option v-for="item in tag.filterData.wd" :value="item.value" :key="item">{{ item.label }}</i-option>
                                </i-select>
                            </td>
                            <td class="tar">分类：</td>
                            <td>
                                <i-select v-model="tag.filter.category">
                                    <i-option v-for="item in tag.filterData.category" :value="item.value" :key="item">{{ item.label }}</i-option>
                                </i-select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div style="margin-top: 20px;">
                                    <i-Button type="primary" @click="query">查询</i-Button>
                                    <a class="ivu-btn ivu-btn-primary" href="/backend/tag/add_cluster">创建群组</a>
                                    <i-Button type="primary">创建营销活动</i-Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <i-table border ref="selection" :context="self" :columns="tag.columns" :data="tag.data" loading="true"></i-table>

                <Page v-if="tag.page.total > 20" :total="tag.page.total" :page-size="tag.page.size" :current="tag.page.current" show-total @on-change="pageChange" class="mb20"></Page>
            </Tab-pane>
            <Tab-pane label="运营记录" name="yyjl">2</Tab-pane>
            <Tab-pane label="创建活动" name="cjhd">3</Tab-pane>
        </Tabs>
    </template>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    Vue.component('edit-text', {
        template: '<div>\
                <div v-if="!edit" @click="modify">{{ row[key] }}</div>\
                <div v-else>\
                    <Input v-model="editvalue" :autofocus="true" @on-blur="save" ref="input"></Input>\
                </div>\
            </div>',
        data: function () {
            return {
                edit: false,
                editvalue: ''
            }
        },
        props: {
            row: {
                type: Object,
                default: null
            },
            key: {
                type: String,
                default: ''
            },
            updateApi: {
                type: String,
                default: ''
            }
        },
        created: function () {
            this.editvalue = this.row[this.key];
        },
        watch: {
            edit: function (val) {
                if (val) {
                    this.$nextTick(function () {
                        // 手动focus
                        this.$refs.input.focus();
                    }.bind(this))
                }
            }
        },
        methods: {
            modify: function () {
                this.edit = !this.edit;
            },
            save: function () {
                var _this = this,
                    key = this.key;
                    data = {};
                data[key] = this.editvalue;
                if (this.editvalue !== this.row[key]) {
                    this.$Modal.confirm({
                        content: '确认修改？',
                        onOk: function () {
                            if (_this.updateApi) {
                                axios.post(_this.updateApi + '/' + _this.row.id, data, {
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
                                        _this.$emit('blur', _this.editvalue)
                                        _this.$Message.success(res.msg);
                                    } else {
                                        _this.$Message.error(res.msg);
                                        _this.editvalue = _this.row[key];
                                    }
                                    _this.edit = !_this.edit;
                                }).catch(function (e) {
                                    console.log(e)
                                    _this.editvalue = _this.row[key];
                                    _this.edit = !_this.edit;
                                    _this.$Message.error('修改请求失败');
                                })
                            }
                        },
                        onCancel: function () {
                            _this.editvalue = _this.row[key];
                            _this.edit = !_this.edit;
                        }
                    })
                } else {
                    this.edit = !this.edit;
                }
            }
        }
    });


    Vue.component('tag-info', {
        template: '<ul class="showList">\
                <li><b>集群名称：</b>{{ data.cluster_name }}</li>\
                <li><b>集群解释：</b>{{ data.cluster_desc }}</li>\
                <li><b>集群标签：</b>{{ data.tag_ids }}</li>\
                <li><b>现有人数：</b>{{ data.ucount }}人</li>\
                <li><b>更新周期：</b>{{ map.update_logic[data.update_logic] }}</li>\
            </ul>',
        data () {
            return {
                map: {
                    update_logic: {
                        0: '不更新',
                        1: '每日更新',
                        2: '每周更新',
                        3: '每月更新'
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
                    getData: '/backend/tag/get_cluster_list',
                    getSingleData: '/backend/tag/get_cluster',
                    getSingleDataInclude: '/backend/tag/get_tags_info',
                    delSingleData: '/backend/tag/del_cluster',
                    getScopes: '/backend/tag/get_scopes'
                },
                tab: 'gl',
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
                            title: '群名称',
                            key: 'cluster_name',
                            render: function (h, params) {
                                return h('edit-text', {
                                    props: {
                                        'row': params.row,
                                        'key': 'cluster_name',
                                        'update-api': '/backend/tag/save_cluster'
                                    },
                                    on: {
                                        blur: function (val) {
                                            params.row.cluster_name = val
                                        }
                                    }
                                });
                            }
                        },
                        {
                            title: '分类',
                            key: 'ctype'
                        },
                        {
                            title: '描述',
                            key: 'cluster_desc',
                            render: function (h, params) {
                                return h('edit-text', {
                                    props: {
                                        'row': params.row,
                                        'key': 'cluster_desc',
                                        'update-api': '/backend/tag/save_cluster'
                                    },
                                    on: {
                                        blur: function (val) {
                                            params.row.cluster_desc = val
                                        }
                                    }
                                });
                            }
                        },
                        {
                            title: '涉及标签',
                            key: 'tag_ids',
                            render: function (row, column, index) {
                                return '<span @click="showNote(row)" class="cBlue">' + row.tag_ids + '</span>'
                            }
                        },
                        {
                            title: '创建时间',
                            key: 'created'
                        },
                        {
                            title: '最近更新时间',
                            key: 'update_date'
                        },
                        {
                            title: '更新周期',
                            key: 'update_logic'
                        },
                        {
                            title: '详细',
                            key: 'detail',
                            width: 140,
                            render (row, column, index) {
                                return '\
                                    <i-button type="primary" size="small" @click="showDetail(row)">查看</i-button>\
                                    <i-button type="primary" size="small" @click="exportTable(row)">导出</i-button>\
                                    <i-button type="primary" size="small" @click="delTr(row)">删除</i-button>\
                                ';
                            }
                        }
                    ],
                    data: [],
                    filterData: {
                        wd:[],
                        category: [
                            {
                                value: 0,
                                label: '全部'
                            },
                            {
                                value: 1,
                                label: '日常推送'
                            },
                            {
                                value: 2,
                                label: '日常短信'
                            },
                            {
                                value: 3,
                                label: '日常红包'
                            },
                            {
                                value: 4,
                                label: '数据监控'
                            },
                            {
                                value: 5,
                                label: '临时活动'
                            },
                            {
                                value: 6,
                                label: '其他'
                            }
                        ]
                    },
                    filter: {
                        wd: 0,
                        category: 0,
                        datevalue: [],
                    },
                    page: {
                        total: 0,
                        size: 20,
                        current: 1
                    }
                },
                noteTxt: ''
            }
        },
        created: function () {
            this.getData()
            axios.get(this.api.getScopes).then(function (res) {
                for(var k in res.data) {
                    this.tag.filterData.wd.push({
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
            showNote: function (row) {
                var _this = this;
                axios.get(this.api.getSingleDataInclude + '/' + row.id).then(function (res) {
                    var html = '';
                    html += '<ul>';
                    res.data.forEach(function (item, idx) {
                        html += '<li>' + item.id + '：' + item.tag_name + '</li>';
                    })
                    html += '</ul>';
                    _this.$Modal.info({
                        title: '涉及标签',
                        content: html
                    })
                })
            },
            delLot: function (index) {
                this.newTag.lot.splice(index, 1);
            },
            datePickFormat: function (val) {
                this.tag.filter.datevalue = val.split(' - ');
            },
            query: function () {
                if (this.tagName || this.tag.filter.datevalue || this.tag.filter.category || this.tag.filter.wd) {
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
                        ctype: this.tag.filter.category,
                        scope: this.tag.filter.wd,
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
                }).catch(function (err) {
                    console.log(err)
                    _this.noteTxt = '';
                })
            },
            showDetail: function (row) {
                var _this = this;
                axios.get(this.api.getSingleData + '/' + row.id).then(function (res) {
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
                })
            },
            delTr: function (row) {
                var _this = this;
                this.$Modal.confirm({
                    title: '提示',
                    content: '确认删除这个群组',
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
                location.href = '/backend/tag/export_cluster/' + row.id;
            }
        }
    })
  </script>