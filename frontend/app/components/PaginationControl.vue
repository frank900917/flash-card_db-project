<template>
    <div v-if="datas.total > 0">
        <div class="my-4 text-center">
            顯示第 {{ datas.from }} 到 {{ datas.to }} 筆資料，共有 {{ datas.total }} 筆資料
        </div>
        <nav v-if="datas.last_page > 1" class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- 上一頁 -->
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <button class="page-link" @click="goToPage(page - 1)" :disabled="page === 1">上一頁</button>
                </li>

                <!-- 第一頁 -->
                <li class="page-item" :class="{ active: page === 1 }">
                    <button class="page-link" @click="goToPage(1)">1</button>
                </li>

                <li class="page-item disabled" v-if="startPage > 2">
                    <span class="page-link">…</span>
                </li>

                <li v-for="p in middlePages" :key="p" class="page-item" :class="{ active: p === page }">
                    <button class="page-link" @click="goToPage(p)">{{ p }}</button>
                </li>

                <li class="page-item disabled" v-if="endPage < datas.last_page - 1">
                    <span class="page-link">…</span>
                </li>

                <!-- 最後一頁 -->
                <li v-if="datas.last_page > 1" class="page-item" :class="{ active: page === datas.last_page }">
                    <button class="page-link" @click="goToPage(datas.last_page)">{{ datas.last_page }}</button>
                </li>

                <!-- 下一頁 -->
                <li class="page-item" :class="{ disabled: page === datas.last_page }">
                    <button class="page-link" @click="goToPage(page + 1)"
                        :disabled="page === datas.last_page">下一頁</button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
const { datas, fetchDatas } = defineProps({
    datas: Object,
    fetchDatas: Function
});
const page = defineModel();
function goToPage(p) {
    if (p >= 1 && p <= datas.last_page) {
        page.value = p;
        fetchDatas(false);
    }
}
const startPage = computed(() => Math.max(Math.min(datas.last_page - 6, page.value - 2), 2));
const endPage = computed(() => Math.min(page.value + Math.max(6 - page.value, 2), datas.last_page - 1));
const middlePages = computed(() => {
    const pages = [];
    for (let i = startPage.value; i <= endPage.value; i++) {
        pages.push(i);
    }
    return pages;
});
</script>