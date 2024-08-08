<template>
    <Header :pageTitle="'Manage Coach'" @add-new="onAddNew" @search="onSearch"/>
    <div class="users-container">
        <div class="list-card">
            <el-card class="user-item" v-for="user in filteredUsers" :key="user.id">
                <template #header>{{ user.name }}</template>
                <p><strong>Name:</strong> {{ user.name }}</p>
                <p><strong>Gender:</strong> {{ user.gender }} </p>
                <p v-if="user.birth_year"><strong>Year of Birth:</strong> {{ user.birth_year }} </p>
                <p><strong>Email:</strong> {{ user.email }}</p>

                <div v-if="user.teams">
                    <strong>Team:</strong>
                    <el-tag v-for="team in user.teams" :key="team.id" type="success">{{ team.name }}</el-tag>
                </div>
            
                <template #footer>
                <el-button size="small" @click="editUser(user)">Edit</el-button>
                <el-popconfirm
                    confirm-button-text="Yes"
                    cancel-button-text="No"
                    icon-color="#626AEF"
                    title="Are you sure to delete this?"
                    @confirm="deleteUser(user.id)"
                >
                    <template #reference>
                    <el-button type="danger" size="small">Delete</el-button>
                    </template>
                </el-popconfirm>
                </template>
            </el-card>
        </div>
    </div>
    <el-dialog v-model="dialogFormVisible" class="form-modal" :title="dialogTitle" max-width="500">
    <el-form :model="form" :rules="rules" ref="userForm" label-width="auto" label-position="top">
        <el-form-item v-if="'Athlete' === userType" label="Team ID" prop="team_id">
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
        <el-form-item v-if="'Athlete' === userType" label="Year of Birth" prop="birth_year">
            <el-input v-model="form.birth_year" type="number" min="1970" max="2024" />
        </el-form-item>
        <el-form-item label="Email" prop="email">
            <el-input v-model="form.email" />
        </el-form-item>

        <el-form-item v-if="!form.id" label="Password" prop="password">
            <el-input v-model="form.password" type="password" />
        </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
            <el-button @click="resetForm">Cancel</el-button>
      </div>
    </template>
  </el-dialog>
</template>
<script>
import { inject, reactive, ref, onMounted } from 'vue';
import Header from "../Header";
import http from "../../http/http-common";
import { loader } from '../../composables/Loader';
export default {
    name: "ManageUser",
    components: {
        Header,
    },
    props: ['userType'],

    setup(props) {
        const form = reactive({
            id: null,
            team_id: '',
            name: '',
            gender: '',
            user_type: props.userType,
            birth_year: '',
            email: '',
            password: ''
        });
        const userType = props.userType;
        const alert = inject('alert');
        const { success, error } = alert();
        const dialogFormVisible = ref(false);
        const dialogTitle = ref('');
        const { startLoading, stopLoading } = loader();
        const users = ref([]);
        const filteredUsers = ref([]);
        const isEditing = ref(false);
        const userForm = ref(null);
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

        const fetchUsers = async () => {
            startLoading('Fetching ' +userType +' ...');
            try {
                const response = await http.get(`users-by-type/${userType}`);
                stopLoading();
                users.value = response.data.data;
                filteredUsers.value = response.data.data;
            } catch (err) {
                //error(err.response.data.message);
            }
        };

        const onAddNew = () => {
            getTitle();
            dialogFormVisible.value = true;
        }
        const resetForm = () => {
            form.id         = null;
            form.team_id    = '';
            form.name       = '';
            form.gender     = '';
            form.user_type  = props.userType;
            form.birth_year = '';
            form.email      = '';
            form.password   = '';
            isEditing.value = false;
            dialogFormVisible.value = false;
            getTitle();
        };

        const editUser = ( user ) => {
            form.id         = user.id;
            form.team_id    = user.team_id;
            form.name       = user.name;
            form.gender     = user.gender;
            form.user_type  = user.user_type;
            form.birth_year = user.birth_year;
            form.email      = user.email;
            isEditing.value = false;
            dialogFormVisible.value = false;
            getTitle();
        };

        const onSearch = (q) => {
            queryArg.value = q;
            filterClubs();
        }
        
        const getTitle = () => {
            dialogTitle.value = isEditing.value ? 'Update ' : 'Add ';
            dialogTitle.value += ' ' + props.userType;
        }

        const onSubmit = () => {
            userForm.value.validate(async (valid) => {
            if (valid) {
                try {
                    startLoading('Creating account...');
                    const response = await http.post('user/create', form);
                    stopLoading();

                    if( response.data.success ) {
                        users.value.push(response.data.data);
                        success(response.data.message);
                    } else {
                        error(response.data.message);
                    }
                } catch (err) {
                    error(err.response.data.message);
                }
                resetForm();
            }
            });
        };

        onMounted(() => {
            fetchUsers();
        });

        return {
            filteredUsers,
            onAddNew,
            onSearch,
            dialogFormVisible,
            dialogTitle,
            form,
            userForm,
            rules,
            isEditing,
            resetForm,
            editUser,
            onSubmit
        }
    }
}
</script>
<style scoped>
.users-container {
  margin: 2% 4%;
}

.list-card {
  display: flex;
  gap: 2%; 
  flex-wrap: wrap;
  justify-content: flex-start; 
}

.user-item {
  width: 23%;
  text-align: left;
  margin-top: 3%;
}

.el-tag{
  margin: 0 5px;
}
</style>