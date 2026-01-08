<script setup lang="ts">
import { LucideMapPin, LucideX, LucideHeart, LucideShare2, LucideChevronRight } from 'lucide-vue-next'

const { getJobDetailContent } = usePageContent()
const content = ref<any>(null)
const job = ref<any>(null)

onMounted(async () => {
  content.value = await getJobDetailContent()
  // In a real app, this would come from an API based on route params
  job.value = content.value?.sample_job || null
})
</script>

<template>
  <div v-if="content && job" class="min-h-screen bg-white">
    <!-- Header -->
    <header class="border-b border-gray-200 py-4">
      <div class="container mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-brand-pink">
          <LucideMapPin class="w-5 h-5" />
          <span class="font-medium">{{ job.location }}</span>
        </div>

        <button class="w-10 h-10 bg-brand-yellow rounded-full flex items-center justify-center border-2 border-black hover:scale-110 transition-transform">
          <LucideX class="w-5 h-5" />
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <!-- Title & Tags -->
      <div class="mb-8">
        <h1 class="font-heading text-4xl md:text-5xl font-bold mb-6">{{ job.title }}</h1>

        <div class="flex flex-wrap gap-3">
          <span class="tag-blue">{{ content.quick_facts.department_label }} · {{ job.department }}</span>
          <span class="tag-lime flex items-center gap-1">
            <span>🌿</span> {{ job.type }}
          </span>
          <span class="tag-yellow flex items-center gap-1">
            <span>💰</span> {{ job.salary }}
          </span>
        </div>
      </div>

      <!-- Description -->
      <p class="text-gray-600 text-lg mb-8 max-w-4xl font-body leading-relaxed">
        {{ job.description }}
      </p>

      <!-- CTA Banner -->
      <div class="bg-brand-blue border-organic p-6 mb-12 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h3 class="font-heading text-xl font-bold mb-1">{{ content.cta_banner.title }}</h3>
          <p class="text-gray-700 font-body">{{ content.cta_banner.subtitle }}</p>
        </div>
        <button class="btn-primary whitespace-nowrap">
          {{ content.cta_banner.apply_button }}
        </button>
      </div>

      <!-- Life at Location -->
      <section class="mb-12">
        <h2 class="font-heading text-2xl font-bold mb-6">{{ content.life_at_location.title_template.replace('{location}', job.location.split(',')[0]) }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="(img, i) in job.images" :key="i" class="border-organic overflow-hidden aspect-video">
            <NuxtImg :src="img" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" />
          </div>
        </div>
      </section>

      <!-- Job Details Grid -->
      <section class="mb-12">
        <h2 class="font-heading text-2xl font-bold mb-6">{{ content.job_description.section_title }}</h2>

        <div class="grid md:grid-cols-2 gap-6">
          <!-- What You'll Do -->
          <div class="card-organic">
            <h3 class="font-heading text-lg font-bold mb-4 flex items-center gap-2">
              <span class="w-6 h-6 bg-brand-pink rounded-full flex items-center justify-center text-white text-xs">✓</span>
              {{ content.job_description.what_you_do_title }}
            </h3>
            <p class="text-gray-500 text-sm mb-4 font-body">{{ content.job_description.what_you_do_intro }}</p>
            <ul class="space-y-3">
              <li v-for="(item, i) in job.what_you_do" :key="i" class="flex items-start gap-3 text-sm font-body">
                <LucideChevronRight class="w-4 h-4 text-brand-pink flex-shrink-0 mt-0.5" />
                {{ item }}
              </li>
            </ul>
          </div>

          <!-- Requirements -->
          <div class="card-organic bg-brand-lime">
            <h3 class="font-heading text-lg font-bold mb-4">{{ content.job_description.requirements_title }}</h3>
            <p class="text-gray-700 text-sm mb-4 font-body">{{ content.job_description.requirements_intro }}</p>
            <ul class="space-y-3">
              <li v-for="(item, i) in job.requirements" :key="i" class="flex items-start gap-3 text-sm font-body">
                <LucideChevronRight class="w-4 h-4 text-brand-dark flex-shrink-0 mt-0.5" />
                {{ item }}
              </li>
            </ul>
          </div>
        </div>
      </section>

      <!-- Bottom Section -->
      <section class="grid md:grid-cols-3 gap-6 mb-12">
        <!-- Why Join Us -->
        <div class="bg-brand-pink text-white p-6 border-organic">
          <h3 class="font-heading text-lg font-bold mb-4 flex items-center gap-2">
            <LucideHeart class="w-5 h-5" /> {{ content.why_join_us.title }}
          </h3>
          <ul class="space-y-3">
            <li v-for="(item, i) in job.why_join_us" :key="i" class="flex items-start gap-3 text-sm font-body">
              <LucideChevronRight class="w-4 h-4 flex-shrink-0 mt-0.5" />
              {{ item }}
            </li>
          </ul>
        </div>

        <!-- Quick Facts -->
        <div class="card-organic">
          <h3 class="font-heading text-lg font-bold mb-4">{{ content.quick_facts.title }}</h3>
          <div class="space-y-4 text-sm">
            <div>
              <p class="text-gray-500 uppercase text-xs mb-1">{{ content.quick_facts.location_label }}</p>
              <p class="font-bold">{{ job.location }}</p>
            </div>
            <div>
              <p class="text-gray-500 uppercase text-xs mb-1">{{ content.quick_facts.department_label }}</p>
              <p class="font-bold">{{ job.department }}</p>
            </div>
            <div>
              <p class="text-gray-500 uppercase text-xs mb-1">{{ content.quick_facts.employment_type_label }}</p>
              <p class="font-bold">{{ job.type }}</p>
            </div>
            <div>
              <p class="text-gray-500 uppercase text-xs mb-1">{{ content.quick_facts.available_positions_label }}</p>
              <p class="font-bold text-brand-pink">{{ job.slots }}</p>
            </div>
          </div>
        </div>

        <!-- Share -->
        <div class="card-organic">
          <h3 class="font-heading text-lg font-bold mb-4">{{ content.share_section.title }}</h3>
          <p class="text-gray-600 text-sm mb-4 font-body">{{ content.share_section.subtitle }}</p>
          <button class="btn-secondary flex items-center gap-2 w-full justify-center">
            <LucideShare2 class="w-4 h-4" />
            {{ content.share_section.share_button }}
          </button>
        </div>
      </section>

      <!-- Final CTA -->
      <section class="bg-brand-yellow border-organic p-8 text-center">
        <h2 class="font-heading text-3xl md:text-4xl font-bold mb-4">{{ content.final_cta.title }}</h2>
        <p class="text-gray-700 max-w-2xl mx-auto mb-8 font-body">
          {{ content.final_cta.description }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="btn-lime">{{ content.final_cta.apply_button }}</button>
          <NuxtLink to="/careers" class="btn-secondary">{{ content.final_cta.back_button }}</NuxtLink>
        </div>
      </section>
    </main>
  </div>
</template>
