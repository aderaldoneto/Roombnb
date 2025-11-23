<script setup>
import AppHeader from '@/Components/Admin/AppHeader.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post(route('admin.users.store'))
}
</script>

<template>
  <AppHeader />
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50">
    <Head title="Criar administrador" />
    <header class="border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/80">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <h1 class="text-lg font-semibold tracking-tight">
          Novo Administrador
        </h1>
      </div>
    </header>

    <main class="max-w-xl mx-auto px-4 py-10">
      <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <form @submit.prevent="submit" class="space-y-4">

          <!-- Nome -->
          <div>
            <label class="block text-sm font-medium mb-1">Nome</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
              required
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">
              {{ form.errors.name }}
            </p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
              required
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">
              {{ form.errors.email }}
            </p>
          </div>

          <!-- Senha -->
          <div>
            <label class="block text-sm font-medium mb-1">Senha</label>
            <input
              v-model="form.password"
              type="password"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
              required
            />
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">
              {{ form.errors.password }}
            </p>
          </div>

          <!-- Confirmar senha -->
          <div>
            <label class="block text-sm font-medium mb-1">Confirmar senha</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
              required
            />
          </div>

          <div class="pt-2 flex justify-end gap-2">
            <Link
              :href="route('admin.dashboard')"
              class="rounded-lg border border-zinc-300 px-4 py-2 text-sm hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800"
            >
              Cancelar
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 disabled:opacity-70 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
            >
              Criar administrador
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>
