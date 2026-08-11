import axios from 'axios'
import { defineStore } from 'pinia'

import type { User } from '@/types/auth'

const api = axios.create({ baseURL: 'http://localhost', withCredentials: true })

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: null as User | null, isAuthenticated: false }),
  actions: {
    async fetchCsrfCookie(): Promise<void> {
      await api.get('/sanctum/csrf-cookie')
    },
    async login(email: string, password: string): Promise<void> {
      const { data } = await api.post<User>('/api/auth/login', { email, password })
      this.user = data
      this.isAuthenticated = true
    },
    async logout(): Promise<void> {
      await api.post('/api/auth/logout')
      this.user = null
      this.isAuthenticated = false
    },
    async fetchMe(): Promise<void> {
      const { data } = await api.get<User>('/api/auth/me')
      this.user = data
      this.isAuthenticated = true
    },
  },
})
