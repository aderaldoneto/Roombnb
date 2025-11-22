<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'

const appName = usePage().props.app.name 
const authUser = usePage().props.auth.user

const props = defineProps({
  room: {
    type: Object,
    required: true,
  },
})

function formatPriceCents(cents) {
  const n = (Number(cents || 0) / 100)
  return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

console.log(props.room)

</script>

<template>
  <Head :title="`${room.title} — ${appName}`" />

  <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
    <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
      <AppHeader />
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
      <nav class="text-xs text-zinc-500 dark:text-zinc-400">
        <Link :href="route('welcome')" class="hover:underline">Início</Link>
        <span class="mx-2">/</span>
        <span class="hover:underline">{{ room.city?.name }} - {{ room.city?.state }}</span>
      </nav>

      <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <h1 class="text-2xl font-semibold sm:text-3xl">{{ room.title }}</h1>
        <div class="text-right">
          <p class="text-xl font-semibold">{{ formatPriceCents(room.price) }} <span class="text-sm font-normal text-zinc-500">/ diária</span></p>
          <p class="text-xs text-zinc-500">Especialidade: {{ room.specialty?.name }}</p>
        </div>
      </div>

      <!-- Galeria -->
      <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-12">
        <div class="sm:col-span-8">
          <img
            :src="room.cover_url"
            alt="Foto do consultório"
            class="aspect-[16/10] w-full rounded-2xl object-cover"
          />
        </div>
        <div class="grid gap-3 sm:col-span-4">
          <div
            v-for="p in room.pictures?.slice(0,4)"
            :key="p.id"
            class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800"
          >
            <img :src="p.url" alt="" class="aspect-[16/10] w-full object-cover" />
          </div>
        </div>
      </section>

      <section class="mt-8 grid gap-8 sm:grid-cols-12">
        <article class="sm:col-span-8">
          <h2 class="text-lg font-semibold">Sobre o espaço</h2>
          <p class="mt-2 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-300">
            {{ room.description }}
          </p>

          <div class="mt-6 rounded-2xl border border-zinc-200 p-4 text-sm dark:border-zinc-800">
            <p class="text-zinc-500">Localização</p>
            <p class="mt-1 font-medium">{{ room.city?.name }} - {{ room.city?.state }}</p>
          </div>
        </article>

        <aside class="sm:col-span-4">
          <div class="rounded-2xl border border-zinc-200 p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-lg font-semibold">{{ formatPriceCents(room.price) }} <span class="text-sm font-normal text-zinc-500">/ diária</span></p>
                <p class="text-xs text-zinc-500">Média: {{ room.rating_avg ?? 0 }} ★</p>
              </div>
            </div>

            <div class="mt-5 flex gap-2">
              <Link
                v-if="authUser && authUser.id === room.user_id"
                :href="route('rooms.edit', room.id)"
                class="w-full rounded-xl bg-zinc-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
              >
                Editar anúncio
              </Link>

              <Link
                v-else
                :href="route('reservations.create', room.id)"
                class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-emerald-500 dark:bg-emerald-400 dark:text-zinc-900 dark:hover:bg-emerald-300"
              >
                Reservar
              </Link>
            </div>

            <div class="mt-6 rounded-xl border border-zinc-200 p-3 text-xs dark:border-zinc-700">
              <p class="text-zinc-500">Anfitrião</p>
              <p class="mt-1 font-medium">{{ room.owner?.name }}</p>
              <p class="text-zinc-500">{{ room.owner?.email }}</p>
            </div>
          </div>
        </aside>
      </section>
    </main>

    <AppFooter />
  </div>
</template>
