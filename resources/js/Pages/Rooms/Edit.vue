<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'
import { formatCurrencyInput } from '@/Utils/Currency'

const appName = usePage().props.app.name

const props = defineProps({
  room: {
    type: Object,
    required: true,
    // esperado:
    // { id, title, description, price (centavos), city_id, specialty_id,
    //   city: {id,name,state}, specialty: {id,name},
    //   pictures: [{id, url, is_cover}], cover_picture_id, cover_url }
  },
  cities: Array,
  specialties: Array,
})

/** Helpers preço */
function toCents(v) {
  const s = (v || '0').toString().replace(/\./g, '').replace(',', '.')
  const n = Number(s)
  return Math.round((isNaN(n) ? 0 : n) * 100)
}
function fromCents(cents) {
  const n = (Number(cents || 0) / 100)
  return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const form = useForm({
  title: props.room.title || '',
  description: props.room.description || '',
  city_id: String(props.room.city_id || ''),
  specialty_id: String(props.room.specialty_id || ''),
  price: fromCents(props.room.price || 0),

  // fotos existentes
  existing_pictures: props.room.pictures || [], 
  delete_pictures: [], 

  // novas fotos
  new_pictures: [], 
  cover_id: props.room.cover_picture_id || null, 
  cover_new_index: null, 
})

/** Previews novas fotos */
const newPreviews = ref([])

function onSelectNewFiles(e) {
  const files = Array.from(e.target.files || [])
  form.new_pictures = files
  newPreviews.value = files.map(f => URL.createObjectURL(f))
}

function removeExistingPicture(picId) {
  // marca para deletar e remove visualmente
  if (!form.delete_pictures.includes(picId)) {
    form.delete_pictures.push(picId)
  }
  form.existing_pictures = form.existing_pictures.filter(p => p.id !== picId)

  if (form.cover_id === picId) form.cover_id = null
}

function removeNewPicture(idx) {
  const arr = Array.from(form.new_pictures)
  arr.splice(idx, 1)
  form.new_pictures = arr
  newPreviews.value.splice(idx, 1)
  if (form.cover_new_index === idx) form.cover_new_index = null
}

function handlePriceInput(e) {
  form.price = formatCurrencyInput(e.target.value ?? '')
}

const canSubmit = computed(() =>
  form.title && form.city_id && form.specialty_id && toCents(form.price) > 0
)

function clsInput(error) {
  return [
    'mt-1 block w-full rounded-xl border bg-white px-3 py-2 text-sm dark:bg-zinc-900',
    error ? 'border-red-500 focus-visible:outline-red-500'
          : 'border-zinc-300 focus-visible:outline-zinc-400 dark:border-zinc-700'
  ].join(' ')
}

function submit() {
  form
    .transform((data) => ({
      ...data,
      price: toCents(form.price),
      cover_id: form.cover_new_index != null ? '' : (form.cover_id ?? ''),
      cover_new_index:
        form.cover_new_index != null ? String(form.cover_new_index) : '',
      _method: 'PUT', 
    }))
    .post(route('rooms.update', props.room.id), {
      forceFormData: true,
      onSuccess: () => {
        newPreviews.value.forEach((u) => URL.revokeObjectURL(u))
      },
    })
}

</script>

<template>
  <Head :title="`${appName} — Editar acomodação`" />

  <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
    <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
      <AppHeader />
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10">
      <div class="mb-6">
        <nav class="text-xs text-zinc-500 dark:text-zinc-400">
          <Link :href="route('welcome')" class="hover:underline">Início</Link>
          <span class="mx-2">/</span>
          <Link :href="route('rooms.show', room.id)" class="hover:underline">Detalhe</Link>
          <span class="mx-2">/</span>
          <span>Editar</span>
        </nav>
        <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Editar acomodação</h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Atualize informações e gerencie fotos.</p>
      </div>

      <div class="grid gap-8 lg:grid-cols-12">
        <section class="lg:col-span-12">
          <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <form @submit.prevent="submit" class="grid grid-cols-1 gap-5">
              <div>
                <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Título</label>
                <input v-model="form.title" type="text" :class="clsInput(form.errors?.title)" maxlength="255" required />
                <p v-if="form.errors?.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
              </div>

              <div class="grid gap-5 sm:grid-cols-3">
                <div>
                  <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Cidade</label>
                  <select v-model="form.city_id" :class="clsInput(form.errors?.city_id)" required>
                    <option value="">Selecione</option>
                    <option v-for="c in cities" :key="c.id" :value="String(c.id)">{{ c.name }} - {{ c.state }}</option>
                  </select>
                  <p v-if="form.errors?.city_id" class="mt-1 text-xs text-red-600">{{ form.errors.city_id }}</p>
                </div>

                <div>
                  <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Especialidade</label>
                  <select v-model="form.specialty_id" :class="clsInput(form.errors?.specialty_id)" required>
                    <option value="">Selecione</option>
                    <option v-for="s in specialties" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
                  </select>
                  <p v-if="form.errors?.specialty_id" class="mt-1 text-xs text-red-600">{{ form.errors.specialty_id }}</p>
                </div>

                <div>
                  <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Preço (R$/diária)</label>
                  <input
                    :value="form.price"
                    @input="handlePriceInput"
                    inputmode="numeric"
                    :class="clsInput(form.errors?.price)"
                    placeholder="0,00"
                    required
                  />
                  <p v-if="form.errors?.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</p>
                </div>
              </div>

              <div>
                <label class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Descrição</label>
                <textarea v-model="form.description" rows="5" maxlength="65000" :class="clsInput(form.errors?.description)" required></textarea>
                <p v-if="form.errors?.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
              </div>

              <!-- Fotos existentes -->
              <div>
                <div class="mb-2 flex items-center justify-between">
                  <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">Fotos existentes</label>
                  <span class="text-xs text-zinc-500">Selecione a capa & remova as indesejadas</span>
                </div>

                <div v-if="form.existing_pictures.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                  <div v-for="pic in form.existing_pictures" :key="pic.id" class="group relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <img :src="pic.url" alt="" class="aspect-[4/3] w-full object-cover" />
                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-black/40 p-2 text-xs text-white">
                      <label class="inline-flex items-center gap-1">
                        <input type="radio" :value="pic.id" v-model="form.cover_id" :checked="pic.is_cover" />
                        capa
                      </label>
                      <button type="button" @click="removeExistingPicture(pic.id)" class="rounded bg-white/20 px-2 py-0.5 hover:bg-white/30">remover</button>
                    </div>
                  </div>
                </div>

                <p v-else class="rounded-xl border border-dashed border-zinc-300 p-4 text-center text-sm text-zinc-500 dark:border-zinc-700">
                  Não há fotos existentes.
                </p>
              </div>

              <!-- Novas fotos -->
              <div>
                <label class="mb-2 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Adicionar novas fotos</label>
                <input
                  type="file"
                  accept="image/*"
                  multiple
                  @change="onSelectNewFiles"
                  class="block w-full text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:hover:file:bg-zinc-700"
                />

                <div v-if="newPreviews.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                  <div v-for="(src, idx) in newPreviews" :key="idx" class="group relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <img :src="src" alt="" class="aspect-[4/3] w-full object-cover" />
                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-black/40 p-2 text-xs text-white">
                      <label class="inline-flex items-center gap-1">
                        <input type="radio" :value="idx" v-model="form.cover_new_index" />
                        capa nova
                      </label>
                      <button type="button" @click="removeNewPicture(idx)" class="rounded bg-white/20 px-2 py-0.5 hover:bg-white/30">remover</button>
                    </div>
                  </div>
                </div>
                
              </div>

              <div class="mt-2 flex items-center justify-end gap-3">
                <Link :href="route('rooms.show', room.id)" class="text-sm text-zinc-600 underline underline-offset-4 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                  Cancelar
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing || !canSubmit"
                  :class="[
                    'rounded-xl px-4 py-2 text-sm font-semibold transition',
                    (form.processing || !canSubmit) ? 'opacity-50 cursor-not-allowed' : '',
                    'bg-emerald-600 text-white hover:bg-emerald-700'
                  ]"
                >
                  Salvar alterações
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
