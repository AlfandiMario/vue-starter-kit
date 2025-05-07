<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubItem, SidebarMenuSubButton } from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const openMenus = ref<Record<string, boolean>>({});

const toggleMenu = (title: string) => {
    openMenus.value[title] = !openMenus.value[title];
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Regular menu item without children -->
                <SidebarMenuItem v-if="!item.children?.length">
                    <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                        <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- Collapsible menu item with children -->
                <SidebarMenuItem v-else>
                    <Collapsible v-model:open="openMenus[item.title]">
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton @click="toggleMenu(item.title)" :tooltip="item.title">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronDown class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200"
                                    :class="{ 'rotate-180': openMenus[item.title] }" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="child in item.children" :key="child.title">
                                    <SidebarMenuSubButton as-child :is-active="child.href === page.url">
                                        <Link :href="child.href">
                                        <span>{{ child.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
