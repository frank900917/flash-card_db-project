<template>
    <div v-if="flashCardSet" class="container-lg py-5">
        <!-- 標題區 -->
        <div class="d-flex row items-center justify-between">
            <h1 class="mb-4 me-auto col-xl pe-0">{{ flashCardSet.title }}</h1>
            <div class="ms-auto col-xl-4 align-self-center d-flex justify-content-xl-end ps-0">
                <button v-if="quizState === 0" class="btn btn-info m-2" data-bs-toggle="modal"
                    data-bs-target="#startQuizModal">測驗</button>
                <button v-if="quizState === 1" class="btn btn-outline-danger m-2" @click="quizState = 0">取消測驗</button>
                <button v-if="quizState === 2" class="btn btn-outline-info m-2" @click="quizState = 0">離開測驗</button>
                <NuxtLink :to="{ name: 'flashCard-edit-id', params: { id: id } }"
                    v-if="user?.id === flashCardSet.user_id" class="btn btn-primary m-2 align-self-center">
                    編輯
                </NuxtLink>
                <button v-if="user?.id === flashCardSet.user_id" type="button"
                    class="btn btn-danger m-2 align-self-center" :disabled="isSubmitting" data-bs-toggle="modal"
                    data-bs-target="#confirmModal">
                    刪除
                </button>
                <NuxtLink to="/account" v-if="route.query.from === 'account'"
                    class="btn btn-success m-2 align-self-center">
                    返回帳戶
                </NuxtLink>
                <NuxtLink to="/public" v-else class="btn btn-success m-2 align-self-center">
                    返回
                </NuxtLink>
            </div>
        </div>
        <!-- 單字集基本資料 -->
        <span class="align-self-center badge" :class="[flashCardSet.isPublic ? 'bg-primary' : 'bg-success']">
            {{ flashCardSet.isPublic ? "公開" : "私人" }}單字集
        </span>
        <p class="text-gray-600 my-3">{{ flashCardSet?.description }}</p>
        <!-- 顯示單字集 -->
        <div v-if="quizState === 0">
            <div class="d-flex align-items-center my-3 pb-2 border-bottom">
                <h2 class="me-auto">單字列表</h2>
                <button class="btn btn-secondary m-2" data-bs-toggle="modal" data-bs-target="#ttsModal">語音設定</button>
                <PaginationSizeSelector v-model="perPage" :onChange="fetchFlashCardSet" />
            </div>
            <div class="space-y-4 mt-6">
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <div class="col-4 d-flex justify-content-center">單字</div>
                    <div class="col-7 d-flex justify-content-center">說明</div>
                </div>
                <div v-for="(detail, index) in flashCardSet.details.data" :key="index"
                    class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index +
                        flashCardSet.details.from }}</div>
                    <div class="col-4 p-3 border-start d-flex align-items-center">
                        <button type="button" class="btn p-0 d-flex align-items-center" @click="speak(detail.word)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-volume-up-fill" viewBox="0 0 16 16">
                                <path
                                    d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z" />
                                <path
                                    d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z" />
                                <path
                                    d="M8.707 11.182A4.5 4.5 0 0 0 10.025 8a4.5 4.5 0 0 0-1.318-3.182L8 5.525A3.5 3.5 0 0 1 9.025 8 3.5 3.5 0 0 1 8 10.475zM6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06" />
                            </svg>
                            <div class="text-start">
                                {{ detail.word }}
                            </div>
                        </button>
                    </div>
                    <div class="col-7 p-3 border-start d-flex align-items-center">{{ detail.word_description }}</div>
                </div>
            </div>
            <PaginationControl v-model="page" :datas="flashCardSet.details" :fetchDatas="fetchFlashCardSet" />
        </div>
        <!-- 單字集測驗表單 -->
        <form v-if="quizState === 1" @submit.prevent="handlefinishQuiz">
            <div class="d-flex my-3 pb-2 border-bottom">
                <h2 class="me-auto align-self-center mb-0">測驗</h2>
                <button v-if="quizType === 'listening'" type="button" class="btn btn-secondary m-2"
                    data-bs-toggle="modal" data-bs-target="#ttsModal">語音設定</button>
                <button type="submit" class="btn btn-info align-self-center">完成測驗</button>
            </div>
            <div class="space-y-4 mt-6">
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <template v-if="quizType === 'listening'">
                        <div class="col-1 d-flex justify-content-center">聽力</div>
                        <div class="col-4 d-flex justify-content-center">單字</div>
                        <div class="col-6 d-flex justify-content-center">說明</div>
                    </template>
                    <template v-else>
                        <div class="col-4 d-flex justify-content-center">單字</div>
                        <div class="col-7 d-flex justify-content-center">說明</div>
                    </template>
                </div>
                <div v-for="(item, index) in quizData" :key="index" class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index + 1 }}</div>
                    <template v-if="quizType === 'listening'">
                        <div class="col-1 p-3 border-start d-flex align-items-center justify-content-center">
                            <button type="button" class="btn p-0" @click="speak(item.word)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-volume-up-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z" />
                                    <path
                                        d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z" />
                                    <path
                                        d="M8.707 11.182A4.5 4.5 0 0 0 10.025 8a4.5 4.5 0 0 0-1.318-3.182L8 5.525A3.5 3.5 0 0 1 9.025 8 3.5 3.5 0 0 1 8 10.475zM6.717 3.55A.5.5 0 0 1 7 4v8a.5.5 0 0 1-.812.39L3.825 10.5H1.5A.5.5 0 0 1 1 10V6a.5.5 0 0 1 .5-.5h2.325l2.363-1.89a.5.5 0 0 1 .529-.06" />
                                </svg>
                            </button>
                        </div>
                    </template>
                    <div class="col-4 p-3 border-start d-flex align-items-center">
                        <template v-if="quizType === 'word' || quizType === 'listening'">
                            <input v-model="item.wordAnswer" type="text" class="form-control" placeholder="輸入單字" />
                        </template>
                        <template v-else>
                            {{ item.word }}
                        </template>
                    </div>
                    <template v-if="quizType === 'listening'">
                        <div class="col-6 p-3 border-start d-flex align-items-center">
                            <input v-model="item.descriptionAnswer" type="text" class="form-control"
                                placeholder="輸入說明" />
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-7 p-3 border-start d-flex align-items-center">
                            <template v-if="quizType === 'description'">
                                <input v-model="item.descriptionAnswer" type="text" class="form-control"
                                    placeholder="輸入說明" />
                            </template>
                            <template v-else>
                                {{ item.word_description }}
                            </template>
                        </div>
                    </template>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-info m-2">完成測驗</button>
                </div>
            </div>
        </form>
        <!-- 單字集測驗結果 -->
        <div v-if="quizState === 2">
            <div class="d-flex my-3 pb-2 border-bottom">
                <h2 class="me-auto align-self-center mb-0">測驗結果</h2>
            </div>
            <div class="mt-3">
                <div class="fw-bold">答題數：{{ correctCount }}/{{ quizData.length }}</div>
                <div class="fw-bold">答題率：{{ accuracyRate }}%</div>
            </div>
            <div class="space-y-4 mt-6">
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <template v-if="quizType === 'word'">
                        <div class="col-2 d-flex justify-content-center">單字</div>
                        <div class="col-2 d-flex justify-content-center">你的回答</div>
                        <div class="col-7 d-flex justify-content-center">說明</div>
                    </template>
                    <template v-if="quizType === 'description'">
                        <div class="col-3 d-flex justify-content-center">單字</div>
                        <div class="col-4 d-flex justify-content-center">說明</div>
                        <div class="col-4 d-flex justify-content-center">你的回答</div>
                    </template>
                    <template v-if="quizType === 'listening'">
                        <div class="col-2 d-flex justify-content-center">單字</div>
                        <div class="col-2 d-flex justify-content-center">你的回答</div>
                        <div class="col-7 d-flex">
                            <div class="col-6 d-flex justify-content-center">說明</div>
                            <div class="col-6 d-flex justify-content-center">你的回答</div>
                        </div>
                    </template>
                </div>
                <div v-for="(item, index) in quizData" :key="index" class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index + 1 }}</div>
                    <template v-if="quizType === 'word'">
                        <div class="col-2 p-3 border-start d-flex align-items-center">{{ item.word }}</div>
                        <div class="col-2 p-3 border-start d-flex align-items-center">
                            <div :class="item.isWordCorrect ? 'text-success' : 'text-danger'">{{ item.answer }}</div>
                        </div>
                        <div class="col-7 p-3 border-start d-flex align-items-center">{{ item.word_description }}</div>
                    </template>
                    <template v-if="quizType === 'description'">
                        <div class="col-3 p-3 border-start d-flex align-items-center">{{ item.word }}</div>
                        <div class="col-4 p-3 border-start d-flex align-items-center">{{ item.word_description }}</div>
                        <div class="col-4 p-3 border-start d-flex align-items-center">
                            <div :class="item.isDescriptionCorrect ? 'text-success' : 'text-danger'">{{ item.answer }}
                            </div>
                        </div>
                    </template>
                    <template v-if="quizType === 'listening'">
                        <div class="col-2 p-3 border-start d-flex align-items-center">{{ item.word }}</div>
                        <div class="col-2 p-3 border-start d-flex align-items-center">
                            <div :class="item.isWordCorrect ? 'text-success' : 'text-danger'">{{ item.answer.word }}
                            </div>
                        </div>
                        <div class="col-7 d-flex">
                            <div class="col-6 p-3 border-start d-flex align-items-center">{{ item.word_description }}
                            </div>
                            <div class="col-6 p-3 border-start d-flex align-items-center">
                                <div :class="item.isDescriptionCorrect ? 'text-success' : 'text-danger'">{{
                                    item.answer.description }}</div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-outline-info m-2" @click="quizState = 0">離開測驗</button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="error?.statusCode === 404" class="container-lg py-5">
        <p class="border rounded p-5 row justify-content-center fs-5 text-danger">此單字集不存在</p>
    </div>
    <!-- 刪除單字集確認視窗 -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">確認</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">確定要刪除此單字集？</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" @click="handleDelate">確定</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 測驗設定視窗 -->
    <div class="modal fade" id="startQuizModal" tabindex="-1" aria-labelledby="startQuizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="startQuizForm" @submit.prevent="handleStartQuiz">
                    <div class="modal-header">
                        <h5 class="modal-title" id="startQuizModalLabel">開始測驗</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quizType" class="form-label w-100">測驗類型</label>
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="quizType" id="wordCheckbox"
                                    value="word" :class="{ 'is-invalid': errors.quizType }">
                                <label for="wordCheckbox">單字</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quizType" id="descriptionCheckbox"
                                    value="description" :class="{ 'is-invalid': errors.quizType }">
                                <label for="descriptionCheckbox">說明</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quizType" id="listeningCheckbox"
                                    value="listening" :class="{ 'is-invalid': errors.quizType }">
                                <label for="listeningCheckbox">聽力</label>
                            </div>
                            <div class="text-danger">{{ errors.quizType }}</div>
                        </div>
                        <div>
                            <label for="quizNumber" class="form-label">測驗單字數</label>
                            <input type="number" class="form-control" id="quizNumber"
                                :placeholder="`需介於 1 至 ${flashCardSet.details.total} 之間`"
                                :class="{ 'is-invalid': errors.quizNumber }">
                            <div class="invalid-feedback">{{ errors.quizNumber }}</div>
                        </div>
                        <div class="text-danger">{{ errors.unique }}</div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary">確定</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 語音設定視窗 -->
    <div class="modal fade" id="ttsModal" tabindex="-1" aria-labelledby="ttsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="ttsForm" @submit.prevent="handleTTSSubmit">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ttsModalLabel">語音設定</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 語音服務 -->
                        <div class="mb-3">
                            <label for="ttsType" class="form-label">語音服務</label>
                            <div id="ttsType">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="webTTS" value="web"
                                        v-model="tempTTSSettings.type">
                                    <label class="form-check-label" for="webTTS">Web Speech</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="googleTTS" value="google"
                                        v-model="tempTTSSettings.type" :disabled="!ttsForm.isGoogleTTSEnabled">
                                    <label class="form-check-label" for="googleTTS">Google TTS</label>
                                </div>
                            </div>
                        </div>

                        <!-- 語言選擇 -->
                        <div class="mb-3">
                            <label for="ttsLanguage" class="form-label">語言</label>
                            <div id="ttsLanguage">
                                <!-- 常見語言單選框 -->
                                <div class="form-check form-check-inline"
                                    v-for="(label, key) in ttsForm.language[tempTTSSettings.type].common" :key="key">
                                    <input class="form-check-input" type="radio" :id="'lang_' + key" :value="key"
                                        v-model="tempTTSSettings.lang">
                                    <label class="form-check-label" :for="'lang_' + key">{{ label }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="lang_other" value="other"
                                        v-model="tempTTSSettings.lang">
                                    <label class="form-check-label" for="lang_other">其他</label>
                                </div>
                                <!-- 其他語言下拉選單 -->
                                <div v-if="tempTTSSettings.lang === 'other'" class="mt-2">
                                    <select v-model="tempTTSSettings.customLang" class="form-select" :class="{ 'is-invalid': errors.customLang }">
                                        <option v-for="lang in ttsForm.language[tempTTSSettings.type].other" :key="lang"
                                            :value="lang">{{ lang }}</option>
                                    </select>
                                    <div class="text-danger">{{ errors.customLang }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- 語速 -->
                        <div class="mb-3">
                            <label for="ttsSpeed" class="form-label">語速</label>
                            <div id="ttsSpeed">
                                <div class="form-check form-check-inline" v-for="rate in ttsForm.speechRates"
                                    :key="rate">
                                    <input class="form-check-input" type="radio" :id="'speed_' + rate" :value="rate"
                                        v-model.number="tempTTSSettings.speed">
                                    <label class="form-check-label" :for="'speed_' + rate">{{ rate }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary">確定</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
const route = useRoute();
const id = route.params.id;
const isSubmitting = ref(false);
const user = useSanctumUser();
const page = ref(1);
const perPage = ref(25);
const quizState = ref(0);
const quizData = ref([]);
const quizType = ref('');
const correctCount = ref(0);
const accuracyRate = ref(0);
const errors = ref({});
const { $bootstrap } = useNuxtApp();
const { apiBase } = useRuntimeConfig().public;
const { csrfURL } = useRuntimeConfig().public;

onMounted(() => {
    // TTS 初始化
    initTTS();
});

// 取得單字集資料
const { data: flashCardSet, error } = await useSanctumFetch(`${apiBase}/flashCard/${id}?&page=${page.value}&perPage=${perPage.value}`);
if (error.value?.statusCode === 404) {
    throw createError({ statusCode: 404, message: '此單字集不存在' });
} else if (error.value?.statusCode === 403) {
    throw createError({ statusCode: 403, message: '此單字集為私人單字集' });
}

// 換頁更新單字集清單
async function fetchFlashCardSet(isPerPage) {
    if (isPerPage) {
        page.value = 1;
    }
    const data = await $fetch(`${apiBase}/flashCard/${id}?&page=${page.value}&perPage=${perPage.value}`, {
        method: 'GET',
        credentials: 'include'
    });
    flashCardSet.value = data;
}

// 刪除單字集
async function handleDelate() {
    const confirmModal = $bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    confirmModal.hide();
    try {
        isSubmitting.value = true;
        await $fetch(csrfURL, {
            credentials: 'include'
        });
        await $fetch(`${apiBase}/flashCard/${id}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
            }
        });
        navigateTo('/account');
    } catch (error) {
        alert(error);
        isSubmitting.value = false;
    }
}

// 開始測驗
async function handleStartQuiz() {
    quizType.value = document.querySelector('input[name="quizType"]:checked')?.value;
    const quizNumberInput = document.getElementById('quizNumber');
    const quizNumber = Number(quizNumberInput.value);

    errors.value = {};
    if (!quizType.value) {
        errors.value.quizType = '請選擇測驗類型';
        return false;
    }

    if (!Number.isInteger(quizNumber)) {
        errors.value.quizNumber = '請輸入有效的整數';
        return false;
    }

    const data = await $fetch(`${apiBase}/flashCard/details/${id}`, {
        method: 'GET',
        credentials: 'include'
    });

    if (quizNumber < 1 || quizNumber > data.length) {
        errors.value.quizNumber = `請輸入介於 1 至 ${data.length} 之間的整數`;
        return false;
    }

    if (quizType.value === 'word') {
        const unique = new Set();
        for (const item of data) {
            if (unique.has(item.word_description)) {
                errors.value.unique = `此單字集含有重複的說明：${item.word_description}，無法進行單字測驗`;
                return false;
            }
            unique.add(item.word_description);
        }
    }

    // 隨機排序抽取單字
    const shuffled = [...data].sort(() => 0.5 - Math.random());
    quizData.value = shuffled.slice(0, quizNumber);

    quizState.value = 1;

    const startQuizModal = $bootstrap.Modal.getInstance(document.getElementById('startQuizModal'));
    startQuizModal.hide();
}

// 結束測驗
function handlefinishQuiz() {
    let score = 0;

    quizData.value = quizData.value.map(item => {
        const quizTypeVal = quizType.value;

        const normalize = (text) => text.split(/[；;,、\s]+/).map(s => s.trim().toLowerCase()).filter(s => s.length > 0).sort();

        const correctWord = item.word;
        const correctDesc = item.word_description;

        let userWord = (item.wordAnswer || '').trim();
        let userDesc = (item.descriptionAnswer || '').trim();

        let isWordCorrect = false;
        let isDescriptionCorrect = false;

        if (quizTypeVal === 'word') {
            isWordCorrect = userWord.toLowerCase() === correctWord.toLowerCase();
            if (isWordCorrect) score += 1;
            userWord = userWord || '未作答';
        } else if (quizTypeVal === 'description') {
            const userParts = normalize(userDesc);
            const correctParts = normalize(correctDesc);
            isDescriptionCorrect = userParts.length > 0 && userParts.every((val, idx) => val === correctParts[idx]);
            if (isDescriptionCorrect) score += 1;
            userDesc = userDesc || '未作答';
        } else if (quizTypeVal === 'listening') {
            isWordCorrect = userWord.toLowerCase() === correctWord.toLowerCase();
            const userParts = normalize(userDesc);
            const correctParts = normalize(correctDesc);
            isDescriptionCorrect = userParts.length > 0 && userParts.every((val, idx) => val === correctParts[idx]);
            if (isWordCorrect) score += 0.5;
            if (isDescriptionCorrect) score += 0.5;
            userWord = userWord || '未作答';
            userDesc = userDesc || '未作答';
        }

        return {
            ...item,
            answer: quizTypeVal === 'listening' ? { word: userWord, description: userDesc } :
                quizTypeVal === 'word' ? userWord : userDesc,
            isWordCorrect,
            isDescriptionCorrect
        };
    });

    correctCount.value = score;
    accuracyRate.value = ((score / quizData.value.length) * 100).toFixed(2);

    quizState.value = 2;
}

// 預設 TTS 設定
const defaultTTSSettings = {
    type: 'web',
    speed: 1.0,
    lang: 'en-US'
};

// 儲存應用的語音設定
const ttsSettings = ref(defaultTTSSettings);

// 表單暫存設定
const tempTTSSettings = ref({ ...ttsSettings.value, customLang: '' });

// 語音設定視窗參數
const ttsForm = ref({
    isGoogleTTSEnabled: false,
    language: {
        web: {
            common: {
                'en-US': '英文（美國）',
                'zh-TW': '中文（台灣）',
                'ja-JP': '日文'
            },
            other: []
        },
        google: {
            common: {
                'en-US': '英文（美國）',
                'cmn-TW': '中文（台灣）',
                'ja-JP': '日文'
            },
            other: []
        }
    },
    speechRates: [0.5, 0.75, 1.0, 1.25, 1.5, 2.0]
});

// TTS 初始化
function initTTS() {
    // 取得 Web TTS 支援語言
    const setVoices = () => {
        const voices = window.speechSynthesis.getVoices();
        const langs = [...new Set(voices.map(voice => voice.lang))].sort();
        const commonLangs = Object.keys(ttsForm.value.language.web.common);
        ttsForm.value.language.web.other = langs.filter(lang => !commonLangs.includes(lang));
    };
    setVoices();
    window.speechSynthesis.onvoiceschanged = setVoices;

    // 從 localStorage 載入語音設定或使用預設值
    const saved = localStorage.getItem('ttsSettings');
    if (saved) {
        ttsSettings.value = JSON.parse(saved);
        // 檢查語言是否在常見語言中
        if (ttsForm.value.language[ttsSettings.value.type].common[ttsSettings.value.lang]) {
            tempTTSSettings.value = {
                type: ttsSettings.value.type,
                speed: ttsSettings.value.speed,
                lang: ttsSettings.value.lang,
                customLang: ''
            };
        } else {
            tempTTSSettings.value = {
                type: ttsSettings.value.type,
                speed: ttsSettings.value.speed,
                lang: 'other',
                customLang: ttsSettings.value.lang
            };
        }
    }

    // 關閉語音設定視窗時重置選項
    document.getElementById('ttsModal').addEventListener('hidden.bs.modal', () => {
        // 檢查語言是否在常見語言中
        if (ttsForm.value.language[ttsSettings.value.type].common[ttsSettings.value.lang]) {
            tempTTSSettings.value = {
                type: ttsSettings.value.type,
                speed: ttsSettings.value.speed,
                lang: ttsSettings.value.lang,
                customLang: ''
            };
        } else {
            tempTTSSettings.value = {
                type: ttsSettings.value.type,
                speed: ttsSettings.value.speed,
                lang: 'other',
                customLang: ttsSettings.value.lang
            };
        }
    });
}

// 檢查 Google TTS 是否可用
const { data: googleTTSStatus } = await useSanctumFetch(`${apiBase}/google-tts/status`);
ttsForm.value.isGoogleTTSEnabled = googleTTSStatus.value.enabled === true;

// 取得 Google TTS 支援語言
if (ttsForm.value.isGoogleTTSEnabled) {
    const { data: googleTTSLanguages } = await useSanctumFetch(`${apiBase}/google-tts/languages`);
    ttsForm.value.language.google.other = googleTTSLanguages.value.filter(
        lang => !Object.keys(ttsForm.value.language.google.common).includes(lang)
    );
}

// 提交表單，更新 ttsSettings
const handleTTSSubmit = () => {
    const selectedLang = tempTTSSettings.value.lang === 'other' ? tempTTSSettings.value.customLang : tempTTSSettings.value.lang;
    errors.value = {};
    if (tempTTSSettings.value.lang === 'other' && !ttsForm.value.language[tempTTSSettings.value.type].other.includes(selectedLang)) {
        errors.value.customLang = '請選擇有效的語言';
        return;
    }
    ttsSettings.value = {
        type: tempTTSSettings.value.type,
        speed: tempTTSSettings.value.speed,
        lang: selectedLang
    };
    localStorage.setItem('ttsSettings', JSON.stringify(ttsSettings.value));
    const ttsModal = $bootstrap.Modal.getInstance(document.getElementById('ttsModal'));
    ttsModal.hide();
};

// Web TTS 朗讀
const webTTSSpeak = (text, lang, speed) => {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    utterance.rate = speed;
    speechSynthesis.speak(utterance);
};

// Google TTS 朗讀
const googleTTSSpeak = async (text, lang, speed) => {
    await $fetch(csrfURL, { credentials: 'include' });
    const blob = await $fetch(`${apiBase}/google-tts/synthesize`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
        },
        body: { text, lang, speed }
    });
    const audio = new Audio(URL.createObjectURL(blob));
    audio.play();
};

// 朗讀功能
const speak = (text) => {
    if (ttsSettings.value.type === 'google') {
        googleTTSSpeak(text, ttsSettings.value.lang, ttsSettings.value.speed);
    } else {
        webTTSSpeak(text, ttsSettings.value.lang, ttsSettings.value.speed);
    }
};
</script>