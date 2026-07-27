/**
 * Composable for WordPress pages (built-in `page` post type)
 *
 * Data flows through the Nuxt server proxies (/nuxt-api/pages) rather than
 * hitting WordPress directly — this avoids CORS on client-side navigation and
 * gives us the local JSON fallback for free.
 *
 * NOTE: unrelated to usePageContent(), which reads the /pages-content endpoint
 * (option-based editorial copy, not WP page records).
 */

/** Minimal page shape used for ancestors, children and card links */
export interface WpPageRef {
    id: number
    slug: string
    /** Full hierarchical path without leading/trailing slash, e.g. 'parent/child' */
    path: string
    title: {
        rendered: string
    }
}

/** Shape returned by the list endpoint — no rendered content */
export interface WpPageSummary extends WpPageRef {
    excerpt: {
        rendered: string
    }
    /** Featured image URL, or `false` when the page has no thumbnail */
    featured_media: string | false
    parent: number
    parent_path: string | null
    menu_order: number
    date: string
    modified: string
}

/** Shape returned by the single endpoint — content + hierarchy */
export interface WpPage extends WpPageSummary {
    content: {
        rendered: string
    }
    template: string
    /** Ordered root → direct parent */
    ancestors: WpPageRef[]
    children: WpPageRef[]
}

/**
 * Decode HTML entities WordPress may leave in titles and excerpts.
 * Handles non-string inputs safely (objects, undefined, null).
 */
export const decodeHtmlEntities = (input: unknown): string => {
    if (!input) return ''
    const html = typeof input === 'string' ? input : String(input)
    return html
        .replace(/&#038;/g, '&')
        .replace(/&amp;/g, '&')
        .replace(/&#8230;/g, '…')
        .replace(/&hellip;/g, '…')
        .replace(/\[&hellip;\]/g, '…')
        .replace(/\[…\]/g, '…')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'")
        .replace(/&nbsp;/g, ' ')
}

/** Strip tags and entities from an excerpt, then truncate */
export const toPlainText = (html: string | undefined, maxLength = 200): string => {
    if (!html) return ''
    const text = decodeHtmlEntities(html.replace(/<[^>]+>/g, ' ')).replace(/\s+/g, ' ').trim()
    return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + '…' : text
}

/** Normalise a page path: no leading or trailing slashes */
const normalizePath = (path: string): string => path.replace(/^\/+|\/+$/g, '')

export const useWpPages = () => {
    /**
     * Get all published WordPress pages (without their rendered content)
     *
     * Network failures are NOT swallowed: they must reach useAsyncData so the
     * page can show its retry UI. `null` / empty is reserved for "nothing published".
     */
    const getWpPages = async (): Promise<WpPageSummary[]> => {
        const pages = await $fetch<WpPageSummary[]>('/nuxt-api/pages')
        return Array.isArray(pages) ? pages : []
    }

    /**
     * Get a single page by its full hierarchical path (e.g. 'parent/child')
     *
     * Returns `null` only when the page genuinely does not exist (the proxy
     * answers 204). Anything else throws, so the caller can tell "introuvable"
     * apart from "serveur injoignable".
     */
    const getWpPageByPath = async (path: string): Promise<WpPage | null> => {
        const clean = normalizePath(path)
        if (!clean) return null

        const page = await $fetch<WpPage | null>(`/nuxt-api/pages/${clean}`)
        return page || null
    }

    /**
     * Top-level pages only (no WordPress parent)
     */
    const getTopLevelPages = async (): Promise<WpPageSummary[]> => {
        const pages = await getWpPages()
        return pages.filter(page => page.parent === 0)
    }

    return {
        getWpPages,
        getWpPageByPath,
        getTopLevelPages,
        decodeHtmlEntities,
        toPlainText
    }
}
