<template>
    <div class="d-flex justify-content-center align-items-center vh-100 bg-body-secondary">
        <div class="card shadow p-4" style="min-width: 350px;">
            <h2 class="text-center mb-4 fw-bolder">變更密碼</h2>
            <form @submit.prevent="submit">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">目前密碼</label>
                    <input type="password" id="currentPassword" v-model="form.current_password" class="form-control" :class="{ 'is-invalid': errors.current_password }" required>
                    <div class="text-danger mt-1" v-if="errors.current_password">{{ errors.current_password }}</div>
                </div>

                <div class="mb-3">
                    <label for="newPassword" class="form-label">新密碼</label>
                    <input type="password" id="newPassword" v-model="form.new_password" class="form-control" :class="{ 'is-invalid': errors.new_password }" required>
                    <div class="text-danger mt-1" v-if="errors.new_password">{{ errors.new_password }}</div>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">確認新密碼</label>
                    <input type="password" id="confirmPassword" v-model="form.new_password_confirmation" class="form-control" :class="{ 'is-invalid': errors.new_password_confirmation }" required>
                    <div class="text-danger mt-1" v-if="errors.new_password_confirmation">{{ errors.new_password_confirmation }}</div>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">確認變更</button>
                <div class="text-center mt-3">
                    <NuxtLink to="/account" class="btn btn-outline-secondary w-100">返回帳戶</NuxtLink>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true" style="display: none !important;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">訊息</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">密碼變更成功，請重新登入</div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">確定</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
    definePageMeta({
        middleware: ['sanctum:auth']
    });
    const { $bootstrap } = useNuxtApp();
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;
    const form = ref({
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
    });
    const errors = ref({});
    const isSubmitting = ref(false);
    async function submit() {
        errors.value = {};

        if (form.value.new_password !== form.value.new_password_confirmation) {
            errors.value.new_password = '密碼與確認密碼不一致';
            errors.value.new_password_confirmation = '密碼與確認密碼不一致';
            return;
        }

        await $fetch(csrfURL, {
            credentials: 'include'
        });

        isSubmitting.value = true;

        try {await $fetch(`${apiBase}/changePassword`, {
                method: 'POST',
                body: form.value,
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                }
            });
            const infoModalEl = document.getElementById('infoModal');
            const infoModal = new $bootstrap.Modal(infoModalEl);
            infoModal.show();
            infoModalEl.addEventListener('hidden.bs.modal', function () {
                useSanctumAuth().logout();
            })
        } catch (error) {
            const backendErrors = error.response?._data?.errors;
            if (backendErrors) {
                for (const key in backendErrors) {
                    errors.value[key] = backendErrors[key][0];
                    if (key === 'new_password') {
                        errors.value.new_password_confirmation = errors.value[key];
                    }
                }
            } else {
                alert(error);
            }
            isSubmitting.value = false;
        }
    }
</script>