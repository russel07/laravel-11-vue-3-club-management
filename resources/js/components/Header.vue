<template>
  <el-header class="header-area">
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
  </el-header>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
export default {
  name: 'Header',
  setup(){
    const activeIndex = ref('/');
    const loggedInUser = ref([]);
    const router = useRouter();

    const handleSelect = (key, keyPath) => {
      activeIndex.value = key
    }

    const getLoggedInUser = () => {
      loggedInUser.value = JSON.parse(localStorage.getItem('_GymAppLoggedInUser'));
      console.log(loggedInUser);
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
      logout
    }
  }
}
</script>

<style>
.flex-grow {
  flex-grow: 1;
}
</style>