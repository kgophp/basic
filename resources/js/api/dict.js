import qs from 'qs'
import http from '../libs/http'

const basicRoute = '/api/dictionary'


export const getTypeList = (params) => {
  return http.get(basicRoute+'/getTypeList', {
    params
  })
}
