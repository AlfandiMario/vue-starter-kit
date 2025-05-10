<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Smart BAMS',
        href: '/dashboard',
    },
    {
        title: 'Electrical',
        href: '/electrical',
    },
];

// Feature card data
const features = [
    {
        title: 'Monitor',
        description: 'Real-time monitoring of electrical systems and power consumption across all connected devices.',
        image: '/images/electrical/monitor-preview.png', // You'll need to create this image
        href: '/electrical/monitor',
    },
    {
        title: 'Control',
        description: 'Control all electrical devices remotely, organized by room for easy management.',
        image: '/images/electrical/control-preview.png', // You'll need to create this image
        href: '/electrical/control',
    },
    {
        title: 'Statistics',
        description: 'Detailed analytics and statistics on power usage, trends, and optimization recommendations.',
        image: '/images/electrical/statistics-preview.png', // You'll need to create this image
        href: '/electrical/stats',
    },
    {
        title: 'IKE Standard',
        description: 'Index IKE standard for monitoring the system with national standards and regulations.',
        image: '/images/electrical/ike-preview.png', // You'll need to create this image
        href: '/electrical/ike',
    },
];
</script>

<template>
    <Head title="Electrical System" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-foreground">Electrical System Management</h1>
                <p class="text-muted-foreground mt-2">View, monitor, and control all electrical devices in your system</p>
            </div>

            <!-- Feature cards container -->
            <div class="grid gap-6 md:grid-cols-4 max-w-5xl mx-auto">
                <!-- Feature cards -->
                <Link 
                    v-for="feature in features" 
                    :key="feature.title" 
                    :href="feature.href"
                    class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all hover:shadow-md dark:bg-card/50"
                >
                    <!-- Card image -->
                    <div class="relative h-40 w-full overflow-hidden bg-muted">
                        <!-- Fallback pattern if image is not available -->
                        <div class="absolute inset-0 bg-pattern opacity-40"></div>
                        
                        <!-- Actual image - uncomment when you have real images -->
                        <!-- <img 
                            :src="feature.image" 
                            :alt="`${feature.title} Preview`" 
                            class="h-full w-full object-cover" 
                        /> -->

                        <!-- Placeholder for demo -->
                        <div class="flex h-full w-full items-center justify-center bg-accent/10">
                            <div class="text-lg font-medium text-accent-foreground">{{ feature.title }} Preview</div>
                        </div>
                    </div>

                    <!-- Card content -->
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="text-xl font-semibold text-foreground group-hover:text-primary">{{ feature.title }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">{{ feature.description }}</p>
                        <div class="mt-4 flex items-center text-sm text-primary">
                            <span>Check this out</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-1 h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.bg-pattern {
    background-image: radial-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>