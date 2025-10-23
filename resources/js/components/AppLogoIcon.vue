<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
    alt?: string;
}

defineProps<Props>();
</script>

<template>
  <img
    :src="'/images/auroralogo.png'"
    :class="className"
    :alt="alt || 'Aurora Logo'"
    v-bind="$attrs"
    style="background: transparent; filter: none;"
  />
</template>

