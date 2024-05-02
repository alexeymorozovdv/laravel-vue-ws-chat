<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link
    }
}

</script>

<template>
    <div class="w-3/4 p-4 m-auto">
        <div>
            <div class="mb-4 ">
                <Link :href="route('chats.index')">Chats</Link>
            </div>
        </div>
        <div>
            <slot />
        </div>
    </div>
</template>

<style scoped>

</style>
