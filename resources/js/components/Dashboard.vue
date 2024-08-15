<template>
  <div v-if="'Athlete' === userType">
    <Test/>
  </div>

  <div v-else>
    <Header />
    <div class="dashboard-container">
      <div class="list-card" v-if="userType === 'Coach'">
        <el-card class="team-item" v-for="team in teams" :key="team.id">
          <template #header>{{ team.name }}</template>
          <p><strong>Club:</strong> {{ team.club.name }}</p>
          <p><strong>Coach:</strong> {{ team.coach_name }}</p>
          <p><strong>Email:</strong> {{ team.coach_email }}</p>
          <p><strong>Sports:</strong> <el-tag type="success">{{ team.sport.name }}</el-tag></p>
        </el-card>
      </div>
      <div class="list-card" v-else-if="userType === 'Club Admin'">
        <p>Welcome</p>
      </div>
      <div class="list-card" v-else>
        <p>Welcome</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import Header from "./Header";
import Test from "./test/Test";
import http from "../http/http-common";
import {loader} from '../composables/Loader';

export default {
  name: 'Admin',
  components: {
    Header,
    Test
  },
  setup() {
    const { startLoading, stopLoading } = loader();

    const loggedInUser = ref([]);
    const userType = ref('');
    const teams = ref([]);

    const getLoggedInUser = () => {
      loggedInUser.value = JSON.parse(localStorage.getItem('_GymAppLoggedInUser'));
      userType.value = loggedInUser.value.user_type;
    };

    const loadDashboardData = () => {
      if( userType.value === 'Coach') {
        fetchTeams();
      } else if( userType.value === 'Coach') {
        fetchTeams();
      }
    }

    const fetchTeams = async () => {
      startLoading('Fetching teams...');
      try {
        const response = await http.get('teams');
        teams.value = response.data.data;
        filteredTeams.value = response.data.data;
      } catch (err) {
        //error(err.response.data.message);
      }
      stopLoading();
    };

    onMounted(() => {
      getLoggedInUser();
      loadDashboardData();
    });

    return {
      userType,
      teams
    }
  }
}
</script>