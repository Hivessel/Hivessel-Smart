<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editGradeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Grade</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="" class="form-label">Grade</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.level"/>
                        
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
    level: '',
    id: '',
    active: true
});

onBeforeMount(() => {
    eventBus.on('setEditGradeData', (data) => {
        form.level = data.level;
        form.id = data.id;
    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditGradeData');
});

const handleSubmit = () => {
    form.put(route('admin.grades.update', {
        id: form.id,
        level: form.level,
        active: form.active
    }), {
        onSuccess: (response) => {
            form.reset();
            $('#editGradeModal').modal('hide');
            eventBus.emit('gradeUpdated', 'success');
        },
        onError: (error) => {
            toastr.error(error?.level || 'An error occurred while saving the grade.');
        }
    });
};

const close = () => {
    form.reset();
    $('#editGradeModal').modal('hide'); // Hide the modal when closing
}
</script>
