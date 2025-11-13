<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'

import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'

const appName = usePage().props.app.name

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
})
</script>

<template>
  <Head :title="`${appName} — Meu perfil`" />

  <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
    <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
      <AppHeader />
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <nav class="text-xs text-zinc-500 dark:text-zinc-400">
            <Link :href="route('welcome')" class="hover:underline">Início</Link>
            <span class="mx-2">/</span>
            <span>Perfil</span>
          </nav>
          <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">Meu perfil</h1>
          <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
            Gerencie suas informações, senha e preferências da conta.
          </p>
        </div>
      </div>

      <div v-if="status"
           class="mb-6 rounded-xl border border-emerald-300/60 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-950/40 dark:text-emerald-200">
        {{ status }}
      </div>

      <div class="grid gap-8 lg:grid-cols-12">
        <!-- Lado esquerdo (dados do usuário) -->
        <section class="lg:col-span-7">
          <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="text-lg font-semibold">Informações da conta</h2>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
              Nome, e-mail e verificação.
            </p>

            <div class="mt-6">
              <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
                class="max-w-xl"
              />
            </div>
          </div>

          <!-- Senha -->
          <div class="mt-8 rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="text-lg font-semibold">Segurança</h2>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
              Atualize sua senha de acesso.
            </p>

            <div class="mt-6">
              <UpdatePasswordForm class="max-w-xl" />
            </div>
          </div>
        </section>

        <!-- Coluna direita: ações perigosas -->
        <aside class="lg:col-span-5">
          <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">Zona de perigo</h2>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
              Excluir sua conta remove permanentemente seus dados.
            </p>

            <div class="mt-6">
              <DeleteUserForm class="max-w-xl" />
            </div>
          </div>
        </aside>
      </div>
    </main>

    <AppFooter />
  </div>
</template>
