import axios  from 'axios'
import config from '../config'
import { Message } from 'element-ui'
import i18n from '../lang'

const httpRequest = axios.create({
  timeout: 5000,
  baseURL: config.apiUrl
})

httpRequest.interceptors.request.use(
  config => {
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

export function setHttpToken(token) {
  httpRequest.defaults.headers.common.Authorization = `Bearer ${token}`
}

httpRequest.interceptors.response.use(
  response => {
    return response
  },
  error => {
    let message =''
    let dangerouslyUseHTMLString = false
    if (error.response) {
        message = error.response.data.message ? error.response.data.message : error.response.statusText

        if (error.response.status === 422 && error.response.data.hasOwnProperty('errors')) {
            message += '<br>';
            for (let key in error.response.data.errors) {
                let items = error.response.data.errors[key]
                if (typeof items === 'string') {
                    message += `${items} <br>`
                } else {
                    error.response.data.errors[key].forEach(item => {
                        message += `${item} <br>`
                    })
                }
            }
            dangerouslyUseHTMLString = true
        }

        let errorType = error.response.data.error ? error.response.data.error : 'unknown_error'
        if (errorType === 'invalid_credentials')
            message = i18n.t('invalid_credentials')
    }else{
        message=error.toString();
        if (message.indexOf('Network'))
            message = i18n.t('network_error')
    }
    Message({
      dangerouslyUseHTMLString,
      message: message,
      type: 'error'
    })

    return Promise.reject(error)
  }
)

export default httpRequest
