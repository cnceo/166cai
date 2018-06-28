<?php $this->load->view("templates/head") ?>
<div id="app" v-cloak>

  <Breadcrumb class="mb20">
    <Breadcrumb-item href="">首页</Breadcrumb-item>
    <Breadcrumb-item href="">运营管理</Breadcrumb-item>
    <Breadcrumb-item>限号管理</Breadcrumb-item>
  </Breadcrumb>

  <div class="mb20">
    <i-select v-model="lottery" style="width:200px">
        <i-option v-for="(item, key) in lotteryArr" :value="key" :key="key" v-on:click="alert(1)">{{ item }}</i-option>
    </i-select>
    <i-button type="primary" @click="addLimit">新增限号</i-button>
  </div>
  
  <i-table :context="self" :columns="limit.columns" :data="limit.data" class="msgTable mb20"></i-table>
  <Page v-if="mgcPage.total > 20" :total="mgcPage.total" :page-size="mgcPage.size" show-total @on-change="pageAjax" class="mb20"></Page>
</div>
<style>
	th .ivu-table-cell {
	 width: 100%;
    }
  .ivu-modal-confirm-footer {
    margin-top: 20px;
  }
  .xh-table tbody th, .xh-table tbody td {
    padding: 4px 0;
    line-height: 32px;
  }
  .xh-table tbody th {
    vertical-align: top;
    font-weight: normal;
    text-align: right;
  }
  .xh-ipt {
    vertical-align: top;
    width: 30px;
    margin-right: -1px;
  }
  .xh-ipt input {
    padding: 4px 2px;
    text-align: center;
    border-radius: 0;
  }
  .xh-ipt input:hover, .xh-ipt input:focus {
    position: relative;
    z-index: 1;
  }
  .xh-ipt:first-child input {
    border-radius: 4px 0 0 4px;
  }
  .xh-ipt:last-child input {
    border-radius: 0 4px 4px 0;
  }
  .xh-ipt:only-child input {
  	border-radius: 4px;
  }
  .xh-table .codesTpl {
    margin-right: 10px;
  }
</style>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
  Vue.component('xh-table', {
    template: `
      <div class="xh-table" :value="value">
        <table>
          <tbody>
            <tr>
              <th width="80">彩种：</th>
              <td>
                {{ lottery }}
              </td>
            </tr>
            <tr>
              <th>玩法：</th>
              <td>
                <i-select v-model="playType" style="width:200px">
                  <i-option v-for="(item, key) in lotteryTypeArr" :value="key" :key="key">{{ item }}</i-option>
                </i-select>
              </td>
            </tr>
            <tr>
              <th>方案：</th>
              <td>
                <i-input class="xh-ipt" v-for="n in num" v-model="codes1[n - 1]" maxlength="maxlength" @input="iptUED($event)"></i-input>
              </td>
            </tr>
            <tr>
              <th>确认方案：</th>
              <td>
                <i-input class="xh-ipt" v-for="n in num" v-model="codes2[n - 1]" maxlength="maxlength" @input="iptUED($event)"></i-input>
              </td>
            </tr>
            <tr>
              <th>方案模板：</th>
              <td><span class="codesTpl" v-for="item in tpl">{{ item }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    `,
    data () {
      return {
        playType: '',
        codes1: [],
        codes2: [],
        tpl: [],
        num: 0,
      }
    },
    props: {
      value: Object,
      lid:String,
      lottery: String,
      lotteryInfo: Array
    },
    created: function () {
    },
    computed: {
    	value: function () {
        	if (this.playType) {
        		return {
            		lid:this.lid,
                    playType: this.playType,
                    codes1: this.codes1.join(this.lotteryInfo[this.playType].separator),
                    codes2: this.codes2.join(this.lotteryInfo[this.playType].separator)
                  }
        	}
        },
      	lotteryTypeArr: function () {
    		var arr = {}, k = 0;
        	for(var i in this.lotteryInfo) {
	            if (k == 0) this.playType = i;
	            arr[i] = this.lotteryInfo[i].cname;
	            k++;
        	}
        	return arr;
      	}
    },
    watch: {
      value: function (val) {
        this.$emit("input", val)
      },
      playType: function () {
        this.tpl = this.lotteryInfo[this.playType].txt;
        this.num = this.lotteryInfo[this.playType].num;
        this.maxlength = this.lotteryInfo[this.playType].maxlength;
        this.codes1 = [];
        this.codes2 = [];
        for(var i = 0; i < this.num; i++) {
          this.codes1.push(this.lotteryInfo[this.playType].value ? this.lotteryInfo[this.playType].value[i] : '')
          this.codes2.push(this.lotteryInfo[this.playType].value ? this.lotteryInfo[this.playType].value[i] : '')
        }
      }
    },
    methods: {
      iptUED: function (value) {
        if(value.length >= this.maxlength) {
          var $node = $(event.path[1]);
          $node.next().find('input').focus();
          if (!$node.next().length) {
            $node.closest('tr').next().find('input').eq(0).focus()
          }
        }
      }
    }
  });

  var xh = new Vue({
    el: '#app',
    data () {
      return {
        api: {
          'getLimit': '/backend/Limitcode/getLimit',
          'getLimitLottery': '/backend/Limitcode/getLimitLottory',
          'createLimit': '/backend/Limitcode/createLimit',
          'overLimit': '/backend/Limitcode/overLimit'
        },
        lotteryInfo: [],
        lottery: '54',
        playTypeArr: {},
        postOrder: {},
        self: this,
        lidCname : {52:'福彩3D',33:'排列三',35:'排列五',21406:'山东十一选五',21407:'江西十一选五',21408:'湖北十一选五',53:'上海快三',54:'快乐扑克',55:'重庆时时彩',56:'吉林快三',57:'江西快三', 21421: '广东十一选五'},
        mgcPage: {
            total: 40,
            size: 20,
            current: 1
        },
        limit: {
          columns: [
            {
                title: '限号期次',
                key: 'issue',
                width: 120
            },
            {
                title: '玩法',
                key: 'playType',
                width: 120
            },
            {
                title: '方案',
                key: 'codes'
            },
            {
                title: '开始时间',
                key: 'created'
            },
            {
                title: '结束时间',
                key: 'endTime'
            },
            {
                title: '开奖时间',
                key: 'awardTime'
            },
            {
                title: '操作',
                key: 'id',
                align: 'center',
                render (row, column, index) {
                    return (!row.endTime ? `<i-button type="primary" size="small" @click="over(row.id, row.playType, row.codes)">结束限号</i-button>` : '');
                }
            }
          ],
          data: [],
          totalDate: [],
          currentPage: 1
        },
        addData: null,
      }
    },
    created () {
      this.getLottery();
    },
    watch: {
        lottery: function () {
        	this.mgcPage.current = 1;
          this.getDate(this.lottery, this.mgcPage.current);
        },
    	mgcPage: {
	        handler: function () {
	          this.getDate(this.lottery, this.mgcPage.current)
	        },
        deep: true
      }
    },
    computed: {
      lotteryArr: function () {
        var arr = {};
        for(var i in this.lotteryInfo) {
            arr[i] = this.lidCname[i];
        }
        return arr;
      },
      lotterySingle: function () {
        return this.lotteryInfo[this.lottery]
      }
    },
    methods: {
      getDate (lid, pageNum) {
        var _this = this;
        axios.get(this.api.getLimit, {params: {'pageNum': pageNum ? pageNum : 1, 'lid': lid}}).then(function (res) {
            var data = res.data.data;
            _this.mgcPage.total = res.data.total;
            _this.limit.data = [];
          data.forEach(function (item, i) {
            _this.limit.data.push(item)
          })
        }).catch(function (err) {
          _this.$Message.error('加载失败，刷新重新加载');
        })
      },
      pageAjax (index) {
          this.mgcPage.current = index;
       },
      getLottery () {
        var _this = this;
        axios.get(this.api.getLimitLottery).then(function (res) {
          _this.lotteryInfo = res.data;
          _this.lottery = '54';
          _this.getDate(_this.lottery, _this.mgcPage.current);
        }).catch(function (err) {
          _this.$Message.error('限购彩种加载失败，刷新重新加载');
        })
      },
      addLimit () {
        var _this = this;
        if (this.lottery) {
          this.$Modal.confirm({
            render: function (h) {
                return h('xh-table', {
                    props: {
                      'lottery': _this.lidCname[_this.lottery],
                      'lid':_this.lottery,
                      'lotteryInfo': _this.lotterySingle,
                      'v-model': _this.postOrder
                    },
                    on: {
                      input (val) {
                        _this.postOrder = val
                      }
                    }
                })
            },
            onOk: function () {
                var unfull = false;
                $('.ivu-input').each(function(k, e){
                    if ($(this).val() == '') {
                    	unfull = true;
                    }
                })
                if (unfull)
                {
                	_this.$Message.error('请填写完整的号码');
                	return;
                }
                $.post(_this.api.createLimit, _this.postOrder, function(res){
                    if (res.status == 200) {
                    	_this.$Message.success(res.message);
                    }else {
                    	_this.$Message.error(res.message);
                    }
                    _this.getDate(_this.lottery, _this.mgcPage.current);
                }, 'json')
            }
          })
        } else {
          this.$Message.error('请先选择彩种');
        }
      },
      over (index, playType, codes) {
        // 结束限号
        var _this = this;
        this.$Modal.confirm({
            content: '确定要结束限号'+playType+codes+'吗？',
        	onOk: function () {
        		axios.post(_this.api.overLimit, {
                    id: index
                }, {transformRequest: [function (data) {
                    var ret = ''
                        for (var k in data) {
                            ret += encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) + '&'
                        }
                        return ret
                    }],
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }}).then(function (res) {
                  var data = res.data;
                  if(data.status == 'n')
                  {
                	  _this.$Message.error(data.message);
                  }
                  _this.getDate(_this.lottery, _this.mgcPage.current);
                })
            }
        });
      }
    }
  })
</script>