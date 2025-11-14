<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'
import { formatCurrencyInput } from '@/Utils/Currency'


const appName = usePage().props.app.name

const props = defineProps({
  cities: Array,
  specialties: Array,
})

const form = useForm({
  title: '',
  description: '',
  city_id: '',
  specialty_id: '',
  price: '0,00',

  pictures: [],         
  cover_index: null, 
})

const previews = ref([]) // urls temporárias pra preview

function onSelectFiles(e) {
  const files = Array.from(e.target.files || [])
  form.pictures = files
  previews.value = files.map(f => URL.createObjectURL(f))
}

function removePicture(idx) {
  const arr = Array.from(form.pictures)
  arr.splice(idx, 1)
  form.pictures = arr
  previews.value.splice(idx, 1)
  if (form.cover_index === idx) form.cover_index = null
}

function toCents(v) {
  const s = (v || '0').toString().replace(/\./g, '').replace(',', '.')
  const n = Number(s)
  return Math.round((isNaN(n) ? 0 : n) * 100)
}

const canSubmit = computed(() =>
  form.title && form.city_id && form.specialty_id && toCents(form.price) > 0
)

function submit() {

    form.transform((data) => ({
        ...data,
        price: toCents(form.price), 
        cover_index: data.cover_index ?? ''
    }))
    .post(route('rooms.store'), {
        forceFormData: true, // envia pictures[] certinho
        onSuccess: () => {
            previews.value.forEach((u) => URL.revokeObjectURL(u))
        },
    })
}

function clsInput(error) {
  return [
    'mt-1 block w-full rounded-xl border bg-white px-3 py-2 text-sm dark:bg-zinc-900',
    error ? 'border-red-500 focus-visible:outline-red-500'
          : 'border-zinc-300 focus-visible:outline-zinc-400 dark:border-zinc-700'
  ].join(' ')
}

function handlePriceInput(e) {
  const raw = e.target.value
  const formatted = formatCurrencyInput(raw)
  form.price = formatted
}

</script>

<template>
    <Head :title="`${appName} — Nova acomodação`" />

    <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
        <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
            <AppHeader />
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10">
            <div class="mb-6">
                <nav class="text-xs text-zinc-500 dark:text-zinc-400">
                    <Link :href="route('welcome')" class="hover:underline">Início</Link>
                    <span class="mx-2">/</span>
                    <span>Nova</span>
                </nav>
                <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Cadastrar acomodação</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Preencha os detalhes e adicione fotos.</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-12">
                <section class="lg:col-span-12">
                    <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Título</label>
                                <input v-model="form.title" type="text" :class="clsInput(form.errors.title)" maxlength="255" placeholder="Ex.: Consultório amplo no Centro" required/>
                                <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                            </div>


                            <div class="grid gap-5 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Cidade</label>
                                    <select v-model="form.city_id" :class="clsInput(form.errors.city_id)" required>
                                        <option value="">Selecione</option>
                                        <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }} - {{ c.state }}</option>
                                    </select>
                                    <p v-if="form.errors.city_id" class="mt-1 text-xs text-red-600">{{ form.errors.city_id }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Especialidade</label>
                                    <select v-model="form.specialty_id" :class="clsInput(form.errors.specialty_id)" required>
                                        <option value="">Selecione</option>
                                        <option v-for="s in specialties" :key="s.id" :value="s.id">{{ s.name }}</option>
                                    </select>
                                    <p v-if="form.errors.specialty_id" class="mt-1 text-xs text-red-600">{{ form.errors.specialty_id }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Preço (R$/diária)</label>
                                    <input :value="form.price"
                                        @input="handlePriceInput"
                                        inputmode="numeric"
                                        class="mt-1 block w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
                                        placeholder="0,00" 
                                        required/>
                                    <p v-if="form.errors.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Descrição</label>
                                <textarea v-model="form.description" rows="5" maxlength="65000" :class="clsInput(form.errors.description)" placeholder="Detalhes do espaço, horários, regras..." required></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Fotos (até 10)</label>
                                <input type="file" accept="image/*" multiple @change="onSelectFiles"
                                        class="block w-full text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:hover:file:bg-zinc-700" />
                                <p v-if="form.errors.pictures" class="mt-1 text-xs text-red-600">{{ form.errors.pictures }}</p>

                                <!-- Grid previews -->
                                <div v-if="previews.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                    <div v-for="(src, idx) in previews" :key="idx" class="group relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    <img :src="src" alt="" class="aspect-[4/3] w-full object-cover" />
                                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-black/40 p-2 text-xs text-white">
                                        <label class="inline-flex items-center gap-1">
                                        <input type="radio" :value="idx" v-model="form.cover_index" />
                                        capa
                                        </label>
                                        <button type="button" @click="removePicture(idx)" class="rounded bg-white/20 px-2 py-0.5 hover:bg-white/30">remover</button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 flex items-center justify-end gap-3">
                                <Link :href="route('welcome')" class="text-sm text-zinc-600 underline underline-offset-4 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                                    Cancelar
                                </Link>
                                <button type="submit"
                                        :disabled="form.processing || !canSubmit"
                                        :class="[
                                            'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                            (form.processing || !canSubmit) ? 'opacity-50 cursor-not-allowed' : '',
                                            'bg-emerald-600 text-white hover:bg-emerald-700'
                                        ]">
                                    Salvar acomodação
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>

    <AppFooter />
    </div>
</template>
