<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onBeforeUnmount  } from 'vue'

const page = usePage()
const appName = page.props.app?.name ?? 'RoomBnB'
const authUser = page.props.auth?.user ?? null
const menuOpen = ref(false)

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
})

const isTenant = computed(() => {
  if (!authUser || !authUser.roles) return false

  return authUser.roles.includes('tenant')
})

const isHost = computed(() => {
  if (!authUser || !authUser.roles) return false

  return authUser.roles.includes('host')
})

function closeOnClickOutside(event) {
  if (!event.target.closest('.user-menu')) {
    menuOpen.value = false
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('click', closeOnClickOutside)
  }
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('click', closeOnClickOutside)
  }
})
</script>

<template> 
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:py-4">
    <!-- logo -->
    <div class="flex items-center gap-3">
        <Link :href="route('welcome')" class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
            <!-- estetoscópio -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 3a1 1 0 0 0-1 1v4a5 5 0 1 0 10 0V4a1 1 0 1 0-2 0v4a3 3 0 1 1-6 0V4a1 1 0 0 0-1-1z"/><path d="M19 12a3 3 0 0 0-2.83 4H15a3 3 0 0 1-3-3V9h-2v4a5 5 0 0 0 5 5h1.17A3 3 0 1 0 19 12z"/>
            </svg>
        </span>
        <span class="text-lg font-semibold tracking-tight">{{ appName }}</span>
        </Link>
    </div>

    <!-- Auth links -->
    <nav class="flex items-center gap-2">

        <template v-if="$page.props.auth?.user">

            <Link 
                v-if="isTenant" 
                :href="route('tenant.dashboard')" 
                class="text-sm font-semibold tracking-tight">
                Dashboard 
            </Link>

            <Link 
                v-if="isTenant" 
                :href="route('rooms.create')" 
                class="text-sm font-semibold tracking-tight"
                >
                    Criar acomodação 
            </Link>
            
            <Link :href="route('profile.edit')" class="text-sm underline underline-offset-4">{{ $page.props.auth.user.name }}</Link>

            <div class="relative user-menu">
                <button @click.stop="menuOpen = !menuOpen"
                        class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-700 dark:text-zinc-200" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6h16a1 1 0 1 1 0 2H4a1 1 0 0 1 0-2Zm0 5h16a1 1 0 1 1 0 2H4a1 1 0 0 1 0-2Zm0 5h16a1 1 0 1 1 0 2H4a1 1 0 0 1 0-2Z"/>
                    </svg>
                </button>

                <div v-if="menuOpen"
                    class="absolute right-0 mt-2 w-44 rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                    <Link :href="route('profile.edit')"
                            class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Perfil
                    </Link>
                    <Link
                        v-if="isTenant"
                        :href="route('tenant.reservations.index')" 
                        class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                        Minhas reservas
                    </Link>
                    <Link
                        v-if="isTenant"
                        :href="route('rooms.list')" 
                        class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                        Minhas salas
                    </Link>
                    <Link 
                        v-if="isHost"
                        :href="route('reservations.index')"
                        class="block px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Histórico
                    </Link>
                    <!-- Histórico -->
                    <div class="border-t border-zinc-200 dark:border-zinc-700 my-1"></div>
                    <Link :href="route('logout')" method="post" as="button"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30">
                        Sair
                    </Link>
                </div>
            </div>

            
        </template>

        
        <Link v-if="!$page.props.auth?.user"
            :href="route('register')"
            class="rounded-full px-4 py-2 text-sm font-medium ring-1 ring-zinc-300 transition hover:bg-zinc-50 dark:ring-zinc-700 dark:hover:bg-zinc-900"
        >
            Criar conta
        </Link>
        <Link v-if="!$page.props.auth?.user"
            :href="route('login')"
            class="rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
        >
            Entrar
        </Link>
    </nav>
    </div>
</template>
