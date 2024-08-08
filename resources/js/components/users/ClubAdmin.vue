<template>
  <div class="wrapper">
    <ManageUser :userType="'Club Admin'"/>
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