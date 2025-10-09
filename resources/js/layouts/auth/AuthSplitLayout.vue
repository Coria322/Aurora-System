<script setup lang="ts">
// import AppLogoIcon from '@/components/AppLogoIcon.vue'; // Removed unused import
import { Link } from '@inertiajs/vue3';

// const page = usePage(); // Removed unused variable
// const name = page.props.name; // Removed unused variable
// const quote = page.props.quote; // Removed unused variable

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh grid-cols-2 flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:px-0">
        <div class="relative flex h-full flex-col p-10 text-white">
            <!-- Panel izquierdo color plano -->
            <div class="absolute inset-0 bg-[#34699A]" />
            <Link :href="route('home')" class="relative z-20 flex items-center">
                <img src="/auroralogo.png" alt="Aurora" class="mr-4 size-[72px] rounded-full bg-white/10 p-1" />
                <div class="leading-tight">
                    <span class="block text-[25px] font-[700]" style="font-family: 'EB Garamond', serif;">AURORA </span>
                    <span class="block -mt-1 text-[25px] font-[400] text-white/95" style="font-family: 'EB Garamond', serif;">Suites &amp; Resorts</span>
                </div>
            </Link>
            <div class="relative z-20 mt-auto">
                <p class="text-[20px] md:text-[28px] italic text-white" style="font-family: 'Rubik', sans-serif;">Un hotel para ti. Donde cada amanecer es un lujo</p>
            </div>
        </div>
        <div class="lg:p-8 text-white/90 h-full flex items-center justify-center" style="background-color: #113F67;">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[452px]" style="font-family: 'Rubik', sans-serif;">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-3xl font-semibold tracking-tight text-white" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-neutral-300" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
