<script setup lang="ts">
import { ref, onMounted } from 'vue'

const { getContentByPath } = usePageContent()
const content = ref<any>(null)

// Sample blog data (in production, this would come from an API)
const featuredPosts = ref([
  {
    id: 1,
    slug: 'future-of-stadium-catering',
    title: 'The Future Of Stadium Catering: 5 Trends Reshaping The Industry',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?q=80&w=800&auto=format&fit=crop',
    date: '3 hours ago'
  },
  {
    id: 2,
    slug: 'sustainability-journey',
    title: 'Our Sustainability Journey: From 5 Percent To 87 Percent Waste Diversion',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1567521464027-f127ff144326?q=80&w=800&auto=format&fit=crop',
    date: '1 day ago'
  }
])

const allPosts = ref([
  {
    id: 3,
    slug: 'feeding-250000-fans',
    title: 'Behind The Scenes: Feeding 250,000 Fans At The 24 Hours Of Le Mans',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=600&auto=format&fit=crop',
    date: '2 days ago'
  },
  {
    id: 4,
    slug: 'stadium-catering-trends-2',
    title: 'The Future Of Stadium Catering: 5 Trends Reshaping The Industry',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=600&auto=format&fit=crop',
    date: '3 days ago'
  },
  {
    id: 5,
    slug: 'stadium-catering-trends-3',
    title: 'The Future Of Stadium Catering: 5 Trends Reshaping The Industry',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=600&auto=format&fit=crop',
    date: '4 days ago'
  },
  {
    id: 6,
    slug: 'stadium-catering-trends-4',
    title: 'The Future Of Stadium Catering: 5 Trends Reshaping The Industry',
    excerpt: 'The stadium food experience has evolved dramatically over the past decade. Gone are the days when a basic hot dog and warm beer were the only options. Today\'s fans expect quality, variety, and convenience — and technology is making it all possible.',
    image: 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?q=80&w=600&auto=format&fit=crop',
    date: '5 days ago'
  }
])

onMounted(async () => {
  content.value = await getContentByPath('blog.index')
})
</script>

<template>
  <div class="blog-page">
    <!-- Hero Section -->
    <section class="blog-hero">
      <div class="hero-decoration pink-blob"></div>
      <div class="container">
        <h1 class="hero-title">
          <span class="title-white">Insightful Stories</span>
          <span class="title-highlight">From The</span>
          <span class="title-black">Kitchen</span>
        </h1>
      </div>
    </section>

    <!-- Most Recent Insights -->
    <section class="recent-insights">
      <div class="container">
        <h2 class="section-title">Most Recent Insights</h2>

        <!-- Featured Post 1 - Image Left -->
        <article class="featured-post">
          <div class="post-image">
            <NuxtLink :to="`/blog/${featuredPosts[0].slug}`">
              <img :src="featuredPosts[0].image" :alt="featuredPosts[0].title" />
            </NuxtLink>
          </div>
          <div class="post-content">
            <h3 class="post-title">
              <NuxtLink :to="`/blog/${featuredPosts[0].slug}`">
                {{ featuredPosts[0].title }}
              </NuxtLink>
            </h3>
            <p class="post-excerpt">{{ featuredPosts[0].excerpt }}</p>
            <NuxtLink :to="`/blog/${featuredPosts[0].slug}`" class="read-more-btn">
              Read more
            </NuxtLink>
          </div>
        </article>

        <!-- Featured Post 2 - Image Right -->
        <article class="featured-post reverse">
          <div class="post-image">
            <NuxtLink :to="`/blog/${featuredPosts[1].slug}`">
              <img :src="featuredPosts[1].image" :alt="featuredPosts[1].title" />
            </NuxtLink>
          </div>
          <div class="post-content">
            <h3 class="post-title">
              <NuxtLink :to="`/blog/${featuredPosts[1].slug}`">
                {{ featuredPosts[1].title }}
              </NuxtLink>
            </h3>
            <p class="post-excerpt">{{ featuredPosts[1].excerpt }}</p>
            <NuxtLink :to="`/blog/${featuredPosts[1].slug}`" class="read-more-btn">
              Read more
            </NuxtLink>
          </div>
        </article>
      </div>
    </section>

    <!-- Explore All Insights -->
    <section class="all-insights">
      <div class="container">
        <h2 class="section-title">Explore All Insight</h2>

        <div class="posts-grid">
          <article v-for="post in allPosts" :key="post.id" class="post-card">
            <div class="card-image">
              <NuxtLink :to="`/blog/${post.slug}`">
                <img :src="post.image" :alt="post.title" />
              </NuxtLink>
            </div>
            <div class="card-content">
              <h3 class="card-title">
                <NuxtLink :to="`/blog/${post.slug}`">
                  {{ post.title }}
                </NuxtLink>
              </h3>
              <p class="card-excerpt">{{ post.excerpt }}</p>
              <NuxtLink :to="`/blog/${post.slug}`" class="read-more-btn">
                Read more
              </NuxtLink>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.blog-page {
  min-height: 100vh;
  background: #fff;
}

// Hero Section
.blog-hero {
  position: relative;
  background: #4ECDC4;
  padding: 4rem 0;
  overflow: hidden;
}

.hero-decoration.pink-blob {
  position: absolute;
  top: 0;
  left: 0;
  width: 300px;
  height: 100%;
  background: #FF6B9D;
  border-radius: 0 0 150px 0;
}

.hero-title {
  position: relative;
  z-index: 1;
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 700;
  line-height: 1.15;
  margin: 0;
  padding-left: 2rem;
}

.title-white {
  display: block;
  color: #fff;
}

.title-highlight {
  display: block;
  color: #1a1a1a;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    bottom: 0.1em;
    left: 0;
    width: 100%;
    height: 0.3em;
    background: #FFDD00;
    z-index: -1;
  }
}

.title-black {
  display: block;
  color: #1a1a1a;
}

// Section Title
.section-title {
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 2.5rem;
}

// Recent Insights Section
.recent-insights {
  padding: 4rem 0;
  background: #fff;
}

.featured-post {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
  margin-bottom: 3rem;
  padding-bottom: 3rem;
  border-bottom: 1px solid #eee;

  &:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }

  &.reverse {
    .post-image {
      order: 2;
    }
    .post-content {
      order: 1;
    }
  }
}

.post-image {
  border-radius: 12px;
  overflow: hidden;

  img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  &:hover img {
    transform: scale(1.03);
  }
}

.post-content {
  padding: 1rem 0;
}

.post-title {
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1.3;
  margin: 0 0 1rem;

  a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.2s ease;

    &:hover {
      color: #FF4D6D;
    }
  }
}

.post-excerpt {
  font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  font-size: 0.9375rem;
  color: #666;
  line-height: 1.7;
  margin: 0 0 1.5rem;
}

.read-more-btn {
  display: inline-block;
  padding: 0.75rem 1.75rem;
  background: #fff;
  color: #1a1a1a;
  font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  border: 2px solid #1a1a1a;
  border-radius: 50px;
  transition: all 0.2s ease;

  &:hover {
    background: #1a1a1a;
    color: #fff;
  }
}

// All Insights Section
.all-insights {
  padding: 4rem 0;
  background: #fff;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2.5rem;
}

.post-card {
  display: flex;
  flex-direction: column;
}

.card-image {
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1.25rem;

  img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  &:hover img {
    transform: scale(1.03);
  }
}

.card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-title {
  font-family: var(--font-heading, 'Recoleta', serif);
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1.3;
  margin: 0 0 0.75rem;

  a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.2s ease;

    &:hover {
      color: #FF4D6D;
    }
  }
}

.card-excerpt {
  font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  font-size: 0.875rem;
  color: #666;
  line-height: 1.6;
  margin: 0 0 1.25rem;
  flex: 1;

  // Clamp to 3 lines
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

// Responsive
@media (max-width: 992px) {
  .featured-post {
    grid-template-columns: 1fr;
    gap: 1.5rem;

    &.reverse {
      .post-image {
        order: 1;
      }
      .post-content {
        order: 2;
      }
    }
  }

  .post-image img {
    height: 250px;
  }
}

@media (max-width: 768px) {
  .blog-hero {
    padding: 3rem 0;
  }

  .hero-decoration.pink-blob {
    width: 150px;
  }

  .hero-title {
    font-size: 2rem;
    padding-left: 1rem;
  }

  .posts-grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .hero-decoration.pink-blob {
    width: 100px;
  }

  .post-image img,
  .card-image img {
    height: 200px;
  }
}
</style>
