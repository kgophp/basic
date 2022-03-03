<template>
  <div id="app">
    <transition name="fade">
      <router-view/>
    </transition>
  </div>
</template>

<script>
  import { mapActions } from 'vuex'
  import { routeFormatTag } from './libs/util'
  import { getToken } from './libs/auth'
  import { setHttpToken } from './libs/http'

  export default {
    name: 'App',
    methods: {
      ...mapActions([
        'openTagView'
      ])
    },
    watch: {
      $route(route) {
        this.$store.commit('SET_BREADCRUMB', route.matched)

        if (route.name !== 'adminLogin') {
            let tmpinfo=routeFormatTag(route)
            if (route.params && route.params.title)
                tmpinfo.title=route.params.title
            this.openTagView(tmpinfo)
        }
      }
    },
    mounted () {
      this.$store.commit('SET_BREADCRUMB', this.$route.matched)
    }
  }
</script>

<style>
  html,body{width:100%;height:100%;}
  body {
    margin:0;
  }
  #app {
    height:100%;
  }
</style>
