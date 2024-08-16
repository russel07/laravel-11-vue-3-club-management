<template>
<div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Header :pageTitle="'Coach List'" @search="onSearch"/>
        <div class="list-container">
            <div class="list-card">
                <el-card class="list-item coach" v-for="user in filteredUsers" :key="user.id">
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
      </el-main>
        <el-footer>
            <Footer/>
        </el-footer>
    </el-container>
</div>
</template>
<script>
import { inject, ref, onMounted } from 'vue';
import Header from "../Header";
import Footer from "../Footer";
import http from "../../http/http-common";
import { loader } from '../../composables/Loader';
export default {
  name: "Coach",
  components: {
      Header,
      Footer
  },

  setup() {
      const alert = inject('alert');
      const { success, error } = alert();
      const { startLoading, stopLoading } = loader();
      const users = ref([]);
      const filteredUsers = ref([]);
      const queryArg = ref(null);

      const fetchUsers = async () => {
          startLoading('Fetching Coaches ...');
          try {
              const response = await http.get(`coach-users`);
              stopLoading();
              users.value = response.data.data;
              filteredUsers.value = response.data.data;
          } catch (err) {
              error(err.response.data.message);
          }
          stopLoading();
      };


      const filterUsers = () => {
          filteredUsers.value = users.value.filter(user =>
          !queryArg.value || user.name.toLowerCase().includes(queryArg.value.toLowerCase())
          );
      };

      const onSearch = (q) => {
          queryArg.value = q;
          filterUsers();
      }

      const deleteUser = async (id) => {
          try {
              startLoading('Deleting...');
              const response = await http.delete(`user/${id}`);
              stopLoading();
              if(response.data.success) {
                  success(response.data.message);
                  fetchUsers();
              } else {
                  error(response.data.message);
              }
          } catch (err) {
          error(err.response.data.message);
          }
      };

      onMounted(() => {
          fetchUsers();
      });

      return {
          filteredUsers,
          onSearch,
          deleteUser
      }
  }
}
</script>