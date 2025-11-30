<template>
    <div class="container-lg py-5">
        <div class="d-flex">
            <h1 class="mb-4">建立單字集</h1>
            <NuxtLink to="/account" class="btn btn-success align-self-center ms-auto mx-2">返回帳戶</NuxtLink>
        </div>
        <FlashCardForm :form="form" :author="user.username" />
    </div>
</template>

<script setup>
definePageMeta({
    middleware: ['sanctum:auth']
});

const user = useSanctumUser();

// 表單資料
const form = ref({
    title: '',
    description: '',
    author: '',
    isPublic: false,
    details: [
        { word: '', word_description: '' }
    ]
});
</script>