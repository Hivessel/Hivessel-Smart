<template>
  <div class="content-wrapper p-3">
    <section class="content">
      <div class="card mt-3 border-0 shadow-sm">
        <div class="card-body">
          <div class="custom-tab-nav mb-0" role="tablist">
            <a
              class="nav-link"
              :class="{ active: currentTab === 'generate' }"
              id="generate-tab"
              role="tab"
              :aria-selected="currentTab === 'generate'"
              aria-controls="generate"
              href="javascript:void(0);"
              @click="switchTab('generate')"
            >Generate</a>

            <a
              class="nav-link"
              :class="{ active: currentTab === 'history' }"
              id="history-tab"
              role="tab"
              :aria-selected="currentTab === 'history'"
              aria-controls="history"
              href="javascript:void(0);"
              @click="switchTab('history')"
            >History</a>
          </div>

          <div class="tab-content">
            <div
              class="tab-pane fade"
              :class="{ show: currentTab === 'generate', active: currentTab === 'generate' }"
              id="generate"
              role="tabpanel"
            >
              <div class="p-3 border rounded bg-white">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-label">Grade</label>
                      <Multiselect
                        class="border" :class="gradeValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :options="apiData.grades"
                        placeholder="-- Select Grade --"
                        v-model="selectedGrade"
                        label="level"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">Subject</label>
                      <Multiselect
                        class="border" :class="subjectValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedGrade?.id"
                        :options="apiData.subjects"
                        placeholder="-- Select Subject --"
                        v-model="selectedSubject"
                        label="subject"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">Quarter</label>
                      <Multiselect
                        class="border" :class="quarterValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedSubject?.id"
                        :options="apiData.quarters"
                        placeholder="-- Select Quarter --"
                        v-model="selectedQuarter"
                        label="quarter"
                      />
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-label">Content</label>
                      <Multiselect
                        class="border" :class="raw_contentValidator.$invalid ? 'border-danger' : 'border-warning'"
                        multiple
                        :close-on-select="false"
                        data-width="100%"
                        track-by="id"
                        :options="apiData.contents"
                        placeholder="-- Select Contents --"
                        v-model="selectedContent"
                        label="content"
                      />
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-label">Competencies</label>
                      <Multiselect
                        class="border" :class="raw_competenciesValidator.$invalid ? 'border-danger' : 'border-warning'"
                        multiple
                        track-by="id"
                        data-width="100%"
                        :options="apiData.competencies"
                        placeholder="-- Select Competencies --"
                        v-model="selectedCompetencies"
                        label="competency"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">Proficiency Level</label>
                      <Multiselect
                        class="border" :class="proficiency_levelValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedGrade?.id"
                        :options="apiData.proficiency_levels"
                        placeholder="-- Select Proficiency Level --"
                        v-model="selectedProficiencyLevel"
                        label="level"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">Language</label>
                      <Multiselect
                        class="border" :class="languageValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedSubject?.id"
                        :options="apiData.languages"
                        placeholder="-- Select Language --"
                        v-model="selectedLanguage"
                        label="language"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">No. Of Questions</label>
                      <Multiselect
                        class="border" :class="no_of_questionsValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedNoOfQuestions?.id"
                        :options="apiData.no_of_questions"
                        placeholder="-- Select No. Of Questions --"
                        v-model="selectedNoOfQuestions"
                        label="item"
                      />
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-label">No. Of Choices</label>
                      <Multiselect
                        class="border" :class="no_of_choicesValidator.$invalid ? 'border-danger' : 'border-warning'"
                        data-width="100%"
                        track-by="id"
                        :key="selectedNoOfChoices?.id"
                        :options="apiData.no_of_choices"
                        placeholder="-- Select No. Of Choices --"
                        v-model="selectedNoOfChoices"
                        label="item"
                      />
                    </div>
                  </div>

                  <!-- <div class="col-12">
                    <button class="btn btn-warning w-100 text-white" @click="submitGenerate">Generate
                      <div class="spinner-border" role="status"></div>
                  </button>
                  </div> -->

                  <div class="col-12">
                    <button 
                      class="btn btn-warning w-100 text-white" 
                      :disabled="isLoading" 
                      @click="submitGenerate"
                    >
                      <span v-if="!isLoading">Generate</span>
                      <span v-else>
                        <span class="spinner-border spinner-border-sm text-dark" role="status">
                        </span>
                        Generating...
                      </span>
                    </button>
                  </div>

                  <!-- <div class="col-12">
                    <button class="btn btn-success w-100 text-white" @click="sendPrompt">Send Prompt</button>
                  </div> -->


                </div>
                <!-- <pre>{{ prompt }}</pre> -->
              </div>
            </div>

            <div
              class="tab-pane fade"
              :class="{ show: currentTab === 'history', active: currentTab === 'history' }"
              id="history"
              role="tabpanel"
            >
              <div class="p-3 border rounded bg-white">
                <div class="row">
                  <div class="col-3">
                    <ul class="list-group">
                    <!-- <li class="list-group-item text-truncate border-0" v-for="(item, index) in messages" :key="index">
                      <Link :href="route('client.plugins.assessment-tool', {id: item.id, tab: 'history'})">{{ item.context[0].content }}</Link>
                    </li> -->
                    <li class="list-group-item text-truncate border-0 hover:bg-gray-900" v-for="(item, index) in messages" :key="index">
                      <Link :href="route('client.plugins.assessment-tool', {id: item.id, tab: 'history'})">{{ item.context[0].content }}</Link>
                    </li>
                  </ul>
                  </div>
                  <div class="col-9">
                    <template v-if="chat">
                      <div class="w-full flex h-screen bg-slate-900">
                          <div class="w-full overflow-auto pb-36 scrollable-section" ref="chatContainer">
                              <template v-for="(content, index) in chat?.context" :key="index">
                                  <ChatContent :content="content"/>
                              </template>
                          </div>
                      </div>
                  </template>


                  </div>
                </div>
                <!-- <pre>ID: {{ page?.props?.urlQuery?.id || 'wala'  }}</pre>
                <pre>Tab: {{ page?.props?.urlQuery?.tab || 'wala'  }}</pre> -->
                
                
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
defineOptions({ layout: Layout });

const props = defineProps({
    messages: Array,
    chat: null | Object
});


const page = usePage();
const isLoading = ref(false);

const apiData = reactive({
  grades: [],
  subjects: [],
  quarters: [],
  contents: [],
  competencies: [],
  proficiency_levels: [],
  languages: [],
  no_of_questions: [
    {id: 1, item: 5},
    {id: 2, item: 10},
    {id: 3, item: 15},
    {id: 4, item: 20},
    {id: 5, item: 25},
    {id: 6, item: 30},
    {id: 7, item: 35},
    {id: 8, item: 40},
    {id: 9, item: 45},
    {id: 10, item: 50},
  ],
  no_of_choices: [
    {id: 1, item: 4},
    {id: 2, item: 5},
    {id: 3, item: 6},
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
  proficiency_level: computed(() =>selectedProficiencyLevel.value?.level || null),
  language: computed(() =>selectedLanguage.value?.language || null),
  no_of_questions: computed(() =>selectedNoOfQuestions.value?.item || null),
  no_of_choices: computed(() =>selectedNoOfChoices.value?.item || null),
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
      grade_id,
      subject_id,
      quarter_id,
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

const fetchCompetencies = async (contentIds = []) => {
  try {
    const response = await axios.get(route('admin.competencies.all', {
      content_id: JSON.stringify(contentIds),
      active: 1,
    }));
    apiData.competencies = response.data;
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
    no_of_questions: { required },
    no_of_choices: { required },
}))

const generate$ = useVuelidate(rules, form);
const gradeValidator = generate$.value.grade;
const subjectValidator = generate$.value.subject;
const quarterValidator = generate$.value.quarter;
const raw_contentValidator = generate$.value.raw_content;
const raw_competenciesValidator = generate$.value.raw_competencies;
const proficiency_levelValidator = generate$.value.proficiency_level;
const languageValidator = generate$.value.language;
const no_of_questionsValidator = generate$.value.no_of_questions;
const no_of_choicesValidator = generate$.value.no_of_choices;





const prompt = computed(() => {
  return `
Generate ${form.no_of_questions} multiple-choice questions, each with ${form.no_of_choices} choices.

Grade Level: ${form.grade}
Subject: ${form.subject}
Quarter: ${form.quarter}
Language: ${form.language}
Proficiency Level: ${form.proficiency_level}

Content Coverage:
${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Competency Focus:
${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Guidelines:
- Questions should be strictly based on the **Content Coverage** and aligned with the **Competency Focus**.
- Use language appropriate for ${form.grade} students.
- Ensure variety in question types and maintain the level of difficulty suitable for the "${form.proficiency_level}" proficiency level.
- Avoid repetition and ensure clarity in question and choices.
`.trim();
});

const submitGenerate = () => {
  if(gradeValidator.$invalid || gradeValidator.$invalid || subjectValidator.$invalid || quarterValidator.$invalid || raw_contentValidator.$invalid || raw_competenciesValidator.$invalid || proficiency_levelValidator.$invalid || languageValidator.$invalid || no_of_questionsValidator.$invalid || no_of_choicesValidator.$invalid){
    toastr.error('Some of the fields required to proceed are currently empty or incomplete. Kindly fill in all the necessary details before continuing.');
    return false;
  }

  const actualPromt = useForm({
    prompt: prompt.value,
    plugin: 'Assessment Tool'
  });

  isLoading.value = true;

  actualPromt.post(route('client.plugins.chats.store'), {
    onSuccess: (response) => {
      // console.log(response.data);
    },
    onError: (error) => {
      console.log(error);
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });

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
  border: 1px solid #dee2e6;
  border-radius: 8px;
  display: inline-flex;
  overflow: hidden;
  background-color: #f8f9fa;
}

.custom-tab-nav .nav-link {
  border: none;
  background: transparent;
  color: #6c757d;
  padding: 5px 15px;
  font-weight: 500;
  border-radius: 0;
  margin: 8px;
  cursor: pointer;
}

.custom-tab-nav .nav-link.active {
  background-color: #fff;
  color: #000;
  box-shadow: 0 0 0 1px #dee2e6;
  border-radius: 8px;
}

.tab-content {
  margin-top: 1rem;
}

.scrollable-section {
  max-height: 600px; /* Adjust height as needed */
  overflow-y: auto; /* Enables vertical scrolling */
}
</style>
