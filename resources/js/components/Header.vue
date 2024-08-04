<template>
  <el-header>
    <div class="header-content">
      <el-menu
          :default-active="activeIndex"
          class="el-menu-demo"
          mode="horizontal"
          @select="handleSelect"
          :router="true"
      >
        <el-menu-item index="/">
          <img style="width: 60px" src="../../images/logo.png" alt="App logo" />
        </el-menu-item>

        <el-sub-menu index="1">
          <template #title>User</template>
          <el-menu-item index="users/super-admin">Super Admin</el-menu-item>
          <el-menu-item index="users/club-admin">Club Admin</el-menu-item>
          <el-menu-item index="users/coach">Coach</el-menu-item>
          <el-menu-item index="users/athlete">Athlete</el-menu-item>
        </el-sub-menu>

        <el-sub-menu index="2">
          <template #title>Manage</template>
          <el-menu-item index="sports">Sports</el-menu-item>
          <el-menu-item index="clubs">Clubs</el-menu-item>
          <el-menu-item index="teams">Teams</el-menu-item>
        </el-sub-menu>

        <el-sub-menu index="3" v-if="loggedInUser">
          <template #title>{{loggedInUser.name}}</template>
          <el-menu-item index="change-password">Change Pasword</el-menu-item>
          <el-menu-item @click="logout" index="logout">Logout</el-menu-item>
        </el-sub-menu>
      </el-menu>
      <div class="h-6" />
    </div>
  </el-header>
  <div class="header-row">
    <div class="title">
      <h1>{{ pageTitle }}</h1>
    </div>
    <div class="actions">
      <el-button type="primary" @click="addNew">Add New</el-button>
      <el-input v-model="searchQuery" placeholder="Search..." class="search-input" @keyup="onSearch">
        <template #append>
        <el-button :icon="Search" @click="onSearch" />
      </template>
      </el-input>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Search } from '@element-plus/icons-vue'
export default {
  name: 'Header',
  props: ['pageTitle'],
  emits: ['add-new', 'search'],
  setup(props, { emit }){
    const activeIndex = ref('/');
    const loggedInUser = ref([]);
    const router = useRouter();
    
    const searchQuery = ref('');

    const addNew = () => {
      emit('add-new');
    };

    const onSearch = () => {
      emit('search', searchQuery.value);
    };

    const handleSelect = (key, keyPath) => {
      activeIndex.value = key
    }

    const getLoggedInUser = () => {
      loggedInUser.value = JSON.parse(localStorage.getItem('_GymAppLoggedInUser'));
    };

    const logout = () => {
      localStorage.removeItem('_GymAppUserToken');
      localStorage.removeItem('_GymAppLoggedInUser');
      router.push('/login');
    }

    onMounted(() => {
      getLoggedInUser();
    });

    return {
      activeIndex,
      handleSelect,
      loggedInUser,
      logout,
      addNew,
      onSearch,
      searchQuery,
      Search
    }
  }
}
</script>

<style scoped>
.logo {
  height: 50px;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 4%;
  border-bottom: 1px solid #ebeef5;
}

.title h1 {
  margin: 0;
}

.actions {
  display: flex;
  align-items: center;
}

.search-input {
  margin-left: 10px;
  max-width: 300px;
}
</style>