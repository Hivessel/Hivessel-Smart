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
                        class="border border-warning"
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
                        class="border border-warning"
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
                        class="border border-warning"
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
                        class="border border-warning"
                        multiple
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
                        class="border border-warning"
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
                        class="border border-warning"
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
                        class="border border-warning"
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
                        class="border border-warning"
                        data-width="100%"
                        track-by="id"
                        :key="selectedGrade?.id"
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
                        class="border border-warning"
                        data-width="100%"
                        track-by="id"
                        :key="selectedSubject?.id"
                        :options="apiData.no_of_choices"
                        placeholder="-- Select No. Of Choices --"
                        v-model="selectedNoOfChoices"
                        label="item"
                      />
                    </div>
                  </div>

                  <div class="col-12">
                    <button class="btn btn-warning w-100 text-white">Generate</button>
                  </div>


                </div>
                <!-- <pre>{{ form }}</pre> -->
              </div>
            </div>

            <div
              class="tab-pane fade"
              :class="{ show: currentTab === 'history', active: currentTab === 'history' }"
              id="history"
              role="tabpanel"
            >
              <div class="p-3 border rounded bg-white">
                This is the <strong>History</strong> tab content.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { onMounted, reactive, ref, computed, watch } from 'vue';
import Layout from '../Shared/Layout.vue';
import Multiselect from 'vue-multiselect';
defineOptions({ layout: Layout });

const page = usePage();

const apiData = reactive({
  grades: [],
  subjects: [],
  quarters: [],
  contents: [],
  competencies: [],
  proficiency_levels: [],
  languages: [],
  no_of_questions: [
    {item: 5},
    {item: 10},
    {item: 15},
    {item: 20},
    {item: 25},
    {item: 30},
    {item: 35},
    {item: 40},
    {item: 45},
    {item: 50},
  ],
  no_of_choices: [
    {item: 4},
    {item: 5},
    {item: 6},
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

// const form = useForm({
//   grade: computed(() => selectedGrade.value?.level || null),
//   subject: computed(() => selectedSubject.value?.subject || null),
//   quarter: computed(() => selectedQuarter.value?.quarter || null),
//   content: computed(() =>
//     Array.isArray(selectedContent.value)
//       ? selectedContent.value.map(el => el.id)
//       : []
//   ),
//   competencies: computed(() =>
//     Array.isArray(selectedCompetencies.value)
//       ? selectedCompetencies.value.map(el => el.id)
//       : []
//   ),
// });

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
</style>
