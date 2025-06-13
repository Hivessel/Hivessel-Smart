<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">
                
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createLanguageModal">
                            <i class="fas fa-plus"></i> Add Language
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="languageTable" class="table table-striped table-hover text-center">
                            <thead class="" >
                                <tr>
                                    <th>ID</th>
                                    <th>LANGUAGE</th>
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
        
        <!-- Create Grade -->
        <div class="modal fade" id="createLanguageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Language</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Language</label>
                            <input type="text" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.language" />
                            
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
        <EditLanguageModal />
    </div>
</template>
<script setup>
import { onMounted, onBeforeUnmount, onBeforeMount } from 'vue';
import Layout from '../../Shared/Layout.vue';
import { useForm, router } from '@inertiajs/vue3';
import EditLanguageModal from './EditLanguageModal.vue';
import eventBus from '../../../../Scripts/eventBus';
defineOptions({
    layout: Layout,
})

const form = useForm({
    language: '',
});

const save = () => {
    form.post(route('admin.languages.store'), {
        onSuccess: (response) => {
            toastr.success('Language saved successfully.');
            $('#createLanguageModal').modal('hide'); // Hide the modal after saving
            form.reset(); // Reset the form after saving
            fetchLanguages(); // Reload the languages table
        },
        onError: (error) => {
            toastr.error(error?.language || 'An error occurred while saving the grade.');
        }
    });
}

const fetchLanguages = async () => {
    if ($.fn.DataTable.isDataTable("#languageTable")) {
        $("#languageTable").DataTable().ajax.reload(); // ✅ Reload existing DataTable
    } else {
        $("#languageTable").DataTable({ // ✅ Initialize only if not initialized
            responsive: true, // Enable responsive feature
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: route('admin.languages.ajax'),
            columns: [
                { data: "id" },
                { data: "language" },
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
    fetchLanguages();

    // Handle Delete Button Click
    $(document).on("click", ".delete-language-btn", async function () {

        if(confirm("Are you sure you want to delete this language?")) {
            const id = $(this).data("id");
            await axios.delete(route('admin.languages.destroy', {id: id}))
                .then(response => {
                    if (response.status == 200) {
                        toastr.success('Language has been deleted successfully!');
                        fetchLanguages();
                    }
                })
                .catch(error => {
                    console.log(error.response.data.message)
                    // toastr.error(error.response.data.message);
                });
        }
        
    });


    // Handle Edit Button Click
    $(document).on("click", ".edit-language-btn", async function () {
        const id = $(this).data("id");
        const language = $(this).data("language");
        $('#editLanguageModal').modal('show');
        eventBus.emit('setEditLanguageData', { language: language, id: id });
    });


});

onBeforeMount(() => {
    eventBus.on('languageUpdated', (status) => {
        if (status === 'success') {
            toastr.success('Language updated successfully.');
            fetchLanguages(); // Reload the grades table after update
        }
    });
})

onBeforeUnmount(() => {
    eventBus.off('languageUpdated');
});

const close = () => {
    form.reset();
    $('#createLanguageModal').modal('hide'); // Hide the modal when closing
}
</script>