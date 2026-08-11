<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
</script>

<template>
  <main class="mx-auto max-w-4xl p-6"><h1 class="text-2xl font-bold text-slate-900">ダッシュボード</h1><p class="mt-2 text-slate-600">{{ auth.user?.name }}さん、おかえりなさい。</p></main>
</template>
