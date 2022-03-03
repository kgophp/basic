import Vue from 'vue'
import Router from 'vue-router'
import store from  '../store'
import routes from './routers'
import config from '../config'
import { getToken, getPermissions } from '../libs/auth'
import { Message } from 'element-ui'
import i18n from '../lang'

Vue.use(Router)

const router = new Router({
  saveScrollPosition: true,
  routes
})


router.beforeEach((to, from, next) => {
  const provider = to.meta.provider
  if (provider) {
    const providerConfig = config[Vue.prototype.$provider]
    if (providerConfig.loginRouteName === to.name) {
      next()
    }

    if (Vue.prototype.$provider !== provider) {
      Message({
        message: i18n.$t('NotAllowAccessProject'),
        type: 'error'
      })
      next({name: providerConfig.dashboardName})
    } else {
      let login = new Promise((resolve, reject) => {
        getToken(provider).then( token => {
          if (!token || !token.hasOwnProperty('access_token') || ((new Date().getTime() - token.created_at) / 1000) >= token.expires_in) {
            reject({ name : providerConfig.loginRouteName})
          } else {
            if (!store.getters.token) {
              store.commit('SET_TOKEN', {token, provider})
            }
            resolve()
          }
        }).catch(error => {
          reject(error)
        })
      })

      let permission = new Promise((resolve, reject) => {
        if (!to.meta.permission) {
          resolve()
        } else {
          getPermissions(provider).then( permissions => {
            if (permissions.indexOf(to.meta.permission) < 0) {
              reject(i18n.t('NotHavePermission')+i18n.t(to.meta.permission))
            }
            resolve()
          }).catch(error => {
            reject(error)
          })
        }
      })

      Promise.all([login, permission]).then( result => {
        next()
      }).catch( error => {
        let varType = typeof error;
        if (varType === 'object') {
          next({name: error.name})
        } else {
          if (error==='Unauthenticated'){
            next({name: 'adminLogin'})
          }else {
              Message({
                  message: error,
                  type: 'error'
              })
              next({name: from.name})
          }
        }
      })
    }
  } else {
    next()
  }
})

export default router
