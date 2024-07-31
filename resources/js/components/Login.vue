<template>
  <Header />
  <div class="wrapper">
    <h1>Login</h1>
  </div>
</template>

<script>
  import { ref } from 'vue';
  import Header from "./Header";
  export default {
    name: 'Login',
    components: {
      Header,
    },

    setup(){

      return {

      }
    }
  }
</script>