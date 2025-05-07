<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { NavItemParent, type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, UserCog, ShieldPlus, MonitorSpeaker, Zap } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth);

// Function to check if user has a specific role
const hasRole = (role: string) => {
    if (!auth.value.user || !auth.value.user.roles) return false;
    return auth.value.user.roles.some((userRole: any) => userRole.name === role);
};

// Check if user has permission to see admin footer
const canSeeAdminFooter = computed(() => {
    return hasRole('super-admin');
});

const mainNavItems: NavItemParent[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Electricity',
        icon: Zap,
        children: [
            {
                title: 'Monitoring',
                href: '/electricity/monitoring',
            },
            {
                title: 'Control',
                href: '/electricity/control',
            },
            {
                title: 'Statistics',
                href: '/electricity/statistics',
            },
        ]
    },
    {
        title: 'Devices',
        href: '/devices',
        icon: MonitorSpeaker,
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users',
        icon: UserCog,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: ShieldPlus,
    }
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter v-if="canSeeAdminFooter" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
