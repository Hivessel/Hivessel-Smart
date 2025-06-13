<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">
                
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createProficiencyLevelModal">
                            <i class="fas fa-plus"></i> Add Proficiency
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="proficiencyLevelTable" class="table table-striped table-hover text-center">
                            <thead class="" >
                                <tr>
                                    <th>ID</th>
                                    <th>LEVEL</th>
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
        <div class="modal fade" id="createProficiencyLevelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Proficiency Level</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Proficiency Level</label>
                            <input type="text" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.level" />
                            
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
        <EditProficiencyLevelModal />
    </div>
</template>
<script setup>
import { onMounted, onBeforeMount, onBeforeUnmount  } from 'vue';
import Layout from '../../Shared/Layout.vue';
import { useForm, router } from '@inertiajs/vue3';
import EditProficiencyLevelModal from './EditProficiencyLevelModal.vue';
import eventBus from '../../../../Scripts/eventBus';
defineOptions({
    layout: Layout,
})

const form = useForm({
    level: '',
});

const save = () => {
    form.post(route('admin.proficiency-levels.store'), {
        onSuccess: (response) => {
            toastr.success('Proficiency Level saved successfully.');
            $('#createProficiencyLevelModal').modal('hide'); // Hide the modal after saving
            form.reset(); // Reset the form after saving
            fetchProficiencyLevels(); // Reload the proficiency levels table
        },
        onError: (error) => {
            toastr.error(error?.level || 'An error occurred while saving the proficiency level.');
        }
    });
}

const fetchProficiencyLevels = async () => {
    if ($.fn.DataTable.isDataTable("#proficiencyLevelTable")) {
        $("#proficiencyLevelTable").DataTable().ajax.reload(); // ✅ Reload existing DataTable
    } else {
        $("#proficiencyLevelTable").DataTable({ // ✅ Initialize only if not initialized
            responsive: true, // Enable responsive feature
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: route('admin.proficiency-levels.ajax'),
            columns: [
                { data: "id" },
                { data: "level" },
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
    fetchProficiencyLevels();

    // Handle Delete Button Click
    $(document).on("click", ".delete-proficiency-level-btn", async function () {

        if(confirm("Are you sure you want to delete this proficiency level?")) {
            const id = $(this).data("id");
            await axios.delete(route('admin.proficiency-levels.destroy', {id: id}))
                .then(response => {
                    if (response.status == 200) {
                        toastr.success('Proficiency Level has been deleted successfully!');
                        fetchProficiencyLevels();
                    }
                })
                .catch(error => {
                    console.log(error.response.data.message)
                    // toastr.error(error.response.data.message);
                });
        }
        
    });



    // Handle Edit Button Click
    $(document).on("click", ".edit-proficiency_level-btn", async function () {
        const id = $(this).data("id");
        const level = $(this).data("level");
        $('#editProficiencyLevelModal').modal('show');
        eventBus.emit('setEditProficiencyLevelData', { level: level, id: id });
    });
})

onBeforeMount(() => {
    eventBus.on('proficiencyLevelUpdated', (status) => {
        if (status === 'success') {
            toastr.success('Proficiency Level updated successfully.');
            fetchProficiencyLevels(); // Reload the grades table after update
        }
    });
})

onBeforeUnmount(() => {
    eventBus.off('proficiencyLevelUpdated');
});

const close = () => {
    form.reset();
    $('#createProficiencyLevelModal').modal('hide'); // Hide the modal when closing
}
</script>