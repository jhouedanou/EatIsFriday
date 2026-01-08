<script setup lang="ts">
// Default layout
</script>

<template>
  <div class="flex flex-col min-h-screen bg-brand-gray overflow-x-hidden">
    <LayoutHeader />
    
    <main class="flex-grow pt-20">
      <slot />
    </main>

    <LayoutFooter />
  </div>
</template>
