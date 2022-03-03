import baseIndex from './BaseIndex'
import dictSelect from  './Select/Dict'

function plugin(Vue){
    if (plugin.installed){
        return
    }
    Vue.component('BaseIndex',baseIndex)
    Vue.component('DictSelect',dictSelect)
}
export default  plugin
