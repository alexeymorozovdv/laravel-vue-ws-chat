<script>
import Main from "@/Layouts/Main.vue"

export default {
    name: 'Show',

    props: [
        'chat'
    ],

    layout: Main,
}
</script>

<template>
    <div class="flex">
        <div class="p-4 w-3/4 bg-white border border-gray-200 mr-4 rounded-lg">
        </div>

        <div class="p-4 w-1/4 bg-white border border-gray-200 rounded-lg">
        </div>
    </div>
</template>

<style scoped>

</style>
