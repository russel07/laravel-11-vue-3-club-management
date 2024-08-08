<template>
  <div class="wrapper">
    <ManageUser :userType="'Admin'" :addButton="'Add New Admin'"/>
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