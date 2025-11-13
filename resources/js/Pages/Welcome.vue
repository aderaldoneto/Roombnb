<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'
import { formatCurrencyInput } from '@/Utils/Currency'

const appName = usePage().props.app.name

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,

    rooms: { type: Array, default: () => [] }, 
    cities: { type: Array, default: () => [] }, 
    specialties: { type: Array, default: () => [] }, 

    // filtros atuais (para manter estado na UI)
    filters: {
        type: Object,
        default: () => ({ city_id: null, specialty_id: null, check_in: '', check_out: '' }),
    },
})

const cityId = ref(props.filters.city_id ?? '')
const specialtyId = ref(props.filters.specialty_id ?? '')
const checkIn = ref(props.filters.check_in ?? '')
const checkOut = ref(props.filters.check_out ?? '')

function search() {
    router.get(
        route('rooms.index'),
        {
            city_id: cityId.value || undefined,
            specialty_id: specialtyId.value || undefined,
            check_in: checkIn.value || undefined,
            check_out: checkOut.value || undefined,
        },
        { preserveState: true, replace: true }
    )
}

function formatPrice(v) {
    const n = Number(v || 0)/100
    return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}
</script>

<template>
    <Head :title="`${appName} | Locação de Consultórios por Temporada`" />

    <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">

            <AppHeader></AppHeader>

            <!-- Busca -->
            <div class="border-t border-zinc-200/70 dark:border-zinc-800">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-3 px-4 py-3 sm:grid-cols-12">
                    <!-- Cidade -->
                    <div class="sm:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Cidade</label>
                        <select v-model="cityId" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="">Todas</option>
                            <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }} - {{ c.state }}</option>
                        </select>
                    </div>

                    <!-- Especialidade -->
                    <div class="sm:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Especialidade</label>
                        <select v-model="specialtyId" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <option value="">Todas</option>
                            <option v-for="s in specialties" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <!-- Check-in -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Check-in</label>
                        <input v-model="checkIn" type="date" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900" />
                    </div>

                    <!-- Check-out -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Check-out</label>
                        <input v-model="checkOut" type="date" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900" />
                    </div>

                    <!-- Botão buscar -->
                    <div class="sm:col-span-2 flex items-end">
                        <button @click="search"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 2a8 8 0 1 1 5.293 13.707l3 3a1 1 0 0 1-1.414 1.414l-3-3A8 8 0 0 1 10 2zm0 2a6 6 0 1 0 0 12A6 6 0 0 0 10 4z"/></svg>
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Banner -->
        <section class="relative">
            <div class="mx-auto max-w-7xl px-4">
                <div class="mt-8 rounded-3xl bg-[url('img_background.avif')] bg-cover bg-center p-8 sm:p-12">
                    <div class="max-w-xl rounded-2xl bg-white/80 p-6 backdrop-blur-md dark:bg-zinc-900/60">
                    <h1 class="text-2xl font-semibold sm:text-3xl">Encontre salas para suas consultas</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        Reserve consultórios por período ou diária, em diversas cidades e especialidades.
                    </p>
                    <div class="mt-4">
                        <button @click="search"
                                class="rounded-xl bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                                Ver resultados
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Desaques -->
        <main class="mx-auto max-w-7xl px-4 py-10">
            <h2 class="mb-4 text-lg font-semibold">Destaques</h2>

            <div v-if="rooms.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="room in rooms" :key="room.id" class="rounded-2xl border border-zinc-200/70 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                    <Link :href="route('rooms.show', room.id)">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded-t-2xl bg-zinc-100 dark:bg-zinc-800">
                        <img :src="room.cover_url || 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200&auto=format&fit=crop'"
                            alt="Foto da sala"
                            class="h-full w-full object-cover transition duration-300 hover:scale-[1.03]" />
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="line-clamp-1 text-base font-semibold">{{ room.title }}</h3>
                            <div class="flex items-center gap-1 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" viewBox="0 0 24 24" fill="currentColor"><path d="m12 17.27 6.18 3.73-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <span>{{ room.rating_avg ?? 0 }}</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-zinc-500">{{ room.city_name }}</p>
                        <p class="mt-3 text-sm font-semibold">{{ formatPrice(room.price) }} <span class="font-normal text-zinc-500">/ diária</span></p>
                    </div>
                    </Link>
                </article>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <p class="text-sm text-zinc-600 dark:text-zinc-300">Nenhum resultado encontrado.</p>
            </div>
        </main>

        <AppFooter></AppFooter>

    </div>
</template>
