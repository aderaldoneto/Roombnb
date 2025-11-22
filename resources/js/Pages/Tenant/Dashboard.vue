<script setup>
import { Head } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'


const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_reservations: 0,
      pending: 0,
      confirmed: 0,
      canceled: 0,
      total_rooms: 0,
    }),
  },
})
</script>

<template>

  <AppHeader />

  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">
    <Head title="Dashboard" />

    <main class="max-w-5xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        
        <!-- Total reservas -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
          <p class="text-sm text-zinc-500">Total de reservas</p>
          <p class="text-3xl font-bold mt-1">{{ stats.total_reservations }}</p>
        </div>

        <!-- Pendentes -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
          <p class="text-sm text-zinc-500">Pendentes</p>
          <p class="text-3xl font-bold mt-1">{{ stats.pending }}</p>
        </div>

        <!-- Confirmadas -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
          <p class="text-sm text-zinc-500">Confirmadas</p>
          <p class="text-3xl font-bold mt-1">{{ stats.confirmed }}</p>
        </div>

        <!-- Canceladas -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
          <p class="text-sm text-zinc-500">Canceladas</p>
          <p class="text-3xl font-bold mt-1">{{ stats.canceled }}</p>
        </div>

        <!-- Total de acomodações -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
          <p class="text-sm text-zinc-500">Minhas acomodações</p>
          <p class="text-3xl font-bold mt-1">{{ stats.total_rooms }}</p>
        </div>

      </div>
    </main>
  </div>
</template>
