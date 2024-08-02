<template>
  <el-header class="header-area">
    <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        @select="handleSelect"
        :router="true"
    >
      <el-menu-item index="0">
        <img style="width: 60px" src="../../images/logo.png" alt="App logo" />
      </el-menu-item>

      <el-sub-menu index="1">
        <template #title>Manage</template>
        <el-menu-item index="sports">Sports</el-menu-item>
        <el-menu-item index="clubs">Club</el-menu-item>
        <el-menu-item index="teams">Team</el-menu-item>
      </el-sub-menu>

      <el-sub-menu index="2">
        <template #title>Russel Hussain</template>
        <el-menu-item index="change-password">Change Pasword</el-menu-item>
        <el-menu-item index="logout">Logout</el-menu-item>
      </el-sub-menu>
    </el-menu>
    <div class="h-6" />
  </el-header>
</template>

<script>
import {ref} from "vue";
export default {
  name: 'Header',
  setup(){
    const activeIndex = ref('/')
    const handleSelect = (key, keyPath) => {
      activeIndex.value = key
    }
    return {
      activeIndex,
      handleSelect
    }
  }
}
</script>

<style>
.flex-grow {
  flex-grow: 1;
}
</style>