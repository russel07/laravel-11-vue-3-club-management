<template>
    <Header :pageTitle="'Manage Test'" :addButton="'Insert Test'" @add-new="onAddNew" @search="onSearch" />
    <div class="tests-container">
      <div class="list-card">
        <el-card class="test-item" v-for="(test, index) in filteredTests" :key="test.id">
            <template #header>Test {{ index+1 }}</template>
            <p><strong>Test Date:</strong> {{ formatDate(test.test_date) }} </p>
            <p><strong>Last Update:</strong> {{ formatDate(test.updated_at) }} </p>
            <p><strong>Test Status:</strong>
                <el-tag v-if="getStatus(test)" type="success">Complete</el-tag>
                <el-tag v-else type="warning">Incomplete</el-tag>
            </p>
            <template #footer>
                <el-button type="primary" size="small" @click="editTestResult(test)" round>Edit</el-button>
                <el-button type="success" size="small" @click="viewResult(test)" round>Result</el-button>
                <el-button type="info" :disabled="!getStatus(test)" size="small" @click="viewGraph(test)" round>Chart</el-button>
                <el-popconfirm
                    confirm-button-text="Yes"
                    cancel-button-text="No"
                    icon-color="#626AEF"
                    title="Are you sure to delete this?"
                    @confirm="deleteAthlete(athlete.id)"
                >
                    <template #reference>
                    <el-button type="danger" size="small" round>Delete</el-button>
                    </template>
                </el-popconfirm>
            </template>
        </el-card>
      </div>
    </div>
    <el-dialog v-model="dialogFormVisible" class="form-modal" :title="dialogTitle">
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

    <el-dialog v-model="resultDialog" class="form-modal" title="Test Results">
        <p v-for="(label, key) in test_label"><strong>{{ label }}:</strong>
            <el-tag v-if="test_results[key]" type="primary">{{ test_results[key] }}</el-tag> </p>
    </el-dialog>
</template>

<script>
  import { inject, ref, reactive, onMounted } from 'vue';
  import { useRoute } from 'vue-router'; 
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
        const alert                         = inject('alert');
        const { success, error }            = alert();
        const route                         = useRoute(); 
        const tests                         = ref([]);
        const filteredTests                 = ref([]);
        const queryArg                      = ref(null);
        const athleteId                     = ref(null);
        const dialogFormVisible             = ref(false);
        const dialogTitle                   = ref('');
        const isEditing                     = ref(false);
        const selectedTest                  = ref(null);
        const resultDialog                  = ref(false);
        const test_results                  = ref([]);

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
            user_id: athleteId,
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

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
            const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
            const formattedDate = date.toLocaleDateString('en-US', dateOptions);
            const formattedTime = date.toLocaleTimeString('en-US', timeOptions);
            return `${formattedDate} at ${formattedTime}`;
        }
  
        const fetchTests = async () => {
            startLoading('Fetching tests...');
            try {
                const response = await http.get(`athlete-tests/${athleteId.value}`);
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

        const getStatus = (test) => {
            let complete = false;
            if(test.test_results.length) {
                let testResultsObject = JSON.parse(test.test_results);
                let has_empty = false;

                Object.keys(form.test_results).forEach((key) => { 
                    if ( !testResultsObject[key] ) {
                        has_empty = true;
                    }
                });

                if( ! has_empty ) {
                    complete =  true;
                }
            }
            
            return complete;
        }
  
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

            if(test.test_results.length) {
                let testResultsObject = JSON.parse(test.test_results);
                Object.keys(form.test_results).forEach((key) => { 
                    if (testResultsObject[key] !== undefined) {
                        form.test_results[key] = testResultsObject[key];
                    }
                });
            }
           
            isEditing.value = true;
            getTitle();
            dialogFormVisible.value = true;
        };

        const viewResult = (test) => {
            if(test.test_results.length) {
                test_results.value = JSON.parse(test.test_results);
                Object.keys(test_label).forEach((key) => {
                    if ( ! test_results.value[key] ) {
                        test_results.value[key] = null;
                    }
                });
            } else {
                Object.keys(test_label).forEach((key) => {
                    if ( ! test_results.value[key] ) {
                        test_results.value[key] = null;
                    }
                });
            }
            resultDialog.value =  true;
        };

        const viewGraph = (test) => {
            const path = `#/graph/${test.id}`;
            const fullPath = `${window.location.origin}${path}`;
            window.open(fullPath, '_blank');
        }

        const onSubmit = async () => {
            if (isEditing.value) {
                startLoading('Storing result...');
                const response = await http.put(`test-results/${form.id}`, form);
                stopLoading();
                if(response.data.success) {
                    fetchTests();
                    success(response.data.message);
                } else {
                    error(response.data.message);
                }
                isEditing.value = false;
            } else {
                startLoading('Storing result...');
                const response = await http.post('test-results', form);
                stopLoading();
                
                if(response.data.success) {
                    fetchTests();
                    success(response.data.message);
                } else {
                    error(response.data.message);
                }
                fetchTests();
            }
            resetForm();
        };

        const onAddNew = () => {
            getTitle();
            dialogFormVisible.value = true;
        }

        const resetForm = () => {
            form.id = null;
            form.user_id = athleteId.value;
            form.test_date = new Date().toISOString().split('T')[0];

            Object.keys(form.test_results).forEach((key) => { 
                form.test_results[key] = '';
            });

            isEditing.value = false;
            getTitle();
            dialogFormVisible.value = false;
        }

      onMounted(() => {
        let user = JSON.parse(localStorage.getItem('_GymAppLoggedInUser'));
        if( !route.params.athleteId && 'Athlete' === user.user_type) {
            athleteId.value = user.id;
        } else {
            athleteId.value = route.params.athleteId ? route.params.athleteId : '';
        }
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
        editTestResult,
        getStatus,
        resetForm,
        resultDialog,
        viewResult,
        test_results,
        viewGraph,
        formatDate
      }
    }
  }
  </script>