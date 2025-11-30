<template>
    <form @submit.prevent="handleSubmit">
        <div class="mb-3">
            <label class="form-label">標題</label>
            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': errors.title }"
                required />
            <div class="invalid-feedback">{{ errors.title }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">說明</label>
            <textarea v-model="form.description" class="form-control"
                :class="{ 'is-invalid': errors.description }"></textarea>
            <div class="invalid-feedback">{{ errors.description }}</div>
        </div>

        <div class="form-check form-switch mb-4">
            <input v-model="form.isPublic" class="form-check-input" type="checkbox" id="publicSwitch">
            <label class="form-check-label" for="publicSwitch">公開此單字集</label>
        </div>

        <div class="d-flex items-center">
            <h2 class="mb-3 me-auto">單字列表</h2>
            <button type="button" class="btn btn-outline-primary align-self-center mx-2" data-bs-toggle="modal"
                data-bs-target="#importModal">
                導入單字
            </button>
            <button type="submit" class="btn btn-primary align-self-center mx-2"
                :disabled="isSubmitting || form.details.length === 0">保存</button>
        </div>


        <div v-for="(detail, index) in form.details" :key="index" class="card mb-3 p-3">
            <div class="row align-items-center g-2">
                <div class="col-sm-1 d-flex justify-content-center align-items-center">
                    <span class="fw-bold">{{ index + 1 }}</span>
                </div>
                <div class="col-sm d-flex flex-column" style="min-width: 150px;">
                    <label class="form-label text-center">單字</label>
                    <input v-model="detail.word" type="text" class="form-control"
                        :class="{ 'is-invalid': errors[`details.${index}.word`] }" required>
                    <div class="invalid-feedback">{{ errors[`details.${index}.word`] }}</div>
                </div>
                <div class="col-sm d-flex flex-column" style="min-width: 150px;">
                    <label class="form-label text-center">說明</label>
                    <input v-model="detail.word_description" type="text" class="form-control"
                        :class="{ 'is-invalid': errors[`details.${index}.word_description`] }" required>
                    <div class="invalid-feedback">{{ errors[`details.${index}.word_description`] }}</div>
                </div>
                <div class="col-lg-1 col-sm-auto d-flex align-items-center">
                    <button type="button" class="btn btn-danger w-100" @click="removeDetail(index)">刪除</button>
                </div>
            </div>
        </div>
        <div v-if="form.details.length === 0" class="text-danger text-center my-3">至少需要新增一個單字</div>

        <div class="d-flex justify-content-center mb-4">
            <button type="button" class="btn btn-outline-success" @click="addDetail">
                新增單字
            </button>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary"
                :disabled="isSubmitting || form.details.length === 0">保存</button>
        </div>
    </form>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">導入單字</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    輸入 CSV 格式文字：
                    <textarea class="form-control" :placeholder="'單字1, 說明1\n單字2, 說明2\n單字3, 說明3'" id="floatingTextarea2"
                        rows="5" v-model="bulkInput" @input="parseInput"></textarea>
                    <div :class="['mt-2', parseStatus.isError ? 'text-danger' : 'text-success']">
                        {{ parseStatus.message }}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        @click="handleBulk">確定</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const { form, author, id } = defineProps({
    form: Object,
    author: String,
    id: String
});
const errors = ref({});
const isSubmitting = ref(false);
const { $bootstrap } = useNuxtApp();

// 新增單字欄
function addDetail() {
    form.details.push({ word: '', word_description: '' });
};

// 刪除單字欄
function removeDetail(index) {
    form.details.splice(index, 1);
};

// 導入單字
const bulkInput = ref('');
const tempDetails = ref([]);
const parseStatus = ref({
    message: '已解析 0 個單字',
    isError: false
});

// 導入單字輸入框、暫存
function parseInput() {
    const lines = bulkInput.value.split('\n').filter(line => line.trim() !== '');
    const parsed = [];
    for (const line of lines) {
        const [word, description] = line.split(',').map(s => s?.trim())
        if (!word || !description) {
            parseStatus.value = {
                message: '解析失敗，請確認格式為：單字, 說明',
                isError: true
            };
            return
        }
        parsed.push({ word, word_description: description });
    }
    tempDetails.value = parsed;
    parseStatus.value = {
        message: `已解析 ${parsed.length} 個單字`,
        isError: false
    };
}

// 導入單字確認處理
function handleBulk() {
    const importModal = $bootstrap.Modal.getInstance(document.getElementById('importModal'));
    importModal.hide();
    while (form.details.length > 0) {
        const last = form.details[form.details.length - 1];
        if (last.word === '' && last.word_description === '') {
            form.details.pop();
        } else {
            break
        }
    }
    form.details.push(...tempDetails.value);
    tempDetails.value = [];
    bulkInput.value = '';
    parseStatus.value = {
        message: '',
        isError: false
    };
}

// 表單驗證
function validate() {
    errors.value = {};

    if (form.details.length === 0) {
        return false;
    }

    if (form.title.length === 0) {
        errors.value.title = '請輸入標題';
        return false;
    }

    if (form.title.length > 255) {
        errors.value.title = '標題不能超過 255 個字元';
        return false;
    }

    const wordIndices = {};

    const wordError = form.details.some((detail, index) => {
        // 去除頭尾空格
        detail.word = detail.word.trim();

        if (detail.word.length === 0) {
            errors.value[`details.${index}.word`] = '請輸入單字';
            return true;
        }

        if (detail.word.length > 255) {
            errors.value[`details.${index}.word`] = '單字不能超過 255 個字元';
            return true;
        }

        // 大小寫視為相同
        const word = detail.word.toLowerCase();
        if (wordIndices[word] !== undefined) {
            errors.value[`details.${index}.word`] = '單字不可重複';
            errors.value[`details.${wordIndices[word]}.word`] = '單字不可重複';
            return true;
        } else {
            wordIndices[word] = index;
        }

        if (detail.word_description.length === 0) {
            errors.value[`details.${index}.word`] = '請輸入說明';
            return true;
        }

        return false;
    });

    if (wordError) {
        return false;
    }

    return true;
}

// 保存單字集
const { apiBase } = useRuntimeConfig().public;
const { csrfURL } = useRuntimeConfig().public;
async function handleSubmit() {
    if (!validate()) {
        return false;
    }
    isSubmitting.value = true;
    form.author = author;
    await $fetch(csrfURL, {
        credentials: 'include'
    });
    try {
        if (!id) {
            // 新增單字集
            const data = await $fetch(`${apiBase}/flashCard`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                },
                body: form
            });
            navigateTo(`/flashCard/${data.data.id}?from=account`);
        } else {
            // 編輯單字集
            await $fetch(`${apiBase}/flashCard/${id}`, {
                method: 'PUT',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                },
                body: form
            });
            navigateTo(`/flashCard/${id}?from=account`);
        }
    } catch (error) {
        const backendErrors = error.response?._data?.errors;
        if (backendErrors) {
            for (const key in backendErrors) {
                errors.value[key] = backendErrors[key][0];
            }
        } else {
            alert(error);
        }
        isSubmitting.value = false;
    }
}
</script>