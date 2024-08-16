<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Header :pageTitle="'Manage Sports'" :addButton="'Add Sport'" @add-new="onAddNew" :show_search="true" @search="onSearch" />
        <div class="list-container">
          <div class="list-card">
            <el-card class="list-item sports" v-for="sport in filteredSports" :key="sport.id">
              {{ sport.name }}
              <template #footer>
                <el-button size="small" @click="handleEdit(sport)">Edit</el-button>
                <el-popconfirm
                  confirm-button-text="Yes"
                  cancel-button-text="No"
                  icon-color="#626AEF"
                  title="Are you sure to delete this?"
                  @confirm="handleDelete(sport.id)"
                >
                  <template #reference>
                    <el-button type="danger" size="small">Delete</el-button>
                  </template>
                </el-popconfirm>
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
      </el-main>
      <el-footer>
        <Footer/>
      </el-footer>
    </el-container>
  </div>
</template>
<script>
import { inject, reactive, ref, onMounted } from 'vue';
import Header from "./Header";
import Footer from "./Footer";
import http from "../http/http-common";
import {loader} from '../composables/Loader';

export default {
  name: 'Sports',
  components: {
      Header,
      Footer
  },
  setup() {
    const form = reactive({
      id: null,
      name: '',
    });
    const alert = inject('alert');
    const { success, error } = alert();
    const sports = ref([]);
    const filteredSports = ref([]);
    const queryArg = ref(null);
    const isEditing = ref(false);
    const sportForm = ref(null);
    const dialogFormVisible = ref(false);
    const dialogTitle = ref('');
    const { startLoading, stopLoading } = loader();

    const rules = reactive({
      name: [
        { required: true, message: 'Please input sport name', trigger: 'blur' }
      ]
    });

    const fetchSports = async () => {
      startLoading('Fetching sports...');
      try {
        const response = await http.get("sports");
        sports.value = response.data.data;
        filteredSports.value = response.data.data;
      } catch (error) {
        console.error('Failed to fetch sports:', error.response.data);
      }
      stopLoading();
    };

    const onAddNew = () => {
      getTitle();
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
              startLoading('Updating sports...');
              const response = await http.put(`sports/${form.id}`, form);
              if(response.data.success) {
                fetchSports();
                success(response.data.message);
              } else {
                error(response.data.message);
              }
            } else {
              startLoading('Storing sports...');
              const response = await http.post('sports', form);
              if(response.data.success) {
                sports.value.push(response.data.data);
                success(response.data.message);
              } else {
                error(response.data.message);
              }
            }
            stopLoading();
            resetForm();
          } catch (error) {
           // error(error.response);
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
      getTitle();
    };

    const handleEdit = (sport) => {
      form.id = sport.id;
      form.name = sport.name;
      isEditing.value = true;
      getTitle();
      dialogFormVisible.value = true;
    };

    const handleDelete = async (id) => {
      try {
        startLoading('Deleting sports...');
        const response = await http.delete(`sports/${id}`);
        if(response.data.success) {
          success(response.data.message);
          fetchSports();
        } else {
          error(response.data.message);
        }
        
      } catch (error) {
        error('Failed to delete sport:', error.response.data);
      }
      stopLoading();
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
