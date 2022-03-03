<template>
  <el-header>
    <el-row class="header-menu">
      <el-col :span="1" class="open-menu">
        <i class="fa fa-bars fa-lg" :class="menuOpenOrClose_icon" @click="menuOpenOrClose"></i>
      </el-col>
      <el-col :span="15">
        <el-breadcrumb separator="/">
          <el-breadcrumb-item v-for="bc in breadcrumb" :to="{ path: bc.path }" :key="bc.path"> {{ $t(`meta.title.${bc.meta.title}`) }}</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
      <el-col :span="8">
        <div class="avatar">
          <el-dropdown>
            <el-button :plain="true">
              <img src="https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80" width="30" height="30" style="border-radius:30px">
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item @click.native="openDialogChangePasswordForm">{{ $t('changePassword')  }} </el-dropdown-item>
              <el-dropdown-item  @click.native="clearAllCache()" v-if="hasClearCacheRight">{{ $t('clearCache')  }} </el-dropdown-item>
              <el-dropdown-item @click.native="logout">{{ $t('logout') }}</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
        <!--<div class="lang">
          <el-dropdown>
            <span class="el-dropdown-link">
              语言<i class="el-icon-arrow-down el-icon&#45;&#45;right"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>中文简体</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>-->
      </el-col>
    </el-row>
    <el-dialog
            :title="$t('changePassword')"
            :visible.sync="dialogChangePasswordFormVisible"
            :close-on-press-escape="!is_update_password"
            :close-on-click-modal="!is_update_password"
            :show-close="!is_update_password"
            width="30%"
            center>
      <el-form :model="changePasswordForm" :rules="rules" ref="changePasswordForm">
        <el-form-item :label="$t('oldPassword')" prop="old_password" :label-width="formLabelWidth">
          <el-input v-model="changePasswordForm.old_password" type="password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item :label="$t('password')" prop="password" :label-width="formLabelWidth">
          <el-input v-model="changePasswordForm.password" type="password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item :label="$t('confirmPassword')" prop="password_confirmation" :label-width="formLabelWidth">
          <el-input v-model="changePasswordForm.password_confirmation" type="password" autocomplete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer change-password-form-footer">
        <el-button v-if="!is_update_password"  @click="dialogChangePasswordFormVisible = false">{{ $t('cancel') }}</el-button>
        <el-button type="primary" @click="handleChangePassword">{{ $t('confirm') }}</el-button>
      </div>
    </el-dialog>
  </el-header>
</template>

<script>
  import { mapActions } from 'vuex'
  import config from '../../config'
  import { changePassword } from '../../api/changePassword'
  import {clearCache} from '../../api/adminUser'
  import notify from '../../libs/notify'

  export default {
    name: 'Header',
    props: {
      isCollapse: Boolean
    },
    data() {
      let confirmPassword = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('Please enter your password again'))
        } else if (value !== this.changePasswordForm.password) {
          callback(new Error('输入两次密码不一致'))
        } else {
          callback()
        }
      }
      return {
        formLabelWidth: '100px',
        dialogChangePasswordFormVisible: false,
        menuOpenOrClose_icon:'',
        changePasswordForm:{
          old_password: null,
          password: null,
          password_confirmation: null
        },
        rules: {
          old_password: [
            { required: true },
            { min: 8, max: 32 }
          ],
          password: [
            { required: true },
            { min: 8, max: 32 }
          ],
          password_confirmation: [
            { required: true },
            { validator: confirmPassword },
            { min: 8, max: 32 }
          ]
        }
      }
    },
    methods: {
      ...mapActions([
        'logoutHandle'
      ]),
      menuOpenOrClose: function (event) {
        if (this.isCollapse)
            this.menuOpenOrClose_icon=''
        else
            this.menuOpenOrClose_icon='menu-open-icon'
        this.$emit('menu', !this.isCollapse)
      },
      logout ()  {
        this.logoutHandle(this.$provider).then(this.$router.push({
          name: config[this.$provider].loginRouteName
        }))
      },
        clearAllCache() {
            this.$confirm(this.$t('askClearCache'),this.$t('prompt'),
                {confirmButtonText:this.$t('confirm'),
                    cancelButtonText:this.$t('cancel'),
                    type: 'warning'}
            ).then(_ => {
                clearCache().then(
                    this.$notify({
                        title: this.$t('success'),
                        message: this.$t('deleteSuccess'),
                        type: 'success',
                        duration: 2000
                    })
                )})
        },
      openDialogChangePasswordForm () {
        this.dialogChangePasswordFormVisible = true
      },
      handleChangePassword () {
        this.$refs['changePasswordForm'].validate((valid) => {
          if (valid) {
            changePassword(this.changePasswordForm).then( response => {
              notify.editSuccess(this)
              this.dialogChangePasswordFormVisible = false
              this.$refs['changePasswordForm'].resetFields()
              if (this.is_update_password()){
                 this.logout()
              }
            })
          } else {
            return false;
          }
        });
      }
    },
    computed: {
      breadcrumb () {
        return this.$store.getters.breadcrumb
      },
      hasClearCacheRight(){
            return this.$store.getters.permissions.indexOf('role.assign-permissions') >= 0
      },
      is_update_password(){
          return this.$store.getters.token.is_update_password
      }
    },
    created(){
          if(this.is_update_password){
              this.dialogChangePasswordFormVisible = true
          }
      }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .el-header {
    border-bottom: 1px solid #e6e6e6;
    height: 60px;
    line-height:60px;
  }
  .el-button {
    border:none;
  }
  .avatar {
    float:right;
  }
  .lang {
    float:right;
    width:50px;
  }
  .open-menu {
    cursor:pointer;
  }
  .menu-open-icon{
    transform:rotate(90deg);
    -ms-transform:rotate(90deg);
    -moz-transform:rotate(90deg);
    -webkit-transform:rotate(90deg);
    -o-transform:rotate(90deg);
  }
  .el-breadcrumb {
    line-height:60px;
  }
  a {
    text-decoration:none;
  }
  .change-password-form{
    padding-right: 20px;
  }
  .change-password-form-footer{
    text-align: center;
  }
</style>
