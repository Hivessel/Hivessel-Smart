<template>
    <!-- Edit Grade -->
    <div class="modal fade" id="editContentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Content</h1>
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
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.subject" readonly/>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label">Quarter</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.quarter" readonly/>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label">Content</label>
                        <input type="text" id="" class="form-control" aria-describedby="passwordHelpBlock" v-model="form.content"/>
                    </div>

                    <div class="form-group">
                            <button class="btn btn-secondary" @click.prevent="addCompetency"><i class="fa fa-plus"></i> Add Competencies</button>
                    </div>

                    <div class="">
                        <table class="table table-borderless align-middle w-100">
                            <tbody>
                                <tr v-for="(item, index) in form.competencies" :key="item.id || index">
                                <td style="width: 45%;">
                                    <div class="form-group mb-0">
                                    <label for="inputPassword5" class="form-label">Competency</label>
                                    <textarea
                                        v-model="item.competency"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Enter competency..."
                                    ></textarea>
                                    </div>
                                </td>
                                <td style="width: 45%;">
                                    <div class="form-group mb-0">
                                    <label for="inputPassword5" class="form-label">Link/Reference</label>
                                    <textarea
                                        v-model="item.reference"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Enter reference link..."
                                    ></textarea>
                                    </div>
                                </td>
                                <td style="width: 10%;" class="text-center align-middle">
                                    <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click.prevent="removeCompency(item.id)"
                                    title="Remove"
                                    >
                                    <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                </tr>
                            </tbody>
                        </table>
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
    subject_id: '',
    subject: '',
    quarter_id: '',
    quarter: '',
    content: '',
    competencies: [],
    active: true
});

onBeforeMount(() => {
    eventBus.on('setEditContentData', (data) => {
        form.id = data.id;
        form.grade_id = data.grade_id;
        form.grade = data.grade;
        form.subject_id = data.subject_id;
        form.subject = data.subject;
        form.quarter_id = data.quarter_id;
        form.quarter = data.quarter;
        form.content = data.content;
        form.competencies = data.competencies;

    });
})

onBeforeUnmount(() => {
    eventBus.off('setEditContentData');
});

const generateUUIDv4 = () => {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    const r = Math.random() * 16 | 0;
    const v = c === 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

const addCompetency = () => {
    form.competencies.push({
        id: generateUUIDv4(),
        competency: '',
        reference: null,
        active: true
    })
}

const removeCompency = (id) => {
    form.competencies = form.competencies.filter((item) => item.id !== id);
}

const handleSubmit = () => {
    form.put(route('admin.contents.update'), {
        onSuccess: (response) => {
            $('#editContentModal').modal('hide');
            eventBus.emit('contentUpdated', 'success');
        },
        onError: (error) => {
            if (error.content) {
                toastr.error(error.content);
            }
            if (error.competencies) {
                toastr.error(error.competencies);
            }
            
        }
    });
};

const close = () => {
    form.reset();
    $('#editContentModal').modal('hide'); // Hide the modal when closing
}
</script>
