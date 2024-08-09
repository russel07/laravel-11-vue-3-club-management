<template>
    <Header :pageTitle="'Manage Test'" :addButton="'Insert Test'" @add-new="onAddNew" @search="onSearch" />
    <div class="tests-container">
      <div class="list-card">
        <el-card class="test-item" v-for="(test, index) in filteredTests" :key="test.id">
            <template #header>Test {{ index+1 }}</template>
            <p><strong>Test Date:</strong> {{ test.test_date }} </p>
            <p><strong>Last Update:</strong> {{ test.updated_at }} </p>
            <template #footer>
                <el-button size="small" @click="editTestResult(test)">Edit</el-button>
                <el-button size="small" @click="viewGraph(test.id)">View Graph</el-button>
                <el-popconfirm
                    confirm-button-text="Yes"
                    cancel-button-text="No"
                    icon-color="#626AEF"
                    title="Are you sure to delete this?"
                    @confirm="deleteAthlete(athlete.id)"
                >
                    <template #reference>
                    <el-button type="danger" size="small">Delete</el-button>
                    </template>
                </el-popconfirm>
            </template>
        </el-card>
      </div>
    </div>
    <el-dialog v-model="dialogFormVisible" class="form-modal" :title="dialogTitle" width="500">
        <el-form :model="form" label-width="180px" style="max-width: 450px;">
            <el-form-item label="Test Date" prop="test_date" label-position="top">
                <el-date-picker v-model="form.test_date" type="date" placeholder="Select date"></el-date-picker>
            </el-form-item>

            <!-- Dropdown to select the test -->
            <el-form-item label="Select Test" label-position="top">
                <el-select v-model="selectedTest" placeholder="Select a test">
                    <el-option
                        v-for="(label, key) in test_label"
                        :key="key"
                        :label="label"
                        :value="key"
                    ></el-option>
                </el-select>
            </el-form-item>
            
            <!-- Render the input field for the selected test -->
            <el-form-item v-if="selectedTest" :label="test_label[selectedTest]" label-position="top">
                <el-input type="number" min="0" v-model="form.test_results[selectedTest]" placeholder="Enter the result"></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
        <div class="dialog-footer">
            <el-button type="primary" @click="onSubmit">{{ isEditing ? 'Update' : 'Insert' }}</el-button>
                <el-button @click="resetForm">Cancel</el-button>
        </div>
        </template>
    </el-dialog>
  </template>
  
  <script>
  import { inject, ref, reactive, onMounted } from 'vue';
  import { useRoute } from 'vue-router';  // Correct import
  import Header from "../Header";
  import http from "../../http/http-common";
  import { loader } from '../../composables/Loader';
  
  export default {
    name: 'Test',
    components: {
      Header,
    },
  
    setup() {
        const { startLoading, stopLoading } = loader();
        const alert = inject('alert');
        const { success, error } = alert();
        const route = useRoute(); 
        const tests = ref([]);
        const filteredTests = ref([]);
        const queryArg = ref(null);
        const athleteId = route.params.athleteId;
        const dialogFormVisible = ref(false);
        const dialogTitle = ref('');
        const isEditing = ref(false);
        const selectedTest = ref(null);

        const test_label = {
            'test_2': "Yläraajojen kestovoima (Leuanveto)",
            'test_11': "Liikkuvuus (Valakyykky Kepillä)",
            'test_5': "Alaraajojen voima/hallinta (Yhden jalan kyykky)",
            'test_8': "Alaraajan lihaksiston liikkuvuus (Aktiivinen suoran jalan nosto selin)",
            'test_4': "Keski- ja ylävartalon voima (Jalkojen nosto riipunnasta)",
            'test_1': "Ylävartalon kestovoima (Etunojapunnerrus)",
            'test_6': "Alaraajojen Räjähtävyys (Vauhditon Pituus)",
            'test_10': "Vartalonkierto (Lying Trunk Rotation)",
            'test_7': "Nilkan liikkuvuus ja puolierot (Nilkan liikkuvuustesti)",
            'test_12': "Hartiarenkaan liikkuvuus (Olkanivel, Lapa, solisluu, Rintakehä)",
            'test_3': "Yläraajojen vetävä voima ja tukilihasten staattinen kesto (Kulmaveto)",
            'test_9': "Alaraajojen ojennusliikkuvuus (Reisi, Sääri, Jalkalinja - Thomas1,2&3)",
        };

        const form = reactive({
            id: null,
            user_id: route.params.athleteId,
            test_date: new Date().toISOString().split('T')[0],
            test_results:{
                'test_1': '',
                'test_2': '',
                'test_3': '',
                'test_4': '',
                'test_5': '',
                'test_6': '',
                'test_7': '',
                'test_8': '',
                'test_9': '',
                'test_10': '',
                'test_11': '',
                'test_12': '',
            },
        });
  
        const fetchTests = async () => {
            startLoading('Fetching tests...');
            try {
                const response = await http.get(`athlete-tests/${athleteId}`);
                tests.value = response.data.data;
                filteredTests.value = response.data.data;
            } catch (err) {
                error(err.response.data.message);
            }
            stopLoading();
        }

        const filterTest = () => {
            filteredTests.value = tests.value.filter(test => !queryArg.value || test.name.toLowerCase().includes(queryArg.value.toLowerCase()));
        };
  
      const onSearch = (q) => {
        queryArg.value = q;
        filterTest();
      }

      const getTitle = () => {
            dialogTitle.value = isEditing.value ? 'Update ' : 'Insert ';
            dialogTitle.value += ' Test Result';
        }

        const editTestResult = (test) => {
            form.id = test.id;
            form.test_date = test.test_date;
            let testResultsObject = JSON.parse(test.test_results);console.log(testResultsObject['test_1']);
            Object.keys(form.test_results).forEach((key) => { 
                if (testResultsObject[key] !== undefined) {
                    form.test_results[key] = testResultsObject[key];
                }
            });
            console.log(form.test_results);
            isEditing.value = true;
            getTitle();
            dialogFormVisible.value = true;
        };

        const onSubmit = async () => {
            if (isEditing.value) {
                startLoading('Storing result...');
                const response = await http.put(`test-results/${form.id}`, form);
                stopLoading();
                if(response.data.success) {
                    
                    success(response.data.message);
                } else {
                    error(response.data.message);
                }
                isEditing.value = false;
            } else {
                startLoading('Storing club...');
                const response = await http.post('test-results', form);
                stopLoading();
                
                if(response.data.success) {
                    success(response.data.message);
                } else {
                    error(response.data.message);
                }
            }
        };

    const onAddNew = () => {
        getTitle();
        dialogFormVisible.value = true;
    }
  
      onMounted(() => {
        fetchTests();
      });
  
      return {
        filteredTests,
        onSearch,
        dialogFormVisible,
        dialogTitle,
        isEditing,
        onAddNew,
        form,
        test_label,
        selectedTest,
        onSubmit,
        editTestResult
      }
    }
  }
  </script>
  <style scoped>
  .tests-container {
    margin: 2% 4%;
  }
  
  .list-card {
    display: flex;
    gap: 2%; 
    flex-wrap: wrap;
    justify-content: flex-start; 
  }
  
  .test-item {
    width: 23%;
    text-align: left;
    margin-top: 3%;
  }

  </style>