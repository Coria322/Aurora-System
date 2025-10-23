<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>
<template>
    <div class="flex aspect-square size-10 items-center justify-center rounded-md">
        <AppLogoIcon class="size-10" />
    </div>


    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight" :style="{
            fontFamily: 'var(--font-title)',
            fontSize: '18px',
        }">
            Aurora Suites & Resorts
        </span>
    </div>
</template>
