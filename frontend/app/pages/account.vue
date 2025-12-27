<template>
    <div class="container-lg py-5">
        <h1 class="mb-4">帳戶資料</h1>
        <div class="card p-4 mt-3">
            <div v-if="user" class="d-flex mb-0">
                <div>
                    <div class="fw-bold">帳號：{{ user.username }}</div>
                    <div class="fw-bold">註冊時間：{{ formatDate(user.created_at) }}</div>
                    <div class="fw-bold">已建立單字集：{{ FlashCardLists.total }}</div>
                    <div class="fw-bold">等級：{{ level?.result?.level_info?.name }}</div>
                    <div class="fw-bold">經驗值: {{user.exp}}</div>
                </div>
                <NuxtLink to="/changePassword" class="btn btn-primary align-self-center mx-2 ms-auto">變更密碼</NuxtLink>
            </div>
        </div>
        <div class="d-flex">
            <h2 class="my-3 pb-2 me-auto">我的單字集</h2>
            <NuxtLink to="/flashCard/create" class="btn btn-success align-self-center mx-2">建立新單字集</NuxtLink>
        </div>
        <form @submit.prevent="fetchFlashCardLists(true)" class="d-flex align-items-center row mb-2">
            <input class="form-control me-2 mb-2 col-sm flex-grow-1" type="text" v-model="search" placeholder="搜尋標題">
            <div class="d-flex col-sm-auto mb-2">
                <button type="submit" class="btn btn-secondary me-2">搜尋</button>
                <button class="btn btn-secondary me-2" @click="resetSearch">重置</button>
                <PaginationSizeSelector class="ms-auto" v-model="perPage" :onChange="fetchFlashCardLists" />
            </div>
        </form>
        <div v-if="FlashCardLists.data.length > 0" class="space-y-4">
            <FlashCardList v-for="(set, index) in FlashCardLists.data" :key="index" :flashCardSet="set"
                :isFormAccount="true" />
        </div>
        <div v-else class="border rounded p-5 row justify-content-center fs-5 text-danger">
            查無單字集！
        </div>
        <PaginationControl v-model="page" :datas="FlashCardLists" :fetchDatas="fetchFlashCardLists" />
    </div>
</template>

<script setup>
definePageMeta({
    middleware: ['sanctum:auth']
});

const user = useSanctumUser();
const page = ref(1);
const perPage = ref(25);
const search = ref('');
const { apiBase } = useRuntimeConfig().public;

// 獲取帳戶單字集清單
const { data: FlashCardLists } = await useSanctumFetch(`${apiBase}/flashCard?page=${page.value}&perPage=${perPage.value}`);

// 獲取使用者等級
const { data: level } = await useSanctumFetch(`${apiBase}/levelExpMap`);

// 更新單字集清單
async function fetchFlashCardLists(isPerPage) {
    if (isPerPage) {
        page.value = 1;
    }
    const data = await $fetch(`${apiBase}/flashCard?search=${search.value}&page=${page.value}&perPage=${perPage.value}`, {
        method: 'GET',
        credentials: 'include'
    });
    FlashCardLists.value = data;
}

// 格式化時間
function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString()
}

// 重置搜尋欄
function resetSearch() {
    search.value = '';
    fetchFlashCardLists(true);
}
</script>