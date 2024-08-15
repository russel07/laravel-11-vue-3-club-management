<template>
  <Header :pageTitle="'Manage Athlete'" @search="onSearch" />
  <div class="athletes-container">
    <div class="list-card">
      <el-card class="athlete-item" v-for="athlete in filteredAthletes" :key="athlete.id">
        <template #header>{{ athlete.name }}</template>
        <p><strong>Name:</strong> {{ athlete.name }} </p>
        <p><strong>Gender:</strong> {{ athlete.gender }} </p>
        <p><strong>Year of Birth:</strong> {{ athlete.birth_year }} </p>
        <p><strong>Email:</strong> {{ athlete.email }}</p>
        <div v-if="athlete.team">
          <strong>Team:</strong>
          <el-tag type="success">{{ athlete.team.name }}</el-tag>
        </div>
        <template #footer>
          <el-button size="small" @click="viewTest(athlete.id)">Test</el-button>
          <el-popconfirm
            confirm-button-text="Yes"
            cancel-button-text="No"
            icon-color="#626AEF"
            title="Are you sure to delete this?"
            @confirm="deleteAthlete(athlete.id)"
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
  import { useRouter } from 'vue-router';
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
      const queryArg          = ref(null);
      const { startLoading, stopLoading } = loader();
      const alert             = inject('alert');
      const { success, error }= alert();
      const router            = useRouter();

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

      const filterAthletes = () => {
        filteredAthletes.value = athletes.value.filter(athlete =>
          !queryArg.value || athlete.name.toLowerCase().includes(queryArg.value.toLowerCase())
        );
      };

      const viewTest = (athleteId) => {
        const path = '/test/'+athleteId;
        router.push(path);
      }

      const onSearch = (q) => {
        queryArg.value = q;
        filterAthletes();
      }

      onMounted(() => {
        fetchAthletes();
      });
      
      return {
        athletes,
        filteredAthletes,
        onSearch,
        viewTest
      }
    }
  }
</script>