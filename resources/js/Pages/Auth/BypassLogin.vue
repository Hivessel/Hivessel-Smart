<template>
    <section class="page--login">
        <div class="wrapper">
            <h1 class="page-heading">Hivessel Smart</h1>
            <div class="page--login_form form--container">
                <h3 class="text-center"><b>Sign in with your Hivessel Account</b></h3>
                 <form @submit.prevent="login" id="hivessel-smart-login">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="email">Username</label>
                            <input
                                id="email"
                                type="text"
                                class="form-control"
                                v-model="credentials.username"
                                autocomplete="username"
                            />
                        </div>
                        
                        <div class="col-md-12">
                             <button class="btn--primary btn--submit" type="submit" :disabled="credentials.processing">
                                Login
                                <span v-if="credentials.processing" class="text-white spinner-border spinner-border-sm text-dark" role="status">
                                        </span>
                            </button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
    </section>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3'
import Layout from '../Admin/Shared/Layout.vue'
const credentials = useForm({
    username: '',
})

defineOptions({
    layout: Layout
})

const login = () => {
    credentials.post(route('admin.login-as-process'), {
        onSuccess: (response) => {
            router.get(route('client.home'));
        },
        onError: () => {
            credentials.reset('username')
        }
    });
}
</script>

