<template>
  <header v-if="content" class="header">
    <div class="container header-grid torn-paper">
      <!-- Left Navigation -->

      <nav class="nav-right desktop-only">
        <NuxtLink to="/about" class="nav-link">{{ content.nav_links.about }}</NuxtLink>
        <NuxtLink to="/apply-activities" class="nav-link">{{ content.nav_links.activities }}</NuxtLink>
        <NuxtLink to="/events" class="nav-link">{{ content.nav_links.events }}</NuxtLink>
      </nav>
      <!-- Center Logo -->
      <div class="logo-container">
        <NuxtLink to="/" class="logo">
          {{ content.logo }}
        </NuxtLink>
      </div>

      <!-- Right Navigation -->
      <nav class="nav-left desktop-only">
        <NuxtLink to="/careers" class="nav-link">{{ content.nav_links.careers }}</NuxtLink>
        <NuxtLink to="/blog" class="nav-link">{{ content.nav_links.blogs }}</NuxtLink>
        <button class="btn-cta" @click="openContactModal">{{ content.nav_links.get_in_touch }}</button>
      </nav>

      <!-- Mobile Menu Toggle -->
      <div class="mobile-toggle">
        <LayoutNavigation @open-contact="openContactModal" />
      </div>
    </div>

    <LayoutContactModal :is-open="isContactModalOpen" @close="closeContactModal" />
  </header>
</template>

<script setup lang="ts">
const { getHeaderContent } = usePageContent()
const content = ref<any>(null)
const isContactModalOpen = ref(false)

onMounted(async () => {
  content.value = await getHeaderContent()
})

const openContactModal = () => {
  isContactModalOpen.value = true
}

const closeContactModal = () => {
  isContactModalOpen.value = false
}
</script>

<style scoped lang="scss">
.header {
  padding: 1.5rem 0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-grid {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  padding:1em;
    a{
    font-family: "Recoleta",sans-serif;
      font-size: 18px;
  font-weight: 600;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #0d0a00;
  }
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 2rem;
  justify-content: flex-end;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 2rem;
  justify-content: flex-start;
}

.nav-link {
  font-weight: 500;
  color: var(--color-text-dark);
  transition: color 0.2s ease;
  font-family: var(--font-body);
}

.nav-link:hover {
  color: var(--color-primary-blue);
}

.logo {
  font-family: var(--font-heading);
  font-size: 2rem;
  font-weight: 900;
  color: var(--color-text-dark);
  text-decoration: none;
  letter-spacing: -1px;
}

.mobile-toggle {
  display: none;
}

/* Button override for header */
.btn-cta {
  font-size: 0.9rem;
  padding: 0.5rem 1.25rem;
}

@media (max-width: 968px) {
  .header-grid {
    display: flex;
    justify-content: space-between;
  }

  .desktop-only {
    display: none;
  }

  .mobile-toggle {
    display: block;
  }
}
</style>
