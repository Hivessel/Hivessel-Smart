<template>
    <div class="modal fade" id="importContentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Import Content</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitFile">
                        <div class="form-group">
                            <label for="importFile" class="form-label">File</label>
                            <input
                                type="file"
                                name="file"
                                id="importFile"
                                class="form-control"
                                ref="fileInput"
                                @change="handleFileChange"
                            />
                            <div v-if="data.file" class="mt-2">
                                Selected file: <strong>{{ data.file.name }}</strong>
                            </div>
                            <div v-if="data.errors.file" class="text-danger mt-1">
                                {{ data.errors.file }}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="submitFile" :disabled="data.processing">
                        {{ data.processing ? 'Importing...' : 'Import' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";

const fileInput = ref(null);

const data = useForm({
    file: null
});

const handleFileChange = (event) => {
    data.file = event.target.files[0];
};

const submitFile = () => {
    if (!data.file) {
        alert('Please select a file to import.');
        return;
    }

    data.post(route('admin.contents.import'), {
        forceFormData: true, // Important for file uploads with Inertia
        onSuccess: () => {
            $('#importContentModal').modal('hide');
            alert('File imported successfully!');
            data.reset(); // Clear the form
            fileInput.value.value = ''; // Clear the file input display
            // You might want to close the modal here using Bootstrap's JS
        },
        onError: (errors) => {
            console.error('Error importing file:', errors);
            // Errors are automatically available in data.errors
        }
    });
};
</script>