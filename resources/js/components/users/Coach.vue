<template>
  <div class="wrapper">
    <ManageUser :userType="'Coach'"/>
  </div>
</template>

<script>
  import ManageUser from './ManageUser.vue';
  export default {
    name: 'Coach',
    components: {
      ManageUser
    },
  }
</script>