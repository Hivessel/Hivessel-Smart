<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">
                
                <div class="card-body">
                    Grade Levels
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGradeModal">
                            <i class="fas fa-plus"></i> Add Grade
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
        
        <!-- Create Grade -->
        <div class="modal fade" id="createGradeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Grade</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Grade</label>
                            <input type="text" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.grade" />
                            
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
    </div>
</template>
<script setup>
import Layout from '../../Shared/Layout.vue';
import { useForm, router } from '@inertiajs/vue3';

defineOptions({
    layout: Layout,
})

const form = useForm({
    grade: '',
});

const save = () => {
    form.post(route('admin.grades.store'), {
        onSuccess: (response) => {
            toastr.success('Grade saved successfully.');
            $('#createGradeModal').modal('hide'); // Hide the modal after saving
            form.reset(); // Reset the form after saving
        
        },
        onError: (error) => {
            toastr.error(error?.grade || 'An error occurred while saving the grade.');
        }
    });
}

const close = () => {
    form.reset();
    $('#createGradeModal').modal('hide'); // Hide the modal when closing
}
</script>