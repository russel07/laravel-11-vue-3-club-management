<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <ManageUser :userType="'Admin'" :addButton="'Add New Admin'"/>
      </el-main>
      <el-footer style="text-align: center;">Copyrigt &copy; by Md. Russel hussain</el-footer>
    </el-container>
  </div>
</template>

<script>
  import ManageUser from './ManageUser.vue';
  export default {
    name: 'Admin',
    components: {
      ManageUser
    },
  }
</script>