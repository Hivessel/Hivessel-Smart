<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">
                
                <div class="card-body">

                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createContentModal">
                            <i class="fas fa-plus"></i> Add Contents
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="contentTable" class="table table-striped table-hover text-center">
                            <thead class="" >
                                <tr>
                                    <th>ID</th>
                                    <th>GRADE</th>
                                    <th>SUBJECT</th>
                                    <th>QUARTER</th>
                                    <th>CONTENT</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
        
        <!-- Create Quarter -->
        <div class="modal fade" id="createContentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Contents</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Grade</label>
                            <Multiselect class="border-1" data-width="100%" track-by="id" :options="apiData.grades" placeholder="-- Select Grade --" v-model="selectedGrade" label="level"></Multiselect>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Subject</label>
                            <Multiselect class="border-1" data-width="100%" track-by="id" :options="apiData.subjects" placeholder="-- Select Subject --" v-model="selectedSubject" label="subject"></Multiselect>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Quarter</label>
                            <Multiselect class="border-1" data-width="100%" track-by="id" :options="apiData.quarters" placeholder="-- Select Quarter --" v-model="selectedQuarter" label="quarter"></Multiselect>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Content</label>
                            <input type="text" class="form-control" v-model="form.content" placeholder="Enter content here...">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-secondary" @click.prevent="addCompetency"><i class="fa fa-plus"></i> Add Competencies</button>
                        </div>

                        <!-- <div class="">
                            <table class="table table-borderless align-middle w-100">
                                <tbody>
                                    <tr v-for="(item, index) in form.competencies" :key="item.id || index">
                                    <td style="width: 90%;">
                                        <div class="form-group mb-0">
                                        <textarea
                                            v-model="item.competency"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Enter competency..."
                                        ></textarea>
                                        </div>
                                    </td>
                                    <td style="width: 10%;" class="text-center align-middle">
                                        <button
                                        type="button"
                                        class="btn btn-danger"
                                        @click.prevent="removeCompency(item.id)"
                                        title="Remove"
                                        >
                                        <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> -->

                        <div class="">
                            <table class="table table-borderless align-middle w-100">
                                <tbody>
                                    <tr v-for="(item, index) in form.competencies" :key="item.id || index">
                                    <td style="width: 45%;">
                                        <div class="form-group mb-0">
                                        <label for="inputPassword5" class="form-label">Competency</label>
                                        <textarea
                                            v-model="item.competency"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Enter competency..."
                                        ></textarea>
                                        </div>
                                    </td>
                                    <td style="width: 45%;">
                                        <div class="form-group mb-0">
                                        <label for="inputPassword5" class="form-label">Link/Reference</label>
                                        <textarea
                                            v-model="item.reference"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Enter reference link..."
                                        ></textarea>
                                        </div>
                                    </td>
                                    <td style="width: 10%;" class="text-center align-middle">
                                        <button
                                        type="button"
                                        class="btn btn-danger"
                                        @click.prevent="removeCompency(item.id)"
                                        title="Remove"
                                        >
                                        <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </form>

                    <!-- <pre>{{ form }}</pre> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="close">Close</button>
                    <button type="button" class="btn btn-primary" @click.prevent="save" :disabled="form.processing">Save</button>
                </div>
                </div>
            </div>
        </div>
        <EditContentModal />
    </div>
</template>
<script setup>
import { reactive, ref, onMounted, watch, computed, onBeforeUnmount, onBeforeMount } from 'vue';
import Layout from '../../Shared/Layout.vue';
import { useForm, router } from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect';
import EditContentModal from './EditContentModal.vue';
import eventBus from '../../../../Scripts/eventBus';
defineOptions({
    layout: Layout,
})

const apiData = reactive({
    grades: [],
    subjects: [],
    quarters: []
})

const selectedGrade = ref(null);
const selectedSubject = ref(null);
const selectedQuarter = ref(null);


const form = useForm({
    grade_id: computed(() => selectedGrade.value?.id || null),
    subject_id: computed(() => selectedSubject.value?.id || null),
    quarter_id: computed(() => selectedQuarter.value?.id || null),
    content: '',
    competencies: [],
    reference: ''
});

const competencies = reactive([]);


const fetchGrades = async () => {
    try{
        const response = await axios.get(route('admin.grades.all', {
            active: 1
        }));
        apiData.grades = response.data;
        
    }catch(error){
        console.log(error);
    }finally{

    }
}

const fetchQuarters = async () => {
    try{
        const response = await axios.get(route('admin.quarters.all', {
            active: 1
        }));
        apiData.quarters = response.data;
        
    }catch(error){
        console.log(error);
    }finally{

    }
}

const fetchSubjects = async (grade_id) => {
    try{
        const response = await axios.get(route('admin.subjects.all', {
            grade_id: grade_id,
            active: 1
        }));
        apiData.subjects = response.data;
        
    }catch(error){
        console.log(error);
    }finally{

    }
}

const generateUUIDv4 = () => {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    const r = Math.random() * 16 | 0;
    const v = c === 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

const addCompetency = () => {
    form.competencies.push({
        id: generateUUIDv4(),
        competency: '',
        reference: null
    })
}

const removeCompency = (id) => {
    form.competencies = form.competencies.filter((item) => item.id !== id);
}


const fetchContents = async () => {
    if ($.fn.DataTable.isDataTable("#contentTable")) {
        $("#contentTable").DataTable().ajax.reload(); // ✅ Reload existing DataTable
    } else {
        $("#contentTable").DataTable({ // ✅ Initialize only if not initialized
            responsive: true, // Enable responsive feature
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: route('admin.contents.ajax'),
            columns: [
                { data: "id" },
                { data: "grade" },
                { data: "subject" },
                { data: "quarter" },
                { data: "content" },
                { data: "action", orderable: false, searchable: false }
            ],
            language: {
                searchPlaceholder: "Search...",
                search: "",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            } 
        });
    }
};


onMounted(() => {
    fetchGrades();
    fetchQuarters();
    fetchContents();

    // Handle Delete Button Click
    $(document).on("click", ".delete-content-btn", async function () {

        if(confirm("Are you sure you want to delete this content?")) {
            const id = $(this).data("id");
            await axios.delete(route('admin.contents.destroy', {id: id}))
                .then(response => {
                    if (response.status == 200) {
                        toastr.success('Content has been deleted successfully!');
                        fetchContents();
                    }
                })
                .catch(error => {
                    console.log(error.response.data.message)
                    // toastr.error(error.response.data.message);
                });
        }
        
    });

    // Handle Edit Button Click
    $(document).on("click", ".edit-content-btn", async function () {
        const id = $(this).data("id");
        const grade_id = $(this).data("grade_id");
        const grade = $(this).data("grade");
        const subject_id = $(this).data("subject_id");
        const subject = $(this).data("subject");
        const quarter_id = $(this).data("quarter_id");
        const quarter = $(this).data("quarter");
        const content = $(this).data("content");
        const competencies = $(this).data("competencies");
        $('#editContentModal').modal('show');
        eventBus.emit('setEditContentData', { 
            id: id,
            grade_id: grade_id,
            grade: grade,
            subject_id: subject_id ,
            subject: subject,
            quarter_id: quarter_id ,
            quarter: quarter,
            content: content,
            competencies: competencies
        });
    });
})

const save = () => {
    form.post(route('admin.contents.store'), {
        onSuccess: () => {
            // Handle successful save if needed
            // router.reload();
            toastr.success('Content saved successfully.');
            $('#createContentModal').modal('hide'); // Hide the modal after saving
            form.reset(); // Reset the form after saving
            selectedGrade.value = null;
            selectedSubject.value = null;
            selectedQuarter.value = null;
            fetchContents();
        },
        onError: (errors) => {
            // Handle errors if needed
            if (errors.grade_id) {
                toastr.error(errors.grade_id);
            }
            if (errors.subject_id) {
                toastr.error(errors.subject_id);
            }

            if (errors.quarter_id) {
                toastr.error(errors.quarter_id);
            }

            if (errors.content) {
                toastr.error(errors.content);
            }

            if (errors.competencies) {
                toastr.error(errors.competencies);
            }
        }
    });
}

onBeforeMount(() => {
    eventBus.on('contentUpdated', (status) => {
        if (status === 'success') {
            toastr.success('Content updated successfully.');
            fetchContents();
        }
    });
})

onBeforeUnmount(() => {
    eventBus.off('contentUpdated');
});


watch(() => selectedGrade.value, (grade) => {
    selectedSubject.value = null;
    if(grade){
        fetchSubjects(grade.id);
    }
})

const close = () => {
    form.reset();
    $('#createContentModal').modal('hide'); // Hide the modal when closing
}
</script>