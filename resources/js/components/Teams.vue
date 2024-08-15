<template>
  <Header :pageTitle="'Manage Team'" :addButton="'Register Team'" @add-new="onAddNew" @search="onSearch" />
  <div class="teams-container">
    <div class="list-card">
      <el-card class="team-item" v-for="team in filteredTeams" :key="team.id">
        <template #header>{{ team.name }}</template>
        <p><strong>Club:</strong> {{ team.club.name }}</p>
        <p><strong>Coach:</strong> {{ team.coach_name }}</p>
        <p><strong>Email:</strong> {{ team.coach_email }}</p>
        <p><strong>Sports:</strong> {{ team.sport.name }}</p>
        <template #footer>
          <el-button size="small" @click="editTeam(team)">Edit</el-button>
          <el-popconfirm
            confirm-button-text="Yes"
            cancel-button-text="No"
            icon-color="#626AEF"
            title="Are you sure to delete this?"
            @confirm="deleteTeam(team.id)"
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

      <el-form-item v-if="!isEditing" label="Coach Email" prop="coach_email">
        <el-input v-model="form.coach_email" @blur="getCoachInfo"/>
      </el-form-item>

      <el-form-item v-if="!isEditing" label="Coach Name" prop="coach_name">
        <el-input v-model="form.coach_name"/>
      </el-form-item>

      <el-form-item v-if="!form.coach_id && !isEditing" label="Password" prop="password">
        <el-input v-model="form.password"/>
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
import Header from "./Header";
import http from "../http/http-common";
import {loader} from '../composables/Loader';

export default {
  name: 'Teams',
  components: {
    Header,
  },
  setup() {
    const form = reactive({
      id: null,
      name: '',
      coach_id: '',
      coach_name: '',
      coach_email: '',
      sport_id: '',
      club_id: '',
      password: ''
    });
    const alert = inject('alert');
    const { success, error } = alert();
    const teams = ref([]);
    const filteredTeams = ref([]);
    const queryArg = ref(null);
    const sports = ref([]);
    const clubs = ref([]);
    const coaches = ref([]);
    const isEditing = ref(false);
    const teamForm = ref(null);
    const dialogFormVisible = ref(false);
    const dialogTitle = ref('');
    const { startLoading, stopLoading } = loader();

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
      ],
      password: [
        { required: true, message: 'Please input password', trigger: 'blur' }
      ]
    });

    const fetchTeams = async () => {
      startLoading('Fetching teams...');
      try {
        const response = await http.get('teams');
        teams.value = response.data.data;
        filteredTeams.value = response.data.data;
      } catch (err) {
        error(err.response.data.message);
      }
      stopLoading();
    };

    const fetchSports = async () => {
      startLoading('Fetching sports...');
      try {
        const response = await http.get('sports');
        sports.value = response.data.data;
      } catch (err) {
        error(err.response.data.message);
      }
      stopLoading();
    };

    const fetchClubs = async (sport_id) => {
      startLoading('Fetching clubs...');
      try {
        const response = await http.get(`clubs-by-sports/${sport_id}`);
        stopLoading();
        if( response.data.success ) {
          clubs.value = response.data.data;
        } else {
          error(response.data.message);
        }
      } catch (err) {
        error(err.response.data.message);
      }
      stopLoading();
    };

    const fetchCoaches = async () => {
      startLoading('Fetching coaches...');
      try {
        const response = await http.get(`users-by-type/Coach`);
        stopLoading();
        if( response.data.success ) {
          coaches.value = response.data.data;
        } else {
          error(response.data.message);
        }
      } catch (err) {
        error(err.response.data.message);
      }
      stopLoading();
    };

    const getCoachInfo = async () => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (form.coach_email && emailRegex.test(form.coach_email)) {
        let email = form.coach_email;
        startLoading('Fetching coaches...');
        try {
          const response = await http.get(`user-by-email/${email}`);
          stopLoading();
          if( response.data.success && response.data.data ) {
            form.coach_id = response.data.data.id;
            form.coach_name = response.data.data.name;
          } else {
            form.coach_id = '';
            form.coach_name = '';
          }
        } catch (err) {
          error(err.response.data.message);
        }
        stopLoading();
      }
    };


    const onAddNew = () => {
      getTitle();
      fetchCoaches();
      dialogFormVisible.value = true;
    }

    const filterTeams = () => {
      filteredTeams.value = teams.value.filter(team =>
        !queryArg.value || team.name.toLowerCase().includes(queryArg.value.toLowerCase())
      );
    };

    const onSearch = (q) => {
      queryArg.value = q;
      filterTeams();
    }

    const onSubmit = () => {
      teamForm.value.validate(async (valid) => {
        if (valid) {
          try {
            if (isEditing.value) {
              startLoading('Updating team...');
              const response = await http.put(`teams/${form.id}`, form);
              stopLoading();
              if(response.data.success) {
                const index = teams.value.findIndex(team => team.id === response.data.data.id);
                fetchTeams();
                success(response.data.message);
              } else {
                error(response.data.message);
              }
              isEditing.value = false;
            } else {
              startLoading('Creating team...');
              const response = await http.post('teams', form);
              stopLoading();
              if(response.data.success) {
                fetchTeams();
              } else {
                error(response.data.message);
              }
            }
            resetForm();
          } catch (err) {
            error(err.response.data.message);
          }
          stopLoading();
        }
      });
    };

    const resetForm = () => {
      form.id           = null;
      form.name         = '';
      form.coach_name   = '';
      form.coach_email  = '';
      form.club_id      = '';
      form.sport_id     = '';
      form.coach_id     = '';
      isEditing.value   = false;
      teamForm.value.resetFields();
      dialogFormVisible.value = false;
      getTitle();
    };

    const editTeam = (team) => {
      form.id           = team.id;
      form.name         = team.name;
      form.coach_name   = team.coach_name;
      form.coach_email  = team.coach_email;
      form.sport_id     = team.sport_id;
      filterClubs();
      form.club_id      = team.club_id;
      form.coach_id     = team.coach_id;
      isEditing.value   = true;
      dialogFormVisible.value = true;
      getTitle();
    };

    const deleteTeam = async (id) => {
      try {
        startLoading('Deleting team...');
        const response = await http.delete(`teams/${id}`);
        stopLoading();
        if(response.data.success) {
          fetchTeams();
          success(response.data.message);
        } else {
          error(response.data.message);
        }
      } catch (err) {
        error(err.response.data.message);
      }
    };

    const filterClubs = () => {
      if (form.sport_id) {
        fetchClubs(form.sport_id);
      }
      return clubs.value;
    };

    const getTitle = () => {
      dialogTitle.value = isEditing.value ? 'Update ' : 'Add ';
      dialogTitle.value += ' Team';
    }

    onMounted(() => {
      fetchTeams();
      fetchSports();
      getTitle();
    });

    return {
      form,
      teams,
      sports,
      clubs,
      coaches,
      rules,
      teamForm,
      isEditing,
      onSubmit,
      resetForm,
      editTeam,
      deleteTeam,
      filterClubs,
      filteredTeams,
      onAddNew,
      onSearch,
      dialogFormVisible,
      dialogTitle,
      getCoachInfo
    };
  }
};
</script>
