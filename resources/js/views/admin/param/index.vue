<template>
  <div>
  <base-index
          ref="baseIndex"
          :data-name="dataName"
          :add-form="addForm"
          :edit-form="editForm"
          :data-rule="rules"
          @batchOperateEvent="batchOperateEvent"
          @rowOperateEvent="rowOperateEvent"
  >
    <template slot-scope="slotProps" slot="query-form-items">
      <el-form-item :label="$t('paramName')">
        <el-input v-model="slotProps.queryParams.param_name"></el-input>
      </el-form-item>
    </template>

    <template slot="table-columns">
      <el-table-column
              prop="param_name"
              :label="$t('paramName')">
      </el-table-column>
      <el-table-column
              prop="param_value"
              :label="$t('paramValue')">
      </el-table-column>
    </template>

    <template  slot-scope="slotProps" slot="add-form-rows">
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('paramName')" prop="param_name" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.param_name"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('paramValue')" prop="param_value" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.param_value"
                      type="textarea"
                      :rows="5"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </template>

    <template slot-scope="slotProps" slot="edit-form-rows">
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('paramName')" prop="param_name" :label-width="formLabelWidth">
            <el-input v-model="slotProps.editForm.param_name" disabled></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('paramValue')" prop="param_value" :label-width="formLabelWidth">
            <el-input v-model="slotProps.editForm.param_value"
                      type="textarea"
                      :rows="5"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
    </template>
  </base-index>
  </div>
</template>

<script>
  import { batchOperateData } from '../../../api/common'
  import { batchOperateSuccess } from '../../../libs/tableDataHandle'

  export default {
    name: 'paramIndex',
    components: {

    },
    data() {
      return {
        dataName:'param',
        formLabelWidth:'150px',
        rules: {
          param_name: [
            { required: true,message:this.$t('required') },
            { min: 4, max: 255  ,message:this.$t('errorMessage.charLengthRange', { min:4,max:255 })}
          ],
            param_value: [
            { required: true,message:this.$t('required') },
            { min: 1, max: 255  ,message:this.$t('errorMessage.charLengthRange', { min:1,max:255 })}
          ]
        },
       addForm:{
           param_name:'',
           param_value:''
       },
       editForm:{
              param_name:'',
              param_value:''
       },
      }
    },
    methods: {
        batchOperateEvent(command,sels){
            this.$confirm(this.$t(command)+'?').then(()=>{
                batchOperateData(this.dataName,command,sels).then( response => {
                    batchOperateSuccess(this,'完成')
                    this.$refs.baseIndex.requestData()
                })
            })

        },
        rowOperateEvent(operateName,index,data){
            alert(operateName);
        },
        getRowOperateButtonVisible(operateName,index,row){
                return true
        }
    },
    computed: {
    },
    created() {

    }
  }
</script>
