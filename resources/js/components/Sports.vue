<template>
    <Header />
    <div class="sports-container">
      <el-card class="form-card" style="max-width: 500px; margin: 20px auto;">
        <el-form :model="form" :rules="rules" ref="sportForm" label-width="auto" label-position="top">
          <el-form-item label="Sport Name" prop="name">
            <el-input v-model="form.name" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
            <el-button @click="resetForm">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-card>
      <el-card class="list-card" style="max-width: 600px; margin: 20px auto;">
        <el-table 
            :data="sports"
            :default-sort="{ prop: 'name', order: 'ascending' }">
          <el-table-column prop="name" label="Sport Name" sortable />
          <el-table-column label="Operations" style="width: 20%;">
            <template #default="scope">
                <el-button size="small" @click="handleEdit(scope.row)">Edit</el-button>
                <el-button size="small" type="danger" @click="handleDelete(scope.row.id)">Delete</el-button>
            </template>
            </el-table-column>
        </el-table>
      </el-card>
    </div>
  </template>
  
  <script>
  import { reactive, ref } from 'vue';
  import Header from "./Header";
  import axios from 'axios';
  
  export default {
    name: 'Sports',
    components: {
        Header,
    },
    setup() {
      const form = reactive({
        id: null,
        name: '',
      });
  
      const sports = ref([]);
      const isEditing = ref(false);
      const sportForm = ref(null);
  
      const rules = reactive({
        name: [
          { required: true, message: 'Please input sport name', trigger: 'blur' }
        ]
      });
  
      const fetchSports = async () => {
        try {
          const response = await axios.get('http://lara-rest.test/api/sports');
          sports.value = response.data.data;
        } catch (error) {
          console.error('Failed to fetch sports:', error.response.data);
        }
      };
  
      const onSubmit = () => {
        sportForm.value.validate(async (valid) => {
          if (valid) {
            try {
              if (isEditing.value) {
                const response = await axios.put(`http://lara-rest.test/api/sports/${form.id}`, form);
                const index = sports.value.findIndex(sport => sport.id === response.data.id);
                sports.value.splice(index, 1, response.data.data);
                isEditing.value = false;
              } else {
                const response = await axios.post('http://lara-rest.test/api/sports', form);
                sports.value.push(response.data);
              }
              resetForm();
            } catch (error) {
              console.error('Failed to save sport:', error.response.data);
            }
          }
        });
      };
  
      const resetForm = () => {
        form.id = null;
        form.name = '';
        isEditing.value = false;
        sportForm.value.resetFields();
      };
  
      const handleEdit = (sport) => {
        form.id = sport.id;
        form.name = sport.name;
        isEditing.value = true;
      };
  
      const handleDelete = async (id) => {
        try {
          await axios.delete(`http://lara-rest.test/api/sports/${id}`);
          sports.value = sports.value.filter(sport => sport.id !== id);
        } catch (error) {
          console.error('Failed to delete sport:', error.response.data);
        }
      };
  
      fetchSports();
  
      return {
        form,
        sports,
        rules,
        sportForm,
        isEditing,
        onSubmit,
        resetForm,
        handleEdit,
        handleDelete
      };
    }
  };
  </script>
  
  <style scoped>
  .sports-container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  
  .form-card, .list-card {
    width: 100%;
  }
  
  .el-table th, .el-table td {
    text-align: center;
  }
  </style>
  