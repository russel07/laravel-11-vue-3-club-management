<template>
  <Header />
  <div class="wrapper">
    <h1>Dashboard</h1>
  </div>
</template>

<script>
import { ref } from 'vue';
import Header from "./Header";
export default {
  name: 'Admin',
  components: {
    Header,
  },
}
</script>