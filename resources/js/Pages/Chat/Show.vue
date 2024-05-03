<script>
import Main from "@/Layouts/Main.vue"

export default {
    name: 'Show',

    props: [
        'chat',
        'users'
    ],

    data() {
        return {
            body: '',
        }
    },

    methods: {
        store() {
            // console.log(this.userIds);
            axios.post('/messages', {
                chat_id: this.chat.id,
                body: this.body,
                user_ids: this.userIds
            }).then(res => {
                console.log(res);
            })
        }
    },

    computed: {
        userIds() {
            return this.users.map(user => {
                return user.id
            }).filter(userId => {
                return userId !== this.$page.props.auth.user.id
            })
        }
    },

    layout: Main,
}
</script>

<template>
    <div class="flex">
        <div class="p-4 w-3/4 bg-white border border-gray-200 mr-4 rounded-lg">
            <h3 class="mb-4 text-lg text-gray-600">{{ chat.title ?? 'Unnamed chat' }}</h3>
            <div class="mb-4">

            </div>

            <div>
                <h3 class="mb-4 text-lg text-gray-600">Send message</h3>
                <div class="flex items-center">
                    <div>
                        <input class="rounded-full" type="text" v-model="body" placeholder="Write a message..">
                    </div>
                    <div>
                        <a @click.prevent="store()"
                           class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 ml-4 rounded-lg hover:bg-indigo-500"
                           href="#">
                            Send
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 w-1/4 bg-white border border-gray-200 rounded-lg">
            <h3 class="mb-4 text-lg text-gray-600">Users:</h3>
            <div v-if="users">
                <div v-for="user in users" class="flex items-center mb-4 pb-4 border-b border-gray-300">
                    <p>{{ user.name }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
