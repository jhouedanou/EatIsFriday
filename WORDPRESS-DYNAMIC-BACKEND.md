# WordPress Dynamic Backend Refactoring

## Overview

This document describes the comprehensive refactoring of the WordPress backend to act as a dynamic "engine" for the Vue.js frontend. All hardcoded values in Vue templates can now be managed through WordPress.

## Architecture

```
WordPress Backend                    Vue.js Frontend
┌─────────────────┐                 ┌─────────────────┐
│   Customizer    │                 │  useGlobalSettings()
│   (Settings)    │────────────────▶│  composable     │
└─────────────────┘                 └─────────────────┘
        │                                   │
        ▼                                   ▼
┌─────────────────┐                 ┌─────────────────┐
│  REST API       │                 │  Vue Components │
│  /settings      │────────────────▶│  (dynamic data) │
└─────────────────┘                 └─────────────────┘
```

## New Files Created

### WordPress Theme

| File | Description |
|------|-------------|
| `wordpress-theme/inc/customizer.php` | Complete WordPress Customizer with all settings |

### Vue.js Frontend

| File | Description |
|------|-------------|
| `app/composables/useGlobalSettings.ts` | Composable for fetching and caching global settings |
| `app/plugins/global-settings.ts` | Plugin to load settings on app initialization |

## WordPress Customizer Sections

### 1. Brand Identity (`eatisfamily_brand`)
- **Site Logo** - Image upload with WordPress Media Library
- **Site Logo Dark** - Alternative logo for dark backgrounds
- **Favicon** - Site favicon
- **Brand Name** - First part of brand (e.g., "Eat is")
- **Brand Highlight** - Second part in accent color (e.g., "Family")

### 2. Header Configuration (`eatisfamily_header`)
- **Background Image** - Header background
- **Sticky Header** - Enable/disable sticky navigation
- **CTA Text (Desktop)** - Call-to-action button text
- **CTA Text (Mobile)** - Mobile CTA text
- **CTA Link** - Button destination URL

### 3. Navigation Menu (`eatisfamily_navigation`)
- **Nav Links 1-6** - Text and URL pairs for main navigation

### 4. Map Configuration (`eatisfamily_map`)
- **MapTiler API Key** - API key for interactive maps
- **MapTiler Style URL** - Custom map style
- **Default Center** - Latitude/Longitude
- **Default Zoom** - Map zoom level (1-20)

### 5. Venue Markers (`eatisfamily_markers`)
- **Marker Type** - Default/Custom/Circle
- **Stadium Marker** - Custom image for stadiums
- **Arena Marker** - Custom image for arenas
- **Festival Marker** - Custom image for festivals
- **Default Marker** - Fallback marker image

### 6. SEO & Meta (`eatisfamily_seo`)
- **Default Meta Title** - Site-wide default title
- **Title Template** - Template with %s placeholder
- **Default Description** - Meta description
- **Keywords** - Comma-separated keywords
- **OG Image** - Default social sharing image
- **Twitter Handle** - @username
- **Canonical Base** - Base URL for canonical links

### 7. Social Media (`eatisfamily_social`)
- **Facebook URL**
- **Instagram URL**
- **Twitter/X URL**
- **LinkedIn URL**
- **YouTube URL**
- **TikTok URL**

### 8. Contact Information (`eatisfamily_contact`)
- **Email** - Main contact email
- **Phone** - Contact phone number
- **Address** - Physical address
- **Business Hours** - Operating hours
- **Legal Email** - For privacy/legal inquiries

### 9. Footer Configuration (`eatisfamily_footer`)
- **Footer Logo** - Custom footer logo
- **Footer Tagline** - Description text
- **Copyright Text** - With {year} placeholder
- **Show Back to Top** - Enable/disable button
- **Back to Top Text** - Button label
- **Section Titles** - Company/Policy headings

### 10. Global Configuration (`eatisfamily_config`)
- **Jobs Per Page** - Pagination for careers
- **Events Per Page** - Pagination for events
- **Blog Posts Per Page** - Pagination for blog
- **Site Language** - fr/en locale

### 11. UI Text Strings (`eatisfamily_strings`)
All UI labels and messages can be customized:
- `loading` - Loading indicator text
- `no_results` - Empty state message
- `error_generic` - Error message
- `submit_button` - Form submit text
- `send_message` - Contact form button
- `sending` - Loading state
- `message_sent` - Success title
- `thank_you` - Success description
- `apply_now` - Job apply button
- `view_details` - Detail link text
- `read_more` - Blog read more
- `back_to_jobs` - Navigation text
- `job_not_found` - 404 for jobs
- `all_job_titles` - Filter label
- `all_sites` - Filter label
- `search_placeholder` - Search input

### 12. Background Images (`eatisfamily_backgrounds`)
Page-specific backgrounds managed via Media Library:
- `hero_default` - Default hero background
- `about_hero` - About page hero
- `contact_hero` - Contact page hero
- `careers_hero` - Careers page hero
- `events_hero` - Events page hero
- `blog_hero` - Blog page hero
- `decoration_1` - Decorative element 1
- `decoration_2` - Decorative element 2
- `vector_bg` - Vector background pattern

## REST API Endpoints

### New Endpoint: `/wp-json/eatisfamily/v1/settings`

Returns all Customizer settings in a structured format:

```json
{
  "brand": {
    "logo": "https://...",
    "logo_dark": "https://...",
    "name": "Eat is",
    "highlight": "Family",
    "site_name": "Eat Is Family",
    "tagline": "..."
  },
  "header": {
    "background_image": "...",
    "sticky": true,
    "cta_text": "Get in touch",
    "cta_link": "/contact"
  },
  "navigation": {
    "links": [
      { "text": "About", "to": "/about" },
      ...
    ]
  },
  "map": {
    "maptiler_key": "...",
    "maptiler_style": "...",
    "center": [2.0, 48.5],
    "zoom": 5
  },
  "markers": {
    "type": "custom",
    "stadium": "https://...",
    "arena": "https://...",
    "festival": "https://...",
    "default": "https://..."
  },
  "seo": { ... },
  "social": { ... },
  "contact": { ... },
  "footer": { ... },
  "config": { ... },
  "strings": { ... },
  "backgrounds": { ... }
}
```

## Vue.js Integration

### useGlobalSettings Composable

```typescript
const { 
  settings,           // Reactive settings object
  isLoaded,           // Loading state
  loadSettings,       // Fetch settings from API
  getSetting,         // Get value by dot path
  getString,          // Get UI string
  getBackground,      // Get background image URL
  getMarkerImage,     // Get marker by venue type
  getSocialLink,      // Get social URL
  formatPageTitle,    // Apply title template
  getCopyrightText    // Get copyright with year
} = useGlobalSettings()
```

### Usage Examples

```vue
<script setup>
const { settings, getString, getBackground } = useGlobalSettings()

// Access settings
const brandName = computed(() => settings.value?.brand?.name)

// Get UI string with fallback
const loading = getString('loading')

// Get background image
const heroBg = getBackground('careers_hero')
</script>
```

## Components Updated

### TheNavbar.vue
- Logo from `settings.brand.logo`
- Brand name from `settings.brand.name` + `.highlight`
- Navigation links from `settings.navigation.links`
- CTA text from `settings.header.cta_text`

### TheFooter.vue
- All content from global settings
- Social links from `settings.social`
- Copyright with dynamic year

### VenueMap.vue
- Map center from `settings.map.center`
- Map zoom from `settings.map.zoom`
- MapTiler style from `settings.map.maptiler_style`
- Venue markers from `settings.markers`

### default.vue (Layout)
- SEO meta tags from `settings.seo`
- Title template applied site-wide

### jobs/[slug].vue
- UI strings for loading, not found, buttons

## Installation & Usage

### 1. Upload WordPress Theme

Upload the entire `wordpress-theme/` folder to:
```
/wp-content/themes/eatisfamily/
```

### 2. Access Customizer

In WordPress Admin:
1. Go to **Appearance > Customize**
2. Configure all sections (Brand, Header, Map, SEO, etc.)
3. Click **Publish** to save

### 3. Upload Marker Images

1. Go to **Appearance > Customize > Venue Markers**
2. Click on each marker field
3. Upload or select images from Media Library
4. Recommended size: 40x40px PNG with transparency

### 4. Configure Map

1. Go to **Appearance > Customize > Map Configuration**
2. Enter your MapTiler API key
3. Set default center coordinates
4. Adjust zoom level (1-20)

### 5. Set UI Strings

1. Go to **Appearance > Customize > UI Text Strings**
2. Customize all labels and messages
3. Useful for translations or brand voice

### 6. Verify API

Test the settings endpoint:
```
https://your-site.com/wp-json/eatisfamily/v1/settings
```

### 7. Restart Frontend

After making changes in WordPress:
1. Wait a few seconds for cache to clear
2. Hard refresh the frontend (Ctrl+F5)
3. Or restart the Nuxt dev server

## Benefits

1. **Zero Code Changes** - Content updates via WordPress admin
2. **Media Integration** - All images use WordPress Media Library
3. **Type Safety** - TypeScript interfaces for all settings
4. **Caching** - Settings cached in Vue useState
5. **Fallbacks** - Default values if WordPress unavailable
6. **SEO Control** - Meta tags managed centrally
7. **i18n Ready** - UI strings externalized for translation

## Future Improvements

- [ ] Add color customization section
- [ ] Add typography settings
- [ ] Add animation toggle settings
- [ ] Cache settings in local storage
- [ ] Add webhook for instant updates
