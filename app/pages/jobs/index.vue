<script setup lang="ts">
import { LucideSearch, LucideMapPin, LucideFilter, LucideBriefcase, LucideClock, LucideX, LucideChevronDown } from 'lucide-vue-next'
import type { Job } from '~/composables/useJobs'

interface Venue {
  id: string
  name: string
  location: string
  image?: string
  image2?: string
}

interface LocationsData {
  map_venues: Venue[]
}

const route = useRoute()
const { getJobs } = useJobs()

const jobs = ref<Job[]>([])
const isLoading = ref(true)

const searchQuery = ref('')
const selectedJobType = ref('')
const selectedLocation = ref('')
const showJobTypeDropdown = ref(false)
const showLocationDropdown = ref(false)

// Load venues data for dynamic header
const { data: locationsData } = await useFetch<LocationsData>('/api/locations.json')

// Find the venue matching the selected location
const activeVenue = computed(() => {
  if (!selectedLocation.value || !locationsData.value?.map_venues) return null
  return locationsData.value.map_venues.find(venue =>
    venue.location === selectedLocation.value ||
    venue.name.toLowerCase().includes(selectedLocation.value.toLowerCase())
  ) || null
})

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

// Pagination
const ITEMS_PER_PAGE = 4
const currentPage = ref(1)

const totalPages = computed(() => {
  return Math.ceil(filteredJobs.value.length / ITEMS_PER_PAGE)
})

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return filteredJobs.value.slice(start, end)
})

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

// Reset to page 1 when filters change
watch([searchQuery, selectedJobType, selectedLocation], () => {
  currentPage.value = 1
})

const clearFilters = () => {
  searchQuery.value = ''
  selectedJobType.value = ''
  selectedLocation.value = ''
  currentPage.value = 1
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
    <section class="hero-section" :class="{ 'has-venue': activeVenue }">
      <div
        class="hero-background"
        :style="activeVenue ? { backgroundImage: `url('${activeVenue.image}')` } : {}"
      >
        <div class="hero-overlay"></div>
      </div>
      <div class="container hero-content">
        <span class="hero-tag">{{ activeVenue ? 'Now Hiring' : 'Careers' }}</span>
        <h1 class="hero-title">
          <template v-if="activeVenue">
            Join Our Team At {{ activeVenue.name }}
          </template>
          <template v-else>
            Join Our Team
          </template>
        </h1>
        <p class="hero-subtitle">
          <template v-if="activeVenue">
            <LucideMapPin class="subtitle-icon" /> {{ activeVenue.location }}
            <span class="subtitle-divider">•</span>
            {{ filteredJobs.length }} Open Position{{ filteredJobs.length !== 1 ? 's' : '' }}
          </template>
          <template v-else>
            Discover exciting opportunities in hospitality & culinary excellence
          </template>
        </p>
        <div v-if="!activeVenue" class="hero-stats">
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
          <div class="filters-row">
            <!-- Search Input -->
            <div class="search-field">
              <LucideSearch class="field-icon" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search Job title and category here"
                class="filter-input"
              />
            </div>

            <!-- Job Type Dropdown -->
            <div class="dropdown-wrapper">
              <button
                class="filter-select"
                @click.stop="showJobTypeDropdown = !showJobTypeDropdown"
              >
                <span>{{ selectedJobType || 'All job types' }}</span>
                <LucideChevronDown class="chevron" :class="{ 'rotated': showJobTypeDropdown }" />
              </button>
              <div class="dropdown-menu" :class="{ 'is-visible': showJobTypeDropdown }" @click.stop>
                <button
                  class="dropdown-item"
                  :class="{ 'active': !selectedJobType }"
                  @click="selectedJobType = ''; showJobTypeDropdown = false"
                >
                  All job types
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
          </div>
        </div>
      </div>
    </section>

    <!-- Jobs Listing Section -->
    <section class="jobs-section">
      <div class="container">
        <!-- Loading State -->
        <div v-if="isLoading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading opportunities...</p>
        </div>

        <!-- Jobs Grid -->
        <div v-else-if="filteredJobs.length > 0">
          <div class="jobs-grid">
            <article
              v-for="job in paginatedJobs"
              :key="job.id"
              class="job-card"
            >
              <!-- Card Header -->
              <div class="job-card-header">
                <span class="job-posted">Posted {{ Math.floor(Math.random() * 5) + 1 }} hours ago</span>
              </div>

              <!-- Job Title -->
              <h3 class="job-title">{{ getJobTitle(job) }}</h3>

              <!-- Tags -->
              <div class="job-tags">
                <span class="tag tag-department">Department · Culinary</span>
                <span class="tag tag-type" :class="job.job_type.toLowerCase().replace('-', '').replace(' ', '')">
                  {{ job.job_type }}
                </span>
                <span class="tag tag-salary">{{ job.salary }}</span>
              </div>

              <!-- Excerpt -->
              <p class="job-excerpt" v-html="getJobExcerpt(job)"></p>

              <!-- Actions -->
              <div class="job-actions">
                <NuxtLink :to="`/jobs/${job.slug}#apply`" class="btn-apply">
                  Apply Now
                </NuxtLink>
                <NuxtLink :to="`/jobs/${job.slug}`" class="btn-view-details">
                  View details
                </NuxtLink>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="pagination">
            <button
              class="pagination-btn"
              :disabled="currentPage === 1"
              @click="goToPage(currentPage - 1)"
            >
              &larr;
            </button>
            <button
              v-for="page in totalPages"
              :key="page"
              class="pagination-btn"
              :class="{ 'active': currentPage === page }"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
            <button
              class="pagination-btn"
              :disabled="currentPage === totalPages"
              @click="goToPage(currentPage + 1)"
            >
              &rarr;
            </button>
          </div>
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

  // Dynamic venue header
  &.has-venue {
    padding: 10rem 0 6rem;

    .hero-background {
      background: none;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;

      &::before {
        display: none;
      }
    }

    .hero-overlay {
      background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.7) 100%);
    }

    .hero-title {
      font-size: 2.75rem;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
  }
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.subtitle-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.subtitle-divider {
  margin: 0 0.25rem;
  opacity: 0.6;
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
  padding: 1rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  border: 2px solid var(--brand-dark);
}

.filters-row {
  display: flex;
  gap: 1rem;
  align-items: stretch;
}

.search-field {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.field-icon {
  position: absolute;
  left: 1rem;
  width: 1.25rem;
  height: 1.25rem;
  color: rgba(0, 0, 0, 0.4);
  pointer-events: none;
}

.filter-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 3rem;
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  font-size: 0.9375rem;
  font-family: var(--font-body);
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    border-color: var(--brand-pink);
  }

  &::placeholder {
    color: rgba(0, 0, 0, 0.4);
  }
}

.dropdown-wrapper {
  position: relative;
  min-width: 180px;
}

.filter-select {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  background: white;
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  font-size: 0.9375rem;
  font-family: var(--font-body);
  color: var(--brand-dark);
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    border-color: var(--brand-dark);
  }
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
  right: 0;
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  box-shadow: 4px 4px 0 rgba(0, 0, 0, 1);
  overflow: hidden;
  z-index: 100;
  display: none;

  &.is-visible {
    display: block;
  }
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
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

// Jobs Section
.jobs-section {
  padding: 2rem 0 4rem;
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

// Jobs Grid - 2 columns
.jobs-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.job-card {
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 1rem;
  padding: 1.5rem;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 1);
  }
}

.job-card-header {
  margin-bottom: 0.75rem;
}

.job-posted {
  font-size: 0.75rem;
  color: rgba(0, 0, 0, 0.5);
}

.job-title {
  font-family: var(--font-heading);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--brand-dark);
  margin: 0 0 1rem;
  line-height: 1.3;
}

// Tags
.job-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.tag {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 4px;
}

.tag-department {
  background: var(--brand-blue, #A0C4FF);
  color: var(--brand-dark);
}

.tag-type {
  background: var(--brand-lime, #C8F560);
  color: var(--brand-dark);

  &.fulltime {
    background: var(--brand-lime);
  }

  &.parttime {
    background: var(--brand-blue);
  }
}

.tag-salary {
  background: var(--brand-yellow, #FFDD00);
  color: var(--brand-dark);
}

.job-excerpt {
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.7);
  line-height: 1.6;
  margin: 0 0 1.25rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;

  :deep(p) {
    margin: 0;
  }
}

.job-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-apply {
  padding: 0.75rem 1.5rem;
  background: var(--brand-pink);
  color: white;
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover {
    background: #e63956;
    box-shadow: 2px 2px 0 rgba(0, 0, 0, 1);
    transform: translate(-2px, -2px);
  }
}

.btn-view-details {
  padding: 0.75rem 1.5rem;
  background: white;
  color: var(--brand-dark);
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;

  &:hover {
    background: var(--brand-gray);
  }
}

// Pagination
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2.5rem;
}

.pagination-btn {
  min-width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--brand-dark);
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover:not(:disabled) {
    background: var(--brand-gray);
  }

  &.active {
    background: var(--brand-pink);
    color: white;
    border-color: var(--brand-dark);
  }

  &:disabled {
    opacity: 0.4;
    cursor: not-allowed;
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

    &.has-venue {
      padding: 7rem 0 4rem;

      .hero-title {
        font-size: 1.75rem;
      }
    }
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1rem;
    flex-wrap: wrap;
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
    padding: 0.75rem;
  }

  .filters-row {
    flex-direction: column;
    gap: 0.75rem;
  }

  .search-field {
    width: 100%;
  }

  .dropdown-wrapper {
    width: 100%;
    min-width: auto;
  }

  .filter-select {
    width: 100%;
  }

  .jobs-grid {
    grid-template-columns: 1fr;
  }

  .job-card {
    padding: 1.25rem;
  }

  .job-actions {
    flex-direction: column;
  }

  .btn-apply,
  .btn-view-details {
    width: 100%;
    text-align: center;
  }

  .pagination {
    flex-wrap: wrap;
  }
}
</style>
