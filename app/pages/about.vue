<template>
  <div class="about-page">
    <section v-if="siteContent" id="aboutHero" class="page-hero">
      <div class="container-fluid d-flex flex-column justify-content-center align-items-center h-100">
        <h1 class="heroTitle">{{ siteContent.about.hero.title }}</h1>
        <div class="d-flex">
          <div class="row">
            <div class="col-4">
              <div class="heroTextContainer">
                <h2 class="heroSubtitle">{{ siteContent.about.hero.subtitle }}</h2>
                <p class="heroDescription">{{ siteContent.about.hero.description }}</p>
              </div>
            </div>
            <div class="col-8">
              <img :src="siteContent.about.hero.image.src" :alt="siteContent.about.hero.image.alt"
                class="img-fluid heroImage" />
            </div>
          </div>
        </div>
      </div>

    </section>

    <section v-if="siteContent" class="page-content">
      <div class="container">
        <div class="content-section">
          <h2>{{ siteContent.about.mission.title }}</h2>
          <p>{{ siteContent.about.mission.content }}</p>
        </div>

        <div class="content-section">
          <h2>{{ siteContent.about.vision.title }}</h2>
          <p>{{ siteContent.about.vision.content }}</p>
        </div>

        <div v-if="pageContent" class="values-section">
          <h2>{{ pageContent.section_titles.values }}</h2>
          <div class="row row-cols-1 row-cols-md-2 g-4">
            <div v-for="value in siteContent.about.values" :key="value.title" class="col">
              <div class="value-card h-100">
                <h3>{{ value.title }}</h3>
                <p>{{ value.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="pageContent" class="team-section">
          <h2>{{ pageContent.section_titles.team }}</h2>
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div v-for="member in siteContent.about.team" :key="member.name" class="col">
              <div class="team-card h-100">
                <img :src="member.image" :alt="member.name" />
                <h3>{{ member.name }}</h3>
                <p class="role">{{ member.role }}</p>
                <p>{{ member.bio }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const { getSiteContent } = useSiteContent()
const { getContentByPath } = usePageContent()

const siteContent = ref<any>(null)
const pageContent = ref<any>(null)

onMounted(async () => {
  siteContent.value = await getSiteContent()
  pageContent.value = await getContentByPath('about')
})

useHead(() => ({
  title: pageContent.value?.seo?.title || 'About Us - Eat Is Family',
  meta: [
    { name: 'description', content: pageContent.value?.seo?.meta_description || 'Learn about Eat Is Family, our mission, vision, and the team behind our culinary experiences.' }
  ]
}))
</script>

<style scoped lang="scss">
#aboutHero {
  height: 930px;
  margin: 0 0 14px;
  padding: 22px 60.4px 56px 70px;
  border-radius: 2px;
  background: url(images/vectorBgAbout.svg) no-repeat center 20% #a7f49d !important;

  h1 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 75px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.2;
    letter-spacing: normal;
    text-align: center;
    color: #000b0f;
  }
}

.content-section {
  margin-bottom: 3rem;

  h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #2d3748;
  }

  p {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #4a5568;
  }
}

.values-section,
.team-section {
  margin-top: 4rem;

  h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    color: #2d3748;
  }
}

.value-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

  h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #FF4D6D;
  }
}

.team-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;

  img {
    width: 100%;
    height: 250px;
    object-fit: cover;
  }

  h3 {
    font-size: 1.25rem;
    margin: 1rem 0 0.5rem;
    color: #2d3748;
  }

  .role {
    color: #FF4D6D;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  p:last-child {
    padding: 0 1.5rem 1.5rem;
    color: #4a5568;
  }
}
</style>
