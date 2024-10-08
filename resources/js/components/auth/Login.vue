<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <img class="logo" src="../../../images/logo.png"/>

        <el-card style="max-width: 480px; margin: 20px 0;">
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
      <el-footer>
        <Footer/>
      </el-footer>
    </el-container>
  </div>
  
</template>

<script>
  import { inject, reactive, ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import http from "../../http/http-common";
  import { loader } from '../../composables/Loader';
  import Footer from "../Footer";

  export default {
    name: 'Login',
    components: {
      Footer
    },
    setup() {
      const form = reactive({
        email: '',
        password: '',
      }); 
      const alert = inject('alert');
      const { error } = alert();
      const { startLoading, stopLoading } = loader();

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
            startLoading('Authenticating...');
            try {
              const response = await http.post('login', form);
              if( response.data.success ) {
                localStorage.setItem('_GymAppUserToken', response.data.data.token);
                let user = response.data.data.user;
                localStorage.setItem('_GymAppLoggedInUser',JSON.stringify(user));
                window.location.reload();
                //router.push('/');
              } else {
                error(response.data.message);
              }
              
            } catch (err) {
              error(err.response.data.message);
            }
            
            stopLoading();
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
  justify-content: flex-start;
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

@media (max-width: 768px) {
  .logo {
    width: 35%;
  }
  .el-card {
    width: 98%;
  }
}
</style>
