<script>
import Main from "@/Layouts/Main.vue"

export default {
    name: 'Show',

    props: [
        'chat',
        'users',
        'messages',
    ],

    data() {
        return {
            body: '',
            showTitleInput: false,
            newTitle: this.chat.title
        }
    },

    methods: {
        store() {
            axios.post('/messages', {
                chat_id: this.chat.id,
                body: this.body,
                user_ids: this.userIds
            }).then(res => {
                this.messages.push(res.data);
                this.body = '';
            })
        },

        toggleChatEdit() {
            this.showTitleInput = !this.showTitleInput;
        },

        editChat() {
            if (this.newTitle === '') {
                this.$swal({
                    icon: 'error',
                    html: 'Chat name cannot be empty',
                    timer: 1500
                })

                return;
            }

            axios.patch(route('chats.update', this.chat), {
                title: this.newTitle,
            }).then(res => {
                if (res.data.status === 'success') {
                    this.chat.title = this.newTitle
                    this.$swal({
                        icon: 'success',
                        html: res.data.message,
                        timer: 1500
                    })
                } else {
                    this.$swal({
                        icon: 'error',
                        html: res.data.message,
                        timer: 1500
                    })
                }
            })
        }
    },

    computed: {
        // Передаём на бэк id всех юзеров чата, кроме залогиненного, для передачи в метод сохранения сообщений
        userIds() {
            return this.users.map(user => {
                return user.id
            }).filter(userId => {
                return userId !== this.$page.props.auth.user.id
            })
        },
    },

    layout: Main,
}
</script>

<template>
    <div class="flex items-start">
        <div class="p-4 w-3/4 bg-white border border-gray-200 mr-4 rounded-lg">
            <!-- TODO: Добавить возможность менять название чата  -->
            <div class="flex items-center mb-5">
                <div class="flex items-center" v-if="showTitleInput === false">
                    <h3 class="inline-block text-lg text-gray-600">{{ chat.title }}</h3>
                    <a @click.prevent="toggleChatEdit"
                       class="inline-block bg-sky-500 hover:bg-sky-300 text-white text-xs px-3 py-2 ml-4 rounded-lg"
                       href="#">
                        Edit title
                    </a>
                </div>

                <div class="flex items-center" v-if="showTitleInput === true">
                    <input
                        v-if="showTitleInput"
                        class="rounded-full block p-4 text-gray-900 border border-gray-300 rounded-lg
                            bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                        type="text"
                        :placeholder="newTitle"
                        v-model="newTitle"
                    >
                    <button @click.prevent="editChat"
                            class="mr-2 ml-2 px-4 py-2 text-sm rounded text-white bg-sky-400 focus:outline-none hover:bg-sky-300">
                        Save
                    </button>
                    <button @click.prevent="toggleChatEdit"
                            class="mr-2 px-4 py-2 text-sm rounded text-white bg-yellow-400 focus:outline-none hover:bg-yellow-300">
                        Cancel
                    </button>
                </div>

            </div>

            <div class="mb-4" v-if="messages">
                <div v-for="message in messages" :class="['pb-4 text-sm',
                        message.is_owner ? 'text-right' : 'text-left']">
                    <div :class="['p-4 my-2 border rounded inline-block',
                        message.is_owner ? 'bg-sky-50 border-sky-100' : 'bg-yellow-50 border-yellow-100']"
                    >
                        <p class="font-bold">{{ message.user_name }}</p>
                        <p class="my-4">{{ message.body }}</p>
                        <p class="italic">{{ message.time }}</p>
                    </div>


                </div>
            </div>

            <div>
                <h3 class="mb-4 text-lg text-gray-600">Send a message</h3>
                <div>
                    <div class="mb-4">
                        <input
                            class="rounded-full block w-full p-4 text-gray-900 border border-gray-300 rounded-lg
                                bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500"
                            type="text"
                            v-model="body"
                            placeholder="Write a message.."
                        >
                    </div>
                    <div>
                        <a @click.prevent="store()"
                           class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-400"
                           href="#">
                            Send
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 w-1/4 bg-white border border-gray-200 rounded-lg">
            <h3 class="mb-4 text-lg text-gray-600">Chat users:</h3>
            <div v-if="users">
                <div v-for="(user, index) in users"
                     :class="['flex items-center pb-4', (index !== users.length - 1) ? 'border-b border-gray-300 mb-4' : '']"
                >
                    <p>{{ user.name }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
