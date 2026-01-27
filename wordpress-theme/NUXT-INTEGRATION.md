# Configuration Nuxt.js pour WordPress Backend

## Configuration requise dans nuxt.config.ts

```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      // URL de base de votre WordPress API
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'https://votre-wordpress.com/wp-json/eatisfamily/v1',
      
      // Désactiver le fallback local (WordPress est la source unique)
      useLocalFallback: false,
    }
  }
})
```

## Fichier .env

Créez un fichier `.env` à la racine de votre projet Nuxt :

```env
# Production
NUXT_PUBLIC_API_BASE=https://wordpress.votresite.com/wp-json/eatisfamily/v1

# Ou développement local
# NUXT_PUBLIC_API_BASE=http://localhost:8888/wp-json/eatisfamily/v1
```

## Vérification des Composables

Vos composables existants **ne nécessitent AUCUN changement** ! Ils fonctionneront automatiquement avec WordPress.

### Exemple - useActivities.ts

```typescript
// ✅ Ce code fonctionne tel quel avec WordPress v2.0
export const useActivities = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getActivities = async () => {
    return await $fetch(`${apiBase}/activities`)
  }

  const getActivityBySlug = async (slug: string) => {
    return await $fetch(`${apiBase}/activities/${slug}`)
  }

  return {
    getActivities,
    getActivityBySlug
  }
}
```

### Exemple - useJobs.ts

```typescript
// ✅ Fonctionne avec filtres department et venue_id
export const useJobs = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getJobs = async (filters?: { department?: string; venue_id?: string }) => {
    const params = new URLSearchParams()
    if (filters?.department) params.append('department', filters.department)
    if (filters?.venue_id) params.append('venue_id', filters.venue_id)
    
    const query = params.toString() ? `?${params.toString()}` : ''
    return await $fetch(`${apiBase}/jobs${query}`)
  }

  const getJobBySlug = async (slug: string) => {
    return await $fetch(`${apiBase}/jobs/${slug}`)
  }

  return {
    getJobs,
    getJobBySlug
  }
}
```

### Exemple - useVenues.ts

```typescript
// ✅ Récupère venues avec métadonnées complètes
export const useVenues = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getVenues = async () => {
    // Retourne { metadata, event_types, stats, venues }
    return await $fetch(`${apiBase}/venues`)
  }

  const getVenueById = async (id: string) => {
    return await $fetch(`${apiBase}/venues/${id}`)
  }

  return {
    getVenues,
    getVenueById
  }
}
```

### Exemple - useSiteContent.ts

```typescript
// ✅ Récupère le contenu global du site
export const useSiteContent = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getSiteContent = async () => {
    return await $fetch(`${apiBase}/site-content`)
  }

  return {
    getSiteContent
  }
}
```

### Exemple - usePagesContent.ts

```typescript
// ✅ Récupère le contenu des pages
export const usePagesContent = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getPagesContent = async () => {
    return await $fetch(`${apiBase}/pages-content`)
  }

  return {
    getPagesContent
  }
}
```

## Test de Connexion

### 1. Tester les endpoints manuellement

```bash
# Activités
curl https://votre-wordpress.com/wp-json/eatisfamily/v1/activities

# Jobs
curl https://votre-wordpress.com/wp-json/eatisfamily/v1/jobs

# Venues
curl https://votre-wordpress.com/wp-json/eatisfamily/v1/venues

# Site Content
curl https://votre-wordpress.com/wp-json/eatisfamily/v1/site-content

# Pages Content
curl https://votre-wordpress.com/wp-json/eatisfamily/v1/pages-content
```

### 2. Vérifier dans votre application Nuxt

```vue
<!-- pages/test-api.vue -->
<template>
  <div>
    <h1>API Test</h1>
    <pre>{{ activities }}</pre>
  </div>
</template>

<script setup>
const { getActivities } = useActivities()
const { data: activities } = await useAsyncData('test-activities', () => getActivities())
</script>
```

## Gestion des Erreurs

```typescript
// Ajoutez la gestion d'erreurs dans vos composables si nécessaire
export const useActivities = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const getActivities = async () => {
    try {
      return await $fetch(`${apiBase}/activities`)
    } catch (error) {
      console.error('Failed to fetch activities:', error)
      return []
    }
  }

  return {
    getActivities
  }
}
```

## CORS (si nécessaire)

Si WordPress et Nuxt sont sur des domaines différents, les headers CORS sont déjà configurés dans le thème WordPress.

Si vous avez des problèmes CORS, vérifiez dans `/functions.php`:

```php
function eatisfamily_add_cors_headers() {
    header('Access-Control-Allow-Origin: *'); // Ou votre domaine spécifique
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}
add_action('rest_api_init', 'eatisfamily_add_cors_headers');
```

## Cache (Optionnel)

Pour améliorer les performances, vous pouvez ajouter du cache côté Nuxt :

```typescript
// nuxt.config.ts
export default defineNuxtConfig({
  routeRules: {
    '/api/**': { cache: { maxAge: 60 * 5 } }, // Cache 5 minutes
  }
})
```

Ou utiliser un plugin de cache WordPress comme WP Super Cache ou W3 Total Cache.

## Checklist de Déploiement

- [ ] WordPress installé et thème activé
- [ ] Permaliens configurés (Post name)
- [ ] Contenu ajouté via admin WordPress
- [ ] API testée avec curl
- [ ] Variable `NUXT_PUBLIC_API_BASE` configurée
- [ ] Application Nuxt redéployée avec nouvelle config
- [ ] Tests end-to-end effectués
- [ ] Pas d'erreurs CORS
- [ ] Toutes les pages s'affichent correctement

---

**Questions ?** Contact: hello@eatisfamily.fr
