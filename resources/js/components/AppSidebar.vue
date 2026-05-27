<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { dashboard } from '@/routes';
import clients from '@/routes/clients';
import websiteTypes from '@/routes/website-types';
import businessProcesses from '@/routes/business-processes';
import processStatuses from '@/routes/process-statuses';
import projects from '@/routes/projects';
import websites from '@/routes/websites';
import keywords from '@/routes/keywords';
import tracks from '@/routes/tracks';
import tasks from '@/routes/tasks';
import tags from '@/routes/tags';
import plannings from '@/routes/plannings';
import reports from '@/routes/reports';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },

    {
        title: 'Клиенты',
        href: clients.index().url,
        icon: Users,
    },

    {
        title: 'Типы сайтов',
        href: websiteTypes.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Бизнес-процессы',
        href: businessProcesses.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Проекты',
        href: projects.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Статусы процессов',
        href: processStatuses.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Сайты',
        href: websites.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Ключевые слова',
        href: keywords.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Треки',
        href: tracks.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Задачи',
        href: tasks.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Теги',
        href: tags.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Планирование',
        href: plannings.index(),
        icon: LayoutGrid,
    },

    {
        title: 'Отчёты',
        href: reports.index(),
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
