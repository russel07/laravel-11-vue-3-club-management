<template>
  <Header />
<div class="clubs-container">
  <div class="form-wrapper">
    <h1>{{ isEditing ? 'Update' : 'Add' }} Club</h1>
    <el-card class="form-card" style="max-width: 500px;">
      <el-form :model="form" :rules="rules" ref="clubForm" label-width="auto" label-position="top">
        <el-form-item label="Club Name" prop="name">
          <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="Location" prop="location">
          <el-input v-model="form.location" />
        </el-form-item>
        <el-form-item label="Manager Name" prop="manager_name">
          <el-input v-model="form.manager_name" />
        </el-form-item>
        <el-form-item label="Manager Email" prop="manager_email">
          <el-input v-model="form.manager_email" />
        </el-form-item>
        <el-form-item label="Sport" prop="sports">
          <el-select v-model="form.sports" multiple collapse-tags collapse-tags-tooltip :max-collapse-tags="3" placeholder="Select Sport">
            <el-option v-for="sport in sports" :key="sport.id" :label="sport.name" :value="sport.id" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
          <el-button @click="resetForm">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>

  <div class="clubs-wrapper">
    <h1>Club List</h1>
    <div class="h-6" />
    <div class="list-card">
      <el-card class="club-item" v-for="club in clubs" :key="club.id">
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
          <el-button size="small" type="danger" @click="deleteClub(club.id)">Delete</el-button>
        </template>
      </el-card>
    </div>
  </div>
</div>
</template>

<script>
import { reactive, ref, onMounted } from 'vue';
import Header from "./Header";
import axios from 'axios';

export default {
name: 'Clubs',
components: {
    Header,
},
setup() {
  const form = reactive({
    id: null,
    name: '',
    location: '',
    manager_name: '',
    manager_email: '',
    sports: '',
  });

  const clubs = ref([]);
  const sports = ref([]);
  const isEditing = ref(false);
  const clubForm = ref(null);

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
    ]
  });

  const fetchClubs = async () => {
    try {
      const response = await axios.get('http://lara-rest.test/api/clubs');
      clubs.value = response.data.data;
    } catch (error) {
      console.error('Failed to fetch clubs:', error.response.data);
    }
  };

  const fetchSports = async () => {
    try {
      const response = await axios.get('http://lara-rest.test/api/sports');
      sports.value = response.data.data;
    } catch (error) {
      console.error('Failed to fetch sports:', error.response.data);
    }
  };

  const onSubmit = () => {
    clubForm.value.validate(async (valid) => {
      if (valid) {
        try {
          if (isEditing.value) {
            const response = await axios.put(`http://lara-rest.test/api/clubs/${form.id}`, form);
            const index = clubs.value.findIndex(club => club.id === response.data.data.id);
            fetchClubs();
            isEditing.value = false;
          } else {
            const response = await axios.post('http://lara-rest.test/api/clubs', form);
            clubs.value.push(response.data.data);
          }
          resetForm();
        } catch (error) {
          console.error('Failed to save club:', error.response.data);
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
  };

  const editClub = (club) => {
    form.id = club.id;
    form.name = club.name;
    form.location = club.location;
    form.manager_name = club.manager_name;
    form.manager_email = club.manager_email;
    form.sports = [];
    club.sports.forEach(element => {
      form.sports.push(element.id);
    });
    isEditing.value = true;
  };

  const deleteClub = async (id) => {
    try {
      await axios.delete(`http://lara-rest.test/api/clubs/${id}`);
      clubs.value = clubs.value.filter(club => club.id !== id);
    } catch (error) {
      console.error('Failed to delete club:', error.response.data);
    }
  };

  onMounted(() => {
    fetchClubs();
    fetchSports();
  });

  return {
    form,
    clubs,
    sports,
    rules,
    clubForm,
    isEditing,
    onSubmit,
    resetForm,
    editClub,
    deleteClub
  };
}
};
</script>

<style scoped>
.clubs-container {
display: flex;
flex-direction: row;
justify-content: space-between;
align-items: flex-start;
gap: 2%;
margin: 0 4%;
}

.form-wrapper{
width: 100%;
flex: 1;
}

.clubs-wrapper{
flex: 2;
text-align: center;
}

.list-card {
display: flex;
gap: 2%; 
flex-wrap: wrap;
justify-content: flex-start; 
}

.club-item {
width: 31%;
text-align: left;
}

.el-tag{
margin: 0 5px;
}
</style>
