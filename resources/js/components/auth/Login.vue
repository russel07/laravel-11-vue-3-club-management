<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <img class="logo" src="../../../images/logo.png"/>

        <el-card style="max-width: 480px; margin-top: 20px;">
          <el-form :model="form" :rules="rules" ref="loginForm" label-width="auto" label-position="top" style="max-width: 600px">
            <el-form-item label="Email" prop="email">
              <el-input v-model="form.email" />
            </el-form-item>

            <el-form-item label="Password" prop="password">
              <el-input v-model="form.password" type="password" />
            </el-form-item>
            
            <el-form-item>
              <el-button type="primary" @click="onSubmit">Login</el-button>
            </el-form-item>
          </el-form>

          <div class="form-links">
            <a href="#/forgot-password" class="link">Forgot Password?</a>
            <a href="#/register/athlete" class="link">Join to the Team?</a>
          </div>

          
        </el-card>
        
      </el-main>
      <el-footer style="text-align: center;">Copyrigt &copy; by Md. Russel hussain</el-footer>
    </el-container>
  </div>
  
</template>

<script>
  import { reactive, ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';

  export default {
    name: 'Login',
    setup() {
      const form = reactive({
        email: '',
        password: '',
      });

      const rules = reactive({
        email: [
          { required: true, message: 'Please input email', trigger: 'blur' },
          { type: 'email', message: 'Please input valid email', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Please input password', trigger: 'blur' }
        ]
      });

      const loginForm = ref(null);
      const router = useRouter();

      const checkLoggedIn = () => {
        const token = localStorage.getItem('_GymAppUserToken');
        if (token) {
          router.push('/');
        }
      };

      onMounted(() => {
        checkLoggedIn();
      });

      const onSubmit = () => {
        loginForm.value.validate(async (valid) => {
          if (valid) {
            try {
              const response = await axios.post('http://lara-rest.test/api/login', form);
              localStorage.setItem('_GymAppUserToken', response.data.data.token);
              let user = response.data.data.user;
              localStorage.setItem('_GymAppLoggedInUser',JSON.stringify(user));

              router.push('/');
            } catch (error) {
              console.error('Login failed:', error.response.data);
              // Handle login error, e.g., show error message
            }
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
