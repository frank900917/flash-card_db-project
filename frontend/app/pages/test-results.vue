<template>
  <div class="container-lg py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1>測驗結果紀錄</h1>
      <NuxtLink to="/account" class="btn btn-outline-secondary">返回帳戶</NuxtLink>
    </div>

    <div v-if="autoSubmitting" class="alert alert-info shadow-sm">
      <span class="spinner-border spinner-border-sm me-2"></span>
      正在自動儲存測驗結果...
    </div>
    <div v-if="error" class="alert alert-danger shadow-sm">{{ error }}</div>
    <div v-if="success" class="alert alert-success shadow-sm">儲存成功</div>

    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-white py-3 border-bottom">
        <h2 class="h5 mb-0 text-secondary">歷史紀錄清單</h2>
      </div>
      <div class="card-body p-0">
        <div v-if="!records || records.length === 0" class="text-center py-5 text-muted">
          <p class="mb-0 fs-5">目前沒有測驗資料</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light">
              <tr>
                <th class="text-center">編號</th>
                <th>標題</th>
                <th class="text-center">測驗類型</th>
                <th class="text-center">單字集 ID</th>
                <th class="text-center">答對題數</th>
                <th class="text-center">答題率</th>
                <th class="text-center">測驗時間</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in records" :key="r.id">
                <td class="text-center text-muted">#{{ r.id }}</td>
                <td class="fw-medium" style="max-width: 200px;">
                  <div class="text-truncate" :title="r.title">
                    {{ r.title || '未命名' }}
                  </div>
                </td>
                <td class="text-center">{{ formatType(r.title) }}</td>
                <td class="text-center"><span class="badge bg-light text-dark border">{{ r.flash_card_set_id }}</span></td>
                <td class="text-center">{{ r.correct_count }}</td>
                <td class="text-center">
                  <span>
                    {{ formatRate(r.correct_rate) }}
                  </span>
                </td>
                <td class="text-center text-secondary small">{{ formatDate(r.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-white py-3" v-if="records && records.length > 0">
        <small class="text-muted">共 {{ records.length }} 筆紀錄</small>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ middleware: ['sanctum:auth'] });
const { apiBase } = useRuntimeConfig().public;
const { csrfURL } = useRuntimeConfig().public;
const route = useRoute();

const records = ref([]);
const error = ref('');
const success = ref(false);
const autoSubmitting = ref(false);

// 獲取測驗紀錄列表
async function fetchRecords(perPage) {
  try {
    const url = perPage ? `${apiBase}/testRecord?perPage=${perPage}` : `${apiBase}/testRecord`;
    const { data } = await useSanctumFetch(url);
    const res = data.value;
    // 預期回傳格式 { message: 'Success', result: [...] }
    if (res && res.result) {
      // 處理分頁資料
      if (res.result.data) {
        records.value = res.result.data;
      } else if (Array.isArray(res.result)) {
        records.value = res.result;
      } else {
        records.value = Array.isArray(res.result) ? res.result : [res.result];
      }
    } else if (Array.isArray(res)) {
      records.value = res;
    } else {
      records.value = [];
    }
  } catch (e) {
    console.error('fetchRecords error', e);
    error.value = e?.data?.message || e?.message || '讀取紀錄失敗';
  }
}

// 格式化測驗類型
function formatType(title) {
  if (!title) return '-';
  const end = title.lastIndexOf('(');
  const start = title.lastIndexOf('-', end);
  if (start === -1 || end === -1) return '-';

  const t = title.substring(start + 1, end).trim();
  return t.slice(0, 2) || '-';
}

// 格式化答題率
function formatRate(v) {
  if (v === null || v === undefined) return '-';
  const num = Number(v);
  if (isNaN(num)) return '-';
  if (num > 1) return num.toFixed(2) + '%';
  return (num * 100).toFixed(2) + '%';
}

// 格式化日期顯示
function formatDate(s) {
  if (!s) return '';
  try { return new Date(s).toLocaleString(); } catch { return s; }
}


// 初始載入
await fetchRecords();

if (route.query && Object.keys(route.query).length > 0) {
  autoSubmitFromQuery(route.query);
}
</script>

<style scoped>
.container-lg { max-width: 980px; }
</style>
