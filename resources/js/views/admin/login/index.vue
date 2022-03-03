<template>
  <div id="login">
    <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" class="login-container">
      <h2 class="title-color pdb10">{{$t('systemname')}}<span v-if="$app_env!=''">({{$app_env}})</span></h2>
      <el-form-item prop="username">
        <el-input  v-model="ruleForm.username" auto-complete="off" :placeholder="$t('username')"></el-input>
      </el-form-item>
      <el-form-item prop="password">
        <el-input type="Password" v-model="ruleForm.password" auto-complete="off" :placeholder="$t('password')" @keyup.enter.native="submitForm('ruleForm')"></el-input>
      </el-form-item>
      <el-form-item>
        <el-checkbox v-model="ruleForm.remeber">{{$t('remeber')}}</el-checkbox>
      </el-form-item>
      <el-form-item>
        <el-button type="primary"  @click="submitForm('ruleForm')" style="width:100%">{{$t('submit')}}</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
  import { mapActions } from 'vuex'

  export default {
    data() {
      return {
        ruleForm: {
          username: '',
          password: '',
          remeber:false,
        },
        rules: {
          username: [
            { required: true, trigger: 'blur',message:this.$t('errorMessage.required') }
          ],
          password: [
            { required: true, trigger: 'blur',message:this.$t('errorMessage.required') }
          ]
        }
      };
    },
    methods: {
      ...mapActions([
        'loginHandle',
      ]),
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.loginHandle({
              ...this.ruleForm,
              ...this.$config[this.$provider].authorize,
              provider: this.$provider
            }).then(result => {
              this.$router.push({
                name: 'adminMain'
              })
            })
          }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      }
    },
    created(){

    }
  }
</script>
<style scoped>
  h2 {
    text-align: center;
  }
  #login {
    height:100%;
  }
  .login-container {
    width: 400px;
    padding: 40px;
    background: #fff;
    position: absolute;
    top:45%;
    left:50%;
    margin-top:-210px;
    margin-left:-220px;
    border: 1px solid #eaeaea;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-border-radius: 5px;
    -moz-box-shadow:0 0 25px #cac6c6;
    -webkit-box-shadow:0 0 25px #cac6c6;
    box-shadow:0 0 25px #cac6c6;
  }
  .pdb10{
    padding-bottom: 16px;
  }
</style>
