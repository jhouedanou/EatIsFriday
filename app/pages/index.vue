<script setup lang="ts">
import { LucideChefHat, LucideUtensils, LucidePartyPopper, LucideMapPin, LucideArrowRight } from 'lucide-vue-next'
import gsap from 'gsap'
import type { HomepageContent } from '~/composables/usePageContent'

const heroTitle = ref(null)
const { getHomepageContent } = usePageContent()
const content = ref<HomepageContent | null>(null)

// Icon mapping
const iconMap: Record<string, any> = {
  'chef-hat': LucideChefHat,
  'utensils': LucideUtensils,
  'party-popper': LucidePartyPopper
}

// Color mapping
const bgColorMap: Record<string, string> = {
  'pink': 'bg-brand-pink',
  'lime': 'bg-brand-lime',
  'yellow': 'bg-brand-yellow'
}

const textColorMap: Record<string, string> = {
  'white': 'text-white',
  'dark': 'text-brand-dark'
}

onMounted(async () => {
  content.value = await getHomepageContent()
  
  if (heroTitle.value && content.value) {
    gsap.from(heroTitle.value, {
      y: 60,
      opacity: 0,
      duration: 1,
      ease: 'power4.out',
      delay: 0.2
    })
  }
})
</script>

<template>
  <div class="overflow-hidden" v-if="content">
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center bg-white overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-10 w-40 h-40 border-2 border-black rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-60 h-60 border-2 border-black rounded-full"></div>
      </div>

      <div class="container mx-auto px-4 py-20">
        <div class="flex flex-col lg:flex-row items-center gap-12">
          <!-- Left: Text -->
          <div class="lg:w-1/2">
            <div class="inline-block relative mb-6">
              <span class="tag-pink text-xs">{{ content.hero_section.tag }}</span>
            </div>

            <h1 ref="heroTitle" class="font-heading text-5xl md:text-7xl lg:text-8xl font-bold leading-[0.95] text-brand-dark mb-8">
              <span class="relative inline-block">
                {{ content.hero_section.title.line_1 }}
                <span class="absolute -bottom-2 left-0 w-full h-3 bg-brand-yellow -z-10 transform -rotate-1"></span>
              </span><br/>
              {{ content.hero_section.title.line_2_prefix }} <span class="italic">{{ content.hero_section.title.line_2_highlight }}</span><br/>
              {{ content.hero_section.title.line_3 }}<br/>
              <span class="text-brand-pink">{{ content.hero_section.title.line_4 }}</span>
            </h1>

            <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-lg font-body leading-relaxed">
              {{ content.hero_section.description }}
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
              <BaseButton :to="content.hero_section.cta_primary.link" variant="primary" size="lg">
                {{ content.hero_section.cta_primary.text }}
              </BaseButton>
              <BaseButton :to="content.hero_section.cta_secondary.link" variant="secondary" size="lg">
                {{ content.hero_section.cta_secondary.text }}
              </BaseButton>
            </div>
          </div>

          <!-- Right: Image Grid -->
          <div class="lg:w-1/2 relative">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-4">
                <div class="border-organic overflow-hidden h-48">
                  <NuxtImg
                    :src="content.hero_section.images[0]?.src"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                    :alt="content.hero_section.images[0]?.alt"
                  />
                </div>
                <div class="border-organic overflow-hidden h-64">
                  <NuxtImg
                    :src="content.hero_section.images[1]?.src"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                    :alt="content.hero_section.images[1]?.alt"
                  />
                </div>
              </div>
              <div class="space-y-4 pt-8">
                <div class="border-organic overflow-hidden h-64">
                  <NuxtImg
                    :src="content.hero_section.images[2]?.src"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                    :alt="content.hero_section.images[2]?.alt"
                  />
                </div>
                <div class="border-organic overflow-hidden h-48 bg-brand-blue flex items-center justify-center">
                  <div class="text-center p-4">
                    <p class="font-heading text-4xl font-bold">{{ content.hero_section.experience_badge.number }}</p>
                    <p class="font-body text-sm">{{ content.hero_section.experience_badge.label }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Floating Badge -->
            <div class="absolute -bottom-4 -left-4 bg-brand-lime border-organic px-6 py-3 animate-float">
              <span class="font-heading font-bold">🍴 {{ content.hero_section.floating_badge }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Preview Section -->
    <section class="py-20 bg-brand-gray overflow-hidden">
      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12">
          <div class="lg:w-1/3">
            <span class="tag-blue mb-4 inline-block">{{ content.locations_section.tag }}</span>
            <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">
              {{ content.locations_section.title.line_1 }}<br/>
              <span class="relative inline-block">
                {{ content.locations_section.title.line_2_highlight }}
                <span class="absolute -bottom-1 left-0 w-full h-3 bg-brand-lime -z-10"></span>
              </span>
            </h2>
            <p class="text-gray-600 font-body mb-8 leading-relaxed">
              {{ content.locations_section.description }}
            </p>

            <ul class="space-y-3">
              <li v-for="loc in content.locations_section.locations" :key="loc.name" class="flex items-center justify-between p-4 bg-white border-organic hover:shadow-organic transition-all cursor-pointer group">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-brand-pink flex items-center justify-center border-2 border-black group-hover:scale-110 transition-transform">
                    <LucideMapPin class="w-5 h-5 text-white" />
                  </div>
                  <span class="font-bold text-lg">{{ loc.name }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-brand-pink font-bold">{{ loc.count }} {{ content.locations_section.jobs_suffix }}</span>
                  <LucideArrowRight class="w-4 h-4 text-gray-400 group-hover:translate-x-1 transition-transform" />
                </div>
              </li>
            </ul>
          </div>

          <div class="lg:w-2/3 h-[500px]">
            <div class="w-full h-full border-organic shadow-organic overflow-hidden">
              <ClientOnly>
                <VenueMap :venues="content.locations_section.map_venues.map(v => ({
                  id: v.id,
                  name: v.name,
                  location: v.location,
                  lat: v.lat,
                  lng: v.lng,
                  openPositions: v.open_positions
                }))" />
              </ClientOnly>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 bg-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <span class="tag-yellow mb-4 inline-block">{{ content.services_section.tag }}</span>
          <h2 class="font-heading text-4xl md:text-6xl font-bold">
            {{ content.services_section.title.line_1 }}<br/>
            <span class="relative inline-block">
              {{ content.services_section.title.highlight }}
              <span class="absolute -bottom-1 left-0 w-full h-3 bg-brand-blue -z-10 transform rotate-1"></span>
            </span> {{ content.services_section.title.line_2 }}
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div
            v-for="(service, i) in content.services_section.services"
            :key="i"
            :class="['card-organic relative overflow-hidden group', bgColorMap[service.bg_color || 'pink'], textColorMap[service.text_color || 'white']]"
          >
            <component :is="iconMap[service.icon]" class="w-12 h-12 mb-6 transform group-hover:scale-110 transition-transform duration-300" />
            <h3 class="font-heading text-2xl font-bold mb-4">{{ service.title }}</h3>
            <p class="opacity-90 font-body">{{ service.description }}</p>

            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-black/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>

            <button class="mt-6 flex items-center gap-2 font-bold group-hover:gap-4 transition-all">
              {{ content.services_section.learn_more_button }} <LucideArrowRight class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners Section -->
    <section class="py-16 bg-brand-gray border-y-2 border-black">
      <div class="container mx-auto px-4">
        <p class="text-center font-heading text-xl mb-8">
          {{ content.partners_section.intro_text }}
        </p>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
          <div v-for="(partner, i) in content.partners_section.partners" :key="i" class="text-3xl font-heading font-bold text-gray-400 hover:text-brand-dark transition-colors cursor-pointer">
            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="h-12" />
            <span v-else>{{ partner.name }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-brand-pink text-white text-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-1/4 w-80 h-80 border-2 border-white rounded-full"></div>
        <div class="absolute bottom-0 right-1/4 w-60 h-60 border-2 border-white rounded-full"></div>
      </div>

      <div class="container mx-auto px-4 relative z-10">
        <h2 class="font-heading text-4xl md:text-6xl font-bold mb-6">{{ content.cta_section.title }}</h2>
        <p class="text-white/80 max-w-2xl mx-auto mb-10 text-lg font-body">
          {{ content.cta_section.description }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <BaseButton :to="content.cta_section.cta_primary.link" variant="lime" size="lg">{{ content.cta_section.cta_primary.text }}</BaseButton>
          <BaseButton :to="content.cta_section.cta_secondary.link" variant="secondary" size="lg">{{ content.cta_section.cta_secondary.text }}</BaseButton>
        </div>
      </div>
    </section>

    <!-- Food Gallery Section -->
    <section class="py-20 bg-brand-dark">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="(image, i) in content.gallery_section.images" :key="i" class="border-organic overflow-hidden aspect-square">
            <NuxtImg
              :src="image.src"
              class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
              :alt="image.alt"
            />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.tag-pink {
  @apply inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-black bg-brand-pink text-white;
  border-radius: 50px 10px 45px 10px / 10px 45px 10px 50px;
}
</style>
