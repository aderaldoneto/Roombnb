<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue' 
import AppHeader from '@/Components/AppHeader.vue'

const props = defineProps({
  reservations: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)

function formatDate(value) {
  if (!value) return '—'
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

function formatStatus(status) {
  switch (status) {
    case 'pending':
      return 'Pendente'
    case 'confirmed':
      return 'Confirmada'
    case 'canceled':
      return 'Cancelada'
    default:
      return status || '—'
  }
}

function statusBadgeClasses(status) {
  switch (status) {
    case 'pending':
      return 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300'
    case 'confirmed':
      return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300'
    case 'canceled':
      return 'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-300'
    default:
      return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700/40 dark:text-zinc-200'
  }
}

function formatPayment(method) {
  if (!method) return '—'
  switch (method) {
    case 'credit_card':
      return 'Cartão de crédito'
    case 'cash':
      return 'Dinheiro'
    case 'pix':
      return 'Pix'
    default:
      return method
  }
}
</script>

<template>

  <AppHeader />

  <div class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-50">
    <Head title="Minhas reservas" />

    <main class="mx-auto flex max-w-5xl flex-col gap-6 px-4 py-8">
      <!-- Header -->
      <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            Minhas reservas
          </h1>
          <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Aqui você acompanha, confirma ou cancela as reservas dos seus anúncios.
          </p>
        </div>

        <div
          v-if="authUser"
          class="rounded-full bg-zinc-100 px-4 py-1.5 text-xs text-zinc-700 dark:bg-zinc-900 dark:text-zinc-300"
        >
          Logado como:
          <span class="font-medium">{{ authUser.name }}</span>
        </div>
      </header>

      <!-- Lista de reservas -->
      <section
        v-if="reservations && reservations.length"
        class="space-y-4"
      >
        <div
          v-for="reservation in reservations"
          :key="reservation.id"
          class="flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between"
        >
          <!-- Info principal -->
          <div class="flex-1 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
              <Link
                v-if="reservation.room"
                :href="route('rooms.show', reservation.room.id)"
                class="text-sm font-semibold text-zinc-900 underline-offset-4 hover:underline dark:text-zinc-50"
              >
                {{ reservation.room.title }}
              </Link>
              <span
                v-if="reservation.status"
                :class="[
                  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                  statusBadgeClasses(reservation.status),
                ]"
              >
                {{ formatStatus(reservation.status) }}
              </span>
            </div>

            <p
              v-if="reservation.room && reservation.room.city"
              class="text-xs text-zinc-500 dark:text-zinc-400"
            >
              {{ reservation.room.city.name }}
              <span v-if="reservation.room.city.state">
                – {{ reservation.room.city.state }}
              </span>
            </p>

            <div class="mt-2 grid grid-cols-2 gap-3 text-xs text-zinc-600 dark:text-zinc-300 sm:text-sm">
              <div>
                <p class="text-[11px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                  Check-in
                </p>
                <p class="font-medium">
                  {{ formatDate(reservation.check_in) }}
                </p>
              </div>
              <div>
                <p class="text-[11px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                  Check-out
                </p>
                <p class="font-medium">
                  {{ formatDate(reservation.check_out) }}
                </p>
              </div>
              <div>
                <p class="text-[11px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                  Pagamento
                </p>
                <p class="font-medium">
                  {{ formatPayment(reservation.payment_method) }}
                </p>
              </div>
              <div>
                <p class="text-[11px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                  Criada em
                </p>
                <p class="font-medium">
                  {{ formatDate(reservation.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Ações -->
          <div class="flex flex-col items-stretch gap-2 sm:w-44">
            <template v-if="reservation.status === 'pending'">
              <Link
                :href="route('tenant.reservations.update', reservation.id)"
                method="put"
                as="button"
                :data="{ status: 'confirmed' }"
                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-500 dark:bg-emerald-400 dark:text-zinc-900 dark:hover:bg-emerald-300"
              >
                Aceitar reserva
              </Link>

              <Link
                :href="route('tenant.reservations.update', reservation.id)"
                method="put"
                as="button"
                :data="{ status: 'canceled' }"
                class="inline-flex items-center justify-center rounded-xl border border-red-200 px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-950/40"
              >
                Recusar
              </Link>
            </template>

            <p
              v-else
              class="text-center text-xs text-zinc-500 dark:text-zinc-400"
            >
              Status:
              <span class="font-medium">
                {{ formatStatus(reservation.status) }}
              </span>
            </p>
          </div>
        </div>
      </section>

      <!-- Empty state -->
      <section
        v-else
        class="mt-6 rounded-2xl border border-dashed border-zinc-300 bg-white p-8 text-center text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400"
      >
        <p class="font-medium">
          Você ainda não possui reservas para seus anúncios.
        </p>
        <p class="mt-2">
          Assim que alguém reservar uma acomodação sua, ela aparecerá aqui.
        </p>
        <div class="mt-4">
          <Link
            :href="route('welcome')"
            class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
          >
            Ver página inicial
          </Link>
        </div>
      </section>
    </main>
  </div>
</template>
