<template>
    <div class="container-lg py-5 text-center">
        <h1 class="mb-4 fw-bold">線上單字集</h1>
        <p class="fs-5 mb-3">幫助你學習單字，透過測驗功能測試學習進度</p>
        <p class="fs-5 mb-3">
            立即前往
            <template v-if="user">
                <NuxtLink to="/account" class="text-decoration-underline text-secondary mx-1">帳戶</NuxtLink>
            </template>
            <template v-else>
                <NuxtLink to="/register" class="text-decoration-underline text-secondary mx-1">註冊</NuxtLink>
                或
                <NuxtLink to="/login" class="text-decoration-underline text-secondary mx-1">登入</NuxtLink>
            </template>
            建立自己的單字集
        </p>
        <p class="fs-5">
            或前往
            <NuxtLink to="/public" class="text-decoration-underline text-secondary mx-1">公開單字集</NuxtLink>
            瀏覽其他使用者建立的單字集
        </p>
    </div>
</template>

<script setup>
const user = useSanctumUser();
</script>