<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <img class="logo" src="../../../images/logo.png"/>

        <el-card style="max-width: 480px; margin-top: 20px;">
          <el-form :model="form" :rules="rules" ref="registerForm" label-width="auto" label-position="top" style="max-width: 600px">
            <el-form-item label="Team ID" prop="team_id">
              <el-input v-model="form.team_id" />
            </el-form-item>
            <el-form-item label="Name" prop="name">
              <el-input v-model="form.name" />
            </el-form-item>
            <el-form-item label="Gender" prop="gender">
              <el-select v-model="form.gender" placeholder="Select Gender">
                <el-option v-for="gender in ['Male', 'Female', 'Other']" :key="gender" :label="gender" :value="gender" />
              </el-select>
            </el-form-item>
            <el-form-item label="Year of Birth" prop="birth_year">
              <el-input v-model="form.birth_year" type="number" min="1970" max="2024" />
            </el-form-item>
            <el-form-item label="Email" prop="email">
              <el-input v-model="form.email" />
            </el-form-item>

            <el-form-item label="Password" prop="password">
              <el-input v-model="form.password" type="password" />
            </el-form-item>
            
            <el-form-item>
              <el-button type="primary" @click="onSubmit">Register</el-button>
            </el-form-item>
          </el-form>

          <div class="form-links">
            <a href="#/forgot-password" class="link">Forgot Password?</a>
            <p>Already have account?<a href="#/login" class="link">Login here</a></p>
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
import Footer from "../Footer";
import { useRouter } from 'vue-router';
import http from "../../http/http-common";
import { loader } from '../../composables/Loader';

  export default {
    name: "AthleteRegister",
    components: {
      Footer
    },
    setup() {
      const form = reactive({
        team_id: '',
        name: '',
        gender: '',
        birth_year: '',
        email: '',
        password: '',
        user_type: 'Athlete'
      });

      const alert = inject('alert');
      const { success, error } = alert();
      const { startLoading, stopLoading } = loader();
      const router = useRouter();
      const registerForm = ref(null);
      const rules = reactive({
        team_id: [
          { required: true, message: 'Please input team ID', trigger: 'blur' },
        ],
        name: [
          { required: true, message: 'Please input name', trigger: 'blur' },
        ],
        gender: [
          { required: true, message: 'Please input Gender', trigger: 'change' },
        ],
        birth_year: [
          { required: true, message: 'Please input year of birth', trigger: 'blur' },
        ],
        email: [
          { required: true, message: 'Please input email', trigger: 'blur' },
          { type: 'email', message: 'Please input valid email', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Please input password', trigger: 'blur' }
        ]
      });

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
        registerForm.value.validate(async (valid) => {
          if (valid) {
            startLoading('Creating account...');
            try {
              const response = await http.post('user/register', form);

              if( response.data.success ) {
                localStorage.setItem('_GymAppUserToken', response.data.data.token);
                let user = response.data.data.user;
                localStorage.setItem('_GymAppLoggedInUser',JSON.stringify(user));

                router.push('/');
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
          registerForm,
          onSubmit
      }
    }
  }
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