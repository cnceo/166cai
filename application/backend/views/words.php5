<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">敏感词库</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/links/index/kjdh">快捷导航</a></li>
            <li><a href="/backend/links/index/yqlj">友情链接</a></li>
            <li class="current"><a href="/backend/links/sensitiveWords">敏感词库</a></li>
        </ul>
    </div>
    <div id="app" v-cloak>
        <div class="mt20 mb20">
            <table style="width: 100%">
                <tbody>
                    <tr>
                        <td width="200">
                <i-Input v-model="searchFilter" placeholder="请输入搜索词"></i-Input>
                </td>
                <td width="100" align="center">
                <i-button type="primary" @click="search">搜索</i-button>
                </td>
                <td align="right">
                    <span v-show="fileName" style="margin-right: 10px;">{{ fileName }}</span>
                    <Upload style="display: inline-block;" ref="file" :show-upload-list="false" :format=['txt'] action="/backend/links/uplaodsensitiveWords" :before-upload="beforeUpload" :on-format-error="fileFormat" :on-success="successUpload">
                        <i-Button type="ghost" icon="ios-cloud-upload-outline">上传文件</i-Button>
                    </Upload>
                <i-button type="primary"  @click="submitUpload">提交</i-button>
                </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mb20">
            <Tag v-for="(item, index) in mgc" closable @on-close="delMgc(item.id, index)">{{ item.word }}</Tag>
        </div>

        <Page v-if="mgcPagTotal > 20" :total="mgcPagTotal" :page-size="mgcPage.size" :current="mgcPage.current" show-total @on-change="pageAjax" class="mb20"></Page>
        <i-button type="primary" @click="saveDel">保存并更新</i-button>
    </div>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    var des = new Vue({
        el: '#app',
        data () {
            return {
                api: {
                    'getMgc': '/backend/links/getsensitiveWords',
                    'delMgc': '/backend/links/delsensitiveWords'
                },
                searchFilter: '',
                searchwords: '',
                mgc: [],
                mgcPagTotal: <?php echo $num; ?>,
                mgcPage: {
                    size: 200,
                    current: 1
                },
                delIdArr: [],
                fileName: '',
                uploadFile: null
            }
        },
        created () {
            this.mgc =<?php echo $words;?>
},
        watch: {
            mgcPage: {
                handler: function () {
                    console.log(this.mgcPage.current)
                    this.getMgc(this.mgcPage.current)
                },
                deep: true
            }
        },
        methods: {
        search: function () {
            this.searchwords = this.searchFilter;
            if (this.mgcPage.current === 1) {
                this.getMgc()
            } else {
                this.pageAjax(1)
            }
        },
        fileFormat: function (file, fileList) {
            console.log(file.type)
            this.$Message.error('只支持txt！')
        },
        beforeUpload: function (file) {
            this.fileName = file.name;
            this.uploadFile = file;
            return false
            },
            submitUpload: function () {
            var _this = this;
            this.$Modal.confirm({
            content: '是否确认上传？',
                    onOk: function () {
                        _this.uploadFile ? _this.$refs.file.post(_this.uploadFile) : _this.$Message.warning('请先选择文件!');
                    }
            })
        },
        successUpload: function (res) {
            var _this = this;
            _this.$Message.success(res);
            setTimeout("window.location.reload()",1000)
        },
        getMgc (pageNum) {
            var _this = this;
            axios.get(this.api.getMgc, {
                params: {
                    "word": this.searchwords,
                    "pageNum": pageNum ? pageNum : 1,
                    "num": this.mgcPage.size
                }
            }).then(function (res) {
                _this.mgc = res.data.words;
                if(_this.mgcPagTotal!=res.data.num){
                    _this.mgcPagTotal = res.data.num;
                }
            }).catch(function (err) {
                _this.$Message.error('加载失败');
            })
        },
        pageAjax (index) {
            this.mgcPage.current = index;
        },
        delMgc: function (id, index) {
            if (!!this.delIdArr.indexOf(id)) {
                this.delIdArr.push(id);
            }
                this.mgc.splice(index, 1);
        },
        saveDel: function () {
            // 保存删除结果
            var _this = this;
            this.$Modal.confirm({
                content: '<p>是否确认修改？<p>',
                onOk: function () {
                    axios.post(_this.api.delMgc, {
                        id: _this.delIdArr
                    }).then(function (res) {
                        _this.$Message.success(res.data.msg)
                        setTimeout("window.location.reload()",1000)
                    }).catch(function (err) {
                    _this.$Message.error("呐，这么做最重要的是要有权限啦！");
                    })
                    }
                })
            }
        }
})
</script>