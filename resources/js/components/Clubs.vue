<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Header :pageTitle="'Manage Club'" :addButton="'Add Club'" @add-new="onAddNew" @search="onSearch" />
        <div class="list-container">
          <div class="list-card">
            <el-card class="list-item clubs" v-for="club in filteredClubs" :key="club.id">
              <template #header>{{ club.name }}</template>
              <p><strong>Location:</strong> {{ club.location }}</p>
              <p><strong>Manager:</strong> {{ club.manager_name }} </p>
              <p><strong>Email:</strong> {{ club.manager_email }}</p>
              <div>
                <strong>Sports:</strong>
                <el-tag v-for="sport in club.sports" :key="sport.id" type="success">{{ sport.name }}</el-tag>
              </div>
              <template #footer>
                <el-button size="small" @click="editClub(club)">Edit</el-button>
                <el-popconfirm
                  confirm-button-text="Yes"
                  cancel-button-text="No"
                  icon-color="#626AEF"
                  title="Are you sure to delete this?"
                  @confirm="deleteClub(club.id)"
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
          <el-form :model="form" :rules="rules" ref="clubForm" label-width="auto" label-position="top">

            <el-form-item label="Club Name" prop="name">
              <el-input v-model="form.name" />
            </el-form-item>

            <el-form-item label="Location" prop="location">
              <el-input v-model="form.location" />
            </el-form-item>

            <el-form-item v-if="!isEditing" label="Manager Email" prop="manager_email">
              <el-input v-model="form.manager_email" @blur="getManagerInfo"/>
            </el-form-item>

            <el-form-item v-if="!isEditing" label="Manager Name" prop="manager_name">
              <el-input v-model="form.manager_name" />
            </el-form-item>

            <el-form-item v-if="!form.manager_id && !isEditing" label="Password" prop="password">
              <el-input v-model="form.password"/>
            </el-form-item>

            <el-form-item label="Sport" prop="sports">
              <el-select v-model="form.sports" multiple collapse-tags collapse-tags-tooltip :max-collapse-tags="3" placeholder="Select Sport">
                <el-option v-for="sport in sports" 
                :key="sport.id" 
                :label="sport.name" 
                :value="sport.id" />
              </el-select>
            </el-form-item>

          </el-form>
          <template #footer>
            <div class="dialog-footer">
              <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
                  <el-button @click="resetForm">Cancel</el-button>
            </div>
          </template>
        </el-dialog>
      </el-main>
      <el-footer>
        <Footer/>
      </el-footer>
    </el-container>
  </div>
</template>

<script>
import { inject, reactive, ref, onMounted } from 'vue';
import Header from "./Header";
import Footer from "./Footer";
import http from "../http/http-common";
import { loader } from '../composables/Loader';

export default {
name: 'Clubs',
components: {
  Header,
  Footer
},
setup() {
  const form = reactive({
    id: null,
    name: '',
    location: '',
    manager_name: '',
    manager_email: '',
    manager_id: '',
    sports: [],
  });

  const alert = inject('alert');
  const { success, error } = alert();
  const clubs = ref([]);
  const filteredClubs = ref([]);
  const queryArg = ref(null);
  const sports = ref([]);
  const isEditing = ref(false);
  const clubForm = ref(null);
  const dialogFormVisible = ref(false);
  const dialogTitle = ref('');
  const { startLoading, stopLoading } = loader();

  const rules = reactive({
    name: [
      { required: true, message: 'Please input club name', trigger: 'blur' }
    ],
    location: [
      { required: true, message: 'Please input location', trigger: 'blur' }
    ],
    manager_name: [
      { required: true, message: 'Please input manager name', trigger: 'blur' }
    ],
    manager_email: [
      { required: true, message: 'Please input manager email', trigger: 'blur' },
      { type: 'email', message: 'Please input a valid email', trigger: 'blur' }
    ],
    sports: [
      { required: true, message: 'Please select a sport', trigger: 'change' }
    ],
    password: [
      { required: true, message: 'Please input password', trigger: 'blur' }
    ]
  });

  const fetchClubs = async () => {
    startLoading('Fetching clubs...');
    try {
      const response = await http.get('clubs');
      stopLoading();
      clubs.value = response.data.data;
      filteredClubs.value = response.data.data;
    } catch (err) {
      error(err.response.data.message);
    }
  };

  const fetchSports = async () => {
    try {
      startLoading('Fetching sports...');
      stopLoading();
      const response = await http.get('sports');
      sports.value = response.data.data;
    
    } catch (err) {
      error(err.response.data.message);
    }
  };

  const getManagerInfo = async () => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (form.manager_email && emailRegex.test(form.manager_email)) {
      let email = form.manager_email;
      startLoading('Fetching coaches...');
      try {
        const response = await http.get(`user-by-email/${email}`);
        stopLoading();
        if( response.data.success && response.data.data ) {
          form.manager_id = response.data.data.id;
          form.manager_name = response.data.data.name;
        } else {
          form.manager_id = '';
          form.manager_name = '';
        }
      } catch (err) {
        error(err.response.data.message);
      }
      stopLoading();
    }
  };
  const onAddNew = () => {
    getTitle();
    dialogFormVisible.value = true;
  }

  const filterClubs = () => {
    filteredClubs.value = clubs.value.filter(club =>
      !queryArg.value || club.name.toLowerCase().includes(queryArg.value.toLowerCase())
    );
  };

  const onSearch = (q) => {
    queryArg.value = q;
    filterClubs();
  }

  const onSubmit = () => {
    clubForm.value.validate(async (valid) => {
      if (valid) {
        try {
          if (isEditing.value) {
            startLoading('Updating club...');
            const response = await http.put(`clubs/${form.id}`, form);
            stopLoading();
            if(response.data.success) {
              fetchClubs();
              success(response.data.message);
            } else {
              error(response.data.message);
            }
            isEditing.value = false;
          } else {
            startLoading('Storing club...');
            const response = await http.post('clubs', form);
            stopLoading();
            
            if(response.data.success) {
              clubs.value.push(response.data.data);
              success(response.data.message);
            } else {
              error(response.data.message);
            }
          }
          resetForm();
        } catch (err) {
          error(err.response.data.message);
        }
      }
    });
  };

  const resetForm = () => {
    form.id = null;
    form.name = '';
    form.location = '';
    form.manager_name = '';
    form.manager_email = '';
    form.sports = [];
    isEditing.value = false;
    clubForm.value.resetFields();
    dialogFormVisible.value = false;
    getTitle();
  };

  const editClub = (club) => {
    form.id = club.id;
    form.name = club.name;
    form.location = club.location;
    form.manager_name = club.manager_name;
    form.manager_email = club.manager_email;
    form.manager_id = club.manager_id;
    form.sports = [];
    club.sports.forEach(element => {
      form.sports.push(element.id);
    });
    isEditing.value = true;
    getTitle();
    dialogFormVisible.value = true;
  };

  const deleteClub = async (id) => {

    try {
      startLoading('Deleting club...');
      const response = await http.delete(`clubs/${id}`);
      stopLoading();
      if(response.data.success) {
        success(response.data.message);
        fetchClubs();
      } else {
        error(response.data.message);
      }
    } catch (err) {
      error(err.response.data.message);
    }
  };

  const getTitle = () => {
    dialogTitle.value = isEditing.value ? 'Update ' : 'Add ';
    dialogTitle.value += ' Club';
  }

  onMounted(() => {
    fetchClubs();
    fetchSports();
    getTitle();
  });

  return {
    form,
    filteredClubs,
    sports,
    rules,
    clubForm,
    isEditing,
    onSubmit,
    resetForm,
    editClub,
    deleteClub,
    onAddNew,
    onSearch,
    dialogFormVisible,
    dialogTitle,
    getManagerInfo
  };
}
};
</script>

<style scoped>
.clubs-container {
  margin: 2% 4%;
}

.list-card {
  display: flex;
  gap: 2%; 
  flex-wrap: wrap;
  justify-content: flex-start; 
}

.club-item {
  width: 23%;
  text-align: left;
  margin-top: 3%;
}

.el-tag{
  margin: 0 5px;
}
</style>
