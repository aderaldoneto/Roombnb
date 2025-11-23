<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post(route('admin.login.store'))
}
</script>

<template>
  
  <div class="min-h-screen flex items-center justify-center bg-zinc-100 dark:bg-zinc-950">
    <Head title="Admin Login" />

    <div class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-xl shadow p-6">
      <h1 class="text-xl font-semibold text-center mb-6">
        Painel Administrativo
      </h1>

      <form @submit.prevent="submit">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 mb-4"
          required
        />

        <label class="block text-sm font-medium mb-1">Senha</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 mb-4"
          required
        />

        <label class="flex items-center gap-2 text-sm mb-4">
          <input type="checkbox" v-model="form.remember" />
          Lembrar-me
        </label>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full rounded-lg bg-zinc-900 text-white py-2 font-semibold hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >
          Entrar
        </button>

        <div v-if="form.errors.email" class="mt-3 text-sm text-red-600">
          {{ form.errors.email }}
        </div>
        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
          {{ form.errors.password }}
        </div>
      </form>
    </div>
  </div>
</template>
