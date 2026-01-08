<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { LucideLinkedin, LucideInstagram, LucideTwitter, LucideHeart } from 'lucide-vue-next'
import type { FooterContent } from '~/composables/usePageContent'

const { getFooterContent } = usePageContent()
const content = ref<FooterContent | null>(null)

const socialLinks = [
  { icon: LucideLinkedin, href: '#', label: 'LinkedIn' },
  { icon: LucideInstagram, href: '#', label: 'Instagram' },
  { icon: LucideTwitter, href: '#', label: 'Twitter' }
]

onMounted(async () => {
  content.value = await getFooterContent()
})

const currentYear = new Date().getFullYear()
</script>

<template>
  <footer v-if="content" class="bg-brand-dark text-white py-16">
    <div class="container mx-auto px-4">
      <!-- Main Footer Content -->
      <div class="grid md:grid-cols-3 gap-12 mb-12">
        <!-- Brand Column -->
        <div>
          <div class="flex items-center gap-2 mb-6">
            <LucideHeart class="w-6 h-6 text-brand-pink fill-brand-pink" />
            <span class="font-heading text-xl font-bold">{{ content.brand_name }}</span>
          </div>

          <p class="text-gray-400 mb-6 font-body text-sm leading-relaxed">
            {{ content.brand_description }}
          </p>

          <div class="space-y-2 text-sm text-gray-400">
            <p>{{ content.contact_email }}</p>
            <p>{{ content.contact_phone }}</p>
          </div>

          <!-- Social Links -->
          <div class="flex gap-3 mt-6">
            <a
              v-for="social in socialLinks"
              :key="social.label"
              :href="social.href"
              :aria-label="social.label"
              class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center hover:bg-white hover:text-brand-dark transition-all duration-300"
            >
              <component :is="social.icon" class="w-4 h-4" />
            </a>
          </div>
        </div>

        <!-- Company Links -->
        <div>
          <h4 class="font-heading font-bold text-lg mb-6">{{ content.company_title }}</h4>
          <ul class="space-y-3">
            <li v-for="link in content.company_links" :key="link.text">
              <NuxtLink
                :to="link.to"
                class="text-gray-400 hover:text-white transition-colors font-body text-sm"
              >
                {{ link.text }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Policy Links -->
        <div>
          <h4 class="font-heading font-bold text-lg mb-6">{{ content.policy_title }}</h4>
          <ul class="space-y-3">
            <li v-for="link in content.policy_links" :key="link.text">
              <NuxtLink
                :to="link.to"
                class="text-gray-400 hover:text-white transition-colors font-body text-sm"
              >
                {{ link.text }}
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
        <p>{{ content.copyright_template.replace('{year}', currentYear.toString()) }}</p>
        <a href="#top" class="mt-4 md:mt-0 hover:text-white transition-colors flex items-center gap-2">
          {{ content.back_to_top }} ↑
        </a>
      </div>
    </div>
  </footer>
</template>
