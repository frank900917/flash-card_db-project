<template>
    <div class="d-flex align-items-center gap-2 mx-2">
        <label for="perPage" class="form-label mb-0">每頁顯示</label>
        <select id="perPage" class="form-select w-auto" v-model="perPage" @change="onChange(true)">
            <option v-for="option in options" :key="option" :value="option">{{ option }}</option>
        </select>
        <span>筆</span>
    </div>
</template>

<script setup>
const { onChange } = defineProps({
    onChange: Function
});
const perPage = defineModel(true);
const options = [10, 25, 50, 100];
</script>