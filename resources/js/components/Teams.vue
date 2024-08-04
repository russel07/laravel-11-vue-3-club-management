<template>
  <Header />
  <div class="teams-container">
    <div class="form-wrapper">
      <h1>{{ isEditing ? 'Update' : 'Add' }} Team</h1>
      <el-card class="form-card" style="max-width: 500px;">
        <el-form :model="form" :rules="rules" ref="teamForm" label-width="auto" label-position="top">
          <el-form-item label="Sport" prop="sport_id">
            <el-select v-model="form.sport_id" placeholder="Select Sport" @change="filterClubs">
              <el-option v-for="sport in sports" :key="sport.id" :label="sport.name" :value="sport.id" />
            </el-select>
          </el-form-item>
          <el-form-item label="Club" prop="club_id">
            <el-select v-model="form.club_id" placeholder="Select Club">
              <el-option v-for="club in clubs" :key="club.id" :label="club.name" :value="club.id" />
            </el-select>
          </el-form-item>
          <el-form-item label="Team Name" prop="name">
            <el-input v-model="form.name" />
          </el-form-item>
          <el-form-item label="Coach Name" prop="coach_name">
            <el-input v-model="form.coach_name" />
          </el-form-item>
          <el-form-item label="Coach Email" prop="coach_email">
            <el-input v-model="form.coach_email" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
            <el-button @click="resetForm">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </div>

    <div class="teams-wrapper">
      <h1>Team List</h1>
      <div class="list-card">
        <el-card class="team-item" v-for="team in teams" :key="team.id">
          <template #header>{{ team.name }}</template>
          <p><strong>Coach:</strong> {{ team.coach_name }}</p>
          <p><strong>Email:</strong> {{ team.coach_email }}</p>
          <p><strong>Sports:</strong> {{ team.sport.name }}</p>
          <p><strong>Club:</strong> {{ team.club.name }}</p>
          <template #footer>
            <el-button size="small" @click="editTeam(team)">Edit</el-button>
            <el-button size="small" type="danger" @click="deleteTeam(team.id)">Delete</el-button>
          </template>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script>
import { reactive, ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Header from "./Header";

export default {
  name: 'Teams',
  components: {
    Header,
  },
  setup() {
    const form = reactive({
      id: null,
      name: '',
      coach_name: '',
      coach_email: '',
      sport_id: '',
      club_id: '',
    });

    const teams = ref([]);
    const sports = ref([]);
    const clubs = ref([]);
    const isEditing = ref(false);
    const teamForm = ref(null);

    const rules = reactive({
      name: [
        { required: true, message: 'Please input team name', trigger: 'blur' }
      ],
      coach_name: [
        { required: true, message: 'Please input coach name', trigger: 'blur' }
      ],
      coach_email: [
        { required: true, message: 'Please input coach email', trigger: 'blur' },
        { type: 'email', message: 'Please input a valid email', trigger: 'blur' }
      ],
      sport_id: [
        { required: true, message: 'Please select a sport', trigger: 'change' }
      ],
      club_id: [
        { required: true, message: 'Please select a club', trigger: 'change' }
      ]
    });

    const fetchTeams = async () => {
      try {
        const response = await axios.get('http://lara-rest.test/api/teams');
        teams.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch teams:', error.response.data);
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

    const fetchClubs = async (sport_id) => {
      try {
        const response = await axios.get(`http://lara-rest.test/api/clubs-by-sports/${sport_id}`);
        clubs.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch clubs:', error.response.data);
      }
    };

    const onSubmit = () => {
      teamForm.value.validate(async (valid) => {
        if (valid) {
          try {
            if (isEditing.value) {
              const response = await axios.put(`http://lara-rest.test/api/teams/${form.id}`, form);
              const index = teams.value.findIndex(team => team.id === response.data.data.id);
              fetchTeams();
              isEditing.value = false;
            } else {
              const response = await axios.post('http://lara-rest.test/api/teams', form);
              fetchTeams();
            }
            resetForm();
          } catch (error) {
            console.error('Failed to save team:', error.response.data);
          }
        }
      });
    };

    const resetForm = () => {
      form.id = null;
      form.name = '';
      form.coach_name = '';
      form.coach_email = '';
      form.club_id = '';
      form.sport_id = '';
      isEditing.value = false;
      teamForm.value.resetFields();
    };

    const editTeam = (team) => {
      form.id = team.id;
      form.name = team.name;
      form.coach_name = team.coach_name;
      form.coach_email = team.coach_email;
      form.sport_id = team.sport_id;
      filterClubs();
      form.club_id = team.club_id;
      isEditing.value = true;
    };

    const deleteTeam = async (id) => {
      try {
        await axios.delete(`http://lara-rest.test/api/teams/${id}`);
        teams.value = teams.value.filter(team => team.id !== id);
      } catch (error) {
        console.error('Failed to delete team:', error.response.data);
      }
    };

    const filterClubs = () => {
      if (form.sport_id) {
        fetchClubs(form.sport_id);
      }
      return clubs.value;
    };

    onMounted(() => {
      fetchTeams();
      fetchSports();
    });

    return {
      form,
      teams,
      sports,
      clubs,
      rules,
      teamForm,
      isEditing,
      onSubmit,
      resetForm,
      editTeam,
      deleteTeam,
      filterClubs
    };
  }
};
</script>

<style scoped>
.teams-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2%;
  margin: 0 4%;
}

.form-wrapper {
  width: 100%;
  flex: 1;
}

.teams-wrapper {
  flex: 2;
  text-align: center;
}

.list-card {
  display: flex;
  gap: 2%;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.team-item {
  width: 31%;
  text-align: left;
}

.el-tag {
  margin: 0 5px;
}
</style>
