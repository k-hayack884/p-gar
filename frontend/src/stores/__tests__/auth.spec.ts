import { createPinia, setActivePinia } from 'pinia'
import { createMemoryHistory } from 'vue-router'
import { beforeEach, describe, expect, it, vi } from 'vitest'

import { createAppRouter } from '@/router'
import { useAuthStore } from '@/stores/auth'

const mockApi = vi.hoisted(() => ({
  get: vi.fn(),
  post: vi.fn(),
}))

vi.mock('axios', () => ({
  default: {
    create: vi.fn(() => mockApi),
  },
}))

describe('auth store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('login succeeds and stores the authenticated user', async () => {
    const user = { id: 1, name: 'Admin', email: 'admin@example.com' }
    mockApi.post.mockResolvedValueOnce({ data: user })
    const auth = useAuthStore()

    await auth.login('admin@example.com', 'password')

    expect(mockApi.post).toHaveBeenCalledWith('/api/auth/login', {
      email: 'admin@example.com',
      password: 'password',
    })
    expect(auth.isAuthenticated).toBe(true)
    expect(auth.user).toEqual(user)
  })

  it('login failure leaves the store unauthenticated', async () => {
    const unauthorized = { response: { status: 401 } }
    mockApi.post.mockRejectedValueOnce(unauthorized)
    const auth = useAuthStore()

    await expect(auth.login('admin@example.com', 'wrong')).rejects.toBe(unauthorized)

    expect(auth.isAuthenticated).toBe(false)
    expect(auth.user).toBeNull()
  })

  it('logout clears the authenticated user', async () => {
    mockApi.post.mockResolvedValueOnce({})
    const auth = useAuthStore()
    auth.$patch({
      isAuthenticated: true,
      user: { id: 1, name: 'Admin', email: 'admin@example.com' },
    })

    await auth.logout()

    expect(mockApi.post).toHaveBeenCalledWith('/api/auth/logout')
    expect(auth.isAuthenticated).toBe(false)
    expect(auth.user).toBeNull()
  })
})

describe('authentication route guard', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('redirects an unauthenticated visitor from a protected route to login', async () => {
    const router = createAppRouter(createMemoryHistory())

    await router.push('/')
    await router.isReady()

    expect(router.currentRoute.value.fullPath).toBe('/login')
  })

  it('redirects an authenticated visitor from login to home', async () => {
    const auth = useAuthStore()
    auth.$patch({
      isAuthenticated: true,
      user: { id: 1, name: 'Admin', email: 'admin@example.com' },
    })
    const router = createAppRouter(createMemoryHistory())

    await router.push('/login')
    await router.isReady()

    expect(router.currentRoute.value.fullPath).toBe('/')
  })
})
