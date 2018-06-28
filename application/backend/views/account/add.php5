<?php $this->load->view("templates/head") ?>
<div id="app" v-cloak>
<template>
        <Breadcrumb>
            <Breadcrumb-item>系统管理</Breadcrumb-item>
            <Breadcrumb-item href="/backend/Account/">帐号管理</Breadcrumb-item>
            <Breadcrumb-item>新建帐号</Breadcrumb-item>
        </Breadcrumb>
    </template>
  <i-Form :model="formDate" label-position="left" :label-width="74" style="width: 300px;">
      <Form-item label="账号名：">
          <i-Input v-model="formDate.name"></i-Input>
      </Form-item>
      <Form-item label="账号密码：">
          <i-Input v-model="formDate.pass" type="password"></i-Input>
      </Form-item>
      <Form-item label="手机号：">
          <i-Input v-model="formDate.phone"></i-Input>
      </Form-item>
      <Form-item label="账号身份：">
          <i-Select v-model="formDate.role">
            <i-Option v-for="item in idList" :value="item.value" :key="item.value">{{ item.label }}</i-Option>
          </i-Select>
      </Form-item>
      <Form-item label="备注：">
          <i-Input v-model="formDate.mark" type="textarea"></i-Input>
      </Form-item>
  </i-Form>

  <Tree :data="baseData" show-checkbox ref="tree"></Tree>
  <?php if(!$preview):?>
  <i-Button type="primary" @click="postForm">确认提交</i-Button>
  <?php endif;?>
  <i-Button type="ghost" @click="cancelSubmit">取消</i-Button>
</div>
<style>
  .ivu-tree-children .ivu-tree-arrow, .ivu-tree-children .ivu-checkbox-wrapper {
    float: left;
  	margin-left: -126px;
  	margin-top: 8px;
  }
  .ivu-tree-children .ivu-checkbox-wrapper {
	margin-left: -115px;
  }
  .ivu-tree-children .ivu-tree-title {
    display: block;
    font-size: 18px;
    font-weight: bold;
  }
  .ivu-tree-children li > .ivu-tree-children {
    vertical-align: top;
    padding-left: 130px;
  }
  .ivu-tree-children .ivu-tree-children .ivu-tree-title {
    float: left;
    width: 100px;
    margin-left: -100px;
    margin-top: 8px;
    overflow: hidden;
    font-weight: normal;
    font-size: 14px;
    color: #2d8cf0;
  }
  .ivu-tree-children li > .ivu-tree-children li {
    margin: 0;
  }
  .ivu-tree-children li > .ivu-tree-children li:after {
    content: '';
    display: table;
    clear: both;
  }
  .ivu-tree-children li .ivu-tree-children li > .ivu-tree-children {
    float: left;
    width: auto;
    padding-left: 2px;
  }
  .ivu-tree-children li .ivu-tree-children li > .ivu-tree-children li {
    margin: 8px 0;
  }
  .ivu-tree-children .ivu-tree-children li > .ivu-tree-children .ivu-tree-title {
    float: none;
    display: inline-block;
  	width:auto;
    margin: 0;
  }
  .ivu-tree-children li .ivu-tree-children li > .ivu-tree-children li .ivu-checkbox-wrapper {
    display: block;
    float: left;
    padding-right: 8px;
    margin-right: 0;
  	margin-left: 0;
  	margin-top: 0;
  }
  .ivu-tree-children .ivu-tree-children .ivu-tree-children .ivu-tree-title {
    font-weight: normal;
    font-size: 12px;
    color: #ed3f14;
  }
</style>
<script src="/caipiaoimg/v1.0/js/axios.min.js"></script>
<script type="text/javascript">
  var xh = new Vue({
    el: '#app',
    data () {
      return {
      	api: {
        	saveData: '/backend/Account/add_account',
        },
        idList: [
          <?php foreach ($userRoles as  $key => $value):?>
          {
            value: '<?php echo $key;?>',
            label: '<?php echo $value;?>'
          },
          <?php endforeach;?>
        ],
        formDate: {
            id: '<?php echo $id;?>',
            name: '<?php echo $name;?>',
            pass: '',
            phone: '<?php echo $phone;?>',
            role: '<?php echo $role;?>',
            mark: '<?php echo $mark;?>'
        },
        baseData: [
          <?php foreach ($capacityConfig as $key => $value):?>
          {
            expand: true,
            title: '<?php echo $value['name'];?>',
            children: [
              <?php foreach ($value['child'] as $k => $v):?>
              {
                expand: true,
                title: '<?php echo $v['name'];?>',
                children: [
                  <?php foreach ($v['child'] as $capacity => $name):?>
                  {
                    title: '<?php echo $name;?>',
                    id: '<?php echo $capacity;?>',
                    checked: <?php if(in_array($capacity, $userCapacity, true)):?>true<?php else:?>false<?php endif;?>,
                  },
                  <?php endforeach;?>
                ]
              },
              <?php endforeach;?>
            ]
          },
          <?php endforeach;?>
        ]
      }
    },
    methods: {
    	postForm: function () {
    		var _this = this;
            if((_this.formDate.pass) || (!_this.formDate.id)){
            	var pattern=/^(?!\d+$)[0-9a-zA-Z]{6,16}$/;
            	if (!pattern.test(this.formDate.pass)){
            		_this.$Message.error('密码应该在6-16位之间,且非全数字！');
            		return false;
                }
            }
            var treeResultArr = _this.$refs.tree.getCheckedNodes();
            var idArr = [];
            treeResultArr.forEach(function (item) {
                if (!item.children) {
                	var cdArr = item.id.split('_');
                    cdArr.forEach(function (it, i) {
                    	idArr.push(cdArr.slice(0, i + 1).join('_'));
                    })
                }
            })
            var capacity = idArr.filter(function(item, index, array){
        		return array.indexOf(item) === index;
    		});
            axios.post(_this.api.saveData, {
            	name: _this.formDate.name,
                pass: _this.formDate.pass,
                phone: _this.formDate.phone,
                role: _this.formDate.role,
                capacity: capacity,
                mark: _this.formDate.mark,
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
                	self.location='/backend/Account/';
                } else {
                	_this.$Message.error(res.message);
                }
            }).catch(function (e) {
            	_this.$Message.error('操作失败');
            })
       	},
       	cancelSubmit:function()
       	{
       		self.location='/backend/Account/';
        }
    }
  })
</script>