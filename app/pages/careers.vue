<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { LucideSearch, LucideMapPin, LucideX, LucideChevronDown } from 'lucide-vue-next'
import type { CareersContent } from '~/composables/usePageContent'

const { getCareersContent } = usePageContent()
const content = ref<CareersContent | null>(null)

const searchQuery = ref('')
const selectedJobType = ref('')
const selectedVenue = ref('')
const showFilters = ref(false)

onMounted(async () => {
  content.value = await getCareersContent()
  if (content.value) {
    selectedJobType.value = content.value.search_section.job_types[0]
    selectedVenue.value = content.value.venues[0]?.id || ''
  }
})

const currentVenue = computed(() => {
  if (!content.value) return null
  return content.value.venues.find(v => v.id === selectedVenue.value) || content.value.venues[0]
})

const filteredJobs = computed(() => {
  if (!content.value) return []
  return content.value.jobs.filter(job => {
    const matchesSearch = job.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          job.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesType = selectedJobType.value === content.value!.search_section.job_types[0] || job.type === selectedJobType.value
    const matchesVenue = job.venue_id === selectedVenue.value
    return matchesSearch && matchesType && matchesVenue
  })
})

const selectVenue = (venueId: string) => {
  selectedVenue.value = venueId
}
</script>

<template>
  <div v-if="content" class="min-h-screen bg-brand-gray">
    <!-- Hero Section with Map -->
    <section class="relative h-[500px] md:h-[600px]">
      <!-- Interactive Map Background -->
      <div class="absolute inset-0">
        <ClientOnly>
          <VenueMap
            :venues="content.venues.map(v => ({
              id: v.id,
              name: v.name,
              location: v.location,
              lat: v.lat,
              lng: v.lng,
              openPositions: v.open_positions
            }))"
            :selected-venue="selectedVenue"
            @select-venue="selectVenue"
          />
          <template #fallback>
            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
              <span class="text-gray-500">{{ content.map_loading }}</span>
            </div>
          </template>
        </ClientOnly>
      </div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-brand-gray pointer-events-none"></div>

      <!-- Close Button -->
      <button class="absolute top-6 right-6 z-20 w-12 h-12 bg-brand-yellow rounded-full flex items-center justify-center border-2 border-black hover:scale-110 transition-transform shadow-organic">
        <LucideX class="w-5 h-5" />
      </button>

      <!-- Venue Info Card -->
      <div v-if="currentVenue" class="absolute bottom-8 left-4 right-4 md:left-8 md:right-auto md:max-w-md z-10">
        <div class="bg-white border-organic p-6 shadow-organic">
          <span class="tag-lime text-xs mb-3 inline-block">{{ content.hero_section.tag }}</span>

          <h1 class="font-heading text-3xl md:text-4xl font-bold leading-tight mb-3">
            {{ content.hero_section.title_template.replace('{venue_name}', currentVenue.name) }}
          </h1>

          <div class="flex items-center gap-4 text-gray-600">
            <div class="flex items-center gap-2">
              <LucideMapPin class="w-4 h-4 text-brand-pink" />
              <span class="text-sm">{{ currentVenue.location }}</span>
            </div>
            <span class="w-1.5 h-1.5 rounded-full bg-brand-lime"></span>
            <span class="text-sm font-bold">{{ currentVenue.open_positions }} {{ content.hero_section.open_positions_suffix }}</span>
          </div>
        </div>
      </div>

      <!-- Venue Pills -->
      <div class="absolute top-6 left-4 md:left-8 z-20 flex gap-2 flex-wrap max-w-[60%]">
        <button
          v-for="venue in content.venues"
          :key="venue.id"
          @click="selectVenue(venue.id)"
          :class="[
            'px-4 py-2 text-sm font-bold border-2 border-black transition-all',
            selectedVenue === venue.id
              ? 'bg-brand-pink text-white shadow-organic-sm'
              : 'bg-white hover:bg-brand-lime'
          ]"
          style="border-radius: 50px 10px 45px 10px / 10px 45px 10px 50px;"
        >
          {{ venue.name }}
        </button>
      </div>
    </section>

    <!-- Search & Filter Bar -->
    <section class="container mx-auto px-4 -mt-6 relative z-20">
      <div class="bg-brand-dark border-organic p-4 flex flex-col md:flex-row gap-4 shadow-organic">
        <!-- Search Input -->
        <div class="flex-1 relative">
          <div class="flex items-center gap-3 px-4">
            <LucideSearch class="w-5 h-5 text-white/70" />
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="content.search_section.search_placeholder"
              class="bg-transparent text-white placeholder:text-white/50 outline-none flex-1 py-3 font-body"
            />
          </div>
        </div>

        <!-- Job Type Dropdown -->
        <div class="relative">
          <button
            @click="showFilters = !showFilters"
            class="bg-brand-dark border-l border-white/20 px-6 py-3 flex items-center gap-3 text-white hover:bg-white/5 transition-colors w-full md:w-auto justify-between"
          >
            <span>{{ selectedJobType }}</span>
            <LucideChevronDown class="w-4 h-4" :class="{ 'rotate-180': showFilters }" />
          </button>

          <!-- Dropdown Menu -->
          <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div v-if="showFilters" class="absolute top-full right-0 mt-2 w-48 bg-white border-organic shadow-organic-lg z-30">
              <button
                v-for="type in content.search_section.job_types"
                :key="type"
                @click="selectedJobType = type; showFilters = false"
                :class="[
                  'w-full text-left px-4 py-3 hover:bg-brand-lime/30 transition-colors font-medium',
                  selectedJobType === type ? 'bg-brand-lime/50' : ''
                ]"
              >
                {{ type }}
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </section>

    <!-- Job Grid -->
    <section class="container mx-auto px-4 py-12">
      <div class="flex items-center justify-between mb-8">
        <h2 class="font-heading text-2xl font-bold">
          {{ filteredJobs.length }} {{ filteredJobs.length === 1 ? content.job_listing.positions_available_singular : content.job_listing.positions_available_plural }} {{ content.job_listing.positions_available_suffix }}
        </h2>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="job in filteredJobs"
          :key="job.id"
          class="bg-white border-organic p-6 hover:shadow-organic transition-all duration-300 group"
        >
          <div class="flex flex-col h-full justify-between">
            <!-- Header -->
            <div>
              <h3 class="font-heading font-bold text-xl leading-tight mb-1">{{ job.title }}</h3>
              <p class="text-xs text-gray-500 font-medium mb-4">{{ content.job_listing.posted_prefix }} {{ job.posted_time }}</p>

              <!-- Tags Row -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span class="tag-blue">{{ content.job_listing.department_prefix }} · {{ job.department }}</span>
                <span class="tag-lime flex items-center gap-1">
                  <span class="text-sm">🌿</span> {{ job.type }}
                </span>
                <span class="tag-yellow flex items-center gap-1">
                  <span class="text-sm">💰</span> {{ job.salary }}
                </span>
              </div>

              <!-- Description -->
              <p class="text-sm text-gray-600 mb-6 line-clamp-3 font-body leading-relaxed">
                {{ job.description }}
              </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-auto">
              <NuxtLink :to="`/jobs/${job.id}`" class="btn-primary flex-1 text-center text-sm">
                {{ content.job_listing.apply_button }}
              </NuxtLink>
              <NuxtLink :to="`/jobs/${job.id}`" class="btn-secondary flex-1 text-center text-sm">
                {{ content.job_listing.view_details_button }}
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-if="filteredJobs.length === 0" class="text-center py-20 bg-white border-organic">
        <p class="text-xl text-gray-500 mb-2">{{ content.no_results.title }}</p>
        <p class="text-gray-400 mb-4">{{ content.no_results.description }}</p>
        <button @click="searchQuery = ''; selectedJobType = content.search_section.job_types[0]" class="text-brand-pink font-bold hover:underline">
          {{ content.no_results.clear_filters_button }}
        </button>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-brand-dark py-16 text-center">
      <div class="container mx-auto px-4">
        <h2 class="font-heading text-3xl md:text-5xl font-bold text-white mb-6">
          {{ content.cta_section.title }}
        </h2>
        <p class="text-gray-400 max-w-2xl mx-auto mb-8 font-body">
          {{ content.cta_section.description }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="btn-lime text-lg px-8 py-4">
            {{ content.cta_section.explore_venues_button }}
          </button>
          <button class="btn-secondary text-lg px-8 py-4">
            {{ content.cta_section.general_application_button }}
          </button>
        </div>
      </div>
    </section>
  </div>
</template>
