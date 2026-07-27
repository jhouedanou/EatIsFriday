<script setup lang="ts">
// Une navigation catch-all → catch-all réutilise l'instance du composant :
// la clé de route force le remount, donc le refetch, sans watcher.
definePageMeta({
  key: route => route.fullPath
})

const route = useRoute()
const { getWpPageByPath, decodeHtmlEntities, toPlainText } = useWpPages()
const { isDiviContent } = useDiviParser()

const path = computed(() => {
  const raw = route.params.slug
  return (Array.isArray(raw) ? raw.join('/') : String(raw ?? '')).replace(/^\/+|\/+$/g, '')
})

// useAsyncData non lazy : le rendu SSR doit contenir les balises Open Graph,
// sinon les liens individuels partagés n'ont ni titre ni image d'aperçu.
const { data: page, error, refresh, status } = await useAsyncData(
  `wp-page-${path.value}`,
  () => getWpPageByPath(path.value)
)

const pageTitle = computed(() => decodeHtmlEntities(page.value?.title?.rendered))
const plainExcerpt = computed(() => toPlainText(page.value?.excerpt?.rendered, 200))
const featuredImage = computed(() => page.value?.featured_media || '')

// Le bandeau change de couleur selon la profondeur dans l'arborescence
const depthClass = computed(() => {
  const depth = page.value?.ancestors?.length ?? 0
  return `depth-${Math.min(depth, 2)}`
})

// Remontée d'un niveau : le dernier ancêtre, sinon l'index des pages
const parentCrumb = computed(() => {
  const ancestors = page.value?.ancestors ?? []
  const parent = ancestors[ancestors.length - 1]
  return parent
    ? { to: `/pages/${parent.path}`, label: decodeHtmlEntities(parent.title.rendered) }
    : { to: '/pages', label: 'Pages' }
})

// ------------------------------------------------------------- Lightbox
// Le contenu vient de WordPress en v-html : on ne peut pas y poser de @click
// Vue. On delegue donc l'ecoute sur le conteneur et on ouvre l'image cliquee.
const lightboxSrc = ref<string | null>(null)
const lightboxAlt = ref('')

/** Prefere l'original pleine resolution au derive -1024x724 injecte par WordPress */
const fullSizeSrc = (img: HTMLImageElement): string => {
  const srcset = img.getAttribute('srcset')
  if (srcset) {
    const widest = srcset
      .split(',')
      .map(part => {
        const [url, descriptor] = part.trim().split(/\s+/)
        return { url, width: parseInt(descriptor || '0', 10) || 0 }
      })
      .sort((a, b) => b.width - a.width)[0]
    if (widest?.url) return widest.url
  }
  return img.currentSrc || img.src
}

const onContentClick = (event: MouseEvent) => {
  const target = event.target as HTMLElement | null
  if (!target) return

  // Une image dans un lien garde son comportement de lien
  if (target.closest('a')) return

  const img = target.closest('img') as HTMLImageElement | null
  if (!img) return

  event.preventDefault()
  lightboxSrc.value = fullSizeSrc(img)
  lightboxAlt.value = img.alt || ''
}

const closeLightbox = () => {
  lightboxSrc.value = null
}

const onKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') closeLightbox()
}

// Bloque le defilement de l'arriere-plan pendant l'ouverture
watch(lightboxSrc, (value) => {
  if (import.meta.client) {
    document.body.style.overflow = value ? 'hidden' : ''
  }
})

onMounted(() => window.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown)
  if (import.meta.client) document.body.style.overflow = ''
})

const contentClass = computed(() =>
  isDiviContent(page.value?.content?.rendered || '')
    ? 'wp-page-content wp-page-content--divi'
    : 'wp-page-content wp-page-content--prose'
)

useSeoMeta({
  // Le layout applique déjà un titleTemplate « … | Eat Is Family » : pas de suffixe ici
  title: () => (page.value ? pageTitle.value : 'Page introuvable'),
  ogTitle: () => (page.value ? pageTitle.value : 'Eat Is Family'),
  twitterTitle: () => (page.value ? pageTitle.value : 'Eat Is Family'),
  description: () => plainExcerpt.value || 'Découvrez cette page sur le site Eat Is Family.',
  ogDescription: () => plainExcerpt.value || 'Découvrez cette page sur le site Eat Is Family.',
  twitterDescription: () => plainExcerpt.value || 'Découvrez cette page sur le site Eat Is Family.',
  ogImage: () => featuredImage.value || '/web-app-manifest-512x512.png',
  twitterImage: () => featuredImage.value || '/web-app-manifest-512x512.png',
  ogType: 'article',
  twitterCard: 'summary_large_image'
})
</script>

<template>
  <div class="wp-page-detail">
    <!-- Chargement -->
    <div v-if="status === 'pending'" class="page-loading">
      <span class="skeleton skeleton--title" />
      <span class="skeleton skeleton--lead" />
      <span class="skeleton skeleton--media" />
    </div>

    <!-- Erreur réseau : retry plutôt qu'une page blanche -->
    <div v-else-if="error" class="page-error">
      <p>Impossible de charger cette page.</p>
      <button type="button" @click="refresh()">Réessayer</button>
      <NuxtLink to="/pages" class="back-link">← Retour aux pages</NuxtLink>
    </div>

    <!-- Chargée mais inexistante -->
    <div v-else-if="!page" class="page-notfound">
      <p class="notfound-title">Cette page n'existe pas ou a été déplacée.</p>
      <NuxtLink to="/pages" class="back-link">← Retour aux pages</NuxtLink>
    </div>

    <article v-else class="page-article">
      <!-- Bandeau -->
      <header class="wp-page-hero" :class="depthClass">
        <div class="container">
          <nav class="breadcrumb breadcrumb--full" aria-label="Fil d'ariane">
            <NuxtLink to="/" class="crumb">Accueil</NuxtLink>
            <span class="crumb-sep" aria-hidden="true">/</span>
            <NuxtLink to="/pages" class="crumb">Pages</NuxtLink>

            <template v-for="ancestor in page.ancestors" :key="ancestor.id">
              <span class="crumb-sep" aria-hidden="true">/</span>
              <NuxtLink :to="`/pages/${ancestor.path}`" class="crumb">
                {{ decodeHtmlEntities(ancestor.title.rendered) }}
              </NuxtLink>
            </template>

            <span class="crumb-sep" aria-hidden="true">/</span>
            <span class="crumb crumb--current" aria-current="page">{{ pageTitle }}</span>
          </nav>

          <!-- Sous 480px : uniquement le remontée d'un niveau -->
          <nav class="breadcrumb breadcrumb--compact" aria-label="Fil d'ariane">
            <NuxtLink :to="parentCrumb.to" class="crumb">← {{ parentCrumb.label }}</NuxtLink>
          </nav>

          <h1 class="page-title">{{ pageTitle }}</h1>

          <!--<p v-if="plainExcerpt" class="page-lead">{{ plainExcerpt }}</p>-->
        </div>
      </header>

      <!-- Image mise en avant, chevauchant le bandeau -->
      <div v-if="featuredImage" class="page-media">
        <img :src="featuredImage" :alt="pageTitle" />
      </div>

      <!-- Contenu WordPress (rendu Divi-aware) -->
      <div
        class="page-body"
        :class="{ 'page-body--pulled': !featuredImage }"
        @click="onContentClick"
      >
        <DiviContent :content="page.content?.rendered || ''" :wrapper-class="contentClass" />
      </div>

      <!-- Sous-pages -->
      <section v-if="page.children?.length" class="child-pages">
        <div class="container">
          <SectionHeader title="Dans cette rubrique" />
          <div class="child-grid">
            <NuxtLink
              v-for="child in page.children"
              :key="child.id"
              :to="`/pages/${child.path}`"
              class="child-card"
            >
              <span class="child-title">{{ decodeHtmlEntities(child.title.rendered) }}</span>
              <span class="child-arrow" aria-hidden="true">→</span>
            </NuxtLink>
          </div>
        </div>
      </section>

      <!-- Retour au parent -->
      <div v-if="page.parent !== 0 && page.parent_path" class="page-footer-nav">
        <div class="container">
          <NuxtLink :to="`/pages/${page.parent_path}`" class="parent-link">
            ← Retour à {{ decodeHtmlEntities(page.ancestors?.[page.ancestors.length - 1]?.title?.rendered) || 'la page parente' }}
          </NuxtLink>
        </div>
      </div>
    </article>

    <!-- Lightbox images -->
    <Teleport to="body">
      <Transition name="lightbox-fade">
        <div
          v-if="lightboxSrc"
          class="lightbox"
          role="dialog"
          aria-modal="true"
          aria-label="Image agrandie"
          @click="closeLightbox"
        >
          <button type="button" class="lightbox-close" aria-label="Fermer" @click="closeLightbox">
            &times;
          </button>
          <img :src="lightboxSrc" :alt="lightboxAlt" @click.stop />
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped lang="scss">
.wp-page-detail {
  min-height: 100vh;
  background: #fff;
}

// ---------------------------------------------------------------- Bandeau
.wp-page-hero {
  padding: 9rem 0 5rem;
  // Explicite : `main.scss` définit un `.page-hero` global centré, on ne veut
  // hériter d'aucun alignement venu d'ailleurs.
  text-align: left;

  &.depth-0 { background: $brand-yellow; }
  &.depth-1 { background: $brand-blue; }
  &.depth-2 { background: $brand-purple; }
}

.breadcrumb {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: rgba(26, 26, 26, 0.7);
  margin-bottom: 1.5rem;
}

.breadcrumb--compact {
  display: none;
}

.crumb {
  color: rgba(26, 26, 26, 0.7);
  text-decoration: none;

  &:hover {
    color: $brand-dark;
    text-decoration: underline;
  }
}

.crumb--current {
  color: $brand-dark;
  font-weight: 600;
}

.crumb-sep {
  color: rgba(26, 26, 26, 0.4);
}

.page-title {
  font-family: $font-family-heading;
  font-size: clamp(2.25rem, 5vw, 3.5rem);
  line-height: 1.15;
  color: $brand-dark;
  max-width: 20ch;
  margin: 0;
}

.page-lead {
  margin: 1.5rem 0 0;
  max-width: 55ch;
  font-size: 1.25rem;
  line-height: 1.6;
  color: rgba(26, 26, 26, 0.8);
}

// ------------------------------------------------- Image mise en avant
.page-media {
  position: relative;
  z-index: 2;
  max-width: 1100px;
  margin: -4rem auto 0;
  padding: 0 1.5rem;

  img {
    display: block;
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
    border: 2px solid $brand-dark;
    border-radius: $border-radius-lg;
    box-shadow: $shadow-organic;
  }
}

// ---------------------------------------------------------------- Contenu
.page-body {
  padding: 4rem 1.5rem 5rem;

  &--pulled {
    padding-top: 3rem;
  }
}

:deep(.wp-page-content--prose) {
  max-width: 72ch;
  margin: 0 auto;
  font-size: 1.125rem;
  line-height: 1.7;
  color: $brand-dark;
}

:deep(.wp-page-content--divi) {
  max-width: none;
}

:deep(.wp-page-content) {
  h2,
  h3,
  h4 {
    font-family: $font-family-heading;
    color: $brand-dark;
    line-height: 1.25;
    margin: 2.5rem 0 1rem;
  }

  h2 { font-size: 1.875rem; }
  h3 { font-size: 1.5rem; }

  p {
    margin: 0 0 1.5rem;
  }

  // Bootstrap neutralise les listes : les rétablir explicitement
  ul,
  ol {
    margin: 0 0 1.5rem 1.5rem;
    padding-left: 1rem;
  }

  ul { list-style: disc outside; }
  ol { list-style: decimal outside; }

  li {
    display: list-item;
    margin-bottom: 0.5rem;
  }

  blockquote {
    margin: 2rem 0;
    padding: 1.25rem 1.5rem;
    border-left: 4px solid $brand-pink;
    background: $brand-gray;
    border-radius: 0 $border-radius $border-radius 0;
    font-style: italic;
  }

  a {
    color: $brand-pink;
    text-decoration: underline;

    &:hover {
      color: $brand-dark;
    }
  }

  img {
    max-width: 100%;
    height: auto;
    border-radius: $border-radius;
    cursor: zoom-in;
    transition: transform 0.2s ease;

    &:hover {
      transform: scale(1.01);
    }
  }

  // Une image servant de lien garde le curseur de lien
  a img {
    cursor: pointer;

    &:hover {
      transform: none;
    }
  }

  // Blocs Gutenberg : images et légendes
  figure {
    margin: 2rem 0;
  }

  figcaption {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: rgba(26, 26, 26, 0.65);
    text-align: center;
  }

  // Bloc « Fichier » de Gutenberg (PDF joints)
  .wp-block-file {
    margin: 2.5rem 0;

    // L'aperçu PDF intégré : Gutenberg pose un style inline (width/height),
    // on le rend responsive et on l'habille aux couleurs du site.
    .wp-block-file__embed,
    object[type='application/pdf'],
    embed[type='application/pdf'] {
      // WordPress sort l'aperçu avec `hidden` + `data-wp-bind--hidden`, révélé
      // par son API Interactivity — script qui ne tourne pas côté Nuxt.
      // Sans ce display forcé, l'aperçu PDF reste invisible pour toujours.
      display: block !important;
      width: 100% !important;
      height: 720px !important;
      max-height: 80vh;
      border: 2px solid $brand-dark;
      border-radius: $border-radius-lg;
      background: $brand-gray;
      margin-bottom: 1rem;
    }

    // Lien de téléchargement : indispensable, iOS et Android n'affichent
    // pas les PDF en <object> et n'auraient sinon aucun moyen de l'ouvrir.
    a {
      display: inline-block;
      color: $brand-dark;
      text-decoration: none;

      &::before {
        content: '📄 ';
      }

      &:hover {
        color: $brand-pink;
        text-decoration: underline;
      }
    }

    .wp-block-file__button {
      margin-left: 0.75rem;
      padding: 0.5rem 1.5rem;
      background: $brand-yellow;
      border: 2px solid $brand-dark;
      border-radius: 999px;
      font-weight: 600;

      &::before {
        content: none;
      }

      &:hover {
        text-decoration: none;
        box-shadow: $shadow-organic-sm;
      }
    }
  }

  // PDF inséré à la main via iframe / embed hors bloc Gutenberg
  iframe,
  embed {
    display: block;
    width: 100%;
    min-height: 720px;
    max-height: 80vh;
    border: 2px solid $brand-dark;
    border-radius: $border-radius-lg;
    margin: 2.5rem 0;
  }

  table {
    display: block;
    width: 100%;
    overflow-x: auto;
    border-collapse: collapse;
    margin: 2rem 0;

    th,
    td {
      border: 1px solid rgba(26, 26, 26, 0.15);
      padding: 0.75rem 1rem;
      text-align: left;
    }

    th {
      background: $brand-gray;
      font-weight: 600;
    }
  }
}

// ------------------------------------------------------------ Sous-pages
.child-pages {
  padding: 0 0 5rem;
}

.child-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
}

.child-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem;
  border: 2px solid $brand-dark;
  border-radius: $border-radius-lg;
  background: #fff;
  color: $brand-dark;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: $shadow-organic;
    color: $brand-pink;
  }
}

.child-title {
  font-family: $font-family-heading;
  font-size: 1.125rem;
  line-height: 1.3;
}

.child-arrow {
  flex-shrink: 0;
  font-size: 1.25rem;
}

// -------------------------------------------------------- Retour parent
.page-footer-nav {
  padding: 0 0 5rem;
}

.parent-link {
  display: inline-block;
  padding: 0.75rem 1.75rem;
  border: 2px solid $brand-dark;
  border-radius: 999px;
  background: $brand-lime;
  color: $brand-dark;
  font-weight: 600;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: $shadow-organic-sm;
  }
}

// --------------------------------------------------------------- États
.page-loading {
  max-width: 900px;
  margin: 0 auto;
  padding: 11rem 1.5rem 5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.skeleton {
  display: block;
  border-radius: $border-radius;
  background: linear-gradient(90deg, $brand-gray 25%, #ececec 50%, $brand-gray 75%);
  background-size: 200% 100%;
  animation: skeletonShimmer 1.4s ease-in-out infinite;

  &--title { height: 3rem; width: 70%; }
  &--lead  { height: 1.5rem; width: 90%; }
  &--media { height: 320px; width: 100%; }
}

@keyframes skeletonShimmer {
  from { background-position: 200% 0; }
  to   { background-position: -200% 0; }
}

.page-error,
.page-notfound {
  max-width: 640px;
  margin: 0 auto;
  padding: 12rem 1.5rem 6rem;
  text-align: center;
  font-size: 1.125rem;
  color: rgba(26, 26, 26, 0.8);

  button {
    display: inline-block;
    margin: 1rem 0 1.5rem;
    padding: 0.75rem 2rem;
    background: $brand-yellow;
    border: 2px solid $brand-dark;
    border-radius: 999px;
    font-weight: 600;
    color: $brand-dark;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: $shadow-organic-sm;
    }
  }
}

.notfound-title {
  font-family: $font-family-heading;
  font-size: 1.5rem;
  color: $brand-dark;
  margin-bottom: 1.5rem;
}

.back-link {
  display: block;
  color: $brand-pink;
  text-decoration: underline;
}

// ------------------------------------------------------------ Lightbox
.lightbox {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem 1.5rem;
  background: rgba(26, 26, 26, 0.92);
  cursor: zoom-out;

  img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: $border-radius;
    cursor: default;
  }
}

.lightbox-close {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  line-height: 1;
  color: $brand-dark;
  background: $brand-yellow;
  border: 2px solid $brand-dark;
  border-radius: 50%;
  cursor: pointer;
  transition: transform 0.2s ease;

  &:hover {
    transform: scale(1.08);
  }
}

.lightbox-fade-enter-active,
.lightbox-fade-leave-active {
  transition: opacity 0.2s ease;
}

.lightbox-fade-enter-from,
.lightbox-fade-leave-to {
  opacity: 0;
}

// --------------------------------------------------------- Responsive
@media (max-width: 1024px) {
  .wp-page-hero {
    padding: 7rem 0 4rem;
  }

  .child-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: clamp(1.75rem, 7vw, 2.25rem);
  }

  .page-media {
    margin-top: -2rem;
    padding: 0 1.25rem;
  }

  .page-body {
    padding: 3rem 1.25rem 4rem;
  }

  .child-grid {
    grid-template-columns: 1fr;
  }

  // Sur mobile l'aperçu PDF natif ne fonctionne pas (iOS/Android) :
  // on l'écrase pour laisser la place au lien d'ouverture.
  :deep(.wp-page-content) {
    .wp-block-file .wp-block-file__embed,
    .wp-block-file object[type='application/pdf'],
    iframe,
    embed {
      height: 60vh !important;
      min-height: 0;
    }
  }
}

@media (max-width: 480px) {
  // Le fil d'ariane se réduit à une remontée d'un niveau
  .breadcrumb--full {
    display: none;
  }

  .breadcrumb--compact {
    display: flex;
  }

  .page-lead {
    font-size: 1.0625rem;
  }

  .page-media img {
    aspect-ratio: 4 / 3;
  }
}
</style>
