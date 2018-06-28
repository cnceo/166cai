<?php $this->load->view("templates/head") ?>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tag.min.css">
<div id="app" v-cloak>
    <div class="path mb20">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">用户集群管理－新建</a></div>

    <form class="newTag">
        <h1>新建用户集群</h1>

        <div class="group">
            <span class="label">集群名称：</span>
            <div class="item">
                <i-input v-model="newTag.name" placeholder="给集群取个名字" style="width: 180px;"></i-input>
            </div>
        </div>
        <div class="group">
            <span class="label">集群分类：</span>
            <div class="item">
                <i-select v-model="newTag.type" style="width: 160px;">
                    <i-option v-for="(item, idx) in tagData.type" :value="item.id" :key="idx">{{ item.name }}</i-option>
                </i-select>
            </div>
        </div>
        <div class="group">
            <span class="label">集群描述：</span>
            <div class="item">
                <i-input v-model="newTag.note" type="textarea" :rows="2" placeholder="集群的描述" style="width: 400px;"></i-input>
            </div>
        </div>
        <div class="filter">
            <div class="group">
                <span class="label">常用标签：</span>
                <div class="item">
                    <checkbox-group v-model="newTag.cybq.val">
                        <template v-for="(item, idx) in tagData.cybq">
                            <Checkbox :label="JSON.stringify(item)">{{item.tag_name}}</Checkbox>
                            <br v-if="!((idx + 1) % 3)">
                        </template>
                    </checkbox-group>
                    <i-Button type="text" @click="addOther" style="color: #2d8cf0;">添加其他标签</i-Button>
                </div>
            </div>
        </div>
        <div class="group" v-if="newTag.cybq.val.length">
            <span class="label">群组标签：</span>
            <div class="item">
                <span class="condition-box" v-for="(item, idx) in newTag.cybq.val">
                    <span class="condition-box-logic">且</span>
                    <span v-if="newTag.cybq.val.length" class="condition">
                        <Tag closable @on-close="delLot('cybq', idx)">{{ JSON.parse(item).tag_name }}</Tag>
                    </span>
                </span>
            </div>
        </div>
        <div class="group">
            <span class="label" style="vertical-align: middle;">集群用户更新规则：</span>
            <div class="item" style="vertical-align: middle;">
                <radio-group v-model="newTag.gxgz">
                    <Radio v-for="(item, idx) in tagData.gxgz" :label="item.id">{{ item.name }}</Radio>
                </radio-grou>
            </div>
        </div>
        <div class="group">
            <span class="label">预计用户数：</span>
            <div class="item">
                <span>{{ userNum }}</span> <i-Button type="primary" @click="calc">计算人数</i-Button>
            </div>
        </div>
        
        <div>
            <i-Button type="primary" size="large" @click="post">提交</i-Button>
        </div>
    </form>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    Vue.component('choose-model', {
        template: `
            <Tabs v-model="curTab" :animated="false" size="small">
                <Tab-pane v-for="item in tablist" :label="item" :name="item">
                    <template v-if="page[curTab].total !== '0'">
                        <template v-for="(item, idx) in list[item]" :key="idx">
                            <label class="tabCheckbox"><input v-model="choose" :value="JSON.stringify(item)" type="checkbox">{{ item.tag_name }}</label>
                            <br v-if="!((idx + 1) % 3)">
                        </template>
                        <Page v-if="page[curTab].total > 8" :total="page[curTab].total" page-size="8" :current="page[curTab].current" show-total @on-change="pageChange" class="mt20"></Page>
                    </template>
                    <template v-else>
                        <p>没有数据</p>
                    </template>
                </Tab-pane>
            </Tabs>
        `,
        data () {
            return {
                curTab: '',
                list: {},
                choose: [],
                page: {},
                summary: [],
                typeMap: {
                    '购买彩种': 'lid',
                    '购彩方式': 'method',
                    '购彩渠道': 'channel',
                    '购彩平台': 'platform',
                    '购彩累计天数': 'days',
                    '购彩订单总数': 'orders',
                    '最后购彩时间': 'lasttime',
                    '购彩累计金额': 'money',
                }
            }
        },
        props: {
            tablist: {
                type: Array,
                default: []
            },
            hasChecked: {
                type: Array,
                default: []
            }
        },
        created: function () {
            this.curTab = this.tablist[0];
            this.tablist.forEach(function (item, idx) {
                this.$set(this.list, item, []);

                this.$set(this.page, item, {});
                this.choose = this.hasChecked
            }.bind(this))
        },
        watch: {
            choose: {
                handler: function (val) {
                    // this.summary = [];
                    // for(var k in val) {
                    //     if (val[k].length) {
                    //         this.summary = this.summary.concat(val[k]);
                    //     }
                    // }
                    this.$emit('input', val)
                },
                deep: true
            },
            curTab: function (val) {
                if (this.list[val].length === 0) {
                    this.getData()
                }
            }
        },
        methods: {
            pageChange: function (index) {
                this.getData(index)
            },
            getData: function (page) {
                var _this = this;
                axios.get('/backend/tag/get_tag_ids/' + this.typeMap[this.curTab], {
                    params: {
                        page: page ? page : 1
                    }
                }).then(function (res) {
                    _this.$forceUpdate()
                    var res = res.data,
                        total = parseInt(res.total, 10),
                        current = parseInt(res.curPage, 10);
                    if (!_this.page[_this.curTab].total) {
                        _this.$set(_this.page[_this.curTab], 'total', total);
                    } else {
                        _this.page[_this.curTab].total = total;
                    }
                    if (!_this.page[_this.curTab].current) {
                        _this.$set(_this.page[_this.curTab], 'current', current)
                    } else {
                        _this.page[_this.curTab].current = current;
                    }
                    _this.list[_this.curTab] = res.data;
                    _this.$forceUpdate()
                }).catch(function (err) {
                    console.log(err)
                })
            }
        }
    });

    new Vue({
        el: '#app',
        data () {
            return {
                api: {
                    newTag: '/backend/tag/save_cluster',
                    calc: '/backend/tag/caculate_uids'
                },
                tagName: '',
                tagType: '',
                tagNote: '',
                tagData: {
                    type: [
                        {
                            id: 0,
                            name:'日常推送'
                        },
                        {
                            id: 1,
                            name: '日常短信'
                        },
                        {
                            id: 2,
                            name: '日常红包'
                        },
                        {
                            id: 3,
                            name: '数据监控'
                        },
                        {
                            id: 4,
                            name: '临时活动'
                        },
                        {
                            id: 5,
                            name: '其他'
                        }
                    ],
                    ywcj: ['购彩'],
                    cybq: [],
                    gxgz: [
                        {
                            id: 0,
                            name: '不更新'
                        },
                        {
                            id: 1,
                            name: '每日更新'
                        },
                        {
                            id: 2,
                            name: '每周更新'
                        },
                        {
                            id: 3,
                            name: '每月更新'
                        }
                    ],
                },
                newTag: {
                    name: '',
                    type: 0,
                    note: '',
                    cybq: {
                        val: []
                    },
                    gxgz: 0
                },
                pla: [],
                userNum: ''
            }
        },
        created: function () {
            var _this = this;
            axios.get('/backend/tag/get_tag_ids/top').then(function (res) {
                _this.tagData.cybq = res.data;
            }).catch(function (err) {
                console.log(err)
            })
        },
        methods: {
            post: function () {
                if (!this.newTag.name) {
                    this.$Message.info('请填写集群名称');
                    return;
                } else if (!this.newTag.note) {
                    this.$Message.info('请填写集群描述');
                    return;
                } else if (!this.newTag.cybq.val.length) {
                    this.$Message.info('请填写标签的集合');
                    return;
                }

                var data = {
                        cluster_name: this.newTag.name,
                        cluster_desc: this.newTag.note,
                        ctype: this.newTag.type,
                        tagids: this.newTag.cybq.val.map(function (it) {
                            return JSON.parse(it).id
                        }),
                        update_logic: this.newTag.gxgz
                    },
                    _this = this;
                    
                axios.post(this.api.newTag, data, {
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
                        _this.$Message.success({
                            content: '创建成功',
                            onClose: function () {
                                location.href = "/backend/tag/cluster"
                            }
                        });
                    } else {
                        _this.$Message.error(res.msg);
                    }
                }).catch(function (err) {
                    _this.$Message.error('提交失败');
                })
            },
            delLot: function (item, index) {
                this.newTag[item].val.splice(index, 1);
            },
            addOther: function () {
                var _this = this;
                this.$Modal.confirm({
                    title: '购彩',
                    width: '776',
                    render: function (h) {
                        return h('choose-model', {
                            props: {
                                tablist: ['购买彩种', '购彩方式', '购彩渠道', '购彩平台', '购彩累计天数', '购彩订单总数', '最后购彩时间', '购彩累计金额'],
                                hasChecked: _this.newTag.cybq.val
                            },
                            on: {
                                input: function (val) {
                                    _this.pla = [];
                                    _this.pla = _this.pla.concat(val)
                                    console.log(_this.pla)
                                }
                            }
                        })
                    },
                    onOk: function () {
                        _this.newTag.cybq.val = [...new Set(_this.pla)]
                    }
                })
            },
            calc: function () {
                var tagids = this.newTag.cybq.val.map(function (it) {
                    return JSON.parse(it).id
                })
                if (!tagids.length) {
                    this.$Message.info('需要先选择标签');
                    return;
                }
                var _this = this;
                axios.get(this.api.calc, {
                    params: {
                        tagids: tagids
                    }
                }).then(function (res) {
                    console.log(res)
                    _this.userNum = res.data.count + '人，占总人数' + res.data.percentage + '%';
                }).catch(function (err) {
                    console.log(err)
                })
            }
        }
    })
  </script>