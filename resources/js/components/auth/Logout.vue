<template>
    <div>Logging out...</div>
  </template>
  
  <script>
  import { useRouter } from 'vue-router';
  
  export default {
    name: 'Logout',
    setup() {
      const router = useRouter();
      
      // Clear local storage and redirect to the login page
      localStorage.removeItem('_GymAppUserToken');
      localStorage.removeItem('_GymAppLoggedInUser');
      router.push({ name: 'Login' });
    }
  };
  </script>
  