// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['bootstrap/dist/css/bootstrap.min.css'],
  modules: ['nuxt-auth-sanctum'],
  sanctum: {
    baseUrl: 'http://localhost:8000',
    mode: 'cookie',
    endpoints: {
      csrf: '/sanctum/csrf-cookie',
      login: '/api/login',
      logout: '/api/logout',
      user: '/api/user',
    },
    redirect: {
      onLogin: '/account',
      onAuthOnly: '/login'
    }
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api',
      csrfURL: 'http://localhost:8000/sanctum/csrf-cookie'
    }
  }
})
