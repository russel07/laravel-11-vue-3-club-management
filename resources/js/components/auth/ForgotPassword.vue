<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <img class="logo" src="../../../images/logo.png"/>

        <el-card style="max-width: 480px; margin-top: 20px;">
          <el-form :model="form" :rules="rules" ref="resetForm" label-width="auto" label-position="top" style="max-width: 600px">
            <el-form-item label="Email" prop="email">
              <el-input v-model="form.email" />
            </el-form-item>
            
            <el-form-item>
              <el-button type="primary" @click="onSubmit">Submit</el-button>
            </el-form-item>
          </el-form>

          <div class="form-links">
            <p>Already have account?<a href="#/login" class="link">Login here</a></p>
            <a href="#/register/athlete" class="link">Join to the Team?</a>
          </div>

          
        </el-card>
        
      </el-main>
      <el-footer>
          <Footer/>
      </el-footer>
    </el-container>
  </div>
  
</template>

<script>
import { inject, reactive, ref } from 'vue';
import Footer from "../Footer";
import http from "../../http/http-common";
import { loader } from '../../composables/Loader';

  export default {
    name: 'ForgotPassword',
    components: {
      Footer,
    },
    setup() {
      const alert = inject('alert');
      const { error } = alert();
      const { startLoading, stopLoading } = loader();
      const resetForm = ref(null);
      const form = reactive({
        email: ''
      });

      const rules = reactive({
        email: [
          { required: true, message: 'Please input email', trigger: 'blur' },
          { type: 'email', message: 'Please input valid email', trigger: 'blur' }
        ]
      });


      const onSubmit = () => {
        resetForm.value.validate(async (valid) => {
          if (valid) {
            startLoading('Sending email...');
            try {
              const response = await http.post('password/email', form);
              if( response.data.success ) {
                success(response.data.message);
              } else {
                error(response.data.message);
              }
              
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
        resetForm,
        onSubmit
      };
    }
  };
</script>

<style scoped>
.common-layout {
  display: flex;
  justify-content: center;
  align-items: center;
}

.main-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.logo {
  width: 15%;
  height: auto;
  margin-bottom: 20px;
}

.el-card {
  width: 100%;
}

.form-links {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.link {
  color: #409EFF;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}
</style>
