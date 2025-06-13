<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editLanguageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Language</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="" class="form-label">Language</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.language"/>
                        
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
    language: '',
    id: '',
    active: true
});

onBeforeMount(() => {
    eventBus.on('setEditLanguageData', (data) => {
        form.language = data.language;
        form.id = data.id;
    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditLanguageData');
});

const handleSubmit = () => {
    form.put(route('admin.languages.update', {
        id: form.id,
        language: form.language,
        active: form.active
    }), {
        onSuccess: (response) => {
            form.reset();
            $('#editLanguageModal').modal('hide');
            eventBus.emit('languageUpdated', 'success');
        },
        onError: (error) => {
            toastr.error(error?.language || 'An error occurred while saving the language.');
        }
    });
};

const close = () => {
    form.reset();
    $('#editLanguageModal').modal('hide'); // Hide the modal when closing
}
</script>
