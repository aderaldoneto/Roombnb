<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'

const page = usePage()

const props = defineProps({
  reservations: {
    type: Array,
    default: () => [],
  },
})

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('pt-BR')
}

function formatStatus(status) {
  if (!status) return '-'
  const map = {
    pending: 'Pendente',
    confirmed: 'Confirmada',
    canceled: 'Cancelada',
  }
  return map[status] ?? status
}

function formatPayment(method) {
  if (!method) return '—'
  const map = {
    credit_card: 'Cartão de crédito',
    cash: 'Dinheiro',
    pix: 'Pix',
  }
  return map[method] ?? method
}
</script>

<template>
  <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
    <Head title="Minhas reservas" />

    <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
      <AppHeader />
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:py-10 space-y-6">
      <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-semibold sm:text-2xl">
          Minhas reservas
        </h1>

        <Link
          :href="route('welcome')"
          class="text-xs text-zinc-500 hover:underline"
        >
          Voltar para busca
        </Link>
      </div>

      <div v-if="!reservations.length" class="rounded-2xl border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
        Você ainda não fez nenhuma reserva.
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="reservation in reservations"
          :key="reservation.id"
          class="flex flex-col gap-4 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between"
        >
          <!-- Info do room -->
          <div class="flex flex-1 items-start gap-4">
            <div
              v-if="reservation.room?.cover_url"
              class="hidden h-20 w-28 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 sm:block"
            >
              <img
                :src="reservation.room.cover_url"
                alt=""
                class="h-full w-full object-cover"
              />
            </div>

            <div>
              <p class="text-sm font-semibold">
                {{ reservation.room?.title ?? 'Acomodação removida' }}
              </p>
              <p class="mt-1 text-xs text-zinc-500">
                {{ reservation.room?.city?.name }} - {{ reservation.room?.city?.state }}
              </p>
              <p v-if="reservation.room?.specialty?.name" class="mt-1 text-xs text-zinc-500">
                Especialidade: {{ reservation.room.specialty.name }}
              </p>

              <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                  Check-in: {{ formatDate(reservation.check_in) }}
                </span>
                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                  Check-out: {{ formatDate(reservation.check_out) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Status / ações -->
          <div class="flex flex-col items-end gap-2 text-right text-xs sm:w-48">
            <span
              class="inline-flex items-center rounded-full px-2.5 py-0.5 font-medium"
              :class="{
                'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200': reservation.status === 'pending',
                'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200': reservation.status === 'confirmed',
                'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-200': reservation.status === 'canceled',
              }"
            >
              {{ formatStatus(reservation.status) }}
            </span>

            <p class="text-[11px] text-zinc-500">
              Criada em {{ formatDate(reservation.created_at) }}
            </p>

            <p class="text-[11px] text-zinc-500">
              Pagamento: <span class="font-medium">{{ formatPayment(reservation.payment_method) }}</span>
            </p>

            <div class="mt-2">
              <Link
                v-if="reservation.room"
                :href="route('rooms.show', reservation.room.id)"
                class="text-[11px] font-medium text-emerald-600 hover:underline dark:text-emerald-300"
              >
                Ver acomodação
              </Link>
            </div>
          </div>
        </div>
      </div>
    </main>

    <AppFooter />
  </div>
</template>
