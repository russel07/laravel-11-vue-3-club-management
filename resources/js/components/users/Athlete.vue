<template>
  <Header :pageTitle="'Manage Athlete'" @search="onSearch" />
  <div class="athletes-container">
    <div class="list-card">
      <el-card class="athlete-item" v-for="athlete in filteredAthletes" :key="athlete.id">
        <template #header>{{ athlete.name }}</template>
        <p><strong>Gender:</strong> {{ athlete.gender }} </p>
        <p><strong>Year of Birth:</strong> {{ athlete.birth_year }} </p>
        <p><strong>Email:</strong> {{ athlete.email }}</p>
        <div v-if="athlete.team">
          <strong>Team:</strong>
          <el-tag type="success">{{ athlete.team.name }}</el-tag>
        </div>
        <template #footer>
          <el-button size="small" @click="editClub(athlete)">Test</el-button>
          <el-popconfirm
            confirm-button-text="Yes"
            cancel-button-text="No"
            icon-color="#626AEF"
            title="Are you sure to delete this?"
            @confirm="deleteClub(athlete.id)"
          >
            <template #reference>
              <el-button type="danger" size="small">Delete</el-button>
            </template>
          </el-popconfirm>
        </template>
      </el-card>
    </div>
</div>
</template>

<script>
  import { inject, ref, onMounted } from 'vue';
  import Header from "../Header";
  import http from "../../http/http-common";
  import { loader } from '../../composables/Loader';
  export default {
    name: 'Athlete',
    components: {
      Header,
    },

    setup(){
      const athletes          = ref([]);
      const filteredAthletes  = ref([]);
      const { startLoading, stopLoading } = loader();
      const alert               = inject('alert');
      const { success, error }  = alert();

      const fetchAthletes = async () => {
        startLoading('Fetching athletes...');
        try {

          const response = await http.get('athlete-users');
          athletes.value          = response.data.data;
          filteredAthletes.value  = response.data.data;

        } catch (err) {
          error(err.response.data.message);
        }
        stopLoading();
      }
      onMounted(() => {
        fetchAthletes();
      });
      return {
        athletes,
        filteredAthletes
      }
    }
  }
</script>
<style scoped>
.athletes-container {
  margin: 2% 4%;
}

.list-card {
  display: flex;
  gap: 2%; 
  flex-wrap: wrap;
  justify-content: flex-start; 
}

.athlete-item {
  width: 23%;
  text-align: left;
  margin-top: 3%;
}

.el-tag{
  margin: 0 5px;
}
</style>