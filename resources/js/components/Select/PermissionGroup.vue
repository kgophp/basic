<template>
  <!--<el-select v-model="optionValue" :placeholder="$t('permissionGroup')">-->
    <!--<el-option v-for="item in items" :label="item.name" :value="item.id" :key="item.id"></el-option>-->
  <!--</el-select>-->
  <el-cascader
          v-model="optionValue"
          :options="items"
          :props="defaultItems"
          clearable>

  </el-cascader>
</template>
<script>
  import { getPermissionGroupList } from '../../api/permissionGroup'
  export default {
    name: 'PermissionGroupSelect',
    props: ['nowValue'],
    data () {
      return {
        optionValue: this.nowValue,
        items: [
        ],
        defaultItems:{
            label:'label',
            value:'id',
            checkStrictly: true
        },
      }
    },
    created() {
      getPermissionGroupList().then( response  => {
        this.items = response.data.data
      })
    },
    watch: {
      optionValue(newValue, oldValue) {
        this.$emit("update:nowValue", newValue);
      },
      nowValue(newValue) {
        this.optionValue = newValue
      },
      $route: {
          handler: function(val, oldVal){
              if(val.name == 'permissionIndex'){
                  getPermissionGroupList().then( response  => {
                      this.items = response.data.data
                  })
              }
          },
          deep: true
      }
    }
  }
</script>