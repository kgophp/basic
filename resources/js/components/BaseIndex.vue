<template>
  <div>
    <el-form :inline="true" :model="queryParams"  size="mini">
      <slot name="query-form-items" :queryParams="queryParams"></slot>
      <el-form-item>
        <el-button type="primary" @click="requestData" icon="el-icon-search">{{ $t('search') }}</el-button>
        <el-button type="primary" v-if="addData"  @click="handleAdd()" icon="el-icon-plus">{{ $t('add') }}</el-button>
        <el-button type="success" v-if="exportData"  @click="handleExport()" icon="el-icon-download">{{ $t('export') }}</el-button>
        <a ref="download" class='download' href='' download=""  title="download" v-show="false">download</a>
        <el-dropdown @command="handleCommand" v-if="showBatchButton">
          <el-button type="warning">
            {{ $t('batchButtons') }}<i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item v-for="(op,index) in batchOperations" :key="index" :command="op.name" :icon="op.icon" v-if="checkButtonPermission(op)">{{ $t(op.name) }}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
        <slot name="query-form-buttons"></slot>
      </el-form-item>
    </el-form>

    <el-table
            ref="table"
            :data="tableData"
            @selection-change="selsChange"
            v-loading="loading"
            default-expand-all
            border
            stripe
            row-key="id"
            style="width: 100%">
      <el-table-column
              type="selection"
              width="35" v-if="showBatchButton || exportData">
      </el-table-column>
      <el-table-column fixed :label="$t('table_index')"
                       width="50" align="center" v-if="!isTree">
        <template slot-scope="scope">
          <span>{{scope.$index+(pagination.currentPage - 1) * pagination.pageSize + 1}} </span>
        </template>
      </el-table-column>

      <slot name="table-columns"></slot>
      <el-table-column
              fixed="right"
              min-width="150"
              :label="$t('actions')"
              >
        <template slot-scope="scope">
          <el-button
                  v-if="updateData&setRowOperateButtonVisible('update',scope.$index, scope.row)"
                  size="mini"
                  @click="handleEdit(scope.$index, scope.row)">{{ $t('edit') }}</el-button>
          <el-button
                  v-if="deleteData&&setRowOperateButtonVisible('delete',scope.$index, scope.row)"
                  size="mini"
                  type="danger"
                  @click="handleDelete(scope.$index, scope.row)">{{ $t('delete') }}</el-button>
          <el-button size="mini" v-for="(op,index) in rowOperations" :key="index"
                     @click="handleRowOperate(op.name,scope.$index, scope.row)"
                     :icon="op.icon" :type="op.buttonType"
                     v-if="checkButtonPermission(op)&setRowOperateButtonVisible(op.name,scope.$index, scope.row)">
            {{ $t(op.name) }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination class="mo-page"
                   @current-change="requestData"
                   @size-change="handleSizeChange"
                   :current-page.sync="pagination.currentPage"
                   :page-size="pagination.pageSize"
                   :page-sizes="pageSizes"
                   :layout="tableLayout"
                   :total="pagination.total" v-if="!isTree">
    </el-pagination>


    <el-dialog v-el-drag-dialog :title="$t('add')" :visible.sync="dialogAddFormVisible" :width="dialogWidth" @close="dialogClosed('addForm')">
      <el-form :model="addForm" :rules="dataRule" ref="addForm">
        <slot name="add-form-rows" :addForm="addForm"></slot>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogAddFormVisible = false">{{ $t('cancel') }}</el-button>
        <el-button type="primary" @click="handleAddData">{{ $t('confirm') }}</el-button>
      </div>
    </el-dialog>

    <el-dialog v-el-drag-dialog :title="$t('edit')" :visible.sync="dialogEditFormVisible" :width="dialogWidth" @close="dialogClosed('editForm')">
      <el-form :model="editForm" :rules="dataRule" ref="editForm">
        <slot name="edit-form-rows" :editForm="editForm"></slot>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogEditFormVisible = false">{{ $t('cancel') }}</el-button>
        <el-button type="primary" @click="handleEditData">{{ $t('confirm') }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { getDataList, addData, editData, deleteData,exportData } from '../api/common'
  import { hasPermission } from "../libs/permission"
  import { responseDataFormat, tableDefaultData, editSuccess, addSuccess, deleteSuccess } from '../libs/tableDataHandle'
  import elDragDialog from './el-drag-dialog'

  export default {
    name: 'BaseIndex',
    directives: { elDragDialog },
    components: {
        hasPermission
    },
    props: {
        dataName:{
            type:String,
            required:true
        },
        parentDataName:{
            type:String,
            default:'',
        },
        parentDataId:{
            type:String,
            default:'',
        },
        dataRule:{
            type:Object,
            required:true
        },
        isTree:{
            type:Boolean,
            required:false
        },
        tableLayout: {
            type: String,
            default: 'total, sizes, prev, pager, next, jumper'
        },
        batchOperations:{
            type:Array,
            default:() =>[],
        },
        rowOperations:{
            type:Array,
            default:() =>[],
        },
        dialogWidth:{
            type:String,
            default:'40%'
        },
        hasTenant:{
            type:Boolean,
            default:false,
            required:false
        },
        addForm:{
            type:Object,
            default:()=>{},
        },
        editForm:{
            type:Object,
            default:()=>{},
        },
        loadDataAfterSave:{
            type:Boolean,
            default:false
        },
    },
    data() {
      return {
        ...tableDefaultData(),
        pageSizes:[15,30,50,100],
        sels: [],
      }
    },
    methods: {
      selsChange(sels){
         this.sels=sels
      },
      convertSelsToArray(){
        let ids = [];
        this.sels.forEach( i => {
          ids.push(i.id)
        })
        return ids
      },
      handleAdd(){
          //this.$emit('iniAddForm')
          if (this.hasTenant && (typeof this.addForm.tenant_id=="undefined")){
              this.$set(this.addForm,'tenant_id',null)
          }
          this.$emit('beforeOpenForm','addForm')
          this.dialogAddFormVisible = true
      },
      handleEdit(index, row) {
        this.nowRowData = { index, row }
        let editFields=Object.keys(this.editForm)
        if (editFields.length==0)
          this.editForm=Object.assign({},row)
        else{
          for (let i=0;i<editFields.length;i++){
              if (row[editFields[i]]!== undefined)
                this.$set(this.editForm,editFields[i],row[editFields[i]])
              else
                this.$set(this.editForm,editFields[i],null)
          }
        }

        this.$emit('beforeOpenForm','editForm',this.editForm)
        this.dialogEditFormVisible = true
      },
      handleDelete(index, row) {
        this.$confirm(this.$t('querydelete')).then(()=>{
          deleteData(this.apiUrl,row.id).then( response => {
            deleteSuccess(index, this)
            this.requestData()
          })
        })
      },
      handleExport(){
          this.$confirm(this.$t('queryexport')).then(()=>{

              exportData(this.apiUrl,{...this.queryParams,ids:this.convertSelsToArray()}).then( response => {
                  this.$refs.download.setAttribute('href',response.data.data.downloadUrl)
                  this.$refs.download.click()
              })
          })
      },
      handleCommand(command){
          if (this.sels.length==0){
              return;
          }
          this.$emit('batchOperateEvent',command,this.sels);
      },
      requestData() {
        this.loading = true
          if (this.isTree)
              getDataList(this.apiUrl,{...this.queryParams}).then( response => {
                  this.tableData=response.data.data
                  this.loading = false
              })
          else
              getDataList(this.apiUrl,{...this.queryParams, page:this.pagination.currentPage,pagesize:this.pagination.pageSize}).then( response => {
              responseDataFormat(response, this)
            })
      },
      loadData(){
          this.requestData()
      },
      handleSizeChange(val){
          this.pagination.pageSize=val
          this.requestData()
      },
      handleAddData() {
        this.$refs['addForm'].validate((valid) => {
          if (valid) {
            addData(this.apiUrl,this.addForm).then( response => {
              addSuccess(this)
              this.requestData()
            })
          } else {
            return false;
          }
        });
      },
      handleEditData() {
        this.$refs['editForm'].validate((valid) => {
          if (valid) {
            editData(this.apiUrl,this.nowRowData.row.id, this.editForm).then( response => {
              editSuccess(this)
              if (this.loadDataAfterSave)
                this.requestData()
            })
          } else {
            return false;
          }
        });
      },
      dialogClosed(formType){
         if (formType === 'addForm' && this.$refs['addForm'].resetFields)
             this.$refs['addForm'].resetFields();
         /*
         if (formType === 'editForm' && this.$refs['editForm'].resetFields)
             this.$refs['editForm'].resetFields();
         */
      },
      handleRowOperate(operateName,index,row){
          this.$emit('rowOperateEvent',operateName,index,row);
      },
      checkButtonPermission(operationInfo){
          return (operationInfo.checkPermission && hasPermission(this.dataName+'.'+operationInfo.name))
              || (!operationInfo.checkPermission)
      },
      setRowOperateButtonVisible(operateName,index,row){
          if (this.$parent.getRowOperateButtonVisible)
              return this.$parent.getRowOperateButtonVisible(operateName,index,row)
          else
              return true
      },
    },
    computed: {
      apiUrl:function(){

          let tmp=''

          if (this.parentDataName.length>0)
              tmp=this.parentDataName
          if (this.parentDataId>0)
              tmp=tmp+'/'+this.parentDataId
          if (tmp.length==0)
              return this.dataName
          else
              return  tmp+'/'+this.dataName
      },
      updateData: function() {
        return hasPermission(this.dataName+'.update')
      },
      addData: function() {
        return hasPermission(this.dataName+'.store')
      },
      deleteData: function() {
        return hasPermission(this.dataName+'.destroy')
      },
      exportData:function(){
          return hasPermission(this.dataName+'.export')
      },
      showBatchButton:function(){
          for(let i = 0; i <this.batchOperations.length; i++){
              if (this.checkButtonPermission(this.batchOperations[i]))
                  return true;
          }
          return false
      }
    },
    created() {
      this.$emit('beforeCreate',this.queryParams)
      this.requestData()
    },
  }
</script>
<style>
  .el-button+.el-button{
    margin-left:0;
  }
  .cell .el-button{
    margin-top:3px;
  }
</style>
