<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { LucideArrowRight } from 'lucide-vue-next'

const { getContentByPath } = usePageContent()
const content = ref<any>(null)

onMounted(async () => {
  content.value = await getContentByPath('blog.index')
})
</script>

<template>
  <div v-if="content" class="bg-brand-gray min-h-screen pb-24">
    <section class="bg-white py-20 border-b-2 border-black">
       <div class="container mx-auto px-4 text-center">
          <SectionHeader :title="content.section_title" :subtitle="content.section_subtitle" centered />
       </div>
    </section>

    <div class="container mx-auto px-4 mt-16">
       <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <article v-for="(article, index) in content.articles" :key="index" class="group bg-white rounded-3xl overflow-hidden border-2 border-black shadow-hand hover:shadow-hand-hover transition-all duration-300 hover:-translate-y-2 relative">
             <div class="h-64 overflow-hidden relative border-b-2 border-black">
                <NuxtImg :src="article.image" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" />
                <span class="absolute top-4 right-4 bg-brand-yellow text-brand-black text-xs font-bold px-3 py-1 rounded-full border border-black shadow-sm">
                   {{ article.tag }}
                </span>
             </div>

             <div class="p-8">
                <p class="text-gray-500 text-xs font-bold mb-3 uppercase tracking-wider">{{ article.date }}</p>
                <h3 class="text-2xl font-serif font-bold mb-3 leading-tight group-hover:text-brand-rose transition-colors">
                   <a href="#" class="focus:outline-none">
                      <span class="absolute inset-0 z-10"></span>
                      {{ article.title }}
                   </a>
                </h3>
                <p class="text-gray-600 mb-6 line-clamp-3">
                   {{ article.excerpt }}
                </p>

                <div class="flex items-center text-brand-rose font-bold group-hover:translate-x-2 transition-transform duration-300">
                   {{ content.read_article_link }} <LucideArrowRight class="w-5 h-5 ml-2" />
                </div>
             </div>
          </article>
       </div>

       <div class="mt-16 text-center">
          <BaseButton variant="outline" size="lg">{{ content.load_more_button }}</BaseButton>
       </div>
    </div>
  </div>
</template>
