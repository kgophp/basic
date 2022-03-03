<template>
  <el-select v-model="optionValue"  :placeholder="placeholder">
    <el-option v-for="item in items" :label="item.label" :value="item.value" :key="item.id"></el-option>
  </el-select>
</template>
<script>
import {getTypeList} from '../../api/dict'

  export default {
    name: 'DictSelect',
    props: ['nowValue','dictType','placeholderString'],
    data () {
      return {
        optionValue: this.nowValue,
        placeholder:this.placeholderString,
        items: []
      }
    },
    methods:{
        loadData() {
            getTypeList({dict_type: this.dictType,}).then(response => {
                this.items = response.data.data
            })
        }
    },
    created() {
        this.loadData()
    },
    watch: {
      optionValue(newValue, oldValue) {
        this.$emit("update:nowValue", newValue);
      },
      nowValue (newValue) {
        this.optionValue = newValue
      },
      selectTenantId (newValue) {
          this.optionValue =''
          getTypeList({dict_type:this.dictType}).then( response  => {
              this.items = response.data.data
          })
      },
      placeholderString (newValue) {
        this.optionValue = newValue
        if(newValue !== ''){
          this.placeholder = newValue
        }else{
          this.placeholder = $t('dictType')
        }
      },
    }
  }
</script>
