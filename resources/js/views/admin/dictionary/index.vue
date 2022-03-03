<template>
  <div>
  <base-index
          ref="baseIndex"
          :data-name="dataName"
          :data-rule="rules"
          :is-tree=true
          :add-form="addForm"
          :edit-form="editForm"
          @beforeOpenForm="beforeOpenForm"
  >
    <template slot-scope="slotProps" slot="query-form-items">
    </template>

    <template slot="table-columns">
      <el-table-column
              prop="dict_key"
              :label="$t('dictKey')">
      </el-table-column>
      <el-table-column
              prop="dict_value"
              :label="$t('dictValue')">
      </el-table-column>
    </template>

    <template  slot-scope="slotProps" slot="add-form-rows">
      <el-row :span="22" >
        <el-form-item :label="$t('dictType')" prop="dict_type" :label-width="formLabelWidth">
          <dict-select
                       ref="dSelect"
                       :now-value.sync="slotProps.addForm.dict_type"
                       dict-type="Root"
                       :selectTenantId="slotProps.addForm.tenant_id">
          </dict-select>
        </el-form-item>
      </el-row>
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('dictKey')" prop="dict_key" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.dict_key"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('dictValue')" prop="dict_value" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.dict_value"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <!--el-row>
        <el-col :span="22">
          <el-form-item :label="$t('sort')" prop="sort" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.sort"></el-input>
          </el-form-item>
        </el-col>
      </el-row-->
    </template>

    <template slot-scope="slotProps" slot="edit-form-rows">
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('dictKey')" prop="dict_key" :label-width="formLabelWidth">
            <el-input v-model="slotProps.editForm.dict_key" disabled></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="22">
          <el-form-item :label="$t('dictValue')" prop="dict_value" :label-width="formLabelWidth">
            <el-input v-model="slotProps.editForm.dict_value"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <!--el-row>
        <el-col :span="22">
          <el-form-item :label="$t('sort')" prop="sort" :label-width="formLabelWidth">
            <el-input v-model="slotProps.addForm.sort"></el-input>
          </el-form-item>
        </el-col>
      </el-row-->
    </template>
  </base-index>
  </div>
</template>

<script>

  export default {
    name: 'dictionaryIndex',
    components: {
    },
    data() {
      return {
        dataName:'dictionary',
        formLabelWidth:'150px',
        //tenants:[],
        rules: {
          dict_type: [
                { required: true,message:this.$t('required') }
          ],
          dict_key: [
            { required: true,message:this.$t('required') },
            { min: 1, max: 255  ,message:this.$t('errorMessage.charLengthRange', { min:1,max:255 })}
          ],
          dict_value: [
            { required: true,message:this.$t('required') },
            { min: 1, max: 255  ,message:this.$t('errorMessage.charLengthRange', { min:1,max:255 })}
          ]
        },
        addForm: {
            dict_type: '',
            dict_key: '',
            dict_value: '',
        },
        editForm: {
            dict_type: '',
            dict_key: '',
            dict_value: '',
        },
      }
    },
    methods: {
        beforeOpenForm(formType,data){
            if (this.$refs['dSelect'])
                this.$refs.dSelect.loadData()
        }
    },
    computed: {

    },
    created() {
    }
  }
</script>
