<script setup>
import AppHeader from '@/Components/AppHeader.vue';
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  rooms: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>

  <AppHeader /> 
  
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">
    <Head title="Minhas acomodações" />

    <main class="max-w-6xl mx-auto px-4 py-10">

      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-semibold">Minhas acomodações</h1>

        <Link
          :href="route('rooms.create')"
          class="rounded-xl bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >
          Criar nova
        </Link>
      </div>


      <!-- Estado vazio -->
      <div
        v-if="rooms.length === 0"
        class="rounded-xl border border-dashed border-zinc-300 bg-white dark:bg-zinc-900 dark:border-zinc-700 p-10 text-center"
      >
        <p class="text-lg font-medium mb-2">Você ainda não possui nenhuma acomodação.</p>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
          Clique no botão "Criar nova" para adicionar seu primeiro anúncio.
        </p>

        <div class="mt-5">
          <Link
            :href="route('rooms.create')"
            class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
          >
            Criar nova acomodação
          </Link>
        </div>
      </div>


      <!-- Grid -->
      <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

        <div
          v-for="room in rooms"
          :key="room.id"
          class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm"
        >

          <!-- Capa -->
          <div class="aspect-video bg-zinc-200 dark:bg-zinc-800">
            <img
              v-if="room.cover_url"
              :src="room.cover_url"
              alt=""
              class="w-full h-full object-cover"
            />
            <div v-else class="flex items-center justify-center w-full h-full text-zinc-400 text-sm">
              Sem foto
            </div>
          </div>


          <!-- Infos -->
          <div class="p-4 flex flex-col gap-2">

            <h2 class="text-lg font-semibold truncate">
              {{ room.title }}
            </h2>

            <p class="text-sm text-zinc-600 dark:text-zinc-400 truncate">
              {{ room.city_name }}
            </p>

            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
              R$ {{ (room.price / 100).toFixed(2).replace('.', ',') }}
            </p>

            <!-- Ações -->
            <div class="mt-3 flex gap-2">
              <Link
                :href="route('rooms.show', room.id)"
                class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-sm text-center hover:bg-zinc-100 dark:hover:bg-zinc-800"
              >
                Ver
              </Link>

              <Link
                :href="route('rooms.edit', room.id)"
                class="flex-1 rounded-lg bg-zinc-900 text-white px-3 py-1.5 text-sm text-center hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
              >
                Editar
              </Link>
            </div>

          </div>

        </div>

      </div>

    </main>
  </div>
</template>
