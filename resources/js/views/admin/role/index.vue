<template>
  <div class="role-permission-container">
    <el-row :gutter="10">
      <el-col :span="18">
        <el-card>
          <div class="role-index">
            <el-form :inline="true" :model="queryParams"  size="mini">
              <el-form-item :label="$t('name')">
                <el-input clearable v-model="queryParams.name"></el-input>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="requestData" icon="el-icon-search">{{ $t('search') }}</el-button>
                <el-button type="primary" v-if="addPermission"  @click="dialogAddFormVisible = true" icon="el-icon-plus">{{ $t('add') }}</el-button>
              </el-form-item>
            </el-form>
            <el-table
                    :data="tableData"
                    v-loading="loading"
                    @row-click="handleRowClick"
                    @current-change="handleCurrentChange"
                    :row-class-name="tableRowClassName"
                    :cell-style="cellStyle"
                    :highlight-current-row="true"
                    :height="tableHeight"
                    border
                    ref="table"
                    style="width: 100%">
              <el-table-column
                      prop="name"
                      :label="$t('name')">
              </el-table-column>
              <el-table-column
                      prop="guard_name"
                      :label="$t('guardName')">
              </el-table-column>
              <el-table-column
                      prop="description"
                      :label="$t('description')">
                      <template slot-scope="scope">
                        <el-tooltip :content="scope.row.description" placement="top">
                          <div class="role-desc">{{scope.row.description}}</div>
                        </el-tooltip>
                      </template>
              </el-table-column>
              <el-table-column
                      prop="created_at"
                      width="165"
                      :label="$t('createdAt')">
              </el-table-column>
              <el-table-column
                      prop="updated_at"
                      width="165"
                      :label="$t('updatedAt')">
              </el-table-column>
              <el-table-column
                      width="140"
                      :label="$t('actions')"
                      >
                <template slot-scope="scope">
                  <el-button
                          v-if="updatePermission"
                          size="mini"
                          @click="handleEdit(scope.$index, scope.row)">{{ $t('edit') }}</el-button>
                  <!-- <router-link :to="{ name: 'rolePermission', params: {id: scope.row.id, guardName: scope.row.guard_name}}">
                    <el-button
                            v-if="assignPermission"
                            size="mini">{{ $t('assignPermission') }}</el-button>
                  </router-link> -->
                  <el-button
                          v-if="deletePermission"
                          size="mini"
                          type="danger"
                          @click="handleDelete(scope.$index, scope.row)">{{ $t('delete') }}</el-button>
                </template>
              </el-table-column>
            </el-table>
            <el-pagination class="mo-page"
                          @current-change="requestData"
                          @size-change="handleSizeChange"
                          :current-page.sync="pagination.currentPage"
                          :page-size="pagination.pageSize"
                          :page-sizes="[15,30,50,100]"
                          layout="total, sizes, prev, pager, next, jumper"
                          :total="pagination.total">
            </el-pagination>

            <el-dialog :title="$t('add')" :close-on-click-modal="false" :visible.sync="dialogAddFormVisible" width="30%">
              <el-form :model="addForm" :rules="rules" ref="addForm">
                <el-form-item :label="$t('name')" prop="name" :label-width="formLabelWidth">
                  <el-input clearable v-model="addForm.name"></el-input>
                </el-form-item>
                <el-form-item :label="$t('guardName')" prop="guard_name" :label-width="formLabelWidth">
                  <guard-select :nowValue.sync="addForm.guard_name"></guard-select>
                </el-form-item>
                <el-form-item :label="$t('description')" prop="description" :label-width="formLabelWidth">
                  <el-input type="textarea" clearable v-model="addForm.description"></el-input>
                </el-form-item>
              </el-form>
              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogAddFormVisible = false">{{ $t('cancel') }}</el-button>
                <el-button type="primary" @click="handleAddRole">{{ $t('confirm') }}</el-button>
              </div>
            </el-dialog>

            <el-dialog :title="$t('edit')" :close-on-click-modal="false" :visible.sync="dialogEditFormVisible" width="30%">
              <el-form :model="editForm" :rules="rules" ref="editForm">
                <el-form-item :label="$t('name')" prop="name" :label-width="formLabelWidth">
                  <el-input clearable v-model="editForm.name"></el-input>
                </el-form-item>
                <el-form-item :label="$t('guardName')" prop="guard_name" :label-width="formLabelWidth">
                  <guard-select :nowValue.sync="editForm.guard_name"></guard-select>
                </el-form-item>
                <el-form-item :label="$t('description')" prop="description" :label-width="formLabelWidth">
                  <el-input type="textarea" clearable v-model="editForm.description"></el-input>
                </el-form-item>
              </el-form>
              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogEditFormVisible = false">{{ $t('cancel') }}</el-button>
                <el-button type="primary" @click="handleEditRole">{{ $t('confirm') }}</el-button>
              </div>
            </el-dialog>
          </div>
        </el-card>
      </el-col>

      <el-col :span="6" v-loading="showCheckedKeysLoading">
        <el-card style="position: relative;" v-loading="saveLoading">
            <div v-if="!isHaveSeePermission" class="my-card-mask">暂无查看权限</div>
            <div class="role-permissions" :style="{height: tableHeight + 100 + 'px'}">
              <div id="actionButtonBox" class="role-permission-action" :style="{width: elTreeBoxOffsetWidth - 2 + 'px'}">
                <el-button @click="allExpand(true)" class="action-btn" type="primary" size="mini">展开</el-button>
                <el-button @click="allExpand(false)" class="action-btn" type="primary" size="mini">折叠</el-button>
                <el-button v-if="assignPermission" @click="checkAll" class="action-btn" type="primary" size="mini">全选</el-button>
                <el-button v-if="assignPermission" @click="cancelAllChecked" class="action-btn" type="primary" size="mini">反选</el-button>
                <el-button v-if="assignPermission" :loading="saveLoading" class="action-btn save-btn" @click="saveEdit" type="warning" size="mini">保存修改</el-button>
              </div>

              <div id="elTreeBox" class="el-tree-box">
                <el-tree
                  ref="tree"
                  :data="allPermissionList"
                  show-checkbox
                  node-key="name"
                  :default-expand-all="true"
                  :default-checked-keys="defaultCheckedKeys"
                  @check="handleCheck"
                  :props="defaultProps"
                  @node-click="handleNodeClick"
                >
                  <span class="custom-tree-node"  slot-scope="{ node, data }">
                    <span>
                      <i class="el-icon-folder-opened" style="color:#409EFF" v-if="(!data.pg_id)&&(node.expanded)"></i>
                      <i class="el-icon-folder"  style="color:#409EFF" v-if="(!data.pg_id)&&(!node.expanded)"></i>
                      {{ node.label }}
                    </span>
                  </span>
                </el-tree>
              </div>
            </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
  import { getRoleList, addRole, editRole, deleteRole, rolePermission, roleAssignPermission } from '../../../api/role'
  import { responseDataFormat, tableDefaultData, editSuccess, addSuccess, deleteSuccess } from '../../../libs/tableDataHandle'
  import { hasPermission } from '../../../libs/permission'
  import GuardSelect from '../../../components/Select/Guard'
  import { guardNameForPermissions } from '../../../api/permissionGroup'
  import notify from '../../../libs/notify'

  export default {
    name: 'roleIndex',
    components: {
      GuardSelect,
    },
    data() {
      return {
        setAllPermissionCheckedList: [],
        hasSetDefaultSelected: false,
        selectedRowId: 1,
        elTreeOffsetTop: 40,
        elTreeBoxOffsetWidth: 100,
        checkAllKeys: [],
        tableHeight: 500,
        windowHeight: 500,
        guardName: '',
        defaultRoleId: 0,
        allPermissionList: [],
        rolePermissionList: [],
        defaultCheckedKeys: [],
        defaultProps: {
          children: 'children',
          label: 'display_name',
          disabled: this.disabledFn
        },
        filterText: '',
        checkedKeys: [],
        saveLoading: false,
        loading: false,
        showCheckedKeysLoading: false,
        ...tableDefaultData(),
        addForm: {
          name: '',
          guard_name: '',
          description: ''
        },
        editForm: {
          name: '',
          guard_name: '',
          description: ''
        },
        rules: {
          name: [
            { required: true ,message:this.$t('required')},
            { min: 3, max: 100  ,message:this.$t('errorMessage.charLengthRange', { min:3,max:100 })}
          ],
          guard_name: [
            { required: true ,message:this.$t('requiredSelect')},
            { min: 1, max: 255  ,message:this.$t('errorMessage.charLengthRange', { min:1,max:255 })}
          ]
        },
      }
    },
    watch: {
      $route(route) {
        if (route.name === 'roleIndex') {
          this.getrolePermissionList('')
        }
      }
    },
    methods: {
      handleNodeClick(data, node, self){
        self.expanded = !self.expanded
      },
      disabledFn(data, node){
        if(!this.assignPermission){
          return this.assignPermission
        } else {
          return data.disabled
        }
      },
      tableRowClassName({row, rowIndex}){
        row.index = rowIndex;
      },
      cellStyle({row, column, rowIndex, columnIndex}){
        if(this.selectedRowId == row.id){
          return {"background-color": '#4ebeea', 'color': '#fff'}
        }
      },
      handleCurrentChange(val){
        if(val){
          this.selectedRowId = val.id
        }
      },
      allExpand(isExpand){
        this.$refs.tree.$children.forEach(item => item.expanded = isExpand)
      },
      cancelAllChecked(){
        this.$refs.tree.setCheckedKeys([])
        this.checkedKeys = []
      },
      setAllChecked(data){
        data.forEach(item=>{

          if(item.pg_id){
              if (!item.disabled)
                  this.setAllPermissionCheckedList.push(item.name)
          }
          if(item.children && item.children.length){
            this.setAllChecked(item.children)
          }
        })
      },
      checkAll(){
        this.setAllPermissionCheckedList = []
        this.setAllChecked(this.allPermissionList)
        this.$refs.tree.setCheckedKeys(this.setAllPermissionCheckedList)
        this.checkedKeys = this.setAllPermissionCheckedList
      },

      calcTableHeight() {
        this.$nextTick(() => {
          this.windowHeight =
            window.innerHeight ||
            document.documentElement.clientHeight ||
            document.body.clientHeight;
          this.tableHeight =
            this.windowHeight - this.$refs.table.$el.offsetTop - 220;
        });
      },
      calcElTreeOffsetTop(){
        this.$nextTick(()=>{
          let actionButtonBox = document.getElementById('actionButtonBox')
          let elTreeBox = document.getElementById('elTreeBox')
          if(actionButtonBox){
            this.elTreeOffsetTop = actionButtonBox.offsetHeight
          }
          if(elTreeBox){
            this.elTreeBoxOffsetWidth = elTreeBox.offsetWidth
          }
        })
      },
      handleResize() {
        window.onresize = () => {
          this.calcTableHeight()
          this.calcElTreeOffsetTop()
        };
      },
      updateCheckedKeys(){
        this.$nextTick(()=>{
          if(this.$refs.tree){
            this.$refs.tree.setCheckedKeys(this.defaultCheckedKeys)
          }
        })
      },
      handleRowClick(row, column, event){
        this.showCheckedKeysLoading = true
        //this.guardName = row.guard_name
        this.defaultRoleId = row.id
        this.getrolePermissionList(row.guard_name)
        //this.getClickedRowRolePermission()
      },
      getClickedRowRolePermission(){
        this.showCheckedKeysLoading = true
        if(!this.isHaveSeePermission) {
          this.showCheckedKeysLoading = false
          return
        }
        rolePermission(this.defaultRoleId).then(res=>{
          this.rolePermissionList = res.data.data
          this.handleRolePermission()
        }).catch(err=>{
          this.saveLoading = false
          this.showCheckedKeysLoading = false
        })
      },
      getrolePermissionList(rowGuardName){
        if (this.guardName==rowGuardName && this.guardName!=""){
            this.getClickedRowRolePermission()
            return
        }
        if(rowGuardName){
          this.guardName=rowGuardName
        }
        let permissionGroups = guardNameForPermissions(this.guardName)
        Promise.all([permissionGroups]).then( res => {
          this.allPermissionList = res[0].data.data
          this.getClickedRowRolePermission()
        }).catch( error => {
          this.$store.dispatch('closeTagView', this.$route.fullPath)
        })
      },
      saveEdit(){
        if(this.defaultRoleId == 0){
          this.$message.error('请选择要修改的角色')
          return
        }
        if(!this.checkedKeys.length){
          this.$message.error('请至少选择一个权限')
          return
        }
        this.saveLoading = true
        this.changeRolePermission(this.checkedKeys)
      },
      changeRolePermission(checkedKeys){
        roleAssignPermission(this.defaultRoleId, checkedKeys).then( response => {
          this.saveLoading = false
          notify.editSuccess(this)
        })
      },
      handleCheck(currentData, currentNodeObj){
        this.checkedKeys = this.handleCheckedNodes(currentNodeObj.checkedNodes)
      },
      handleCheckedNodes(checkedNodes){
        let temp = []
        checkedNodes.forEach(item=>{
          if(item.pg_id){
            temp.push(item.name)
          }
        })
        return temp
      },
      findCheckedKeys(rolePermissioName){
        this.rolePermissionList.forEach(item=>{
          if(item.name == rolePermissioName){
            this.defaultCheckedKeys.push(item.name)
          }
        })
      },
      setRolePermissionTree(permissionNode,needDisabledPermission,parentDisabledPermissionStatus){
          if (parentDisabledPermissionStatus||(needDisabledPermission && (permissionNode.set_by_admin==1))){
              this.$set(permissionNode, 'disabled', true)
              parentDisabledPermissionStatus=true
          }
          if (permissionNode.pg_id){
              this.findCheckedKeys(permissionNode.name)
              return
          }
          permissionNode.children.forEach(ele=>{
              this.setRolePermissionTree(ele,needDisabledPermission,parentDisabledPermissionStatus)
          })
      },
      handleRolePermission(){
        this.defaultCheckedKeys = []
        let needDisabledPermission=false
        this.allPermissionList.forEach(item=>{
            this.setRolePermissionTree(item,needDisabledPermission,false)
        })

        this.updateCheckedKeys()
        this.checkedKeys = this.defaultCheckedKeys
        this.showCheckedKeysLoading = false
      },
      handleSizeChange(val){
        this.pagination.pageSize=val
        this.requestData()
      },
      handleEdit(index, row) {
        this.editForm = {
          name: row.name,
          guard_name: row.guard_name,
          description: row.description
        }
        this.nowRowData = { index, row }
        this.dialogEditFormVisible = true
      },
      handleDelete(index, row) {
        if(this.tableData.length == 1 && this.pagination.currentPage>1){
          this.pagination.currentPage-=1
        }
        this.hasSetDefaultSelected = false
        this.$confirm(this.$t('querydelete')).then(()=>{
          deleteRole(row.id).then( response => {
            deleteSuccess(index, this)
            this.requestData()
          })
        })
      },
      requestData() {
        this.loading = true
        this.showCheckedKeysLoading = true
        getRoleList({...this.queryParams, page:this.pagination.currentPage, pagesize:this.pagination.pageSize}).then( response => {
          responseDataFormat(response, this)
          if(this.tableData.length && !this.hasSetDefaultSelected){
            this.defaultRoleId = this.tableData[0].id
            //this.guardName = this.tableData[0].guard_name
            this.selectedRowId = this.tableData[0].id
            this.getrolePermissionList(this.tableData[0].guard_name)
          } else {
            this.showCheckedKeysLoading = false
          }
          this.hasSetDefaultSelected = true
        })
      },
      handleAddRole() {
        this.$refs['addForm'].validate((valid) => {
          if (valid) {
            addRole(this.addForm).then( response => {
              addSuccess(this)
              this.requestData()
            })
          } else {
            return false;
          }
        });
      },
      handleEditRole() {
        this.$refs['editForm'].validate((valid) => {
          if (valid) {
            editRole(this.nowRowData.row.id, this.editForm).then( response => {
              editSuccess(this)
              this.requestData()
            })
          } else {
            return false;
          }
        });
      },
    },
    computed: {
      updatePermission: function () {
        return hasPermission('role.update')
      },
      addPermission: function () {
        return hasPermission('role.store')
      },
      deletePermission: function () {
        return hasPermission('role.destroy')
      },
      assignPermission: function () {
        return hasPermission('role.assign-permissions')
      },
      isHaveSeePermission(){
        return hasPermission('role.permissions')
      },
    },
    created() {
      this.requestData()
    },
    mounted() {
      this.calcTableHeight()
      this.calcElTreeOffsetTop()
      this.handleResize()
    }
  }
</script>

<style lang="scss">
  .el-tooltip__popper {
    max-width: 400px;
    line-height: 1.6;
  }
  .role-permission-container{
    ::-webkit-scrollbar {width:8px; height:10px; background-color:transparent;} /*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
    ::-webkit-scrollbar-track {background-color:#F2F6FC;} /*定义滚动条轨道 内阴影+圆角*/
    ::-webkit-scrollbar-thumb {background-color:#DCDFE6; border-radius:6px;} /*定义滑块 内阴影+圆角*/
    .scrollbarHide::-webkit-scrollbar{display: none}
    .scrollbarShow::-webkit-scrollbar{display: block}
    .role-index{
      .el-table .el-table__body tr{
        cursor: pointer;
      }
      .role-desc {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
      }
    }
    .my-card-mask {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: 999;
      background: #fff;
      top: -2px;
      right: -1px;
      box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
      border-radius: 4px;
      border: 1px solid #ebeef5;
      text-align: center;
      padding-top: 20px;
    }
    .role-permissions{
      overflow-y: scroll;
      .role-permission-action{
        position: fixed;
        top: 125px;
        z-index: 9;
        background: #fff;
        padding: 15px 22px 0px 0px;
        .action-btn{
          margin-bottom: 15px;
        }
      }
    }
    .el-tree-box{
      margin-top: 42px;
    }
  }
  @media screen and (max-width: 1778px) {
    .role-permissions .el-tree-box{
      margin-top: 85px;
    }
  }
</style>
