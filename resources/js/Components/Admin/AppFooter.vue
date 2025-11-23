<script setup>
import { usePage } from '@inertiajs/vue3'

const appName = usePage().props.app.name
</script>

<template> 
    <footer class="border-t border-zinc-200/70 py-10 text-center text-xs text-zinc-500 dark:border-zinc-800">
        Painel Administrativo | Todos os direitos reservado © • {{ appName }}
    </footer>
</template>
