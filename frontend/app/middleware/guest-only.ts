export default defineNuxtRouteMiddleware(() => {
    const user = useSanctumUser()
    if (user.value) {
        return navigateTo('/account')
    }
})