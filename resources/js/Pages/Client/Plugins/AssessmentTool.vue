<template>
  <div class="content-wrapper p-3 assessment--box">
    <h3><i class="fas fa-qrcode mr-2 text-warning"></i>Assessment Tool</h3>
    <section class="content assessment--tools">
      <div class="card mt-3 border-0 shadow-sm">
        
        <div class="card-body">
          
          <div class="custom-tab-nav mb-0 tab--links" role="tablist">
            <a class="nav-link" :class="{ active: currentTab === 'generate' }" id="generate-tab" role="tab"
              :aria-selected="currentTab === 'generate'" aria-controls="generate" href="javascript:void(0);"
              @click="switchTab('generate')">Generate</a>

            <a class="nav-link" :class="{ active: currentTab === 'history' }" id="history-tab" role="tab"
              :aria-selected="currentTab === 'history'" aria-controls="history" href="javascript:void(0);"
              @click="switchTab('history')">History</a>
          </div>
          
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ show: currentTab === 'generate', active: currentTab === 'generate' }"
              id="generate" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="assessment--creation">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                    <label class="form-label">Grade</label>
                                    <Multiselect class="border" :class="gradeValidator.$invalid ? 'border-danger' : 'border-warning'"
                                        data-width="100%" track-by="id" :options="apiData.grades" placeholder="Select Grade"
                                        v-model="selectedGrade" label="level" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">Subject</label>
                                    <Multiselect class="border"
                                        :class="subjectValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedGrade?.id" :options="apiData.subjects"
                                        placeholder="Select Subject" v-model="selectedSubject" label="subject" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">Quarter</label>
                                    <Multiselect class="border"
                                        :class="quarterValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedSubject?.id" :options="apiData.quarters"
                                        placeholder="Select Quarter" v-model="selectedQuarter" label="quarter" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                    <label class="form-label">Content</label>
                                    <Multiselect class="border"
                                        :class="raw_contentValidator.$invalid ? 'border-danger' : 'border-warning'" multiple
                                        :close-on-select="false" data-width="100%" track-by="id" :options="apiData.contents"
                                        placeholder="Select Contents" v-model="selectedContent" label="content" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                    <label class="form-label">Competencies</label>
                                    <Multiselect class="border"
                                        :class="raw_competenciesValidator.$invalid ? 'border-danger' : 'border-warning'" multiple
                                        track-by="id" data-width="100%" :options="apiData.competencies"
                                        placeholder="Select Competencies" v-model="selectedCompetencies" label="competency" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">Proficiency Level</label>
                                    <Multiselect class="border"
                                        :class="proficiency_levelValidator.$invalid ? 'border-danger' : 'border-warning'"
                                        data-width="100%" track-by="id" :key="selectedGrade?.id" :options="apiData.proficiency_levels"
                                        placeholder="Select Proficiency Level" v-model="selectedProficiencyLevel" label="level" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">Language</label>
                                    <Multiselect class="border"
                                        :class="languageValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedSubject?.id" :options="apiData.languages"
                                        placeholder="Select Language" v-model="selectedLanguage" label="language" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                    <label class="form-label">Blooms Taxonomy Level of Learnings</label>
                                    <Multiselect class="border"
                                        :class="blooms_taxonomyValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedBloomsTaxonomy?.id" :options="apiData.blooms_taxonomy"
                                        placeholder="Select Blooms Taxonomy Level" v-model="selectedBloomsTaxonomy" label="item" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">No. Of Questions</label>
                                    <Multiselect class="border"
                                        :class="no_of_questionsValidator.$invalid ? 'border-danger' : 'border-warning'"
                                        data-width="100%" track-by="id" :key="selectedNoOfQuestions?.id"
                                        :options="apiData.no_of_questions" placeholder="Select No. Of Questions"
                                        v-model="selectedNoOfQuestions" label="item" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label">No. Of Choices</label>
                                    <Multiselect class="border"
                                        :class="no_of_choicesValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedNoOfChoices?.id" :options="apiData.no_of_choices"
                                        placeholder="Select No. Of Choices" v-model="selectedNoOfChoices" label="item" />
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <button class="btn btn-warning w-100 text-white btn--primary" :disabled="isGenerating"
                                    @click="submitGenerate">
                                    <span v-if="!isGenerating">Generate Assessment</span>
                                    <span v-else>
                                        <span class="spinner-border spinner-border-sm text-dark" role="status">
                                        </span>
                                        Generating...
                                    </span>
                                    </button>
                                </div>


                            </div>
                            <!-- <pre>{{ prompt }}</pre> -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <section class="section--breadcrumbs">
                            <div class="profile--box">
                                <div class="profile--box__info">
                                <div class="profile--box__pic">
                                    <img src="https://hivessel.com/wp-content/uploads/2024/03/hivessel_logo.png" alt="">
                                </div>
                                <div class="profile--box__name">
                                    <p>{{ page?.props?.authenticatedUser?.name || 'Unknown' }}</p>
                                    <span>{{ page?.props?.authenticatedUser?.email || 'unknown@mail.com' }}</span>
                                </div>
                                </div>
                            </div>
                        </section>

                        <!-- Add More Credits -->
                         <section class="section--add-credits">
                                <h2>Need More Credit? Top Up Your Credit Points Now</h2>
                                <p>Get the flexibility and access you need — buy more credit points now to keep your momentum going.</p>
                                <a href="#" class="add--more-credits">Buy More Credits</a>
                         </section>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" :class="{ show: currentTab === 'history', active: currentTab === 'history' }"
              id="history" role="tabpanel">
              <div class="p-3 history--container">
                <div class="row">
                  <div class="col-md-3 history--sidebar">
                    <ul class="list-group history--list">
                      <li class="list-group-item text-truncate border-0 hover:bg-gray-900"
                        v-for="(item, index) in messages.map((i) => {
                        return {
                          id: i.id,
                          plugin: i.plugin,
                          user_id: i.user_id,
                          options: i.options,
                          updated_at: i.updated_at
                        }
                      })" :key="index">

                        <Link :href="route('client.plugins.assessment-tool', { id: item.id, tab: 'history' })">{{
                          `${item.options?.grade} - ${item.options?.subject}` }}</Link>
                      </li>
                    </ul>
                  </div>
                  <div class="col-md-9 history--content">
                    <template v-if="chat">
                      <div class="w-full flex h-screen bg-slate-900">
                        <div class="w-full overflow-auto pb-36 scrollable-section" ref="chatContainer">
                          <template v-for="(content, index) in chat?.context" :key="index">
                            <ChatContent :content="content" v-if="index != 0" />
                          </template>
                        </div>

                        <div class="position-relative flex-grow-1 d-flex align-items-center">
                            <input type="text" class="form-control rounded"
                            placeholder="Add more instructions if needed..." v-model="inputText"
                            @keyup.enter="addInstruction" ref="promptInput" :disabled="isSendingInstruction">
                             <div class="position-absolute top-50 end-0 translate-middle-y pe-3 d-flex align-items-center">
                                <svg v-if="!isSendingInstruction" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                              stroke-width="1.9" stroke="rgb(252,185,50)" class="me-2"
                              style="width: 1.5rem; height: 1.5rem; color: #fcb932;cursor: pointer;"
                              @click="addInstruction" :disabled="isSendingInstruction">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                                <span v-if="isSendingInstruction" class="spinner-border spinner-border-sm" role="status" style=" color: #fcb932;">
                                </span>
                            </div>
                        </div>
                      </div>
                    </template>
                    

                  </div>
                </div>
                
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { onMounted, reactive, ref, computed, watch } from 'vue';
import Layout from '../Shared/Layout.vue';
import Multiselect from 'vue-multiselect';
import useVuelidate from '@vuelidate/core'
import { required } from '@vuelidate/validators'
import ChatContent from './Components/ChatContent.vue';
import axios from 'axios';
defineOptions({ layout: Layout });

const props = defineProps({
  messages: Array,
  chat: null | Object
});


const page = usePage();
const isGenerating = ref(false);
const isSendingInstruction = ref(false);

const apiData = reactive({
  grades: [],
  subjects: [],
  quarters: [],
  contents: [],
  competencies: [],
  proficiency_levels: [],
  languages: [],
  blooms_taxonomy: [
    { id: 1, item: 'Remembering' },
    { id: 2, item: 'Understanding' },
    { id: 3, item: 'Applying' },
    { id: 4, item: 'Analyzing' },
    { id: 5, item: 'Evaluating' },
    { id: 6, item: 'Creating' },
  ],
  no_of_questions: [
    { id: 1, item: 5 },
    { id: 2, item: 10 },
    { id: 3, item: 15 },
    { id: 4, item: 20 },
    { id: 5, item: 25 },
    { id: 6, item: 30 },
    { id: 7, item: 35 },
    { id: 8, item: 40 },
    { id: 9, item: 45 },
    { id: 10, item: 50 },
  ],
  no_of_choices: [
    { id: 1, item: 4 },
    { id: 2, item: 5 },
    { id: 3, item: 6 },
  ]
});

const selectedGrade = ref(null);
const selectedSubject = ref(null);
const selectedQuarter = ref(null);
const selectedContent = ref(null);
const selectedCompetencies = ref([]);
const selectedProficiencyLevel = ref(null);
const selectedLanguage = ref(null);
const selectedNoOfQuestions = ref(null);
const selectedNoOfChoices = ref(null);
const selectedBloomsTaxonomy = ref(null);

const form = useForm({
  grade: computed(() => selectedGrade.value?.level || null),
  subject: computed(() => selectedSubject.value?.subject || null),
  quarter: computed(() => selectedQuarter.value?.quarter || null),
  content: computed(() =>
    Array.isArray(selectedContent.value)
      ? selectedContent.value.map(el => el.id)
      : []
  ),
  competencies: computed(() =>
    Array.isArray(selectedCompetencies.value)
      ? selectedCompetencies.value.map(el => el.id)
      : []
  ),
  raw_content: computed(() =>
    Array.isArray(selectedContent.value)
      ? selectedContent.value.map(el => el.content)
      : []
  ),
  raw_competencies: computed(() =>
    Array.isArray(selectedCompetencies.value)
      ? selectedCompetencies.value.map(el => el.competency)
      : []
  ),
  proficiency_level: computed(() => selectedProficiencyLevel.value?.level || null),
  language: computed(() => selectedLanguage.value?.language || null),
  blooms_taxonomy: computed(() => selectedBloomsTaxonomy.value?.item || null),
  no_of_questions: computed(() => selectedNoOfQuestions.value?.item || null),
  no_of_choices: computed(() => selectedNoOfChoices.value?.item || null),
});

onMounted(() => {
  fetchGrades();
  fetchQuarters();
  fetchProficiencyLevels();
  fetchLanguages();
});

const fetchGrades = async () => {
  try {
    const response = await axios.get(route('admin.grades.all', { active: 1 }));
    apiData.grades = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchSubjects = async (grade_id) => {
  try {
    const response = await axios.get(route('admin.subjects.all', {
      grade_id,
      active: 1,
    }));
    apiData.subjects = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchQuarters = async () => {
  try {
    const response = await axios.get(route('admin.quarters.all', { active: 1 }));
    apiData.quarters = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchContents = async (grade_id, subject_id, quarter_id) => {
  try {
    const response = await axios.get(route('admin.contents.all', {
      grade_id: grade_id,
      subject_id: subject_id,
      quarter_id: quarter_id,
      active: 1,
    }));
    apiData.contents = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchProficiencyLevels = async () => {
  try {
    const response = await axios.get(route('admin.proficiency-levels.all', { active: 1 }));
    apiData.proficiency_levels = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchLanguages = async () => {
  try {
    const response = await axios.get(route('admin.languages.all', { active: 1 }));
    apiData.languages = response.data;
  } catch (error) {
    console.error(error);
  }
};



// Tab management with URL sync
const urlParams = new URLSearchParams(window.location.search);
const currentTab = ref(urlParams.get('tab') || 'generate');

function switchTab(tabId) {
  currentTab.value = tabId;
  const url = new URL(window.location.href);
  url.searchParams.set('tab', tabId);
  window.history.replaceState({}, '', url);
}

const rules = computed(() => ({
  grade: { required },
  subject: { required },
  quarter: { required },
  raw_content: { required },
  raw_competencies: { required },
  proficiency_level: { required },
  language: { required },
  blooms_taxonomy: { required },
  no_of_questions: { required },
  no_of_choices: { required },
}))

const subtractCredit = async (credits) => {
  try {
    const response = await axios.post(route('subtract-credit'), { credits: credits });
  } catch (error) {
    console.log(error);
  }
}

const generate$ = useVuelidate(rules, form);
const gradeValidator = generate$.value.grade;
const subjectValidator = generate$.value.subject;
const quarterValidator = generate$.value.quarter;
const raw_contentValidator = generate$.value.raw_content;
const raw_competenciesValidator = generate$.value.raw_competencies;
const proficiency_levelValidator = generate$.value.proficiency_level;
const languageValidator = generate$.value.language;
const blooms_taxonomyValidator = generate$.value.blooms_taxonomy;
const no_of_questionsValidator = generate$.value.no_of_questions;
const no_of_choicesValidator = generate$.value.no_of_choices;


// const prompt = computed(() => {
//  return `Create an assessment with ${form.no_of_questions} multiple-choice questions, each with ${form.no_of_choices} choices and the response must be in printable richtext editor format.
//       Grade Level: ${form.grade}
//       Subject: ${form.subject}
//       Quarter: ${form.quarter}
//       Language: ${form.language}
//       Proficiency Level: ${form.proficiency_level}
//       Content Coverage:
//       ${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

//       Competency Focus:
//       ${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}
//       Guidelines:
//       - Questions should be strictly based on the **Content Coverage** and aligned with the **Competency Focus**.
//       - Use language appropriate for ${form.grade} students.
//       - Ensure variety in question types and maintain the level of difficulty suitable for the "${form.proficiency_level}" proficiency level.
//       - Avoid repetition and ensure clarity in question and choices.
//       - Include the answer key at the end. Provide clear instructions and format the material to be printable and in richtext view
//       `.trim();
  
//   });


  const prompt = computed(() => {
    return `Create an assessment with ${form.no_of_questions} multiple-choice questions, each with ${form.no_of_choices} choices and the response must be in html format and do not make it form.
      Grade Level: ${form.grade}
      Subject: ${form.subject}
      Quarter: ${form.quarter}
      Language: ${form.language}
      Proficiency Level: ${form.proficiency_level}
      Blooms Taxonomy Level of Learnings: ${form.blooms_taxonomy}
      Content Coverage:
      ${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

      Competency Focus:
      ${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}
      Guidelines:
      - Questions should be strictly based on the **Content Coverage** and aligned with the **Competency Focus**.
      - Use language appropriate for ${form.grade} students.
      - Ensure variety in question types and maintain the level of difficulty suitable for the "${form.proficiency_level}" proficiency level.
      - Avoid repetition and ensure clarity in question and choices.
      - Include the answer key at the end. Provide clear instructions and format the material to html response
      `.trim();
  
  });


const submitGenerate = () => {
  if (gradeValidator.$invalid || gradeValidator.$invalid || subjectValidator.$invalid || quarterValidator.$invalid || raw_contentValidator.$invalid || raw_competenciesValidator.$invalid || proficiency_levelValidator.$invalid || languageValidator.$invalid || blooms_taxonomyValidator.$invalid || no_of_questionsValidator.$invalid || no_of_choicesValidator.$invalid) {
    toastr.error('Some of the fields required to proceed are currently empty or incomplete. Kindly fill in all the necessary details before continuing.');
    return false;
  }

  const actualPromt = useForm({
    prompt: prompt.value,
    plugin: 'Assessment Tool',
    options: {
      grade: form.grade,
      subject: form.subject,
      quarter: form.quarter
    }
  });

  const remaining_credits = page?.props?.authenticatedUser?.credit_balance?.remaining_credit_points || 0;
  if (remaining_credits > 0 && !actualPromt.processing) {
    isGenerating.value = true;
    subtractCredit(1);
    actualPromt.post(route('client.plugins.chats.store'), {
      onSuccess: (response) => {
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        isGenerating.value = false;
      }
    });
  }else{
    toastr.error('Your credit balance is 0. Make a purchase to continue using the service.');
  }


}

const inputText = ref('');


const addInstruction = () => {
  if(!inputText.value){
    toastr.error('A message cannot be sent without providing instruction');
    return false;
  }

  const input = useForm({
    id: page.props.urlQuery.id,
    prompt: computed(() => inputText.value),
    plugin: 'Assessment Tool',
    options: {
      grade: props?.chat?.options?.grade || '',
      subject: props?.chat?.options?.subject || '',
      quarter: props?.chat?.options?.quarter || ''
    }
  });

  const remaining_credits = page?.props?.authenticatedUser?.credit_balance?.remaining_credit_points || 0;
  if (remaining_credits > 0 && !input.processing) {
    isSendingInstruction.value = true; 
    subtractCredit(1);
    input.post(route('client.plugins.chats.store'), {
      onSuccess: (response) => {
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        isSendingInstruction.value = false; 
      }
    });
  }else{
    toastr.error('Your credit balance is 0. Make a purchase to continue using the service.');
  }
  
}

// Watchers
watch(() => selectedGrade.value, (grade) => {
  selectedSubject.value = null;
  selectedQuarter.value = null;
  selectedContent.value = null;
  selectedCompetencies.value = [];
  apiData.subjects = [];
  apiData.contents = [];
  apiData.competencies = [];

  if (grade?.id) {
    fetchSubjects(grade.id);
  }
});

watch(() => selectedSubject.value, () => {
  selectedQuarter.value = null;
  selectedContent.value = null;
  selectedCompetencies.value = [];
  apiData.contents = [];
  apiData.competencies = [];
});

watch(
  [() => selectedGrade.value, () => selectedSubject.value, () => selectedQuarter.value],
  ([grade, subject, quarter]) => {
    if (grade?.id && subject?.id && quarter?.id) {
      fetchContents(grade.id, subject.id, quarter.id);
    }
  }
);



watch(selectedContent, async (contentArray) => {
  apiData.competencies = [];
  selectedCompetencies.value = [];

  if (!Array.isArray(contentArray) || contentArray.length === 0) return;

  const allCompetencies = [];

  for (const content of contentArray) {
    if (!content?.id) continue;

    try {
      const response = await axios.get(route('admin.competencies.all', {
        content_id: JSON.stringify([content.id]),
        active: 1,
      }));

      const newCompetencies = response.data || [];

      // Merge into allCompetencies avoiding duplicates
      newCompetencies.forEach(c => {
        if (!allCompetencies.find(existing => existing.id === c.id)) {
          allCompetencies.push(c);
        }
      });
    } catch (error) {
      console.error(`Error fetching competencies for content ${content.id}:`, error);
    }
  }

  // Assign to both API data and selected list
  apiData.competencies = allCompetencies;
  selectedCompetencies.value = [...allCompetencies];
});

</script>

<style scoped>
.custom-tab-nav {
  border-radius: 8px;
  display: inline-flex;
  overflow: hidden;
  background-color: #f8f9fa;
}

.custom-tab-nav .nav-link {
  border: none;
  background: transparent;
  color: #6c757d;
  padding: 10px 15px;
  font-family: "Akatab", sans-serif;
  font-weight: 400;
  text-transform: uppercase;
  border-radius: 0;
  margin: 8px;
  cursor: pointer;
}

.custom-tab-nav .nav-link.active {
  background-color: #fdc109;
  color: #fff;
  border-radius: 8px;
}

.tab-content {
  margin-top: 1rem;
}

.scrollable-section {
  max-height: 600px;
  /* Adjust height as needed */
  overflow-y: auto;
  /* Enables vertical scrolling */
}

</style>
