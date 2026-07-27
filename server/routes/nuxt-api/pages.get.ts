/**
 * WordPress Pages Proxy API Endpoint (list)
 * GET /nuxt-api/pages
 *
 * Proxies the WordPress `page` post type list to the REST API (server-side)
 * to avoid CORS issues during client-side navigation.
 * Falls back to local JSON data if WordPress API is unavailable.
 *
 * NOTE: the list intentionally omits `content`, `ancestors` and `children`.
 * Pages are Divi-built and their rendered content is several KB of shortcodes;
 * the detail route (/nuxt-api/pages/[...slug]) fetches those separately.
 */

import { readFileSync } from 'fs'
import { join } from 'path'

export default defineEventHandler(async (_event) => {
    const config = useRuntimeConfig()
    const apiBaseUrl = String(config.public.apiBaseUrl || '')
    const useLocalFallback = config.public.useLocalFallback === true || config.public.useLocalFallback === 'true'

    // If local fallback is forced, serve local JSON directly
    if (useLocalFallback || !apiBaseUrl) {
        return getLocalPages()
    }

    try {
        // Fetch from WordPress API server-side (no CORS issues)
        const data = await $fetch(apiBaseUrl + '/pages', {
            timeout: 10000,
            headers: { 'Accept': 'application/json' },
            // Le serveur WordPress est derriere un cache openresty qui sert des
            // reponses perimees : sans ce parametre, une page modifiee dans
            // l'admin met un temps indefini a apparaitre.
            query: { _t: Date.now() }
        })

        if (Array.isArray(data)) {
            return data
        }

        console.warn('[Server API] WordPress returned non-array pages, falling back to local')
        return getLocalPages()
    } catch (err: unknown) {
        const message = err instanceof Error ? err.message : String(err)
        console.warn('[Server API] WordPress pages fetch failed:', message)
        return getLocalPages()
    }
})

/**
 * Read pages from the local JSON file and strip the detail-only fields
 * so the shape matches what WordPress returns for the list endpoint.
 */
function getLocalPages(): unknown[] {
    try {
        const filePath = join(process.cwd(), 'public', 'data', 'pages.json')
        const raw = readFileSync(filePath, 'utf-8')
        const data = JSON.parse(raw)
        if (!Array.isArray(data)) return []

        return data.map((page: Record<string, unknown>) => {
            const { content, ancestors, children, template, ...summary } = page
            return summary
        })
    } catch (err: unknown) {
        const message = err instanceof Error ? err.message : String(err)
        console.warn('[Server API] Failed to read local pages.json:', message)
        return []
    }
}
