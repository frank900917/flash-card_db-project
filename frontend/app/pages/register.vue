<template>
    <div class="d-flex justify-content-center align-items-center vh-100 bg-body-secondary">
        <div class="card shadow p-4" style="min-width: 350px;">
            <h3 class="text-center mb-4 fw-bolder">註冊</h3>
            <form @submit.prevent="handleRegister">
                <div class="mb-3">
                    <label for="username" class="form-label">使用者名稱</label>
                    <input type="text" class="form-control" id="username" v-model="username" required autocomplete="off"
                        placeholder="輸入使用者名稱" :class="{ 'is-invalid': errors.username }" />
                    <div class="invalid-feedback">{{ errors.username }}</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼</label>
                    <input type="password" class="form-control" id="password" v-model="password" required
                        placeholder="輸入密碼" :class="{ 'is-invalid': errors.password }" />
                    <div class="invalid-feedback">{{ errors.password }}</div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">確認密碼</label>
                    <input type="password" class="form-control" id="confirmPassword" v-model="confirmPassword" required
                        placeholder="再次輸入密碼" :class="{ 'is-invalid': errors.confirmPassword }" />
                    <div class="invalid-feedback">{{ errors.confirmPassword }}</div>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">建立帳號</button>

                <div class="text-center mt-3">
                    <NuxtLink to="/login" class="btn btn-outline-secondary w-100">已有帳號？登入</NuxtLink>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true"
        style="display: none !important;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">訊息</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">註冊成功</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">確定</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
definePageMeta({
    middleware: ['guest-only']
});

const username = ref('');
const password = ref('');
const confirmPassword = ref('');
const errors = ref({});
const isSubmitting = ref(false);
const { $bootstrap } = useNuxtApp();
const { apiBase } = useRuntimeConfig().public;
const { csrfURL } = useRuntimeConfig().public;

// 註冊驗證
function validate() {
    errors.value = {};

    if (username.value.length < 8) {
        errors.value.username = '帳號長度至少需 8 個字元';
        return false;
    }

    if (username.value.length > 20) {
        errors.value.username = '帳號長度不能超過 20 個字元';
        return false;
    }

    if (password.value.length < 8) {
        errors.value.password = '密碼長度至少需 8 個字元';
        return false;
    }

    if (password.value.length > 20) {
        errors.value.password = '密碼長度不能超過 20 個字元';
        return false;
    }

    if (password.value !== confirmPassword.value) {
        errors.value.password = '密碼與確認密碼不一致';
        errors.value.confirmPassword = '密碼與確認密碼不一致';
        return false;
    }
    return true;
}

const handleRegister = async () => {
    if (!validate()) {
        return false;
    }

    isSubmitting.value = true;

    await $fetch(csrfURL, {
        credentials: 'include'
    });
    try {
        const response = await $fetch(`${apiBase}/register`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
            },
            body: {
                username: username.value,
                password: password.value,
                password_confirmation: confirmPassword.value
            }
        });
        const infoModalEl = document.getElementById('infoModal');
        const infoModal = new $bootstrap.Modal(infoModalEl);
        infoModal.show();
        infoModalEl.addEventListener('hidden.bs.modal', function () {
            navigateTo('/login');
        })
    } catch (error) {
        const backendErrors = error.response?._data?.errors;
        if (backendErrors) {
            for (const key in backendErrors) {
                errors.value[key] = backendErrors[key][0];
                if (key === 'password') {
                    errors.value.confirmPassword = errors.value[key];
                }
            }
        } else {
            alert(error);
        }
        isSubmitting.value = false;
    }
}
</script>