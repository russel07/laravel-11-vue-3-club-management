<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <ManageUser :userType="'Club Admin'" :addButton="'Add Club Admin'"/>
      </el-main>
      <el-footer style="text-align: center;">Copyrigt &copy; by Md. Russel hussain</el-footer>
    </el-container>
  </div>
</template>

<script>
  import ManageUser from './ManageUser.vue';
  export default {
    name: 'ClubAdmin',
    components: {
      ManageUser
    },
  }
</script>