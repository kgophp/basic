<template>
  <el-aside :width="isCollapse ? '65px' : '210px'" style="background-color: #304156">
    <el-scrollbar style="height:100%">
    <el-menu
            class="el-menu-vertical"
            mode="vertical"
            :collapse="isCollapse"
            :router="true"
            :default-active="$route.path"
            :collapse-transition="false"
            background-color="#304156"
            text-color="#bfcbd9"
            active-text-color="#409EFF">
      <div class="logo title-color">
        <div v-if="!isCollapse" class="normal ">
          {{ fullName }}
        </div>
        <div v-else class="mini">
          {{ abbrName }}
        </div>
      </div>
      <nav-item v-for="item in this.menuItems" :item="item" :key="item.id"></nav-item>
    </el-menu>
    </el-scrollbar>
  </el-aside>
</template>

<script>
  import NavItem from './NavItem'
  import { myMenu } from '../../api/menu'
  import config from '../../config'

  export default {
    name: 'NavBar',
    components: {
      NavItem
    },
    props: {
      isCollapse: Boolean
    },
    data () {
      return {
        menuItems: []
      }
    },
    created () {
      myMenu().then(response => {
        this.menuItems = response.data.data
      })
    },
    methods: {
    },
    computed: {
      fullName: function () {
         return config[this.$provider].hasOwnProperty('appName') ? config[this.$provider].appName.fullName : 'Mojito Admin'
      },
      abbrName: function () {
        return config[this.$provider].hasOwnProperty('appName') ? config[this.$provider].appName.abbrName : 'Mojito'
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .el-menu-vertical {
    height:100%;
    border:none;
  }
    .logo {
    height: 60px;
    line-height: 60px;
    font-weight: 600;
    background-color: #001528;
    text-align: center;
    .normal {
      padding-left: 20px;
    }
    .mini {
      text-align: center;
    }

  }
</style>
