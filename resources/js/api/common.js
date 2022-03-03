import http from '../libs/http'

const basicRoute = '/api/'

function getDataRoute(dataName){
  return basicRoute+dataName;
}
export const getDataList = (dataName,params) => {
  return http.get(getDataRoute(dataName), {
    params
  })
}

export const addData = (dataName,data) => {
  return http.post(getDataRoute(dataName), data)
}

export const editData= (dataName,id, data) => {
  let dataRoute=getDataRoute(dataName);
  return http.patch(`${dataRoute}/${id}`, data)
}

export const deleteData = (dataName,id) => {
  let dataRoute=getDataRoute(dataName);
  return http.delete(`${dataRoute}/${id}`)
}

export const exportData = (dataName,params) => {
    let dataRoute=getDataRoute(dataName);
    return http.get(`${dataRoute}/export`, {
        params
    })
}


export const batchOperateData = (dataName,operationType,data) => {
    let dataRoute=getDataRoute(dataName)+'/batch/'+operationType.replace('batch','')
    let param={'data':data}
    return http.post(dataRoute, param)
}
