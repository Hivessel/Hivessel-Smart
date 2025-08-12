<template>
    <section class="page--login">
        <div class="wrapper">
            <h1 class="page-heading">Hivessel Smart</h1>
            <div class="page--login_form form--container">
                <h3 class=""><b>Sign with your Hivessel Account</b></h3>
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
                        <div class="col-md-12 form-group">
                            <label for="password" class="form-label d-flex align-items-center justify-content-between">
                                <span>Password</span>
                                <a class="forgotpassword" href="https://hivessel.com/my-account/lost-password/" target="_blank">Forgot your password?</a>
                            </label>
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                v-model="credentials.password"
                                autocomplete="current-password"
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
                <div class="form-footer-link">
                    <p><a class="ssolink" href="https://hivessel.com/register" target="_blank">Don't have an account?</a></p>
                    <p></p>
                    <div class="listing">
                        <span><a href="https://hivessel.com/" target="_blank">© Hivessel</a></span>
                        <span><a href="#">Contact</a></span>
                        <span><a href="https://hivessel.com/privacy-policy/" target="_blank">Privacy & terms</a></span>
                    </div>
                    </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3'

const credentials = useForm({
    username: '',
    password: ''
})

const login = () => {
    credentials.post(route('custom-login'), {
        onSuccess: () => {
            router.get(route('client.home'));
        },
        onError: () => {
            credentials.reset('password')
            toastr.error('Credentials did not match to our records!');
        }
    });
}
</script>

