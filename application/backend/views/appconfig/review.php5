<!DOCTYPE html>
<html lang="en">
  <head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      *::before, *::after {
        box-sizing: inherit;
      }
      body {
        background: #000;
        text-align: center;
      }
      [v-cloak] {
        display: none;
      }
      .app-index {
        display: inline-block;
        width: 360px;
        margin: 10px;
        background: #fff;
        text-align: left;
      }
      .lot-group {
        display: flex;
        flex-wrap: wrap
      }
      .lot-group .item {
        flex-basis: 50%;
        font-size: 12px;
        color: #999;
      }
      .lot-group .item .main {
        display: flex;
        align-items: center;
        height: 75px;
        padding: 0 10px 0 15px;
        border-bottom: 1px solid #eee;
      }
      .lot-group .item .child {
        display: flex;
        flex-direction: column;
        width: 200%;
        padding-top: 4px;
        background: #F3F4F5;
        box-shadow: inset 0 1px 21px 0 rgba(0,0,0,.1);
      }
      .lot-group .item .child.even {
        margin-left: -100%;
      }
      .lot-group .child .item {
        flex-basis: 100%;
        display: flex;
        align-items: center;
        height: 50px;
        padding: 0 10px 0 15px;
        background: transparent;
        border-bottom: 1px solid #eee;
        font-size: 12px;
        color: #999;
      }
      .lot-group .child .item .lot-img {
        flex: 0 0 38px;
      }
      .lot-group .child .item .lot-txt {
        display: inline-flex;
        align-items: center;
      }
      .lot-group .child .item .name {
        margin-right: 10px;
        font-size: 16px;
        color: #333;
      }
      .lot-img {
        position: relative;
        flex: 0 0 50px;
        margin-right: 10px;
      }
      .lot-img img {
        max-width: 100%;
      }
      .lot-txt {
        flex: 1;
      }
      .item .name {
        font-size: 16px;
        color: #333;
      }
      .lot-img.jj:after {
        content: '加奖';
        position: absolute;
        right: -5px;
        top: -2px;
        width: 36px;
        height: 20px;
        background: #fff;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        transform: scale(.5);
        transform-origin: 100% 0;
        color: #e13030;
        border-radius: 99em;
        border: 1px solid #e13030;
        box-shadow: -1px 1px 0 #fff;
      }
      .lot-group .desMark {
        color: #FB4E46;
      }
      /*.child-list {
        
        display: flex;
        flex-direction: column;
        padding-left: 110px;
        background: #F3F4F5;
        box-shadow: inset 0 1px 4px 0 rgba(217,217,217,.2);
      }*/
      /*.child.item {
        flex-basis: 100%;
        display: flex;
        align-items: center;
        height: 50px;
        background: #F3F4F5;
        border-bottom: 1px solid #eee;
        font-size: 12px;
        color: #999;
      }
      .child.item .lot-img {
        flex: 0 0 38px;
      }
      .child.item .lot-txt {
        display: inline-flex;
        align-items: center;
      }
      .child.item .name {
        margin-right: 10px;
        font-size: 16px;
        color: #333;
      }*/
    </style>
  </head>
  <body>
    <div id="app" v-cloak>
      <div class="app-index">
        <div class="lot-group">
          <template v-for="(item, idx) in list">
            <div class="item" v-if="item.isShow && !item.parentLid" :style="'order:' + item.qz">
              <div class="main" @click="more(item)">
                <div class="lot-img" :class="item.jj ? 'jj' : ''">
                  <img :src="item.imgsrc" alt="">
                </div>
                <div class="lot-txt">
                  <p class="name">{{ item.name }}</p>
                  <p v-if="item.des" :class="item.desMark ? 'desMark' : ''">{{ item.des }}</p>
                  <p v-else>奖池15亿8964万</p>
                </div>
              </div>
              <div class="child" v-if="item.child && item.isOpen" :class="item.even ? 'even' : ''">
                <div class="item" v-for="it in item.child" :style="'order:' + it.qz">
                  <div class="lot-img" :class="it.jj ? 'jj' : ''">
                    <img :src="it.imgsrc" alt="">
                  </div>
                  <div class="lot-txt">
                    <p class="name">{{ it.name }}</p>
                    <p v-if="it.des" :class="it.desMark ? 'desMark' : ''">{{ it.des }}</p>
                    <p v-else>奖池15亿8964万</p>
                  </div>
                </div>
              </div>
            </div>

          </template>
        </div>
      </div>

      <div class="app-index">
        <div class="lot-group">
          <template v-for="item in list2" v-if="!item.isSeries">
            <div class="item" v-if="item.isShow" :style="'order:' + item.qz">
              <div class="main">
                <div class="lot-img" :class="item.jj ? 'jj' : ''">
                  <img :src="item.imgsrc" alt="">
                </div>
                <div class="lot-txt">
                  <p class="name">{{ item.name }}</p>
                  <p v-if="item.des" :class="item.desMark ? 'desMark' : ''">{{ item.des }}</p>
                  <p v-else>奖池15亿8964万</p>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </body>
</html>
<script src="/caipiaoimg/v1.0/js/vue.min.js"></script>
<script src="/caipiaoimg/v1.0/js/axios.min.js"></script>
<script>
  var PV = new Vue({
    el: '#app',
    data () {
      return {
        plat: '<?php echo $platform; ?>',
        listSource: [],
        list: [],
        list2: [],
        rank: []
      }
    },
    watch: {
      listSource (val) {
        val.forEach((item) => {
          item.qz *= -1;
        })
        this.list = JSON.parse(JSON.stringify(val));
        this.list2 = JSON.parse(JSON.stringify(val));
      },
      list (val) {
        // 取出系列的子元素 {'42': [], '52': []}
        let obj = {};
        val.forEach((item, idx) => {
          if (item.parentLid) {
            if (!obj[item.parentLid]) {
              obj[item.parentLid] = [];
            }
            obj[item.parentLid].push(item);
          }
        })
        // 把系列的子元素 插入到对应的系列下
        for(let k in obj) {
          val.forEach((it) => {
            if (it.lid === k) {
              it.child = obj[k]
            }
          })
        }
        this.rank = val.map(it => {
          return it.qz
        }).sort((a, b) => a - b);
      },
      rank (val) {
        this.list.forEach((it, idx, arr) => {
          if (val.indexOf(it.qz) % 2) {
            it.even = true;
          }
        })
      }
    },
    created: function () {
      var data = window.localStorage.getItem(this.plat);
      data ? this.listSource = JSON.parse(window.localStorage.getItem(this.plat)) : void ''
    },
    methods: {
      more (item) {
        console.log(item)
        if (!item.isSeries) return false;
        item.isOpen = !item.isOpen;
      }
    }
  })
</script>