<template>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="card card-info mt-2">
                
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createQuarterModal">
                            <i class="fas fa-plus"></i> Add Quarters
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="quarterTable" class="table table-striped table-hover text-center">
                            <thead class="" >
                                <tr>
                                    <th>ID</th>
                                    <th>QUARTER</th>
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
        
        <!-- Create Quarter -->
        <div class="modal fade" id="createQuarterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Quarter</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="inputPassword5" class="form-label">Quarter</label>
                            <input type="text" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.quarter" />
                            
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
import { onMounted } from 'vue';
defineOptions({
    layout: Layout,
})

const form = useForm({
    quarter: '',
});

const fetchQuarters = async () => {
    if ($.fn.DataTable.isDataTable("#quarterTable")) {
        $("#quarterTable").DataTable().ajax.reload(); // ✅ Reload existing DataTable
    } else {
        $("#quarterTable").DataTable({ // ✅ Initialize only if not initialized
            responsive: true, // Enable responsive feature
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: route('admin.quarters.ajax'),
            columns: [
                { data: "id" },
                { data: "quarter" },
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
    fetchQuarters();

    // Handle Delete Button Click
    $(document).on("click", ".delete-quarter-btn", async function () {

        if(confirm("Are you sure you want to delete this quarter?")) {
            const id = $(this).data("id");
            await axios.delete(route('admin.quarters.destroy', {id: id}))
                .then(response => {
                    if (response.status == 200) {
                        toastr.success('Quarter has been deleted successfully!');
                        fetchQuarters();
                    }
                })
                .catch(error => {
                    console.log(error.response.data.message)
                    // toastr.error(error.response.data.message);
                });
        }
        
    });
});

const save = () => {
    form.post(route('admin.quarters.store'), {
        onSuccess: (response) => {
            toastr.success('Qurter saved successfully.');
            $('#createQuarterModal').modal('hide'); // Hide the modal after saving
            form.reset(); // Reset the form after saving
            fetchQuarters(); // Reload the quarters table
        },
        onError: (error) => {
            toastr.error(error?.quarter || 'An error occurred while saving the quarter.');
        }
    });
}

const close = () => {
    form.reset();
    $('#createQuarterModal').modal('hide'); // Hide the modal when closing
}
</script>