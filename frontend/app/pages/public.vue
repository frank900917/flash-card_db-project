<template>
    <div class="container-lg py-5">
        <h1 class="mb-4 me-auto">公開單字集</h1>
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
                :isFormAccount="false" />
        </div>
        <div v-else class="border rounded p-5 row justify-content-center fs-5 text-danger">
            查無單字集！
        </div>
        <PaginationControl v-model="page" :datas="FlashCardLists" :fetchDatas="fetchFlashCardLists" />
    </div>
</template>

<script setup>
const page = ref(1);
const perPage = ref(25);
const search = ref('');
const { apiBase } = useRuntimeConfig().public;

// 獲取公開單字集清單
const { data: FlashCardLists } = await useSanctumFetch(`${apiBase}/flashCard/public?page=${page.value}&perPage=${perPage.value}`);

// 更新單字集清單
async function fetchFlashCardLists(isPerPage) {
    if (isPerPage) {
        page.value = 1;
    }
    const data = await $fetch(`${apiBase}/flashCard/public?search=${search.value}&page=${page.value}&perPage=${perPage.value}`, {
        method: 'GET'
    });
    FlashCardLists.value = data;
}

// 重置搜尋欄
function resetSearch() {
    search.value = '';
    fetchFlashCardLists(true);
}
</script>