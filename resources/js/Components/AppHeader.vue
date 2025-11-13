<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const appName = usePage().props.app.name

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
})

</script>

<template> 
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:py-4">
    <!-- logo -->
    <div class="flex items-center gap-3">
        <Link :href="route('welcome')" class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
            <!-- estetoscópio -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 3a1 1 0 0 0-1 1v4a5 5 0 1 0 10 0V4a1 1 0 1 0-2 0v4a3 3 0 1 1-6 0V4a1 1 0 0 0-1-1z"/><path d="M19 12a3 3 0 0 0-2.83 4H15a3 3 0 0 1-3-3V9h-2v4a5 5 0 0 0 5 5h1.17A3 3 0 1 0 19 12z"/>
            </svg>
        </span>
        <span class="text-lg font-semibold tracking-tight">{{ appName }}</span>
        </Link>
    </div>

    <!-- Auth links -->
    <nav class="flex items-center gap-2">
        <Link
            v-if="$page.props.auth?.user"
            :href="route('dashboard')"
            class="rounded-full px-4 py-2 text-sm font-medium ring-1 ring-zinc-300 transition hover:bg-zinc-50 dark:ring-zinc-700 dark:hover:bg-zinc-900"
            >
            Dashboard
        </Link>
        <Link v-if="!$page.props.auth?.user"
            :href="route('register')"
            class="rounded-full px-4 py-2 text-sm font-medium ring-1 ring-zinc-300 transition hover:bg-zinc-50 dark:ring-zinc-700 dark:hover:bg-zinc-900"
        >
            Criar conta
        </Link>
        <Link v-if="!$page.props.auth?.user"
            :href="route('login')"
            class="rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >
            Entrar
        </Link>
    </nav>
    </div>
</template>
