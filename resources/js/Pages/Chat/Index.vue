<script>
import { Link } from "@inertiajs/vue3"
import Main from "@/Layouts/Main.vue"
import Modal from "@/Components/CustomModal.vue";

export default {
    name: 'Index',

    props: [
        'users',
        'chats'
    ],

    data() {
        return {
            selectedUsers: {},
            showCreateGroupChatModal: false,
            createGroupChatData: {
                userIds: [],
                chatTitle: ''
            },
            isGroup: false,
        }
    },

    components: {
        Modal,
        Link,
    },

    layout: Main,

    methods: {
        createChat(user = null) {
            if (this.isGroup) {
                if (this.createGroupChatData.userIds.length === 0) {
                    this.$swal({
                        icon: 'error',
                        html: 'Select at least one user',
                        timer: 1500
                    })

                    return;
                }

                this.sendCreateChatRequest({ ...this.createGroupChatData.userIds }, this.createGroupChatData.chatTitle)

                return;
            }

            let userId = user.id;
            let title = `${user.name} - ${this.$page.props.auth.user.name} chat`
            this.sendCreateChatRequest({ userId }, title)
        },

        sendCreateChatRequest(users, title) {
            axios.post(route('chats.store'), {
                users: users,
                title: title,
                isGroup: this.isGroup
            }).then(res => {
                if (res.data.status === 'success') {
                    this.chats.push(res.data.chat)
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
            });
        },

        deleteChat(chat) {
            if (!chat.canDelete) {
                this.$swal({
                    icon: 'error',
                    html: 'You cannot delete this chat!',
                    timer: 1500
                });

                return;
            }

            this.$swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(route('chats.destroy', chat))
                        .then(res => {
                            if (res.data.status === 'success') {
                                // Удаляем чат из списка чатов
                                const deleteChatIndex = this.chats.findIndex(p => p.id === chat.id)
                                this.chats.splice(deleteChatIndex, 1)
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
            });
        }
    }
}
</script>

<template>
    <div class="flex items-start">
        <!-- Chats section -->
        <div class="p-4 w-1/2 bg-white border border-gray-200 mr-4 rounded-lg">
            <h3 class="mb-4 text-lg text-gray-600">My Chats:</h3>
            <div v-if="chats.length > 0">
                <div v-for="(chat, index) in chats"
                     :class="['flex items-center pb-4',
                        (index !== chats.length - 1) ? 'border-b border-gray-300 mb-4' : '']">
                    <Link :href="route('chats.show', chat.id)" class="flex hover:text-gray-400">
                        <p class="mr-2">{{ ++index }}.</p>
                        <p>{{ chat.title ?? 'Unnamed chat' }}</p>
                    </Link>
                    <a @click.prevent="deleteChat(chat)"
                       class="ml-auto inline-block bg-red-500 hover:bg-red-300 text-white text-xs px-3 py-2 ml-4 rounded-lg"
                       href="#">
                        Delete
                    </a>
                </div>
            </div>
            <div v-else>No Chats Found</div>
        </div>

        <!-- Users section -->
        <div class="p-4 w-1/2 bg-white border border-gray-200 rounded-lg">
            <div class="flex justify-between items-center">
                <h3 class="mb-4 text-lg text-gray-600">Users:</h3>
                <a @click="showCreateGroupChatModal = true"
                   class="inline-block bg-indigo-600 hover:bg-indigo-400 text-white text-xs px-3 py-2 rounded-lg"
                   href="#">
                    Make a group chat
                </a>
            </div>
            <div v-if="users.length > 0">
                <div v-for="(user, index) in users"
                     :class="['flex items-center pb-4',
                        (index !== users.length - 1) ? 'border-b border-gray-300 mb-4' : '']">
                    <p>{{ user.name }}</p>
                    <a @click.prevent="createChat(user)"
                       @click="this.isGroup = false"
                       class="inline-block bg-sky-500 hover:bg-sky-300 text-white text-xs px-3 py-2 ml-4 rounded-lg"
                       href="#">
                        Message
                    </a>
                </div>
            </div>
            <div v-else>No Users Found</div>
        </div>
    </div>

    <Modal v-if="showCreateGroupChatModal"
           title="Add users to a new chat"
           width="md"
           v-on:close="showCreateGroupChatModal = false"
    >
        <div class="mb-4">
            <h5 class="mb-4 text-md">Chat's name:</h5>
            <input
                class="rounded-full block w-full p-4 text-gray-900 border border-gray-300 rounded-lg
                    bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500"
                type="text"
                v-model="createGroupChatData.chatTitle"
                placeholder="Chat's name..."
            >
        </div>


        <h5 class="mb-3 text-md">Select users:</h5>
        <div v-for="user in users" class="flex mb-2 items-center pb-2">
            <div class="flex items-center">
                <input
                    :id="'group_chat_user_id_' + user.id"
                       type="checkbox"
                       :value="user.id"
                       v-model="createGroupChatData.userIds"
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                        dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label :for="'group_chat_user_id_' + user.id" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ user.name }}</label>
            </div>
        </div>

        <div class="text-right mt-6 flex justify-between">
            <button @click="showCreateGroupChatModal = false" class="mr-2 px-4 py-2 text-sm rounded text-white bg-yellow-400 focus:outline-none hover:bg-yellow-300">Cancel</button>
            <button @click.prevent="createChat" @click="this.isGroup = true" class="mr-2 px-4 py-2 text-sm rounded text-white bg-sky-400 focus:outline-none hover:bg-sky-300">Save</button>
        </div>
    </Modal>

</template>

<style scoped>

</style>
