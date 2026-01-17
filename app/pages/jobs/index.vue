<script setup lang="ts">
import { LucideSearch, LucideMapPin, LucideFilter, LucideBriefcase, LucideClock, LucideX, LucideChevronDown } from 'lucide-vue-next'
import type { Job } from '~/composables/useJobs'

const route = useRoute()
const { getJobs } = useJobs()

const jobs = ref<Job[]>([])
const isLoading = ref(true)

const searchQuery = ref('')
const selectedJobType = ref('')
const selectedLocation = ref('')
const showJobTypeDropdown = ref(false)
const showLocationDropdown = ref(false)

onMounted(async () => {
  const fetchedJobs = await getJobs()
  if (fetchedJobs) {
    jobs.value = fetchedJobs
  }
  isLoading.value = false

  // Apply URL query parameters
  if (route.query.title) {
    searchQuery.value = route.query.title as string
  }
  if (route.query.location) {
    selectedLocation.value = route.query.location as string
  }
  if (route.query.type) {
    selectedJobType.value = route.query.type as string
  }
})

const uniqueJobTypes = computed(() => {
  const types = jobs.value.map(job => job.job_type).filter(Boolean)
  return [...new Set(types)]
})

const uniqueLocations = computed(() => {
  const locations = jobs.value.map(job => job.location).filter(Boolean)
  return [...new Set(locations)]
})

const filteredJobs = computed(() => {
  return jobs.value.filter(job => {
    const title = typeof job.title === 'string' ? job.title : job.title?.rendered || ''
    const excerpt = typeof job.excerpt === 'string' ? job.excerpt : job.excerpt?.rendered || ''

    const matchesSearch = !searchQuery.value ||
      title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      excerpt.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesType = !selectedJobType.value || job.job_type === selectedJobType.value
    const matchesLocation = !selectedLocation.value || job.location === selectedLocation.value

    return matchesSearch && matchesType && matchesLocation
  })
})

const clearFilters = () => {
  searchQuery.value = ''
  selectedJobType.value = ''
  selectedLocation.value = ''
}

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedJobType.value || selectedLocation.value
})

const closeDropdowns = (e: Event) => {
  const target = e.target as HTMLElement
  if (!target.closest('.dropdown-wrapper')) {
    showJobTypeDropdown.value = false
    showLocationDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})

const getJobTitle = (job: Job) => {
  return typeof job.title === 'string' ? job.title : job.title?.rendered || ''
}

const getJobExcerpt = (job: Job) => {
  return typeof job.excerpt === 'string' ? job.excerpt : job.excerpt?.rendered || ''
}

useHead({
  title: 'Careers - Join Our Team | Eat Is Family',
  meta: [
    { name: 'description', content: 'Explore exciting career opportunities in hospitality, culinary arts, and event management. Join our team and be part of creating memorable experiences.' }
  ]
})
</script>

<template>
  <div class="jobs-archive">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
      </div>
      <div class="container hero-content">
        <span class="hero-tag">Careers</span>
        <h1 class="hero-title">Join Our Team</h1>
        <p class="hero-subtitle">Discover exciting opportunities in hospitality & culinary excellence</p>
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number">{{ jobs.length }}</span>
            <span class="stat-label">Open Positions</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-number">{{ uniqueLocations.length }}</span>
            <span class="stat-label">Locations</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Search & Filters Section -->
    <section class="filters-section">
      <div class="container">
        <div class="filters-card">
          <!-- Search Input -->
          <div class="search-wrapper">
            <LucideSearch class="search-icon" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search jobs by title or keyword..."
              class="search-input"
            />
          </div>

          <!-- Filters Row -->
          <div class="filters-row">
            <!-- Job Type Dropdown -->
            <div class="dropdown-wrapper">
              <button
                class="filter-button"
                :class="{ 'has-value': selectedJobType }"
                @click.stop="showJobTypeDropdown = !showJobTypeDropdown; showLocationDropdown = false"
              >
                <LucideBriefcase class="filter-icon" />
                <span>{{ selectedJobType || 'Job Type' }}</span>
                <LucideChevronDown class="chevron" :class="{ 'rotated': showJobTypeDropdown }" />
              </button>
              <div class="dropdown-menu" :class="{ 'is-visible': showJobTypeDropdown }" @click.stop>
                <button
                  class="dropdown-item"
                  :class="{ 'active': !selectedJobType }"
                  @click="selectedJobType = ''; showJobTypeDropdown = false"
                >
                  All Types
                </button>
                <button
                  v-for="type in uniqueJobTypes"
                  :key="type"
                  class="dropdown-item"
                  :class="{ 'active': selectedJobType === type }"
                  @click="selectedJobType = type; showJobTypeDropdown = false"
                >
                  {{ type }}
                </button>
              </div>
            </div>

            <!-- Location Dropdown -->
            <div class="dropdown-wrapper">
              <button
                class="filter-button"
                :class="{ 'has-value': selectedLocation }"
                @click.stop="showLocationDropdown = !showLocationDropdown; showJobTypeDropdown = false"
              >
                <LucideMapPin class="filter-icon" />
                <span>{{ selectedLocation || 'Location' }}</span>
                <LucideChevronDown class="chevron" :class="{ 'rotated': showLocationDropdown }" />
              </button>
              <div class="dropdown-menu" :class="{ 'is-visible': showLocationDropdown }" @click.stop>
                  <button
                    class="dropdown-item"
                    :class="{ 'active': !selectedLocation }"
                    @click="selectedLocation = ''; showLocationDropdown = false"
                  >
                    All Locations
                  </button>
                  <button
                    v-for="location in uniqueLocations"
                    :key="location"
                    class="dropdown-item"
                    :class="{ 'active': selectedLocation === location }"
                    @click="selectedLocation = location; showLocationDropdown = false"
                  >
                    {{ location }}
                  </button>
                </div>
            </div>

            <!-- Clear Filters -->
            <button
              v-if="hasActiveFilters"
              class="clear-filters-btn"
              @click="clearFilters"
            >
              <LucideX class="clear-icon" />
              Clear Filters
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Jobs Listing Section -->
    <section class="jobs-section">
      <div class="container">
        <!-- Results Count -->
        <div class="results-header">
          <h2 class="results-count">
            <span class="count-number">{{ filteredJobs.length }}</span>
            {{ filteredJobs.length === 1 ? 'Position' : 'Positions' }} Available
          </h2>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading opportunities...</p>
        </div>

        <!-- Jobs Grid -->
        <div v-else-if="filteredJobs.length > 0" class="jobs-grid">
          <article
            v-for="job in filteredJobs"
            :key="job.id"
            class="job-card"
          >
            <div class="job-card-image">
              <img :src="job.featured_media" :alt="getJobTitle(job)" loading="lazy" />
              <span class="job-type-badge" :class="job.job_type.toLowerCase().replace('-', '')">
                {{ job.job_type }}
              </span>
            </div>
            <div class="job-card-content">
              <h3 class="job-title">{{ getJobTitle(job) }}</h3>

              <div class="job-meta">
                <div class="meta-item">
                  <LucideMapPin class="meta-icon" />
                  <span>{{ job.location }}</span>
                </div>
                <div class="meta-item">
                  <LucideClock class="meta-icon" />
                  <span>{{ job.job_type }}</span>
                </div>
              </div>

              <p class="job-excerpt" v-html="getJobExcerpt(job)"></p>

              <div class="job-salary">
                <span class="salary-label">Salary:</span>
                <span class="salary-value">{{ job.salary }}</span>
              </div>

              <div class="job-actions">
                <NuxtLink :to="`/jobs/${job.slug}`" class="btn-view-details">
                  View Details
                </NuxtLink>
                <NuxtLink :to="`/jobs/${job.slug}#apply`" class="btn-apply">
                  Apply Now
                </NuxtLink>
              </div>
            </div>
          </article>
        </div>

        <!-- No Results -->
        <div v-else class="no-results">
          <div class="no-results-icon">
            <LucideBriefcase />
          </div>
          <h3>No positions found</h3>
          <p>Try adjusting your search or filters to find more opportunities.</p>
          <button class="btn-clear" @click="clearFilters">Clear All Filters</button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Don't See the Right Role?</h2>
          <p>We're always looking for talented individuals. Send us your resume and we'll keep you in mind for future opportunities.</p>
          <NuxtLink to="/contact" class="cta-button">
            Get in Touch
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.jobs-archive {
  min-height: 100vh;
  background: var(--brand-gray, #F5F5F5);
}

// Hero Section
.hero-section {
  position: relative;
  padding: 8rem 0 6rem;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--brand-pink) 0%, #e63956 100%);

  &::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url('/images/bgIntro.jpg');
    background-size: cover;
    background-position: center;
    opacity: 0.15;
  }
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  color: white;
}

.hero-tag {
  display: inline-block;
  padding: 0.5rem 1.25rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 1.5rem;
}

.hero-title {
  font-family: var(--font-heading);
  font-size: 3.5rem;
  font-weight: 700;
  margin: 0 0 1rem;
  line-height: 1.1;
}

.hero-subtitle {
  font-size: 1.25rem;
  opacity: 0.9;
  margin: 0 0 2.5rem;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.hero-stats {
  display: inline-flex;
  align-items: center;
  gap: 2rem;
  padding: 1.5rem 2.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 1rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-family: var(--font-heading);
  font-size: 2.5rem;
  font-weight: 700;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.8;
  margin-top: 0.25rem;
}

.stat-divider {
  width: 1px;
  height: 3rem;
  background: rgba(255, 255, 255, 0.3);
}

// Filters Section
.filters-section {
  margin-top: -3rem;
  position: relative;
  z-index: 10;
  padding-bottom: 2rem;
}

.filters-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  border: 2px solid var(--brand-dark);
}

.search-wrapper {
  position: relative;
  margin-bottom: 1rem;
}

.search-icon {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: rgba(0, 0, 0, 0.4);
}

.search-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3.5rem;
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.75rem;
  font-size: 1rem;
  font-family: var(--font-body);
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    border-color: var(--brand-pink);
    box-shadow: 0 0 0 3px rgba(255, 77, 109, 0.1);
  }

  &::placeholder {
    color: rgba(0, 0, 0, 0.4);
  }
}

.filters-row {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
}

.dropdown-wrapper {
  position: relative;
}

.filter-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: var(--brand-gray);
  border: 2px solid transparent;
  border-radius: 0.75rem;
  font-size: 0.9375rem;
  font-family: var(--font-body);
  color: var(--brand-dark);
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: white;
    border-color: var(--brand-dark);
  }

  &.has-value {
    background: var(--brand-lime);
    border-color: var(--brand-dark);
  }
}

.filter-icon {
  width: 1rem;
  height: 1rem;
  opacity: 0.6;
}

.chevron {
  width: 1rem;
  height: 1rem;
  opacity: 0.5;
  transition: transform 0.2s ease;

  &.rotated {
    transform: rotate(180deg);
  }
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  min-width: 200px;
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 0.75rem;
  box-shadow: 4px 4px 0 rgba(0, 0, 0, 1);
  overflow: hidden;
  z-index: 100;

  // Caché par défaut
  display: none;

  // Visible quand la classe is-visible est présente
  &.is-visible {
    display: block;
  }
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 0.875rem 1.25rem;
  text-align: left;
  background: none;
  border: none;
  font-size: 0.9375rem;
  font-family: var(--font-body);
  color: var(--brand-dark);
  cursor: pointer;
  transition: background 0.15s ease;

  &:hover {
    background: var(--brand-gray);
  }

  &.active {
    background: var(--brand-lime);
    font-weight: 600;
  }
}

.clear-filters-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: none;
  border: none;
  color: var(--brand-pink);
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s ease;

  &:hover {
    opacity: 0.7;
  }
}

.clear-icon {
  width: 1rem;
  height: 1rem;
}

// Jobs Section
.jobs-section {
  padding: 2rem 0 4rem;
}

.results-header {
  margin-bottom: 2rem;
}

.results-count {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--brand-dark);
}

.count-number {
  color: var(--brand-pink);
}

// Loading State
.loading-state {
  text-align: center;
  padding: 4rem 2rem;

  p {
    margin-top: 1rem;
    color: rgba(0, 0, 0, 0.5);
  }
}

.loading-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid var(--brand-gray);
  border-top-color: var(--brand-pink);
  border-radius: 50%;
  margin: 0 auto;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

// Jobs Grid
.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 2rem;
}

.job-card {
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
  overflow: hidden;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 1);
  }
}

.job-card-image {
  position: relative;
  height: 180px;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .job-card:hover & img {
    transform: scale(1.05);
  }
}

.job-type-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.375rem 0.875rem;
  background: var(--brand-dark);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 50px;
  text-transform: uppercase;
  letter-spacing: 0.03em;

  &.fulltime {
    background: var(--brand-pink);
  }

  &.parttime {
    background: var(--brand-blue);
    color: var(--brand-dark);
  }
}

.job-card-content {
  padding: 1.5rem;
}

.job-title {
  font-family: var(--font-heading);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 1rem;
  line-height: 1.3;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.6);
}

.meta-icon {
  width: 1rem;
  height: 1rem;
  color: var(--brand-pink);
}

.job-excerpt {
  font-size: 0.9375rem;
  color: rgba(0, 0, 0, 0.7);
  line-height: 1.6;
  margin: 0 0 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;

  :deep(p) {
    margin: 0;
  }
}

.job-salary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: var(--brand-gray);
  border-radius: 0.5rem;
  margin-bottom: 1.25rem;
}

.salary-label {
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.5);
}

.salary-value {
  font-weight: 700;
  color: var(--brand-dark);
}

.job-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-view-details,
.btn-apply {
  flex: 1;
  padding: 0.875rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-view-details {
  background: white;
  color: var(--brand-dark);
  border: 2px solid var(--brand-dark);

  &:hover {
    background: var(--brand-gray);
  }
}

.btn-apply {
  background: var(--brand-pink);
  color: white;
  border: 2px solid var(--brand-dark);

  &:hover {
    background: darken(#FF4D6D, 8%);
    box-shadow: 2px 2px 0 rgba(0, 0, 0, 1);
    transform: translate(-2px, -2px);
  }
}

// No Results
.no-results {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
}

.no-results-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1.5rem;
  background: var(--brand-gray);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;

  svg {
    width: 32px;
    height: 32px;
    color: rgba(0, 0, 0, 0.3);
  }
}

.no-results h3 {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  margin: 0 0 0.5rem;
}

.no-results p {
  color: rgba(0, 0, 0, 0.6);
  margin: 0 0 1.5rem;
}

.btn-clear {
  padding: 0.875rem 1.5rem;
  background: var(--brand-lime);
  color: var(--brand-dark);
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    box-shadow: 2px 2px 0 rgba(0, 0, 0, 1);
    transform: translate(-2px, -2px);
  }
}

// CTA Section
.cta-section {
  background: var(--brand-dark);
  padding: 4rem 0;
}

.cta-content {
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
}

.cta-content h2 {
  font-family: var(--font-heading);
  font-size: 2rem;
  color: white;
  margin: 0 0 1rem;
}

.cta-content p {
  color: rgba(255, 255, 255, 0.7);
  margin: 0 0 2rem;
  line-height: 1.6;
}

.cta-button {
  display: inline-block;
  padding: 1rem 2.5rem;
  background: var(--brand-lime);
  color: var(--brand-dark);
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover {
    box-shadow: 4px 4px 0 rgba(255, 255, 255, 0.3);
    transform: translate(-2px, -2px);
  }
}

// Dropdown Animation
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

// Responsive
@media (max-width: 768px) {
  .hero-section {
    padding: 6rem 0 5rem;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }

  .hero-stats {
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem 2rem;
  }

  .stat-divider {
    width: 3rem;
    height: 1px;
  }

  .filters-card {
    padding: 1rem;
  }

  .filters-row {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-button {
    width: 100%;
    justify-content: space-between;
  }

  .dropdown-menu {
    width: 100%;
  }

  .jobs-grid {
    grid-template-columns: 1fr;
  }

  .job-actions {
    flex-direction: column;
  }
}
</style>
