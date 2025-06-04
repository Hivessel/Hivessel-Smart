<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">

                <div class="card-body">
                    Subjects
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                            <i class="fas fa-plus"></i> Add Subjects
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->

        <!-- Create Grade -->
        <div class="modal fade" id="createSubjectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Subjects</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="" class="form-label">Grade</label>
                                <Multiselect class="border-1" data-width="100%" track-by="id" :options="apiData.grades" placeholder="-- Select Grade --" v-model="selectedGrade" label="level"></Multiselect>

                                <!-- <Multiselect data-width="100%" multiple track-by="id" searchable :close-on-select="false" :options="apiData.grades" placeholder="-- Select Grade --" v-model="test" label="level"></Multiselect> -->
                            </div>

                            <div class="form-group">
                                <label for="" class="form-label">Subjects</label>
                                <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock"
                                    v-model="form.subject" />

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="close">Close</button>
                        <button type="button" class="btn btn-primary" @click.prevent="save"
                            :disabled="form.processing">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import axios from 'axios';
import Layout from '../../Shared/Layout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, reactive, ref } from 'vue';
import Multiselect from 'vue-multiselect';
defineOptions({
    layout: Layout,
})

const apiData = reactive({
    grades: []
});

const selectedGrade = ref(null);
const test = ref([]);

const form = useForm({
    grade_id: computed(() => selectedGrade.value?.id || null),
    subject: ''

});

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

onMounted(() => {
    fetchGrades();
})

const save = () => {
    form.post(route('admin.subjects.store'), {
        onSuccess: (response) => {
            toastr.success('Subject saved successfully.');
            $('#createSubjectModal').modal('hide'); // Hide the modal after saving
            form.subject = ''; // Reset the form after saving
            selectedGrade.value = null;
        },
        onError: (errors) => {
            if (errors.grade_id) {
                toastr.error(errors.grade_id);
            }
            if (errors.subject) {
                toastr.error(errors.subject);
            }
        }
    });
}

const close = () => {
    form.reset();
    $('#createSubjectModal').modal('hide'); // Hide the modal when closing
}
</script>
