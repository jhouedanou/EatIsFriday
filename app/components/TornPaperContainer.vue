<template>
  <div class="torn-paper-container" :class="variant">
    <!-- Bordure supérieure déchirée -->
    <svg class="torn-edge torn-edge-top" viewBox="0 0 1200 40" preserveAspectRatio="none">
      <path 
        d="M 0,20 
           C 15,18 25,15 40,17 
           C 55,19 65,22 80,20 
           C 95,18 105,14 120,16 
           C 135,18 150,22 165,20 
           C 180,18 195,15 210,17 
           C 225,19 240,23 255,21 
           C 270,19 285,16 300,18 
           C 315,20 330,24 345,22 
           C 360,20 375,17 390,19 
           C 405,21 420,25 435,23 
           C 450,21 465,18 480,20 
           C 495,22 510,26 525,24 
           C 540,22 555,19 570,21 
           C 585,23 600,27 615,25 
           C 630,23 645,20 660,22 
           C 675,24 690,28 705,26 
           C 720,24 735,21 750,23 
           C 765,25 780,29 795,27 
           C 810,25 825,22 840,24 
           C 855,26 870,30 885,28 
           C 900,26 915,23 930,25 
           C 945,27 960,31 975,29 
           C 990,27 1005,24 1020,26 
           C 1035,28 1050,32 1065,30 
           C 1080,28 1095,25 1110,27 
           C 1125,29 1140,33 1155,31 
           C 1170,29 1185,26 1200,28 
           L 1200,0 
           L 0,0 Z"
        fill="currentColor"
      />
    </svg>
    
    <!-- Contenu -->
    <div class="torn-paper-content">
      <slot />
    </div>
    
    <!-- Bordure inférieure déchirée -->
    <svg class="torn-edge torn-edge-bottom" viewBox="0 0 1200 40" preserveAspectRatio="none">
      <path 
        d="M 0,12 
           C 15,14 30,18 45,16 
           C 60,14 75,10 90,12 
           C 105,14 120,18 135,16 
           C 150,14 165,11 180,13 
           C 195,15 210,19 225,17 
           C 240,15 255,12 270,14 
           C 285,16 300,20 315,18 
           C 330,16 345,13 360,15 
           C 375,17 390,21 405,19 
           C 420,17 435,14 450,16 
           C 465,18 480,22 495,20 
           C 510,18 525,15 540,17 
           C 555,19 570,23 585,21 
           C 600,19 615,16 630,18 
           C 645,20 660,24 675,22 
           C 690,20 705,17 720,19 
           C 735,21 750,25 765,23 
           C 780,21 795,18 810,20 
           C 825,22 840,26 855,24 
           C 870,22 885,19 900,21 
           C 915,23 930,27 945,25 
           C 960,23 975,20 990,22 
           C 1005,24 1020,28 1035,26 
           C 1050,24 1065,21 1080,23 
           C 1095,25 1110,29 1125,27 
           C 1140,25 1155,22 1170,24 
           C 1185,26 1200,30 1200,30 
           L 1200,40 
           L 0,40 Z"
        fill="currentColor"
      />
    </svg>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'white' | 'blue' | 'cream'
}>()
</script>

<style scoped lang="scss">
.torn-paper-container {
  position: relative;
  width: 100%;
  padding: 2rem 0;
  
  &.white {
    background: #ffffff;
    
    .torn-edge {
      color: #ffffff;
    }
  }
  
  &.blue {
    background: #a8d5e2;
    
    .torn-edge {
      color: #a8d5e2;
    }
  }
  
  &.cream {
    background: #fef5e7;
    
    .torn-edge {
      color: #fef5e7;
    }
  }
}

.torn-edge {
  position: absolute;
  left: 0;
  width: 100%;
  height: 40px;
  pointer-events: none;
  
  &.torn-edge-top {
    top: -1px;
    transform: scaleY(-1);
  }
  
  &.torn-edge-bottom {
    bottom: -1px;
  }
}

.torn-paper-content {
  position: relative;
  z-index: 1;
}
</style>
