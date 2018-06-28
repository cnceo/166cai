<?php $this->load->view("templates/head");?>

<div id="app" v-cloak>
  <div class="mod-tab mt20">
    <div class="mod-tab-hd">
      <ul>
        <li><a href="/backend/Management/huizong">出票汇总</a></li>
		<li><a href="/backend/Management/monitorTicket">单彩种监控</a></li>
		<li><a href="/backend/Management/chaseCancel">追号监控</a></li>
		<li class="current"><a href="/backend/Management/ticketLimit">出票限制</a></li>
      </ul>
    </div>

          <div class="cpxz mt20">
            <i-table :context="self" :columns="limit.columns" :data="limit.data" class="msgTable mb20"></i-table>

            <i-table :context="self" :columns="limit2.columns" :data="limit2.data" class="msgTable mb20"></i-table>
          </div>
          <div>
            <div>提示</div>
            <ol>
              <li>出票限制：即针对页面投注截止提前一定时间对订单的票数进行限制。</li>
            </ol>
          </div>
  </div>
</div>
<style>
  .ivu-table table {
    width: 100%;
  }
  .ivu-table th .ivu-table-cell {
    display: block;
  }
  .xh-table td, .xh-table th {
    padding: 4px 0;
    text-align: left;
  }
  .xh-table tr td:nth-child(3) {
    text-align: right;
  }
</style>
<script src="/caipiaoimg/v1.0/js/axios.min.js"></script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
  Vue.component('xh-table', {
    template: `
      <div class="xh-table" :value="value">
        <table>
          <tbody>
            <tr>
              <th width="120">彩种名称：</th>
              <td>{{ lotterName }}</td>
              <td width="20"></td>
            </tr>
            <tr>
              <th>{{column.time1}}：</th>
              <td>
                <i-input class="xh-ipt" v-model="time1""></i-input>
              </td>
              <td>张</td>
            </tr>
            <tr>
              <th>{{column.time2}}：</th>
              <td>
                <i-input class="xh-ipt" v-model="time2""></i-input>
              </td>
              <td>张</td>
            </tr>
            <tr>
              <th>{{column.time3}}：</th>
              <td>
                <i-input class="xh-ipt" v-model="time3""></i-input>
              </td>
              <td>张</td>
            </tr>
          </tbody>
        </table>
      </div>
    `,
    data () {
      return {
        lotterName: this.row.lotterName,
        time1: this.row.time1,
        time2: this.row.time2,
        time3: this.row.time3,
        id: this.row.id
      }
    },
    props: {
      value: Object,
      row: Object,
      column: Object
    },
    computed: {
      value: function () {
        return {
          lotterName: this.lotterName,
          time1: ~~this.time1,
          time2: ~~this.time2,
          time3: ~~this.time3,
          id: this.id
        }
      }
    },
    watch: {
      value: function (val) {
        this.$emit("input", val)
      }
    }
  });

  var cpxz = new Vue({
    el: '#app',
    data () {
      return {
        api: {
          cpxz: '/backend/Management/updateTicket'
        },
        self: this,
        limit: {
          columns: [
            {
                title: '类型',
                key: 'cate',
                width: 120,
                align: 'center',
                render: function (row, column, index) {
                    return `慢频及竞技彩`;
                }
            },
            {
                title: '彩种',
                key: 'lotterName',
                width: 120
            },
            {
                title: '页面截止前5分钟',
                key: 'time1',
                render: function (row, column, index) {
                    return `${row.time1}张`;
                }
            },
            {
                title: '页面截止前15分钟',
                key: 'tim2',
                render: function (row, column, index) {
                    return `${row.time2}张`;
                }
            },
            {
                title: '页面截止前45分钟',
                key: 'time3',
                render: function (row, column, index) {
                    return `${row.time3}张`;
                }
            },
            {
                title: '操作',
                key: 'action',
                align: 'center',
                render (row, column, index) {
                    return `<i-button type="text" size="small" @click="modify(row)">调整配置</i-button>`;
                }
            }
          ],
          data: [
             <?php foreach ($mp as $value):?>
            {
              id: <?php echo $value['id'];?>,
              lotterName: '<?php echo $value['name'];?>',
              time1: <?php echo $value['time1'];?>,
              time2: <?php echo $value['time2'];?>,
              time3: <?php echo $value['time3'];?>,
            },
            <?php endforeach;?>
          ],
        },
        limit2: {
          columns: [
            {
                title: '类型',
                key: 'cate',
                width: 120,
                align: 'center',
                render: function (row, column, index) {
                    return `高频彩`;
                }
            },
            {
                title: '彩种',
                key: 'lotterName',
                width: 120
            },
            {
                title: '页面截止前1分钟',
                key: 'time1',
                render: function (row, column, index) {
                    return `${row.time1}张`;
                }
            },
            {
                title: '页面截止前3分钟',
                key: 'time2',
                render: function (row, column, index) {
                    return `${row.time2}张`;
                }
            },
            {
                title: '页面截止前5分钟',
                key: 'time3',
                render: function (row, column, index) {
                    return `${row.time3}张`;
                }
            },
            {
                title: '操作',
                key: 'action',
                align: 'center',
                render (row, column, index) {
                    return `<i-button type="text" size="small" @click="modify2(row)">调整配置</i-button>`;
                }
            }
          ],
          data: [
			<?php foreach ($gp as $value):?>
			{
			  id: <?php echo $value['id'];?>,
			  lotterName: '<?php echo $value['name'];?>',
			  time1: <?php echo $value['time1'];?>,
			  time2: <?php echo $value['time2'];?>,
			  time3: <?php echo $value['time3'];?>,
			},
			<?php endforeach;?>
          ]
        },
        postInfo: null
      }
    },
    mounted: function () {
      Vue.nextTick(function () {
        // 处理合并单元格
        var tbody = document.querySelectorAll('.cpxz .ivu-table-body');
        for(var i = 0, tbodyL = tbody.length; i < tbodyL; i++) {
          var tr = tbody[i].querySelectorAll('tr');
          var td = tr[0].querySelector('td');
          for(var k = 1, trL = tr.length; k < trL; k++) {
            tr[k].querySelector('td').remove()
          }
          td.setAttribute('rowspan', trL);
          td.style.borderRight = "1px solid #e9eaec"
        }
      })
    },
    methods: {
      modify: function (row) {
        var _this = this;
        this.$Modal.confirm({
          render: function (h) {
              return h('xh-table', {
                  props: {
                    'v-model': _this.postInfo,
                    'row': row,
                    'column': {
						time1: '页面截止前5分钟',
						time2: '页面截止前15分钟',
						time3: '页面截止前45分钟'
                        }
                  },
                  on: {
                    input (val) {
                      _this.postInfo = val
                    }
                  }
              })
          },
          onOk: function () {
        	if (!_this.postInfo) return;
            axios.post(_this.api.cpxz, _this.postInfo, {transformRequest: [function (data) {
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
              if (data.status == 'y') {
                _this.limit.data.splice(row._index, 1, _this.postInfo);
                layer.alert(data.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
              }else{
                layer.alert(data.message, {icon: 2,btn:'',title:'温馨提示',time:0});
              }
              _this.postInfo = null;
            }).catch(function (err) {
              layer.alert('操作失败！', {icon: 2,btn:'',title:'温馨提示',time:0});
            })
          }
        })
      },
      modify2: function (row) {
        var _this = this;
        this.$Modal.confirm({
          render: function (h) {
              return h('xh-table', {
                  props: {
                    'v-model': _this.postInfo,
                    'row': row,
                    'column': {
						time1: '页面截止前1分钟',
						time2: '页面截止前3分钟',
						time3: '页面截止前5分钟'
                        }
                  },
                  on: {
                    input (val) {
                      _this.postInfo = val
                    }
                  }
              })
          },
          onOk: function () {
            if (!_this.postInfo) return;
            axios.post(_this.api.cpxz, _this.postInfo, {transformRequest: [function (data) {
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
              if (data.status == 'y') {
                _this.limit2.data.splice(row._index, 1, _this.postInfo)
              }else{
            	  _this.$Message.error(data.message);
              }
              _this.postInfo = null;
            }).catch(function (err) {
            	_this.$Message.error('操作失败！');
            })
          }
        })
      }
    }
  })
</script>