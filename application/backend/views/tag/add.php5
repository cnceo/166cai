<?php $this->load->view("templates/head") ?>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tag.min.css">
<div id="app" v-cloak>
    <div class="path mb20">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">用户标签管理－新建</a></div>
        
    <form class="newTag">
        <h2>新建标签</h2>
        <div class="basic">
            <div class="group">
                <span class="label">标签名称：</span>
                <div class="item">
                    <i-input v-model="newTag.name" placeholder="给标签取个名字" style="width: 180px;"></i-input>
                </div>
            </div>
            <div class="group">
                <span class="label">标签解释：</span>
                <div class="item">
                    <i-input v-model="newTag.note" type="textarea" :rows="2" placeholder="标签的注释" style="width: 400px;"></i-input>
                </div>
            </div>
        </div>
        <div class="filter">
            <div class="group">
                <span class="label">基本分类：</span>
                <div class="item">
                    <radio-group v-model="newTag.jbfl" type="button">
                        <Radio v-for="(item, idx) in tagData.jbfl" :label="item"></Radio>
                    </radio-group>
                </div>
            </div>
            <div class="group">
                <span class="label">业务场景：</span>
                <div class="item">
                    <radio-group v-model="newTag.ywcj" type="button">
                        <Radio v-for="(item, idx) in tagData.ywcj" :label="item"></Radio>
                    </radio-group>
                </div>
            </div>
            <div class="group">
                <span class="label">基本条件：</span>
                <div class="item">
                    <checkbox-group v-model="newTag.jbtj">
                        <Checkbox v-for="(item, idx) in tagData.jbtj" :label="item"></Checkbox>
                    </checkbox-group>
                </div>
            </div>
            <div class="group">
                <span class="label">复合条件：</span>
                <div class="item">
                    <checkbox-group v-model="newTag.fhtj">
                        <Checkbox v-for="(item, idx) in tagData.fhtj" :label="item"></Checkbox>
                    </checkbox-group>
                </div>
            </div>
        </div>


        <div class="mod-x" v-if="newTag.jbtj.length">
            <template v-for="item in newTag.jbtj">
                <div class="mod-x-item" v-if="item === '购买彩种'">
                    <h3>购买彩种：<s>（代表购买成功过该彩种）</s></h3>
                    <div>
                        <checkbox-group v-model="newTag.lid.val">
                            <template v-for="(item, idx) in tagData.lot">
                                <Checkbox :label="item">{{ item.name }}</Checkbox>
                                <br v-if="!((idx + 1) % 6)">
                            </template>
                        </checkbox-group>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '购彩方式'">
                    <h3>购彩方式：<s>（代表使用过该种方式购买过）</s></h3>
                    <div>
                        <checkbox-group v-model="newTag.orderType.val">
                            <Checkbox v-for="item in tagData.type" :label="item">{{ item.name }}</Checkbox>
                        </checkbox-group>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '购彩渠道'">
                    <h3>购彩渠道：<s>（代表该购彩渠道购买）</s></h3>
                    <div>
                        <i-input v-model="newTag.channel.val" placeholder="格式填写如（10001,10002，10003,10004）" style="width: 300px;"></i-input>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '购彩平台'">
                    <h3>购彩平台：<s>（代表使用过该种方式购买过）</s></h3>
                    <div>
                        <checkbox-group v-model="newTag.platform.val">
                            <Checkbox v-for="item in tagData.platform" :label="item">{{ item.name }}</Checkbox>
                        </checkbox-group>
                    </div>
                </div>
            </template>
        </div>

        <div class="mod-x" v-if="newTag.fhtj.length">
            <template v-for="item in newTag.fhtj">
                <div class="mod-x-item" v-if="item === '购彩累计天数'">
                    <h3>购彩累计天数：<s>（代表发生过购彩行为的天数）</s></h3>
                    <div class="lotname">
                        <i-input v-model="newTag.totalDay[0]" style="width: 100px;" @keyup.native="keyRule('totalDay', 0)"></i-input> ≤购彩天数≤ <i-input v-model="newTag.totalDay[1]" style="width: 100px;" @keyup.native="keyRule('totalDay', 1)"></i-input>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '购彩订单总数'">
                    <h3>购彩订单总数：<s>（出票成功的订单数）</s></h3>
                    <div>
                        <i-input v-model="newTag.totalBuyNum[0]" style="width: 100px;" @keyup.native="keyRule('totalBuyNum', 0)"></i-input> ≤购彩订单总数≤ <i-input v-model="newTag.totalBuyNum[1]" style="width: 100px;" @keyup.native="keyRule('totalBuyNum', 1)"></i-input>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '最后购彩时间'">
                    <h3>最后购买时间：<s>（最后出票成功的订单时间）</s></h3>
                    <div>
                        <radio-group v-model="newTag.last_buy_time_radio">
                            <div class="item mr20">
                                <Radio label="default">
                                    <span>默认时间：</span>
                                </Radio>
                                <i-select v-model="newTag.lastBuyTimeBox.default" style="width: 140px;">
                                    <i-option v-for="(item, idx) in tagData.time" :value="item.val" :key="idx">{{ item.name }}</i-option>
                                </i-select>
                            </div>
                            <div class="item">
                                <Radio label="custom">
                                    <span>自定义时间：</span>
                                </Radio>
                                <Date-picker type="datetimerange" format="yyyy-MM-dd HH:mm:ss" v-model="newTag.lastBuyTimeBox.custom" placeholder="选择日期和时间" @on-change="datePickFormat2(newTag.lastBuyTimeBox.custom)" style="display: inline-block; width: 280px;"></Date-picker> <span @click="newTag.lastBuyTimeBox.custom = []" style="cursor: pointer;">清空</span>
                            </div>
                        </radio-grou>
                    </div>
                </div>

                <div class="mod-x-item" v-if="item === '购彩累计金额'">
                    <h3>购彩金额：<s>（代表购买成功的金额）</s></h3>
                    <div>
                        <i-input v-model="newTag.totalMoney[0]" style="width: 100px;" @keyup.native="keyRule('totalMoney', 0)"></i-input> ≤购彩总额≤ <i-input v-model="newTag.totalMoney[1]" style="width: 100px;" @keyup.native="keyRule('totalMoney', 1)"></i-input>
                    </div>
                </div>
            </template>
        </div>

        <div class="mod-x bet-time">
            <h3>购彩时间选择：</h3>
            <div>
                <radio-group v-model="newTag.gcsjRadio">
                    <div class="item mr20">
                        <Radio label="default">
                            <span>默认时间：</span>
                        </Radio>
                        <i-select v-model="newTag.gcsj.default" style="width: 140px;">
                            <i-option v-for="(item, idx) in tagData.gcsj.default" :value="item.val" :key="idx">{{ item.name }}</i-option>
                        </i-select>
                    </div>
                    <div class="item">
                        <Radio label="custom">
                            <span>自定义时间：</span>
                        </Radio>
                        <Date-picker type="datetimerange" format="yyyy-MM-dd HH:mm:ss" v-model="newTag.gcsj.custom" placeholder="选择日期和时间" @on-change="datePickFormat" style="display: inline-block; width: 280px;"></Date-picker> <span @click="newTag.gcsj.custom = []" style="cursor: pointer;">清空</span>
                    </div>
                </radio-grou>
            </div>
        </div>

        <div v-if="newTag.lid.val.length || newTag.orderType.val.length || newTag.channel.val || newTag.platform.val.length">
            标签基本条件：
            <span v-if="newTag.lid.val.length" class="condition-box">
                <span class="condition-box-logic">且</span>
                （<span v-for="(item, idx) in newTag.lid.val" class="condition">
                    <i-select v-model="newTag.lid.logic" style="width: 60px;">
                        <i-option value="and">且</i-option>
                        <i-option value="or">或</i-option>
                    </i-select>
                    <Tag closable @on-close="delLot('lid', idx)">{{ item.name }}</Tag>
                </span>）
            </span>

            <span v-if="newTag.orderType.val.length" class="condition-box">
                <span class="condition-box-logic">且</span>
                （<span v-for="(item, idx) in newTag.orderType.val" class="condition">
                    <i-select v-model="newTag.orderType.logic" style="width: 60px;">
                        <i-option value="and">且</i-option>
                        <i-option value="or">或</i-option>
                    </i-select>
                    <Tag closable @on-close="delLot('orderType', idx)">{{ item.name }}</Tag>
                </span>）
            </span>
            
            <span v-if="newTag.channel.val" class="condition-box">
                <span class="condition-box-logic">且</span>
                （<span v-for="(item, idx) in newTag.channel.val.split(',')" class="condition">
                    <i-select v-model="newTag.channel.logic" style="width: 60px;">
                        <i-option value="and">且</i-option>
                        <i-option value="or">或</i-option>
                    </i-select>
                    <Tag closable @on-close="delLot('channel')">{{ item }}</Tag>
                </span>）
            </span>

            <span v-if="newTag.platform.val.length" class="condition-box">
                <span class="condition-box-logic">且</span>
                （<span v-for="(item, idx) in newTag.platform.val" class="condition">
                    <i-select v-model="newTag.platform.logic" style="width: 60px;">
                        <i-option value="and">且</i-option>
                        <i-option value="or">或</i-option>
                    </i-select>
                    <Tag closable @on-close="delLot('platform', idx)">{{ item.name }}</Tag>
                </span>）
            </span>
        </div>

        <div v-if="newTag.fhtj.length && ((total_day[0] && total_day[1]) || (total_buy_num[0] && total_buy_num[1]) || last_buy_time || (total_money[0] && total_money[1]))">
            标签复合条件：
            <span v-if="newTag.fhtj.indexOf('购彩累计天数') >= 0 && (total_day[0] && total_day[1])" class="condition-box">
                <span class="condition-box-logic">且</span>
                <span class="condition">
                    <Tag closable @on-close="delLot('totalDay')">购彩累计天数 [{{ total_day.join(', ') }}]</Tag>
                </span>
            </span>

            <span v-if="newTag.fhtj.indexOf('购彩订单总数') >= 0 && (total_buy_num[0] && total_buy_num[1])" class="condition-box">
                <span class="condition-box-logic">且</span>
                <span class="condition">
                    <Tag closable @on-close="delLot('totalBuyNum')">购彩订单总数 [{{ total_buy_num.join(', ') }}]</Tag>
                </span>
            </span>
            <span v-if="newTag.fhtj.indexOf('最后购彩时间') >= 0 && last_buy_time" class="condition-box">
                <span class="condition-box-logic">且</span>
                <span class="condition">
                    <Tag closable @on-close="delLot('lastBuyTimeBox')">最后购买时间 [{{ last_buy_time }}]</Tag>
                </span>
            </span>

            <span v-if="newTag.fhtj.indexOf('购彩累计金额') >= 0 && (total_money[0] && total_money[1])" class="condition-box">
                <span class="condition-box-logic">且</span>
                <span class="condition">
                    <Tag closable @on-close="delLot('totalMoney')">购彩累计金额 [{{ total_money.join(', ') }}]</Tag>
                </span>
            </span>
        </div>
        
        <p>标签条件尽量不超过3个，同维度下支持and与or逻辑，不同维度下支持having逻辑筛选。</p>
        <br>
        <div>
            <i-Button size="large" type="primary" @click="post">提交</i-Button>
        </div>
    </form>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data () {
            return {
                api: {
                    getData: '/backend/Statistics/ticketBalance',
                    getLid: '/backend/tag/get_lids',
                    newTag: '/backend/tag/save_data'
                },
                tagData: {
                    jbfl: ['用户行为'],
                    ywcj: ['购彩'],
                    jbtj: ['购买彩种', '购彩方式', '购彩渠道', '购彩平台'],
                    fhtj: ['购彩累计天数', '购彩订单总数', '最后购彩时间', '购彩累计金额'],
                    lot: [],
                    type: [
                        {
                            name: '自购',
                            id: 0
                        },
                        {
                            name: '追号',
                            id: 1
                        },
                        {
                            name: '发起合买',
                            id: 2
                        },
                        {
                            name: '参与合买',
                            id: 3
                        },
                    ],
                    platform: [
                        {
                            name: '网页',
                            id: 0
                        },
                        {
                            name: 'Android',
                            id: 1
                        },
                        {
                            name: 'iOS',
                            id: 2
                        },
                        {
                            name: 'M版',
                            id: 3
                        },
                    ],
                    time: [
                        {val: 1, name: '1天'},
                        {val: 3, name: '3天'}, 
                        {val: 7, name: '7天'}, 
                        {val: 30, name: '1个月'},
                        {val: 90, name: '3个月'}
                    ],
                    gcsj: {
                        'default': [
                            {val: 1, name: '最近1天'},
                            {val: 7, name: '最近7天'},
                            {val: 30, name: '最近30天'},
                            {val: 90, name: '最近90天'}
                        ]
                    }
                },
                newTag: {
                    name: '',
                    note: '',
                    jbfl: '用户行为',
                    ywcj: '购彩',
                    jbtj: ['购买彩种'],
                    fhtj: [],
                    lid: {
                        val: [],
                        logic: 'and'
                    },
                    orderType: {
                        val: [],
                        logic: 'and'
                    },
                    channel: {
                        val: '',
                        logic: 'and'
                    },
                    platform: {
                        val: [],
                        logic: 'and'
                    },
                    totalMoney: ['', ''],
                    totalDay: ['', ''],
                    totalBuyNum: ['', ''],
                    last_buy_time_radio: 'default',
                    lastBuyTimeBox: {
                        'default': 30,
                        'custom': []
                    },
                    gcsjRadio: 'default',
                    gcsj: {
                        'default': 30,
                        'custom': []
                    }
                },
                map: {
                   jbfl: {
                       '用户行为': 0,
                       '用户信息': 1
                   },
                   ywcj: {
                       '购彩': 0
                   },
                   jbtj: {
                       '购买彩种': 'lid',
                       '购彩方式': 'orderType',
                       '购彩渠道': 'channel',
                       '购彩平台': 'platform'
                   },
                   fhtj: {
                       '购彩累计天数': 'total_day',
                       '购彩订单总数': 'total_buy_num',
                       '最后购彩时间': 'last_buy_time',
                       '购彩累计金额': 'total_money'
                   }
                },
                dateFormat: {
                    start: '',
                    end: ''
                }
            }
        },
        computed: {
            last_buy_time: function () {
                if (this.newTag.last_buy_time_radio === 'default') {
                    return this.newTag.lastBuyTimeBox[this.newTag.last_buy_time_radio]
                } else {
                    var dateArr = this.newTag.lastBuyTimeBox[this.newTag.last_buy_time_radio].map(function (it) {
                        return this.dataFormat(it)
                    }.bind(this))
                    return dateArr.join('-')
                }
            },
            total_money: function () {
                return this.newTag.totalMoney
            },
            total_day: function () {
                return this.newTag.totalDay
            },
            total_buy_num: function () {
                return this.newTag.totalBuyNum
            }
        },
        watch: {
            'newTag.jbtj': function (val) {
                this.delLastOne(val)
                if (val.indexOf('购买彩种') < 0) {
                    this.newTag.lid = {
                        val: [],
                        logic: 'and'
                    }
                }
                if (val.indexOf('购彩方式') < 0) {
                    this.newTag.orderType = {
                        val: [],
                        logic: 'and'
                    }
                }
                if (val.indexOf('购彩渠道') < 0) {
                    this.newTag.channel = {
                        val: '',
                        logic: 'and'
                    }
                }
                if (val.indexOf('购彩平台') < 0) {
                    this.newTag.platform = {
                        val: [],
                        logic: 'and'
                    }
                }
            },
            'newTag.fhtj': function (val) {
                this.delLastOne(val)
                if (val.indexOf('最后购彩时间') < 0) {
                    this.newTag.lastBuyTimeBox.default = 30;
                }
                if (val.indexOf('购彩累计天数') < 0) {
                    this.newTag.totalDay = ['', '']
                } else if (val.indexOf('购彩订单总数') < 0) {
                    this.newTag.totalBuyNum = ['', '']
                } else if (val.indexOf('最后购彩时间') < 0) {
                    this.newTag.lastBuyTimeBox = {
                        'default': '',
                        'custom': []
                    }
                } else if (val.indexOf('购彩累计金额') < 0) {
                    this.newTag.totalMoney = ['', '']
                }
            }
        },
        created: function () {
            axios.get(this.api.getLid).then(function (res) {
                this.tagData.lot = res.data;
            }.bind(this)).catch(function (err) {
                console.log(err)
                this.$Message.error('彩种请求失败，请刷新重新获取');
            }.bind(this))
        },
        methods: {
            keyRule: function (k, i) {
                this.$set(this.newTag[k], i, this.newTag[k][i].replace(/[^\-?\d*]/g,''))
            },
            datePickFormat: function () {
                setTimeout(function () {
                    var start = this.newTag.gcsj.custom[0],
                        end = this.newTag.gcsj.custom[1];
                    this.dateFormat.start = this.dataFormat(start);
                    this.dateFormat.end = this.dataFormat(end);
                }.bind(this), 100) 
            },
            datePickFormat2: function (val) {
                this.newTag.last_buy_time_radio = 'default';
                this.newTag.last_buy_time_radio = 'custom';
            },
            dataFormat: function (date) {
                return date.getFullYear() + '-' + this.padStart(date.getMonth() + 1) + '-' + this.padStart(date.getDate()) + ' ' + this.padStart(date.getHours()) + ':' + this.padStart(date.getMinutes()) + ':' + this.padStart(date.getSeconds());
            },
            padStart: function (val) {
                if(val < 10) {
                    return '0' + val
                } else {
                    return val
                }
            },
            isArray: function (val) {
                return Object.prototype.toString.call(val) === '[object Array]'
            },
            post: function () {
                if (!this.newTag.name) {
                    this.$Message.info('请填写标签名称');
                    return;
                } else if (!this.newTag.note) {
                    this.$Message.info('请填写标签名解释');
                    return;
                }
                var _this = this,
                    data = {
                        tag_name: this.newTag.name,
                        tag_desc: this.newTag.note,
                        base_type: this.map.jbfl[this.newTag.jbfl],
                        sub_type: this.map.ywcj[this.newTag.ywcj],
                        date: {}
                    },
                    flag = 1,
                    hasFilter = this.newTag.jbtj.length && (this.newTag.lid.val.length || this.newTag.orderType.val.length || this.newTag.platform.val.length || this.newTag.channel.val),
                    hasHaving = this.newTag.fhtj.length && ((this.total_day[0] && this.total_day[1]) || (this.total_buy_num[0] && this.total_buy_num[1]) || this.last_buy_time || (this.total_money[0] && this.total_money[1]));
                
                if (!hasFilter && !hasHaving) {
                    this.$Message.info('至少选择基本条件或复合条件的中的任意一项');
                    return;
                } else {
                    if (hasFilter) {
                        data.filter = {};
                        this.newTag.jbtj.forEach(function (it) {
                            var key = this.map.jbtj[it],
                                val = this.newTag[key].val;
                            
                            data.filter[key] = {};

                            if (this.isArray(val)) {
                                data.filter[key].val = val.map(function (it) { return it.id }).join(',');
                            } else {
                                data.filter[key].val = val;
                            }
                            data.filter[key].logic = this.newTag[key].logic;
                            
                        }.bind(this))

                        data.filter = JSON.stringify(data.filter)
                    } else {
                        delete data.filter;
                    }

                    if (hasHaving) {
                        data.having = {};
                        this.newTag.fhtj.forEach(function (it) {
                            var key = this.map.fhtj[it],
                                val = this[key];

                            data.having[key] = {};
                            if (this.isArray(val)) {
                                if (this[key][0] === '' || this[key][1] === '') {
                                    this.$Message.info(it + '的值不能为空');
                                    flag *= 0;
                                    return;
                                }
                                if (this[key][1] !== '*' && parseInt(this[key][0]) > parseInt(this[key][1])) {
                                    this.$Message.info(it + '的最小值不能比最大值大');
                                    flag *= 0;
                                    return;
                                }
                                data.having[key] = this[key].join('-');
                            } else {
                                if (this[key] === '') {
                                    this.$Message.info('请选择' + it);
                                    flag *= 0;
                                }
                                data.having[key] = this[key];
                            }
                        }.bind(this))
                        
                        if (flag) {
                            data.having = JSON.stringify(data.having)
                        } else {
                            return;
                        }
                    } else {
                        delete data.having;
                    }
                }

                

                if (this.newTag.gcsjRadio === 'default') {
                    data.date.range = this.newTag.gcsj[this.newTag.gcsjRadio];
                } else {
                    data.date.start = this.dateFormat.start;
                    data.date.end = this.dateFormat.end;
                }

                if(this.newTag.gcsjRadio === 'custom' && (!data.date.start || !data.date.end)) {
                    this.$Message.info('请选择购彩时间');
                    return;
                }
                data.date = JSON.stringify(data.date);

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
                                location.href = "/backend/tag"
                            }
                        });
                    } else {
                        _this.$Message.error(res.msg);
                    }
                }).catch(function (err) {
                    _this.$Message.error('提交失败');
                })
            },
            isGtThree: function () {
                if (this.newTag.jbtj.concat(this.newTag.fhtj).length > 3) {
                    this.$Message.warning('最多只能选择3个条件');
                    return true;
                }
            },
            delLastOne: function (val) {
                if (this.isGtThree()) {
                    val.splice(val.length - 1, 1);
                }
            },
            addLogic: function (val) {
                var type = typeof this.val.logic;
                if (type === 'string') {
                    this.val.logic === '且';
                } else {
                    this.val.logic.push('且');
                }
            },
            delLot: function (item, index) {
                if (item === 'lastBuyTimeBox') {
                    this.newTag[item] = {
                        'default': '',
                        'custom': []
                    }
                    return;
                }
                if (this.newTag[item].val) {
                    if (this.isArray(this.newTag[item].val)) {
                        this.newTag[item].val.splice(index, 1);
                    } else {
                        this.newTag[item].val = '';
                    }
                } else {
                    if (this.isArray(this.newTag[item])) {
                        this.newTag[item] = [];
                    } else {
                        this.newTag[item] = '';
                    }
                    
                }
            }
        }
    })
  </script>