<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <ManageUser :userType="'Club Admin'" :addButton="'Add Club Admin'"/>
      </el-main>
      <el-footer>
        <Footer/>
      </el-footer>
    </el-container>
  </div>
</template>

<script>
  import ManageUser from './ManageUser.vue';
  import Footer from "../Footer";
  export default {
    name: 'ClubAdmin',
    components: {
      ManageUser,
      Footer
    },
  }
</script>