<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Header :pageTitle="'Change Password'" />
        <div class="cp-form-container">
          <el-card style="width: 450px; margin-top: 20px;">
            <el-form :model="form" :rules="rules" ref="loginForm" label-width="auto" label-position="top" style="max-width: 600px">
              <el-form-item label="Current Password" prop="current_password">
                <el-input v-model="form.current_password" type="password" />
              </el-form-item>

              <el-form-item label="New Password" prop="new_password">
                <el-input v-model="form.new_password" type="password" />
              </el-form-item>

              <el-form-item label="Confirm New Password" prop="confirm_password">
                <el-input v-model="form.confirm_password" type="password" />
              </el-form-item>
              
              <el-form-item>
                <el-button type="primary" @click="onSubmit">Change</el-button>
              </el-form-item>
            </el-form>
          </el-card>
        </div>
      </el-main>
      <el-footer>
        <Footer/>
      </el-footer>
    </el-container>
  </div>
</template>

<script>

import { inject, reactive, ref } from 'vue';
import Header from "../Header";
import Footer from "../Footer";
import http from "../../http/http-common";
import {loader} from '../../composables/Loader';

  export default {
    name: 'ChangePassword',
    components: {
      Header,
      Footer
    },
    setup() {
      const alert = inject('alert');
      const { success, error } = alert();
      const { startLoading, stopLoading } = loader();

      const form = reactive({
        current_password: '',
        new_password: '',
        confirm_password: ''
      });

      const validateConfirmPassword = (rule, value, callback) => {
        if (value !== form.new_password) {
          callback(new Error('Confirm password do not match'));
        } else {
          callback();
        }
      };

      const rules = reactive({
        current_password: [
          { required: true, message: 'Please input current password', trigger: 'blur' },
        ],
        new_password: [
          { required: true, message: 'Please input new password', trigger: 'blur' }
        ],
        confirm_password: [
          { required: true, message: 'Please input confirm password', trigger: 'blur' },
          { validator: validateConfirmPassword, trigger: 'blur' }
        ]
      });

      const loginForm = ref(null);

      const onSubmit = () => {
        loginForm.value.validate(async (valid) => {
          if (valid) {
            startLoading('Updating password ...');
            try {
                const response = await http.post(`change-password`, form);
                stopLoading();
                success(response.data.message);
            } catch (err) {
                error(err.response.data.message);
            }
            stopLoading();
          } else {
            console.log('Validation failed');
          }
        });
      };

      return {
        form,
        rules,
        loginForm,
        onSubmit
      };
    }
  };
</script>
