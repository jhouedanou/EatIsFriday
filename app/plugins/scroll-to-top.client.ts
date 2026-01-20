export default defineNuxtPlugin((nuxtApp) => {
  // Force scroll to top on each route change
  nuxtApp.hook('page:finish', () => {
    // Disable smooth scroll temporarily
    const html = document.documentElement
    const originalBehavior = html.style.scrollBehavior
    html.style.scrollBehavior = 'auto'

    // Scroll to top immediately
    window.scrollTo(0, 0)

    // Re-enable smooth scroll after a short delay
    setTimeout(() => {
      html.style.scrollBehavior = originalBehavior || ''
    }, 100)
  })
})
