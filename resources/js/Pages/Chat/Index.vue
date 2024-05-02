<script>
import Main from "@/Layouts/Main.vue"

export default {
    name: 'Index',

    props: [
        'users'
    ],

    layout: Main,

    methods: {
        store(id) {
            this.$inertia.post('/chats', {users: {id}, title: null});
        }
    }
}
</script>

<template>
    <div class="flex">
        <div class="p-4 w-1/2 bg-white border border-gray-200 mr-4 rounded-lg">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur assumenda corporis culpa cumque deserunt doloremque eos eveniet iusto numquam, perferendis quae quam quos repellendus temporibus tenetur totam velit, voluptas. Repellendus?
        </div>

        <div class="p-4 w-1/2 bg-white border border-gray-200 rounded-lg">
            <h3 class="mb-4 text-lg text-gray-600">Users:</h3>
            <div v-if="users">
                <div v-for="user in users" class="flex items-center mb-4 pb-4 border-b border-gray-300">
                    <p>{{ user.name }}</p>
                    <a @click.prevent="store(user.id)" class="inline-block bg-sky-400 text-white text-xs px-3 py-2 ml-4 rounded-lg" href="#">Message</a>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
