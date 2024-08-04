<template>
  <Header :pageTitle="'Sports List'" @add-new="onAddNew" @search="onSearch" />
  <div class="sports-container">
    <div class="list-card">
      <el-card class="sports-item" v-for="sport in filteredSports" :key="sport.id">
        {{ sport.name }}
        <template #footer>
          <el-button size="small" @click="handleEdit(sport)">Edit</el-button>
              <el-button size="small" type="danger" @click="handleDelete(sport.id)">Delete</el-button>
        </template>
      </el-card>
    </div>
  </div>
  <el-dialog v-model="dialogFormVisible" class="form-modal" :title="dialogTitle" max-width="500">
    <el-form :model="form" :rules="rules" ref="sportForm" label-width="auto" label-position="top">
      <el-form-item label="Sport Name" prop="name">
        <el-input v-model="form.name" />
      </el-form-item>
    </el-form>
    <template #footer>
      <div class="dialog-footer">
        <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Add' }}</el-button>
            <el-button @click="resetForm">Cancel</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script>
import { reactive, ref, onMounted } from 'vue';
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
    const filteredSports = ref([]);
    const queryArg = ref(null);
    const isEditing = ref(false);
    const sportForm = ref(null);
    const dialogFormVisible = ref(false);
    const dialogTitle = ref('');

    const rules = reactive({
      name: [
        { required: true, message: 'Please input sport name', trigger: 'blur' }
      ]
    });

    const fetchSports = async () => {
      try {
        const response = await axios.get('http://lara-rest.test/api/sports');
        sports.value = response.data.data;
        filteredSports.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch sports:', error.response.data);
      }
    };

    const onAddNew = () => {
      dialogFormVisible.value = true;
    }

    const filterSports = () => {
      filteredSports.value = sports.value.filter(sport =>
        !queryArg.value || sport.name.toLowerCase().includes(queryArg.value.toLowerCase())
      );
    };

    const onSearch = (q) => {
      queryArg.value = q;
      filterSports();
    }

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
      dialogFormVisible.value = false;
    };

    const handleEdit = (sport) => {
      form.id = sport.id;
      form.name = sport.name;
      isEditing.value = true;
      dialogFormVisible.value = true;
    };

    const handleDelete = async (id) => {
      try {
        await axios.delete(`http://lara-rest.test/api/sports/${id}`);
        sports.value = sports.value.filter(sport => sport.id !== id);
      } catch (error) {
        console.error('Failed to delete sport:', error.response.data);
      }
    };

    onMounted(() => {
      fetchSports();
      getTitle();
    });

    const getTitle = () => {
      dialogTitle.value = isEditing.value ? 'Update ' : 'Add ';
      dialogTitle.value += ' Sport';
    }

    return {
      form,
      sports,
      rules,
      sportForm,
      isEditing,
      onSubmit,
      resetForm,
      handleEdit,
      handleDelete,
      getTitle,
      filteredSports,
      onAddNew,
      onSearch,
      dialogFormVisible,
      dialogTitle
    };
  }
};
</script>

<style scoped>
.sports-container {
  margin: 2% 4%;
}

.list-card{
  display: flex;
  gap: 2%; 
  flex-wrap: wrap;
  justify-content: flex-start; 
}
.sports-item{
  width: 250px;
  margin-top: 3%;
}
</style>
