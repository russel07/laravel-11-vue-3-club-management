<template>
    <Header />
    <div class="clubs-container">
      <el-card class="form-card">
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
          <el-form-item label="Sport ID" prop="sports">
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

      <el-card class="list-card">
        <el-table :data="clubs" style="width: 100%">
          <el-table-column prop="name" label="Club Name" />
          <el-table-column prop="location" label="Location" />
          <el-table-column prop="manager_name" label="Manager Name" />
          <el-table-column prop="manager_email" label="Manager Email" />
          <el-table-column  label="Sports">
            <template #default="scope">
              <ul>
                <li v-for="sport in scope.row.sports" :key="sport.id">{{ sport.name }}</li>
              </ul>
            </template>
          </el-table-column>
          <el-table-column label="Actions">
            <template #default="scope">
              <el-button @click="editClub(scope.row)" type="primary" size="small">Edit</el-button>
              <el-button @click="deleteClub(scope.row.id)" type="danger" size="small">Delete</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
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

    const editClub = (club) => { console.log(club);
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
  gap: 4%;
  margin: 2% 3%;
}

.form-card{
  flex: 1;
}
.list-card {
  flex: 2;
}
.form-card, .list-card {
  width: 100%;
}

.el-table th, .el-table td {
  text-align: center;
}
</style>