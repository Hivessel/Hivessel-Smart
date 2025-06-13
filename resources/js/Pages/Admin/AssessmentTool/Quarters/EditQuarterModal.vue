<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editQuarterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Quarter</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="" class="form-label">Quarter</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.quarter"/>
                        
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
    quarter: '',
    id: '',
    active: true
});

onBeforeMount(() => {
    eventBus.on('setEditQuarterData', (data) => {
        form.quarter = data.quarter;
        form.id = data.id;
    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditQuarterData');
});

const handleSubmit = () => {
    form.put(route('admin.quarters.update', {
        id: form.id,
        quarter: form.quarter,
        active: form.active
    }), {
        onSuccess: (response) => {
            form.reset();
            $('#editQuarterModal').modal('hide');
            eventBus.emit('quarterUpdated', 'success');
        },
        onError: (error) => {
            toastr.error(error?.quarter || 'An error occurred while saving the quarter.');
        }
    });
};

const close = () => {
    form.reset();
    $('#editQuarterModal').modal('hide'); // Hide the modal when closing
}
</script>
