/**
 * Global Settings Plugin
 * 
 * Loads global settings from WordPress on app initialization
 * Makes settings available throughout the application
 */
export default defineNuxtPlugin(async (nuxtApp) => {
    const { loadSettings } = useGlobalSettings()
    
    // Load settings during SSR/hydration
    if (import.meta.server || !nuxtApp.payload.serverRendered) {
        try {
            await loadSettings()
            console.log('%c[Plugin] ✅ Global settings loaded', 'color: green;')
        } catch (error) {
            console.error('%c[Plugin] ❌ Failed to load global settings:', 'color: red;', error)
        }
    }
})
