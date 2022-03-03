<template>
  <el-select :value="icon" filterable clearable placeholder="请选择" @change="handleChange">
    <el-option
            v-for="item in icons"
            :key="item"
            :label="item"
            :value="item">
      <span style="float: left"><i class="fa" :class="`${item}`"></i></span>
      <span style="float: right; color: #8492a6; font-size: 13px">{{ item }}</span>
    </el-option>
  </el-select>
</template>
<script>
    import icons from '../../libs/icon'
    export default {
        name: 'IconSelect',
        props: {
            icon: {
                type: String,
                default: ""
            }
        },
        data () {
            return {
                icons,
            }
        },
        methods: {
            handleChange(val){
                this.$emit('update:icon','fa fa-lg '+val)
            }
        },
    }
</script>
