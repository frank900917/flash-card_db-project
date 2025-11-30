<template>
    <div class="container-lg py-5">
        <div class="d-flex items-center justify-between">
            <h1 class="mb-4">編輯單字集</h1>
            <button @click="navigateTo(`/flashCard/${id}`)" class="btn btn-success align-self-center ms-auto mx-2">
                返回單字集
            </button>
        </div>
        <FlashCardForm v-if="flashCardSet" :author="user.username" :form="flashCardSet" :id="id" />
        <p v-if="error?.statusCode === 404" class="border rounded p-5 row justify-content-center fs-5 text-danger">
            此單字集不存在</p>
    </div>
</template>

<script setup>
definePageMeta({
    middleware: ['sanctum:auth']
});

const route = useRoute()
const id = route.params.id;
const { apiBase } = useRuntimeConfig().public;

const user = useSanctumUser();

// 取得單字集資料
const { data: flashCardSet, error } = await useSanctumFetch(`${apiBase}/flashCard/edit/${id}`);
</script>