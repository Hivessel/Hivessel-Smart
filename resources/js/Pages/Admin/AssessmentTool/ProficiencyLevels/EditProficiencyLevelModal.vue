<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editProficiencyLevelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Proficiency Level</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="" class="form-label">Proficiency Level</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.level"/>
                        
                    </div>
                </form>
                <pre>{{ form }}</pre>
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
    eventBus.on('setEditProficiencyLevelData', (data) => {
        form.level = data.level;
        form.id = data.id;
    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditProficiencyLevelData');
});

const handleSubmit = () => {
    form.put(route('admin.proficiency-levels.update', {
        id: form.id,
        level: form.level,
        active: form.active
    }), {
        onSuccess: (response) => {
            form.reset();
            $('#editProficiencyLevelModal').modal('hide');
            eventBus.emit('proficiencyLevelUpdated', 'success');
        },
        onError: (error) => {
            toastr.error(error?.level || 'An error occurred while saving the proficiency level.');
        }
    });
};

const close = () => {
    form.reset();
    $('#editProficiencyLevelModal').modal('hide'); // Hide the modal when closing
}
</script>
