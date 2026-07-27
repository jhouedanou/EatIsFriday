/**
 * WordPress Page Proxy API Endpoint (single, hierarchical)
 * GET /nuxt-api/pages/<parent>/<child>
 *
 * Proxies a single WordPress page (built-in `page` post type) to the REST API
 * server-side. The path may contain slashes for nested pages.
 * Falls back to local JSON data if WordPress API is unavailable or 404s.
 *
 * Returns `null` with HTTP 200 when the page does not exist — the whole app
 * handles not-found with inline UI, there is no error.vue / createError usage.
 */

import { readFileSync } from 'fs'
import { join } from 'path'

export default defineEventHandler(async (event) => {
    const config = useRuntimeConfig()
    const apiBaseUrl = String(config.public.apiBaseUrl || '')
    const useLocalFallback = config.public.useLocalFallback === true || config.public.useLocalFallback === 'true'

    // Normalise the catch-all param into a slash-joined path with no leading
    // or trailing slashes. h3 may expose it as an array or as a joined string.
    const raw = getRouterParam(event, 'slug') ?? event.context.params?._
    const path = (Array.isArray(raw) ? raw.join('/') : String(raw ?? '')).replace(/^\/+|\/+$/g, '')

    if (!path) {
        setResponseStatus(event, 400)
        return { error: 'missing path' }
    }

    // If local fallback is forced, serve local JSON directly
    if (useLocalFallback || !apiBaseUrl) {
        return getLocalPage(path)
    }

    try {
        const data = await $fetch(`${apiBaseUrl}/pages/${path}`, {
            timeout: 10000,
            headers: { 'Accept': 'application/json' },
            // Le serveur WordPress est derriere un cache openresty qui sert des
            // reponses perimees : sans ce parametre, le contenu modifie dans
            // l'admin (PDF joints, images) n'arrive jamais jusqu'ici.
            query: { _t: Date.now() }
        })

        if (data && typeof data === 'object') {
            return data
        }

        console.warn('[Server API] WordPress returned unexpected page payload, falling back to local')
        return getLocalPage(path)
    } catch (err: unknown) {
        const message = err instanceof Error ? err.message : String(err)
        console.warn(`[Server API] WordPress page fetch failed (${path}):`, message)
        return getLocalPage(path)
    }
})

/**
 * Look up a single page in the local JSON file by its full path.
 */
function getLocalPage(path: string): unknown | null {
    try {
        const filePath = join(process.cwd(), 'public', 'data', 'pages.json')
        const raw = readFileSync(filePath, 'utf-8')
        const data = JSON.parse(raw)
        if (!Array.isArray(data)) return null

        return data.find((page: Record<string, unknown>) => page.path === path) ?? null
    } catch (err: unknown) {
        const message = err instanceof Error ? err.message : String(err)
        console.warn('[Server API] Failed to read local pages.json:', message)
        return null
    }
}
