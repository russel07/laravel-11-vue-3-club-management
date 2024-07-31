<template>
  <el-header class="header-area">
    <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        @select="handleSelect"
        :router="true"
    >
      <el-menu-item index="/">Dashboard</el-menu-item>
      <el-menu-item index="/login">Login</el-menu-item>
      <el-menu-item index="/register">Register</el-menu-item>
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

</style>