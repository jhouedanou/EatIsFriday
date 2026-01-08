<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { LucideX } from 'lucide-vue-next'
import type { ContactContent } from '~/composables/usePageContent'

const { getContactContent } = usePageContent()
const content = ref<ContactContent | null>(null)

const form = ref({
  name: '',
  email: '',
  eventType: '',
  location: '',
  date: '',
  guests: '',
  message: ''
})

onMounted(async () => {
  content.value = await getContactContent()
})

const submitForm = () => {
  console.log('Form submitted:', form.value)
  // Handle form submission
}
</script>

<template>
  <div v-if="content" class="min-h-screen bg-brand-lime">
    <!-- Close Button (for modal context) -->
    <button class="fixed top-6 right-6 z-50 w-12 h-12 bg-brand-yellow rounded-full flex items-center justify-center border-2 border-black hover:scale-110 transition-transform shadow-organic">
      <LucideX class="w-6 h-6" />
    </button>

    <!-- Decorative Arrow -->
    <div class="absolute top-20 left-8 hidden lg:block">
      <svg width="80" height="80" viewBox="0 0 80 80" fill="none" class="animate-wiggle">
        <path d="M20 10 C 30 30, 50 40, 70 60" stroke="#1A1A1A" stroke-width="2" fill="none" stroke-linecap="round"/>
        <path d="M60 55 L 70 60 L 65 50" stroke="#1A1A1A" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <div class="container mx-auto px-4 py-12 lg:py-20">
      <!-- Hero Section -->
      <div class="flex flex-col lg:flex-row items-start justify-between gap-12 mb-16">
        <!-- Left: Heading -->
        <div class="lg:w-1/2">
          <h1 class="font-heading text-5xl md:text-7xl lg:text-8xl font-bold leading-[0.95] text-brand-dark">
            {{ content.hero_section.title.line_1 }} <span class="relative inline-block">
              {{ content.hero_section.title.line_1_highlight }}
              <span class="absolute -bottom-2 left-0 w-full h-4 bg-brand-blue/60 -z-10 transform -rotate-1"></span>
            </span> {{ content.hero_section.title.line_2 }}<br/>
            {{ content.hero_section.title.line_3 }} <span class="italic">{{ content.hero_section.title.line_3_highlight }}</span>
          </h1>

          <p class="mt-8 text-lg text-brand-dark/80 max-w-md font-body">
            {{ content.hero_section.description }}
          </p>
        </div>

        <!-- Right: Image in Blob Shape -->
        <div class="lg:w-1/2 flex justify-center lg:justify-end">
          <div class="relative w-72 h-72 md:w-80 md:h-80 lg:w-[450px] lg:h-[450px]">
            <!-- The actual blob shaped image -->
            <div class="absolute inset-0 blob-mask overflow-hidden border-4 border-black group">
              <NuxtImg
                :src="content.hero_section.image.src"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                :alt="content.hero_section.image.alt"
              />
            </div>

            <!-- Floating accent circles -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-yellow rounded-full -z-10 animate-float"></div>
            <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-brand-blue rounded-full -z-10 animate-float" style="animation-delay: 1.5s"></div>
          </div>
        </div>
      </div>

      <!-- Form Section -->
      <div class="dashed-organic p-8 lg:p-12 bg-brand-lime/30">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Row 1: Name & Email -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <input
                v-model="form.name"
                type="text"
                :placeholder="content.form.name_placeholder"
                class="input-organic"
              />
            </div>
            <div>
              <input
                v-model="form.email"
                type="email"
                :placeholder="content.form.email_placeholder"
                class="input-organic"
              />
            </div>
          </div>

          <!-- Row 2: Event Type & Location -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <input
                v-model="form.eventType"
                type="text"
                :placeholder="content.form.event_type_placeholder"
                class="input-organic"
              />
            </div>
            <div>
              <input
                v-model="form.location"
                type="text"
                :placeholder="content.form.location_placeholder"
                class="input-organic"
              />
            </div>
          </div>

          <!-- Row 3: Date & Guests -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <input
                v-model="form.date"
                type="text"
                :placeholder="content.form.date_placeholder"
                class="input-organic"
              />
            </div>
            <div>
              <input
                v-model="form.guests"
                type="text"
                :placeholder="content.form.guests_placeholder"
                class="input-organic"
              />
            </div>
          </div>

          <!-- Row 4: Message -->
          <div>
            <textarea
              v-model="form.message"
              :placeholder="content.form.message_placeholder"
              rows="5"
              class="textarea-organic"
            ></textarea>
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button type="submit" class="btn-primary text-lg px-10 py-4">
              {{ content.form.submit_button }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
