<template>
  <div class="content-wrapper p-3 assessment--box">
    <h3><i class="fas fa-magic mr-2 text-warning"></i>Lesson Plan Generator</h3>
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

                                

                                <div class="col-md-12">
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
                                    <label class="form-label">Template</label>
                                    <Multiselect class="border"
                                        :class="languageValidator.$invalid ? 'border-danger' : 'border-warning'" data-width="100%"
                                        track-by="id" :key="selectedTemplate?.id" :options="apiData.templates"
                                        placeholder="Select Template" v-model="selectedTemplate" label="caption" :disabled="true"/>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <button class="btn btn-warning w-100 text-white btn--primary" :disabled="isGenerating"
                                    @click="submitGenerate">
                                    <span v-if="!isGenerating">Generate Lesson Plan</span>
                                    <span v-else>
                                        <span class="spinner-border spinner-border-sm text-dark" role="status">
                                        </span>
                                        Generating...
                                    </span>
                                    </button>
                                </div>


                            </div>
                            <!-- <pre class="bg-success">{{ form }}</pre>
                            <pre>{{ prompt }}</pre> -->
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
                          options: i.options
                        }
                      })" :key="index">
                        <Link :href="route('client.plugins.lesson-plan-generator', { id: item.id, tab: 'history' })">{{
                          `${item.options?.grade} - ${item.options?.subject}` }}</Link>
  
                      </li>
                    </ul>
                  </div>
                  <div class="col-md-9 history--content">
                    <template v-if="chat">
                      <div class="w-full flex h-screen bg-slate-900">
                        <div class="w-full overflow-auto pb-36 scrollable-section section--history" ref="chatContainer">
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
import { useForm, usePage, Link, router } from '@inertiajs/vue3';
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
  languages: [],
  templates: [],
});

const selectedGrade = ref(null);
const selectedSubject = ref(null);
const selectedQuarter = ref(null);
const selectedContent = ref(null);
const selectedCompetencies = ref([]);
const selectedLanguage = ref(null);
const selectedTemplate = ref(null);


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
  raw_reference: computed(() =>
    Array.isArray(selectedCompetencies.value)
      ? selectedCompetencies.value.map(el => el.reference)
      : []
  ),
  language: computed(() => selectedLanguage.value?.language || null),
  template: computed(() => selectedTemplate.value?.link || null),
});

onMounted(() => {
  fetchGrades();
  fetchQuarters();
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
  language: { required },
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
const languageValidator = generate$.value.language;


// const prompt = computed(() => {
//   return `
//   Prepare a lesson plan based on the attached exemplar, using the specified template.
//   Grade Level: ${form.grade}
//   Subject: ${form.subject}
//   Quarter: ${form.quarter}
//   Language: ${form.language}
//   Content Coverage:
//   ${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}
//   Competency Focus:
//   ${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}
  
//   Exemplar:
//   ${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}

//   Strictly follow this template format:
//   ${form.template}  
  
//     `.trim();

// });


// const prompt = computed(() => {
//   return (`
// Prepare a lesson plan based on the attached exemplar and the specified template.:

// Grade Level: ${form.grade}  
// Subject: ${form.subject}  
// Quarter: ${form.quarter}  
// Language: ${form.language}

// Content Coverage:
// ${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

// Competency Focus:
// ${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}

// Reference Materials:
// ${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}

// ----

// Your task:
// - Return a complete and fully filled HTML table with cell border
// - Read this file ${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}
// - Follow this format ${form.template}
// - Fill in all rows and columns for **Sessions 1 to 4** in specified template, session column on the top and others at the left
// - Include unique and meaningful lesson content in each cell
// - content must have 
// OBJECTIVES under row[Content Standards, Performance Standards, Learning Competencies/Objectives Write the LC Code for each],
// CONTENT,
// LEARNING RESOURCES under row['A. References' => [
//   '1: Teacher's Guides/Pages',
//   '2. Learner's Materials Pages',
//   '3. Textbook Pages'
//   '4. Additional Materials from Learning Resources (LR) portal'],
//   'B. 1. Other Learning Resources'
// ],
// Make this row > PROCEDURES,
// Make this row > A: Reviewing previous lesson or presenting the new lesson,
// Make this row > B: Establishing a purpose for the lesson,
// Make this row > C: Presenting examples/instances of the new lesson,
// Make this row > D: Discussing new concepts and practicing new skills #1,
// Make this row > E: Discussing new concepts and practicing new skills #2,
// Make this row > F: Developing mastery (Leads to formative assessment),
// Make this row > G: Finding practical/applications of concepts and skills in daily living,
// Make this row > H: Making generalizations and abstractions about the lesson,
// Make this row > I: Evaluating Learning,
// Make this row > J: Additional activities for application or remediation,
// Make this row > REMARKS ,
// Make this row > REFLECTION,
// Make this row > A: No. of learners who earned 80% of the formative assessment under row,
// Make this row > B: No. of learners who require additional activities to remediation,
// Make this row > C: Did the remedial lessons work? No. of learners who have caught up with the lesson,
// Make this row > D: No. of learners who continue to require remediation,
// Make this row > E: Which of my teaching strategies worked well? Why did these work?,
// Make this row > F: What difficulties did I encounter which my principal or supervisor can help me solve?,
// Make this row > G: What innovation or localized material did I use/discover which I wish to share with other teachers?

// Output:
// - One full HTML <table> with all cells filled
// - Make REMARKS, REFLECTION, and these
// A: No. of learners who earned 80% of the formative assessment under row,
// B: No. of learners who require additional activities to remediation,
// C: Did the remedial lessons work? No. of learners who have caught up with the lesson,
// D: No. of learners who continue to require remediation,
// E: Which of my teaching strategies worked well? Why did these work?,
// F: What difficulties did I encounter which my principal or supervisor can help me solve?,
// G: What innovation or localized material did I use/discover which I wish to share with other teachers? EMPTY,
// - Make each session's content slightly different to reflect progression or reinforcement

// Do not skip any section or cell. Fill everything. Respond only with valid <table> Bordered HTML.
// `).trim();
// });


const englishPrompt = computed(() => {
  return (`
Prepare a lesson plan based on the attached exemplar and the specified template.:

Grade Level: ${form.grade}  
Subject: ${form.subject}  
Quarter: ${form.quarter}  
Language: ${form.language}

Content Coverage:
${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Competency Focus:
${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Reference Materials:
${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}

----

Your task:
- Return a complete and fully filled HTML table with cell border
- Read this file ${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}
- Follow this format ${form.template}
- Fill in all rows and columns for **Sessions 1 to 4** in specified template, session column on the top and others at the left
- Include unique and meaningful lesson content in each cell
- content must have 
OBJECTIVES under row[Content Standards, Performance Standards, Learning Competencies/Objectives Write the LC Code for each],
CONTENT,
LEARNING RESOURCES under row['A. References' => [
  '1: Teacher's Guides/Pages',
  '2. Learner's Materials Pages',
  '3. Textbook Pages'
  '4. Additional Materials from Learning Resources (LR) portal'],
  'B. 1. Other Learning Resources'
],
Make this row > PROCEDURES,
Make this row > A: Reviewing previous lesson or presenting the new lesson,
Make this row > B: Establishing a purpose for the lesson,
Make this row > C: Presenting examples/instances of the new lesson,
Make this row > D: Discussing new concepts and practicing new skills #1,
Make this row > E: Discussing new concepts and practicing new skills #2,
Make this row > F: Developing mastery (Leads to formative assessment),
Make this row > G: Finding practical/applications of concepts and skills in daily living,
Make this row > H: Making generalizations and abstractions about the lesson,
Make this row > I: Evaluating Learning,
Make this row > J: Additional activities for application or remediation,
Make this row > REMARKS ,
Make this row > REFLECTION,
Make this row > A: No. of learners who earned 80% of the formative assessment under row,
Make this row > B: No. of learners who require additional activities to remediation,
Make this row > C: Did the remedial lessons work? No. of learners who have caught up with the lesson,
Make this row > D: No. of learners who continue to require remediation,
Make this row > E: Which of my teaching strategies worked well? Why did these work?,
Make this row > F: What difficulties did I encounter which my principal or supervisor can help me solve?,
Make this row > G: What innovation or localized material did I use/discover which I wish to share with other teachers?

Output:
- One full HTML <table> with all cells filled
- Make REMARKS, REFLECTION, and these
A: No. of learners who earned 80% of the formative assessment under row,
B: No. of learners who require additional activities to remediation,
C: Did the remedial lessons work? No. of learners who have caught up with the lesson,
D: No. of learners who continue to require remediation,
E: Which of my teaching strategies worked well? Why did these work?,
F: What difficulties did I encounter which my principal or supervisor can help me solve?,
G: What innovation or localized material did I use/discover which I wish to share with other teachers? EMPTY,
- Make each session's content slightly different to reflect progression or reinforcement

Do not skip any section or cell. Fill everything. Respond only with valid <table> Bordered HTML.
`).trim();
});


const tagalogPrompt = computed(() => {
  return (`
Prepare a lesson plan based on the attached exemplar and the specified template.:

Grade Level: ${form.grade}  
Subject: ${form.subject}  
Quarter: ${form.quarter}  
Language: ${form.language}

Content Coverage:
${form.raw_content.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Competency Focus:
${form.raw_competencies.map((c, i) => `${i + 1}. ${c}`).join('\n')}

Reference Materials:
${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}

----

Your task:
- Return a complete and fully filled HTML table with cell border
- Read this file ${form.raw_reference.map((c, i) => `${i + 1}. ${c}`).join('\n')}
- Follow this format ${form.template}
- Fill in all rows and columns for **Sesyon 1 to 4** in specified template, Sesyon column on the top and others at the left
- Include unique and meaningful lesson content in each cell
- content must have 
I.LAYUNIN  under row[A.Pamantayang Pangnilalaman , B.Pamantayan sa Pagganap , C.Mga Kasanayan sa Pagkatuto 
Isulat ang code ng bawat kasanayan],
II.NILALAMAN ,
III.KAGAMITANG PANTURO under row['A.SANGGUNIAN ' => [
  '1.Mga Pahina sa Gabay ng Guro',
  '2. Mga Pahina sa Kagamitang Pang-Mag-aaral',
  '3. Mga Pahina sa Teksbuk'
  '4. Karagdagang Kagamitan mula sa Portal ng Learning Resource'],
  'B. => Iba Pang Kagamitang Panturo'
],
Make this row > IV.PAMAMARAAN ,
Make this row > A.Balik-aral sa nakaraang aralin at/o pagsisimula ng bagong aralin,
Make this row > B.Paghahabi sa layunin ng aralin,
Make this row > C.Pag-uugnay ng mga halimbawa sa bagong aralin,
Make this row > D.Pagtalakay ng bagong konsepto at paglalahad ng bagong kasanayan #1,
Make this row > E.Pagtalakay ng bagong konsepto at paglalahad ng bagong kasanayan #2,
Make this row > F: F.Paglinang sa Kabihasaan (tungo sa Formative Assessment),
Make this row > G.Paglalapat ng aralin sa pang-araw-araw na buhay,
Make this row > H. Paglalahat ng Aralin,
Make this row > I.Pagtataya ng Aralin,
Make this row > J.Karagdagang gawain para sa takdang-aralin at remediation,
Make this row > V.MGA TALA,
Make this row > VI.PAGNINILAY,
Make this row > A.Bilang ng mag-aaral na nakakuha ng 80% sa pagtataya,
Make this row > B.Bilang ng mag-aaral na nangangailangan ng iba pang gawain para sa remediation,
Make this row > C.Nakatulong ba ang remedial? Bilang ng mag-aaral na nakaunawa sa aralin,
Make this row > D.Bilang ng mag-aaral na magpapatuloy sa remediation,
Make this row > E.Alin sa mga istratehiya ng pagtuturo na nakatulong ng lubos? Paano ito nakatulong?,
Make this row > F.Anong suliranin ang aking nararanasan, nasolusyunan sa tulong ng aking punungguro at superbisor?,
Make this row > G.Anong kagamitang panturo ang aking nadebuho na nais kong ibahagi sa kapwa ko guro

Output:
- One full HTML <table> with all cells filled
- Make V.MGA TALA, VI.PAGNINILAY, and these
A: No. of learners who earned 80% of the formative assessment under row,
B: No. of learners who require additional activities to remediation,
C: Did the remedial lessons work? No. of learners who have caught up with the lesson,
D: No. of learners who continue to require remediation,
E: Which of my teaching strategies worked well? Why did these work?,
F: What difficulties did I encounter which my principal or supervisor can help me solve?,
G: What innovation or localized material did I use/discover which I wish to share with other teachers? EMPTY,

A.Bilang ng mag-aaral na nakakuha ng 80% sa pagtataya,
B.Bilang ng mag-aaral na nangangailangan ng iba pang gawain para sa remediation,
C.Nakatulong ba ang remedial? Bilang ng mag-aaral na nakaunawa sa aralin,
D.Bilang ng mag-aaral na magpapatuloy sa remediation,
E.Alin sa mga istratehiya ng pagtuturo na nakatulong ng lubos? Paano ito nakatulong?,
F.Anong suliranin ang aking nararanasan, nasolusyunan sa tulong ng aking punungguro at superbisor?,
G.Anong kagamitang panturo ang aking nadebuho na nais kong ibahagi sa kapwa ko guro
- Make each session's content slightly different to reflect progression or reinforcement
- Response must be in tagalog language and level of language is humanize
- Content for V. MGA TALA and VI. PAGNINILAY should be blank
Do not skip any section or cell. Fill everything. Respond only with valid <table> Bordered HTML.
`).trim();
});
  


const submitGenerate = () => {
  if (gradeValidator.$invalid || gradeValidator.$invalid || subjectValidator.$invalid || quarterValidator.$invalid || raw_contentValidator.$invalid || raw_competenciesValidator.$invalid || languageValidator.$invalid) {
    toastr.error('Some of the fields required to proceed are currently empty or incomplete. Kindly fill in all the necessary details before continuing.');
    return false;
  }

  const actualPromt = useForm({
    // prompt: prompt.value,
    prompt: form.language === 'English'
    ? englishPrompt.value
    : form.language === 'Tagalog'
    ? tagalogPrompt.value
    : '',
    plugin: 'Lesson Planner',
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
    plugin: 'Lesson Planner',
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

watch(() => selectedLanguage.value, (language) => {
  if(language){
    if(language.language == 'English'){
      apiData.templates = [
        {
          id: 1,
          caption: 'English daily lesson log',
          link: 'https://hivessel.com/LE/DLL_ENGLISH%20TEMPLATE.pdf'
        },
        // {
        //   id: 3,
        //   caption: 'English detailed lesson Plan',
        //   link: 'https://ncrdeped2-my.sharepoint.com/:w:/g/personal/antonette_castillo_ncr2_deped_gov_ph/EUAwGaaHwSlHp1AUYNCOE40BB3retUjEqWdwGb10oWaveQ?e=LMJ0ex'
        // },
      ];

      selectedTemplate.value = apiData.templates.find((item) => item.id == 1);
    }

    if(language.language == 'Tagalog'){
      apiData.templates = [
        {
          id: 2,
          caption: 'Tagalog daily lesson log',
          link: 'https://ncrdeped2-my.sharepoint.com/:w:/g/personal/antonette_castillo_ncr2_deped_gov_ph/EdCURPB1lr9Ov4KrKryLN_IBoggKgTek9abmWJ3hvne9sg?e=Dzp6Uf'
        },
        //{
        //   id: 4,
        //   caption: 'Tagalog detailed lesson Plan',
        //   link: 'https://ncrdeped2-my.sharepoint.com/:w:/g/personal/antonette_castillo_ncr2_deped_gov_ph/ETWSm35dopdLvpIuwiU-jR4BfJPojbnpYEF_ROioTpjkrg?e=T5bNCf'
        // },
      ];

      selectedTemplate.value = apiData.templates.find((item) => item.id == 2);
    }
  }else{
    apiData.templates = [];
  }
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
