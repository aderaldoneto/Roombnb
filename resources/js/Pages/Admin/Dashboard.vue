<script setup>
import AppHeader from '@/Components/Admin/AppHeader.vue';
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_users: 0,
      total_admins: 0,
      total_hosts: 0,
      total_tenants: 0,
      total_rooms: 0,
      total_reservations: 0,
      reservations_status: {
        pending: 0,
        confirmed: 0,
        canceled: 0,
      },
    }),
  },
})

const { stats } = props
</script>

<template>

  <AppHeader />

  <div class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-50">
    <Head title="Admin Dashboard" />

    <header class="border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/80">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <h1 class="text-lg font-semibold tracking-tight">
          Painel Administrativo
        </h1>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 space-y-6">
      <!-- Resumo geral -->
      <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <p class="text-xs uppercase tracking-wide text-zinc-500">Usuários</p>
          <p class="mt-1 text-2xl font-semibold">
            {{ stats.total_users }}
          </p>
          <p class="mt-1 text-xs text-zinc-500">
            {{ stats.total_admins }} admin · {{ stats.total_hosts }} host · {{ stats.total_tenants }} tenant
          </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <p class="text-xs uppercase tracking-wide text-zinc-500">Acomodações</p>
          <p class="mt-1 text-2xl font-semibold">
            {{ stats.total_rooms }}
          </p>
          <p class="mt-1 text-xs text-zinc-500">
            Total de rooms cadastrados
          </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <p class="text-xs uppercase tracking-wide text-zinc-500">Reservas</p>
          <p class="mt-1 text-2xl font-semibold">
            {{ stats.total_reservations }}
          </p>
          <p class="mt-1 text-xs text-zinc-500">
            Soma de todas as reservas
          </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <p class="text-xs uppercase tracking-wide text-zinc-500">Status das reservas</p>
          <div class="mt-2 space-y-1 text-xs">
            <p>
              <span class="inline-block h-2 w-2 rounded-full bg-amber-400 align-middle"></span>
              <span class="ml-2 text-zinc-500">Pendentes:</span>
              <span class="ml-1 font-medium">{{ stats.reservations_status.pending }}</span>
            </p>
            <p>
              <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 align-middle"></span>
              <span class="ml-2 text-zinc-500">Confirmadas:</span>
              <span class="ml-1 font-medium">{{ stats.reservations_status.confirmed }}</span>
            </p>
            <p>
              <span class="inline-block h-2 w-2 rounded-full bg-red-400 align-middle"></span>
              <span class="ml-2 text-zinc-500">Canceladas:</span>
              <span class="ml-1 font-medium">{{ stats.reservations_status.canceled }}</span>
            </p>
          </div>
        </div>
      </section>

      <!-- Colunas -->
      <section class="grid gap-6 lg:grid-cols-3">
        <!-- Coluna direita: visão de reservas -->
        <div class="lg:col-span-2 space-y-4">
          <div class="rounded-2xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold">
              Visão geral das reservas
            </h2>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
              Distribuição por status.
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-3">
              <div class="rounded-xl bg-amber-50 p-3 text-sm text-amber-800 dark:bg-amber-500/10 dark:text-amber-200">
                <p class="text-xs uppercase tracking-wide">Pendentes</p>
                <p class="mt-1 text-2xl font-semibold">
                  {{ stats.reservations_status.pending }}
                </p>
              </div>
              <div class="rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-200">
                <p class="text-xs uppercase tracking-wide">Confirmadas</p>
                <p class="mt-1 text-2xl font-semibold">
                  {{ stats.reservations_status.confirmed }}
                </p>
              </div>
              <div class="rounded-xl bg-red-50 p-3 text-sm text-red-800 dark:bg-red-500/10 dark:text-red-200">
                <p class="text-xs uppercase tracking-wide">Canceladas</p>
                <p class="mt-1 text-2xl font-semibold">
                  {{ stats.reservations_status.canceled }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>
