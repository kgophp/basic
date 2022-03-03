<template>
  <div>
    <el-form :inline="true" :model="queryParams" size="mini">
      <el-form-item :label="$t('name')">
        <el-input clearable v-model="queryParams.name"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="requestData" icon="el-icon-search">{{ $t('search') }}</el-button>
        <el-button
          type="primary"
          v-if="addPermission"
          @click="dialogAddFormVisible = true"
          icon="el-icon-plus"
        >{{ $t('add') }}</el-button>
      </el-form-item>
    </el-form>
    <el-table :data="tableListData" v-loading="loading" row-key="id" border style="width: 100%">
      <el-table-column prop="name" :label="$t('name')"></el-table-column>
      <el-table-column prop="created_at" :label="$t('createdAt')"></el-table-column>
      <el-table-column prop="updated_at" :label="$t('updatedAt')"></el-table-column>
      <el-table-column prop="sequence" :label="$t('sequence')"></el-table-column>
      <el-table-column prop="set_by_admin" :label="'仅管理员可见'">
        <template slot-scope="scope">{{scope.row.set_by_admin ? '是' : '否'}}</template>
      </el-table-column>
      <el-table-column fixed="right" :label="$t('actions')">
        <template slot-scope="scope">
          <el-button
            v-if="updatePermission"
            size="mini"
            @click="handleEdit(scope.$index, scope.row)"
          >{{ $t('edit') }}</el-button>
          <el-button
            v-if="deletePermission"
            size="mini"
            type="danger"
            @click="handleDelete(scope.$index, scope.row)"
          >{{ $t('delete') }}</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="$t('add')" :visible.sync="dialogAddFormVisible" width="30%">
      <el-form :model="addForm" :rules="rules" ref="addForm">
        <el-form-item :label="$t('parentMenu')" :label-width="formLabelWidth">
          <el-cascader
            ref="elCascader"
            v-model="parentMenuValue"
            :options="cascaderData"
            :props="{ expandTrigger: 'hover', checkStrictly: true, value: 'id', label: 'name' }"
            clearable
            @change="handleCascaderChange($event, 'addForm')"
          >
          </el-cascader>
        </el-form-item>

        <el-form-item :label="$t('name')" prop="name" :label-width="formLabelWidth">
          <el-input clearable v-model="addForm.name"></el-input>
        </el-form-item>

        <el-form-item label="排序" prop="sequence" :label-width="formLabelWidth">
          <el-input clearable v-model="addForm.sequence"></el-input>
        </el-form-item>

        <el-form-item label="仅管理员可见" prop="set_by_admin" :label-width="formLabelWidth">
          <el-switch
            v-model="addForm.set_by_admin"
            :active-value="1"
            :inactive-value="0">
          </el-switch>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogAddFormVisible = false">{{ $t('cancel') }}</el-button>
        <el-button type="primary" @click="handleAddPermissionGroup">{{ $t('confirm') }}</el-button>
      </div>
    </el-dialog>

    <el-dialog :title="$t('edit')" :visible.sync="dialogEditFormVisible" width="30%">
      <el-form :model="editForm" :rules="rules" ref="editForm">
        <el-form-item :label="$t('parentMenu')" :label-width="formLabelWidth">
          <el-cascader
            ref="editElCascader"
            v-model="editParentMenuValue"
            :options="cascaderData"
            :props="{ expandTrigger: 'hover', checkStrictly: true, value: 'id', label: 'name' }"
            clearable
            @change="handleCascaderChange($event, 'editForm')"
          >
          </el-cascader>
        </el-form-item>

        <el-form-item :label="$t('name')" prop="name" :label-width="formLabelWidth">
          <el-input clearable v-model="editForm.name"></el-input>
        </el-form-item>

        <el-form-item label="排序" prop="sequence" :label-width="formLabelWidth">
          <el-input @keydown.native="keydownhandle" clearable v-model="editForm.sequence"></el-input>
        </el-form-item>

        <el-form-item label="仅管理员可见" prop="set_by_admin" :label-width="formLabelWidth">
          <el-switch
            v-model="editForm.set_by_admin"
            :active-value="1"
            :inactive-value="0">
          </el-switch>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogEditFormVisible = false">{{ $t('cancel') }}</el-button>
        <el-button type="primary" @click="handleEditPermissionGroup">{{ $t('confirm') }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  getPermissionGroupList,
  addPermissionGroup,
  editPermissionGroup,
  deletePermissionGroup
} from "../../../api/permissionGroup";
import {
  responseDataFormat,
  tableDefaultData,
  editSuccess,
  addSuccess,
  deleteSuccess
} from "../../../libs/tableDataHandle";
import { hasPermission } from "../../../libs/permission";
import notify from "../../../libs/notify";
import _ from 'lodash'

export default {
  name: "permissionGroupIndex",
  data() {
    return {
      ...tableDefaultData(),
      tableListData: [],
      parentMenuValue: '',
      editParentMenuValue: '',
      addForm: {
        name: "",
        parent_id: 0,
        parent_ids: [],
        sequence: '',
        set_by_admin: '0',
      },
      editForm: {
        name: "",
        parent_id: 0,
        parent_ids: [],
        sequence: '',
        set_by_admin: '0',
      },
      rules: {
        name: [{ required: true }, { min: 1, max: 255 }]
      },
    };
  },
  watch: {
    parentMenuValue(){
      if(this.$refs.elCascader){
        this.$refs.elCascader.dropDownVisible = false
      }
    },
    editParentMenuValue(){
      if(this.$refs.editElCascader){
        this.$refs.editElCascader.dropDownVisible = false
      }
    },
  },
  methods: {
    keydownhandle(event) {
      const { key } = event;
      if (new RegExp(/[0-9]/).test(key) || key === 'Backspace') return;
      event.preventDefault();
    },
    handleCascaderChange(val, name){
      if(val.length){
        this[name].parent_ids = val
        this[name].parent_id = val[val.length - 1]
      } else {
        this[name].parent_id = 0
        this[name].parent_ids = []
      }
    },
    handleEdit(index, row) {
      this.editForm.name = row.name
      this.editForm.sequence = row.sequence
      this.editForm.set_by_admin = row.set_by_admin
      this.nowRowData = { index, row };
      row.parent_id ? (this.editParentMenuValue = row.parent_id) : (this.editParentMenuValue = 9999)
      this.editForm.parent_id = this.editParentMenuValue
      this.dialogEditFormVisible = true
    },
    handleDelete(index, row) {
      this.$confirm(this.$t("querydelete")).then(() => {
        deletePermissionGroup(row.id).then(response => {
          deleteSuccess(index, this);
          this.requestData();
        });
      });
    },
    requestData() {
      this.loading = true;
      getPermissionGroupList({
        ...this.queryParams,
        page: this.pagination.currentPage
      }).then(response => {
        // responseDataFormat(response, this)
        this.tableListData = response.data.data;
        this.cascaderData = this.handleTableListData(this.tableListData)
        this.loading = false;
      });
    },
    handleTableListData(tableData){
      let newData = _.cloneDeep(tableData)
      newData.unshift({ id: 9999, parent_id: 9999, name: '顶级菜单' })
      return newData
    },
    handleAddPermissionGroup() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          if(this.parentMenuValue == 9999){
            this.addForm.parent_id = 0
          }

          addPermissionGroup(this.addForm).then(response => {
            addSuccess(this);
            this.requestData();
            this.parentMenuValue = ''
          });
        } else {
          return false;
        }
      });
    },
    handleEditPermissionGroup() {
      this.$refs["editForm"].validate(valid => {
        if (valid) {
          if(this.editParentMenuValue == 9999){
            this.editForm.parent_id = 0
          }

          editPermissionGroup(this.nowRowData.row.id, this.editForm).then(
            response => {
              editSuccess(this);
              this.requestData()
            }
          );
        } else {
          return false;
        }
      });
    }
  },
  computed: {
    updatePermission: function() {
      return hasPermission("permission-group.update");
    },
    addPermission: function() {
      return hasPermission("permission-group.store");
    },
    deletePermission: function() {
      return hasPermission("permission-group.destroy");
    }
  },
  created() {
    this.requestData();
  }
};
</script>
