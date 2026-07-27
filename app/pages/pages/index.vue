<script setup lang="ts">
import type { WpPageSummary } from '~/composables/useWpPages'

const { getWpPages, decodeHtmlEntities, toPlainText } = useWpPages()

// useAsyncData (non lazy) : la liste doit être rendue côté serveur pour être indexable
const { data: pages, error, refresh, status } = await useAsyncData('wp-pages', () => getWpPages())

const topLevel = computed<WpPageSummary[]>(() =>
  (pages.value ?? []).filter(page => page.parent === 0)
)

const childrenOf = (id: number): WpPageSummary[] =>
  (pages.value ?? []).filter(page => page.parent === id)

useSeoMeta({
  // Le layout applique déjà un titleTemplate « … | Eat Is Family »
  title: 'Toutes nos pages',
  ogTitle: 'Toutes nos pages — Eat Is Family',
  description: 'Parcourez l\'ensemble des pages du site Eat Is Family : notre histoire, nos engagements et nos équipes.',
  ogDescription: 'Parcourez l\'ensemble des pages du site Eat Is Family : notre histoire, nos engagements et nos équipes.',
  ogType: 'website',
  twitterCard: 'summary_large_image'
})
</script>

<template>
  <!-- SVG Filters pour les bordures rugueuses -->
  <svg width="0" height="0" style="position:absolute;overflow:hidden;">
    <defs>
      <filter id="rough-border">
        <feTurbulence type="turbulence" baseFrequency="0.04" numOctaves="4" result="noise" seed="1" />
        <feDisplacementMap in="SourceGraphic" in2="noise" scale="3" xChannelSelector="R" yChannelSelector="G" />
      </filter>
    </defs>
  </svg>

  <div class="wp-pages-index">
    <!-- Hero -->
    <section class="pages-hero">
      <div class="container">
        <h1 class="hero-title">Toutes nos pages</h1>
        <p class="hero-subtitle">
          Tout ce qu'il y a à savoir sur Eat Is Family, réuni au même endroit.
        </p>
      </div>
    </section>

    <section class="pages-listing">
      <div class="container">
        <!-- Chargement -->
        <div v-if="status === 'pending'" class="pages-loading">
          <span v-for="n in 3" :key="n" class="skeleton-card" />
        </div>

        <!-- Erreur réseau -->
        <div v-else-if="error" class="pages-error">
          <p>Impossible de charger la liste des pages.</p>
          <button type="button" @click="refresh()">Réessayer</button>
        </div>

        <!-- Aucune page publiée -->
        <div v-else-if="!topLevel.length" class="pages-empty">
          <p>Aucune page disponible pour le moment.</p>
        </div>

        <!-- Grille -->
        <div v-else class="pages-grid">
          <article v-for="page in topLevel" :key="page.id" class="page-card">
            <NuxtLink :to="`/pages/${page.path}`" class="card-media">
              <img
                v-if="page.featured_media"
                :src="page.featured_media"
                :alt="decodeHtmlEntities(page.title.rendered)"
                loading="lazy"
              />
              <span v-else class="media-placeholder" aria-hidden="true" />
            </NuxtLink>

            <div class="card-body">
              <h2 class="card-title">
                <NuxtLink :to="`/pages/${page.path}`">
                  {{ decodeHtmlEntities(page.title.rendered) }}
                </NuxtLink>
              </h2>

              <p v-if="toPlainText(page.excerpt?.rendered)" class="card-excerpt">
                {{ toPlainText(page.excerpt.rendered, 160) }}
              </p>

              <ul v-if="childrenOf(page.id).length" class="card-children">
                <li v-for="child in childrenOf(page.id)" :key="child.id">
                  <NuxtLink :to="`/pages/${child.path}`">
                    {{ decodeHtmlEntities(child.title.rendered) }}
                  </NuxtLink>
                </li>
              </ul>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.wp-pages-index {
  padding-top: 8em;
  min-height: 100vh;
  background: #fff;
}

// Hero
.pages-hero {
  background: $brand-blue;
  border-radius: $border-radius-lg;
  max-width: 1400px;
  margin: 24px auto 0;
  padding: 3.5rem 0;
  text-align: center;
}

.hero-title {
  font-family: $font-family-heading;
  font-size: clamp(2.25rem, 5vw, 3.5rem);
  line-height: 1.15;
  color: $brand-dark;
  margin: 0;
}

.hero-subtitle {
  margin: 1rem auto 0;
  max-width: 46ch;
  font-size: 1.125rem;
  line-height: 1.6;
  color: rgba(26, 26, 26, 0.8);
}

// Listing
.pages-listing {
  padding: 4rem 0 6rem;
}

.pages-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

// Carte à bordure rugueuse (même traitement que les cartes du blog)
.page-card {
  display: flex;
  flex-direction: column;
  position: relative;
  isolation: isolate;
  background: transparent;
  transition: transform 0.3s ease;

  &::before {
    content: '';
    position: absolute;
    inset: -4px;
    background: $brand-dark;
    border-radius: 20px;
    filter: url(#rough-border);
    z-index: -2;
  }

  &::after {
    content: '';
    position: absolute;
    inset: 0;
    background: #fff;
    border-radius: 16px;
    filter: url(#rough-border);
    z-index: -1;
  }

  &:hover {
    transform: translateY(-4px);
  }
}

.card-media {
  display: block;
  overflow: hidden;
  border-radius: 16px 16px 0 0;

  img,
  .media-placeholder {
    display: block;
    width: 100%;
    height: 260px;
    object-fit: cover;
  }

  .media-placeholder {
    background: $brand-gray;
  }
}

.card-body {
  padding: 1.5rem;
}

.card-title {
  font-family: $font-family-heading;
  font-size: 1.5rem;
  line-height: 1.25;
  margin: 0;

  a {
    color: $brand-dark;
    text-decoration: none;
    transition: color 0.2s ease;

    &:hover {
      color: $brand-pink;
    }
  }
}

.card-excerpt {
  margin: 0.75rem 0 0;
  font-size: 1rem;
  line-height: 1.6;
  color: rgba(26, 26, 26, 0.75);
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-children {
  list-style: none;
  margin: 1.25rem 0 0;
  padding: 1rem 0 0;
  border-top: 1px solid rgba(26, 26, 26, 0.12);

  li + li {
    margin-top: 0.5rem;
  }

  a {
    font-size: 0.9375rem;
    color: $brand-dark;
    text-decoration: none;

    &::before {
      content: '↳ ';
      color: $brand-pink;
    }

    &:hover {
      color: $brand-pink;
      text-decoration: underline;
    }
  }
}

// États
.pages-loading {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.skeleton-card {
  display: block;
  height: 380px;
  border-radius: 16px;
  background: linear-gradient(90deg, $brand-gray 25%, #ececec 50%, $brand-gray 75%);
  background-size: 200% 100%;
  animation: skeletonShimmer 1.4s ease-in-out infinite;
}

@keyframes skeletonShimmer {
  from { background-position: 200% 0; }
  to   { background-position: -200% 0; }
}

.pages-error,
.pages-empty {
  text-align: center;
  padding: 4rem 1rem;
  font-size: 1.125rem;
  color: rgba(26, 26, 26, 0.75);

  button {
    margin-top: 1rem;
    padding: 0.75rem 2rem;
    background: $brand-yellow;
    border: 2px solid $brand-dark;
    border-radius: 999px;
    font-weight: 600;
    color: $brand-dark;
    cursor: pointer;
    transition: box-shadow 0.2s ease, transform 0.2s ease;

    &:hover {
      box-shadow: $shadow-organic-sm;
      transform: translateY(-2px);
    }
  }
}

@media (max-width: 1024px) {
  .pages-grid,
  .pages-loading {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .wp-pages-index {
    padding-top: 6em;
  }

  .pages-hero {
    margin: 16px 1rem 0;
    padding: 2.5rem 1rem;
  }

  .pages-grid,
  .pages-loading {
    grid-template-columns: 1fr;
  }

  .card-media img,
  .card-media .media-placeholder {
    height: 220px;
  }
}
</style>
