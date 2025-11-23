<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'

const props = defineProps({
  room: {
    type: Object,
    required: true,
    // esperado:
    // { id, title, description, price, city, specialty, cover_url, ... }
  },
})

const page = usePage()
const authUser = page.props.auth?.user ?? null

const form = useForm({
  room_id: props.room.id,
  check_in: '',
  check_out: '',
  payment_method: 'credit_card', // valor padrão
})

const formattedPrice = computed(() => {
  // Se vier em centavos, converte; adapte se já vier em reais
  const cents = props.room.price ?? 0
  const value = (cents / 100).toFixed(2)
  return `R$ ${value}`.replace('.', ',')
})

function submit() {
  form.post(route('reservations.store', props.room.id), {
    preserveScroll: true,
    onSuccess: () => {
      // se quiser, pode limpar o form ou exibir toast
      // form.reset('check_in', 'check_out', 'payment_method')
    },
  })
}
</script>

<template>
  <div class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-50">
    <Head title="Reservar quarto" />

    <AppHeader />

    <main class="mx-auto flex max-w-4xl flex-col gap-8 px-4 py-8">
      <!-- Breadcrumb / Voltar -->
      <div class="flex items-center justify-between">
        <Link
          :href="route('rooms.show', room.id)"
          class="text-sm text-zinc-600 underline-offset-4 hover:underline dark:text-zinc-300"
        >
          ← Voltar para o anúncio
        </Link>

      </div>

      <!-- Card do quarto -->
      <section
        class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
      >
        <div class="grid gap-0 md:grid-cols-[2fr,3fr]">
          <!-- Imagem / capa -->
          <div class="relative h-56 w-full md:h-full">
            <img
              v-if="room.cover_url"
              :src="room.cover_url"
              :alt="room.title"
              class="h-full w-full object-cover"
            />
            <div
              v-else
              class="flex h-full w-full items-center justify-center bg-zinc-200 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"
            >
              Sem imagem de capa
            </div>
          </div>

          <!-- Info do quarto -->
          <div class="flex flex-col gap-3 p-5">
            <h1 class="text-xl font-semibold">
              {{ room.title }}
            </h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-300 line-clamp-3">
              {{ room.description }}
            </p>

            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
              <div v-if="room.city">
                {{ room.city.name }}
                <span v-if="room.city.state">– {{ room.city.state }}</span>
              </div>
              <div v-if="room.specialty">
                Tipo: <strong>{{ room.specialty.name }}</strong>
              </div>
            </div>

            <div class="mt-auto pt-3">
              <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Preço por noite
              </p>
              <p class="text-2xl font-bold">
                {{ formattedPrice }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Form de reserva -->
      <section
        class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
      >
        <h2 class="mb-4 text-lg font-semibold">
          Detalhes da reserva
        </h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <!-- Check-in -->
            <div>
              <label for="check_in" class="mb-1 block text-sm font-medium">
                Check-in
              </label>
              <input
                id="check_in"
                v-model="form.check_in"
                type="date"
                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-50"
              />
              <p v-if="form.errors.check_in" class="mt-1 text-xs text-red-500">
                {{ form.errors.check_in }}
              </p>
            </div>

            <!-- Check-out -->
            <div>
              <label for="check_out" class="mb-1 block text-sm font-medium">
                Check-out
              </label>
              <input
                id="check_out"
                v-model="form.check_out"
                type="date"
                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-50"
              />
              <p v-if="form.errors.check_out" class="mt-1 text-xs text-red-500">
                {{ form.errors.check_out }}
              </p>
            </div>
          </div>

          <!-- Método de pagamento -->
          <div class="max-w-xs">
            <label for="payment_method" class="mb-1 block text-sm font-medium">
              Método de pagamento
            </label>
            <select
              id="payment_method"
              v-model="form.payment_method"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-50"
            >
              <option disabled value="">Selecione</option>
              <option value="credit_card">Cartão de crédito</option>
              <option value="cash">Dinheiro</option>
              <option value="pix">Pix</option>
            </select>
            <p v-if="form.errors.payment_method" class="mt-1 text-xs text-red-500">
              {{ form.errors.payment_method }}
            </p>
          </div>

          <!-- Erro genérico -->
          <p v-if="form.hasErrors && !form.recentlySuccessful" class="text-xs text-red-500">
            Verifique os campos destacados antes de continuar.
          </p>

          <!-- Botão -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition disabled:cursor-not-allowed disabled:opacity-70 hover:bg-emerald-500 dark:bg-emerald-400 dark:text-zinc-900 dark:hover:bg-emerald-300 md:w-auto"
            >
              <span v-if="!form.processing">Confirmar reserva</span>
              <span v-else>Enviando...</span>
            </button>
          </div>
        </form>
      </section>
    </main>

    <AppFooter />
  </div>
</template>
