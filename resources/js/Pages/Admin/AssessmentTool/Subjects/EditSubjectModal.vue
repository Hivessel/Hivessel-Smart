<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editSubjectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Subject</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="" class="form-label">Grade</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.grade" readonly/>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label">Subject</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.subject"/>
                    </div>
                </form>
                <!-- <pre>{{ form }}</pre> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="close">Close</button>
                <button type="button" class="btn btn-primary" @click="handleSubmit">Save</button>
            </div>
            </div>
        </div>

        
    </div>
</template>
<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeMount, onBeforeUnmount } from 'vue';
import eventBus from '../../../../Scripts/eventBus';

const form = useForm({
    id: '',
    grade_id: '',
    grade: '',
    subject: '',
    active: true
});

onBeforeMount(() => {
    eventBus.on('setEditSubjectData', (data) => {
        form.id = data.id;
        form.grade_id = data.grade_id;
        form.grade = data.grade;
        form.subject = data.subject;
    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditSubjectData');
});

const handleSubmit = () => {
    form.put(route('admin.subjects.update', {
        id: form.id,
        subject: form.subject,
        active: form.active
    }), {
        onSuccess: (response) => {
            form.reset();
            $('#editSubjectModal').modal('hide');
            eventBus.emit('subjectUpdated', 'success');
        },
        onError: (error) => {
            toastr.error(error?.subject || 'An error occurred while saving the grade.');
        }
    });
};

const close = () => {
    form.reset();
    $('#editSubjectModal').modal('hide'); // Hide the modal when closing
}
</script>
