<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { useAppearance } from '@/composables/useAppearance';
import { Sun, Moon } from 'lucide-vue-next';

withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(),{
    breadcrumbs:()=>[]
});

const { appearance, updateAppearance } = useAppearance();

const toggleAppearance = () => {
    // Cycle between light and dark (skip system for simplicity in the toggle)
    const newAppearance = appearance.value === 'light' ? 'dark' : 'light';
    updateAppearance(newAppearance);
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        
        <!-- Theme toggle button -->
        <div class="ml-auto">
            <button 
                @click="toggleAppearance"
                class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-sidebar-accent focus:outline-none focus:ring-2 focus:ring-sidebar-ring"
                :title="appearance === 'light' ? 'Switch to dark theme' : 'Switch to light theme'"
            >
                <Sun v-if="appearance === 'light'" class="h-4 w-4 text-sidebar-foreground" />
                <Moon v-else class="h-4 w-4 text-sidebar-foreground" />
            </button>
        </div>
    </header>
</template>
