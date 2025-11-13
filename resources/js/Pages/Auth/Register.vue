<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'
import AppFooter from '@/Components/AppFooter.vue'
import { validatePassword } from '@/Utils/Validators'


const appName = usePage().props.app.name

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const termsAccepted = ref(false)

const showPwd = ref(false)
const showPwd2 = ref(false)

const canLogin = true 

const submit = () => { 
    
    const errors = validatePassword(form.password)

    if (errors.length > 0) {
        form.errors.password = errors.join(' ')
        return
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const hasErrors = computed(() => Object.keys(form.errors).length > 0)

function clsInput(error) {
    return [
    'mt-1 block w-full rounded-xl border bg-white px-3 py-2 text-sm dark:bg-zinc-900',
    error
        ? 'border-red-500 focus-visible:outline-red-500'
        : 'border-zinc-300 focus-visible:outline-zinc-400 dark:border-zinc-700'
    ].join(' ')
}

function checkPassword() {
    const errors = validatePassword(form.password)
    if (errors.length > 0) {
        form.errors.password = errors.join(' ')
    } else {
        form.errors.password = null
    }
}
</script>

<template>
    <Head :title="`${appName} — Criar conta`" />

    <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
        <!-- Header (igual ao Welcome) -->
        <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">

            <AppHeader></AppHeader>

        </header>

        <!-- Esquerda -->
        <main class="mx-auto max-w-7xl px-4 py-10">
            <div class="grid gap-8 lg:grid-cols-12">
                <section class="lg:col-span-5">
                    <h1 class="text-2xl font-semibold sm:text-3xl">Crie sua conta</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    Reserve consultórios por diária ou período, filtre por cidade e especialidade e gerencie suas reservas com facilidade.
                    </p>

                    <ul class="mt-6 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                            ✓
                            </span>
                            Encontre salas nas principais cidades
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                            ✓
                            </span>
                            Filtros por especialidade, check-in e check-out
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                            ✓
                            </span>
                            Confirmação de reservas pelo locador
                        </li>
                    </ul>
                </section>

                <!-- Direita (login) -->
                <section class="lg:col-span-7">
                    <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <!-- erros -->
                        <div v-if="hasErrors" class="mb-4 rounded-xl border border-red-300/60 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/40 dark:bg-red-950/40 dark:text-red-200">
                            Verifique os campos destacados abaixo.
                        </div>

                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-5">
                            <div>
                                <label for="name" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Nome</label>
                                <input id="name" type="text" v-model="form.name" required autocomplete="name" :class="clsInput(form.errors.name)" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">E-mail</label>
                                <input id="email" type="email" v-model="form.email" required autocomplete="username" :class="clsInput(form.errors.email)" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Senha</label>
                                    <div class="relative">
                                    <input :type="showPwd ? 'text' : 'password'" id="password" v-model="form.password" required autocomplete="new-password" :class="clsInput(form.errors.password)" />
                                    <button type="button" @click="showPwd = !showPwd"
                                            class="absolute inset-y-0 right-2 my-auto inline-flex h-8 items-center rounded-md px-2 text-xs text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        {{ showPwd ? 'Ocultar' : 'Mostrar' }}
                                    </button>
                                    </div>
                                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                                    <p class="mt-1 text-[11px] text-zinc-500">Mín. 8 caracteres. Use letras e números.</p>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Confirmar senha</label>
                                    <div class="relative">
                                    <input :type="showPwd2 ? 'text' : 'password'" id="password_confirmation" v-model="form.password_confirmation" required autocomplete="new-password" :class="clsInput(form.errors.password_confirmation)" />
                                    <button type="button" @click="showPwd2 = !showPwd2"
                                            class="absolute inset-y-0 right-2 my-auto inline-flex h-8 items-center rounded-md px-2 text-xs text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        {{ showPwd2 ? 'Ocultar' : 'Mostrar' }}
                                    </button>
                                    </div>
                                    <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
                                </div>
                            </div>
                            
                            <label class="flex items-start gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                            <input type="checkbox" v-model="termsAccepted" class="mt-0.5" required>
                                Concordo com os Termos de Uso e Política de Privacidade.
                            </label>

                            <div class="mt-2 flex items-center justify-between">
                                <Link :href="route('login')" class="text-sm text-zinc-600 underline underline-offset-4 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                                    Já tem conta? Entrar
                                </Link>

                                <button type="submit" 
                                        :disabled="form.processing || !termsAccepted"
                                        :class="[
                                            'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                            form.processing ? 'opacity-50 cursor-not-allowed' : '',
                                            'bg-emerald-600 text-white hover:bg-emerald-700'
                                        ]">
                                    Criar conta
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>

        <AppFooter></AppFooter>

    </div>
</template>
