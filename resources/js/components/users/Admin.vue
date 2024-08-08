<template>
  <div class="wrapper">
    <ManageUser :userType="'Admin'"/>
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