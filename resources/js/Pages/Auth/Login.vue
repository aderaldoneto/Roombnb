<script setup>
    import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
    import { ref, computed } from 'vue'
    import AppHeader from '@/Components/AppHeader.vue'
    import AppFooter from '@/Components/AppFooter.vue'

    const appName = usePage().props.app.name

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    })

    const showPwd = ref(false)

    const submit = () => {
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
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
</script>

<template>
    <Head :title="`${appName} — Entrar`" />

    <div class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-950 dark:text-zinc-50">
        <header class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/70 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/60">
            <AppHeader />
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10">
            <div class="grid gap-8 lg:grid-cols-12">
                <!-- Body esquerdo -->
                <section class="lg:col-span-5">
                    <h1 class="text-2xl font-semibold sm:text-3xl">Bem-vindo de volta</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        Acesse sua conta para gerenciar reservas, confirmar solicitações como locador e encontrar novas salas por cidade e especialidade.
                    </p>

                    <ul class="mt-6 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">✓</span>
                            Visualize e gerencie suas reservas
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">✓</span>
                            Confirmações e cancelamentos rápidos
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">✓</span>
                            Histórico e mensagens
                        </li>
                    </ul>
                </section>

                <!-- Direito para login -->
                <section class="lg:col-span-7">
                    <div class="rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div v-if="hasErrors" class="mb-4 rounded-xl border border-red-300/60 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/40 dark:bg-red-950/40 dark:text-red-200">
                            Verifique os campos destacados abaixo.
                        </div>

                        <form @submit.prevent="submit" class="grid grid-cols-1 gap-5">
                            <div>
                                <label for="email" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">E-mail</label>
                                <input id="email" type="email" v-model="form.email" required autocomplete="username" :class="clsInput(form.errors.email)" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label for="password" class="mb-1 block text-xs font-medium text-zinc-500 dark:text-zinc-400">Senha</label>
                                <div class="relative">
                                    <input :type="showPwd ? 'text' : 'password'" id="password" v-model="form.password" required autocomplete="current-password" :class="clsInput(form.errors.password)" />
                                    <button type="button" @click="showPwd = !showPwd"
                                            class="absolute inset-y-0 right-2 my-auto inline-flex h-8 items-center rounded-md px-2 text-xs text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    {{ showPwd ? 'Ocultar' : 'Mostrar' }}
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                                    <input type="checkbox" v-model="form.remember" class="rounded-sm" />
                                    Manter conectado
                                </label>

                                <Link v-if="$page.props?.canResetPassword" :href="route('password.request')"
                                        class="text-xs text-zinc-600 underline underline-offset-4 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                                    Esqueci minha senha
                                </Link>
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                <Link v-if="$page.props?.canRegister !== false" :href="route('register')"
                                        class="text-sm text-zinc-600 underline underline-offset-4 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                                    Criar conta
                                </Link>

                                <button type="submit"
                                        :disabled="form.processing || !form.email || !form.password"
                                        :class="[
                                            'rounded-xl px-4 py-2 text-sm font-semibold transition',
                                            (form.processing || !form.email || !form.password) ? 'opacity-50 cursor-not-allowed' : '',
                                            'bg-emerald-600 text-white hover:bg-emerald-700'
                                        ]">
                                    Entrar
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
