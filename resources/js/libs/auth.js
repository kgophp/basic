import localforage from 'localforage'
import store from  '../store'

const TOKEN = 'token:'

const PERMISSION = 'permissions:'

export const setToken = (token, provider) => {
    if (token.remeber_me)
      return localforage.setItem(getTokenKey(provider), token)
    else
      return new Promise((resolve, reject) => {resolve()})
}

export const getToken = (provider) => {
    return new Promise((resolve, reject) => {
        localforage.getItem(getTokenKey(provider)).then(token => {
            if (token.remeber_me)
                resolve(token)
            else {
                if (store.getters.token)
                    resolve(store.getters.token)
                else
                    reject('Unauthenticated')
            }
        }).catch(error => {
            if (store.getters.token)
                resolve(store.getters.token)
            else
                reject('Unauthenticated')
        })
    })
  //return localforage.getItem(getTokenKey(provider))
}

export const removeToken = (provider) => {
  return localforage.removeItem(getTokenKey(provider))
}

export const getTokenKey = (provider) => {
  return TOKEN + provider
}

export const setPermissions = (permissions, provider) => {
  return localforage.setItem(getPermissionKey(provider), permissions)
}

export const getPermissions = (provider) => {
  return localforage.getItem(getPermissionKey(provider))
}

export const getPermissionKey =  provider => {
  return PERMISSION + provider
}
