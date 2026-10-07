<script setup lang="ts">
import { computed } from 'vue'
import { useData, withBase } from 'vitepress'

const props = defineProps<{ name: string; height?: number; label?: string }>()
const { isDark } = useData()
const url = computed(() => `${withBase(`/preview/${props.name}.html`)}?theme=${isDark.value ? 'dark' : 'light'}`)
</script>

<template>
  <figure class="component-preview">
    <iframe :src="url" :title="label || `Pratinjau ${name}`" :style="{ height: `${height || 340}px` }"
      loading="lazy" sandbox="allow-scripts allow-same-origin" />
    <figcaption>Pratinjau Blade · {{ label || name }} · gunakan Tab untuk mencoba kontrol.</figcaption>
  </figure>
</template>
