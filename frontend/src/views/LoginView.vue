<script setup lang="ts">
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')
const isSubmitting = ref(false)

async function submit(): Promise<void> {
  error.value = ''
  isSubmitting.value = true
  try {
    await auth.fetchCsrfCookie()
    await auth.login(email.value, password.value)
    await router.push('/')
  } catch (caughtError) {
    if (axios.isAxiosError(caughtError)) {
      if (caughtError.response?.status === 422) {
        const errors = caughtError.response.data?.errors as Record<string, string[]> | undefined
        error.value = Object.values(errors ?? {}).flat().join(' ') || '入力内容を確認してください。'
      } else if (caughtError.response?.status === 401) {
        error.value = 'メールアドレスまたはパスワードが正しくありません。'
      } else {
        error.value = 'ログインに失敗しました。しばらくしてから再度お試しください。'
      }
    } else {
      error.value = '通信に失敗しました。'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
    <form class="w-full space-y-6 rounded-lg bg-white p-8 shadow" @submit.prevent="submit">
      <div><h1 class="text-2xl font-bold text-slate-900">p-gar にログイン</h1><p class="mt-2 text-sm text-slate-600">家庭菜園の管理を続けましょう。</p></div>
      <p v-if="error" role="alert" class="rounded bg-red-50 p-3 text-sm text-red-700">{{ error }}</p>
      <label class="block text-sm font-medium text-slate-700">メールアドレス<input v-model="email" type="email" autocomplete="email" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2" /></label>
      <label class="block text-sm font-medium text-slate-700">パスワード<input v-model="password" type="password" autocomplete="current-password" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2" /></label>
      <button type="submit" :disabled="isSubmitting" class="w-full rounded bg-emerald-600 px-4 py-2 font-semibold text-white disabled:opacity-50">{{ isSubmitting ? 'ログイン中…' : 'ログイン' }}</button>
    </form>
  </main>
</template>
