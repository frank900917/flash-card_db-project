<template>
    <form @submit.prevent="handleSubmit" class="border m-2 p-2">
        <div class="h5 text-center">寫入帳戶測驗紀錄</div>
        <input v-model="form.flash_card_set_id" type="number" name="setId" class="mx-1" placeholder="單字集 ID" required>
        <input v-model="form.correct_count" type="text" name="count" class="mx-1" placeholder="答題數" required>
        <input v-model="form.correct_rate" type="number" name="rate" class="mx-1" placeholder="答題率" step="0.01" required>
        <button type="submit" class="mx-1">送出</button>
    </form>
    <div class="border m-2 p-2">
        <div class="h5 text-center">查詢帳戶測驗紀錄</div>
        <div>{{ testRecordList }}</div>
    </div>
</template>

<script setup>
definePageMeta({
    middleware: ['sanctum:auth']
});
const { apiBase } = useRuntimeConfig().public;
const { csrfURL } = useRuntimeConfig().public;
const form = ref({
    flash_card_set_id: null,
    correct_count: '',
    correct_rate: null
});
const { data: testRecordList } = await useSanctumFetch(`${apiBase}/testRecord`);
async function handleSubmit() {
    await $fetch(csrfURL, {
        credentials: 'include'
    });
    await $fetch(`${apiBase}/testRecord`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
        },
        body: form.value
    });
    await updateRecord();
}

async function updateRecord() {
    const data = await $fetch(`${apiBase}/testRecord`, {
        method: 'GET',
        credentials: 'include'
    });
    testRecordList.value = data;
}
</script>