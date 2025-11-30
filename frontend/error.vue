<template>
    <NuxtLayout>
        <div class="container text-center py-5">
            <h1 class="display-4 text-danger">{{ error.statusCode }}</h1>
            <p class="fs-4">{{ error.statusMessage || '發生未知錯誤' }}</p>
            <NuxtLink to="/" class="btn btn-primary mt-4">返回首頁</NuxtLink>
        </div>
    </NuxtLayout>
</template>

<script setup>
    defineProps({
        error: {
            type: Object,
            required: true,
        },
    });
</script>