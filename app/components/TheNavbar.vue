<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { LucideMenu, LucideX } from 'lucide-vue-next'
import type { NavbarContent } from '~/composables/usePageContent'

const isOpen = ref(false)
const { getNavbarContent } = usePageContent()
const content = ref<NavbarContent | null>(null)

onMounted(async () => {
  content.value = await getNavbarContent()
})
</script>

<template>
  <nav class="fixed top-0 left-0 w-full z-50 bg-white/95 backdrop-blur-md border-b-2 border-black">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">

      <!-- Mobile Menu Button -->
      <button @click="isOpen = !isOpen" class="lg:hidden text-brand-dark">
        <LucideMenu v-if="!isOpen" class="w-8 h-8" />
        <LucideX v-else class="w-8 h-8" />
      </button>

      <!-- Left Links (Desktop) -->
      <div v-if="content" class="hidden lg:flex items-center space-x-8">
        <NuxtLink
          v-for="link in content.nav_links.slice(0, 2)"
          :key="link.text"
          :to="link.to"
          class="font-body font-medium hover:text-brand-pink transition-colors"
        >
          {{ link.text }}
        </NuxtLink>
      </div>

      <!-- Logo -->
      <NuxtLink to="/" class="absolute left-1/2 transform -translate-x-1/2 group">
        <h1 v-if="content" class="text-2xl lg:text-3xl font-heading font-bold tracking-tight group-hover:scale-105 transition-transform">
          {{ content.brand_name }} <span class="text-brand-pink italic">{{ content.brand_highlight }}</span>
          <span class="absolute -right-2 -top-1 w-2 h-2 bg-brand-yellow rounded-full border border-black"></span>
        </h1>
      </NuxtLink>

      <!-- Right Links & CTA (Desktop) -->
      <div v-if="content" class="hidden lg:flex items-center space-x-8">
        <NuxtLink
          v-for="link in content.nav_links.slice(2)"
          :key="link.text"
          :to="link.to"
          class="font-body font-medium hover:text-brand-pink transition-colors"
        >
          {{ link.text }}
        </NuxtLink>
        <NuxtLink to="/contact" class="btn-primary text-sm">
          {{ content.cta_desktop }}
        </NuxtLink>
      </div>

      <!-- Mobile CTA -->
      <div v-if="content" class="lg:hidden">
        <NuxtLink to="/contact" class="btn-primary text-sm px-4 py-2">
          {{ content.cta_mobile }}
        </NuxtLink>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-4 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-4 opacity-0"
    >
      <div v-if="isOpen && content" class="lg:hidden bg-white border-t-2 border-black p-6 shadow-lg absolute w-full">
        <div class="flex flex-col space-y-4">
          <NuxtLink
            v-for="link in content.nav_links"
            :key="link.text"
            :to="link.to"
            class="text-lg font-body font-medium hover:text-brand-pink transition-colors"
            @click="isOpen = false"
          >
            {{ link.text }}
          </NuxtLink>
        </div>
      </div>
    </Transition>
  </nav>
</template>
