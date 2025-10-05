<template>
    <div>
        <nav class="sidebar dark_sidebar">
            <div class="logo d-flex justify-content-between" style="background: #fdfdfd">
                <router-link class="large_logo" style="color: white; font-size: 26px;" :to="`/${lang}/dashboard`">
                    <img src="/img/app_icon.png" alt=""></router-link>
                <router-link class="small_logo" style="color: white; font-size: 18px;" :to="`/${lang}/dashboard`">
                    <img style="width: 100px;height: 50px; object-fit: contain;" src="/img/app_icon.png" alt=""></router-link>
                <div class="sidebar_close_icon d-lg-none">
                    <i class="ti-close"></i>
                </div>
            </div>
            <ul id="sidebar_menu" class="metismenu">
                <li class="">
                    <router-link :to="`/${lang}/dashboard/contacts`">
                        <div class="nav_icon_small">
                        </div>
                        <div class="nav_title">
                            <span>{{ __('variable.contacts') }} </span>
                        </div>
                    </router-link>
                </li>
                <li class="">
                    <router-link :to="`/${lang}/dashboard/categories`">
                        <div class="nav_icon_small">
                        </div>
                        <div class="nav_title">
                            <span>{{ __('variable.categories') }} </span>
                        </div>
                    </router-link>
                </li>
                <li class="">
                    <router-link :to="`/${lang}/dashboard/ourworks`">
                        <div class="nav_icon_small">
                        </div>
                        <div class="nav_title">
                            <span>{{ __('variable.ourworks') }} </span>
                        </div>
                    </router-link>
                </li>
                <li class="">
                    <router-link :to="`/${lang}/dashboard/workphotos`">
                        <div class="nav_icon_small">
                        </div>
                        <div class="nav_title">
                            <span>{{ __('variable.workphotos') }} </span>
                        </div>
                    </router-link>
                </li>
                <li class="">
                    <router-link :to="`/${lang}/dashboard/reviews`">
                        <div class="nav_icon_small">
                        </div>
                        <div class="nav_title">
                            <span>{{ __('variable.reviews') }} </span>
                        </div>
                    </router-link>
                </li>
            </ul>
        </nav>

        <section class="main_content dashboard_part large_header_bg">
            <div class="container-fluid g-0">
                <div class="row">
                    <div class="col-lg-12 p-0 ">
                        <div class="header_iner d-flex justify-content-between align-items-center">
                            <div class="nav_div">
                                <div class="sidebar_icon d-lg-none">
                                    <i class="ti-menu"></i>
                                </div>
                                <div class="line_icon open_miniSide d-none d-lg-block">
                                    <img :src="'/img/line_img.png'" style="width: 24px;height: 24px" alt="">
                                </div>
                                <div v-if="currentRouteName == 'Dashboard'" class="languages_div">
                                    <a class="language_a" @click="setLanguage('ru')">
                                        <img style="width: 24px ; height: 24px" :src="'/images/menu/ru.png'"/>
                                    </a>
<!--                                    <a class="language_a" @click="setLanguage('ru')">
                                        <img style="width: 24px ; height: 24px" :src="'/images/menu/ru.png'"/>
                                    </a>-->
                                    <a class="language_a" @click="setLanguage('en')">
                                        <img style="width: 24px ; height: 24px" :src="'/images/menu/en.png'"/>
                                    </a>
                                </div>
                            </div>
                            <div><span class="title_span">{{ pageName }}</span></div>
                            <div class="header_right d-flex justify-content-between align-items-center">
                                <div class="header_notification_warp d-flex align-items-center"></div>
                                <div class="profile_info d-flex align-items-center">
                                    <div class="profile_thumb mr_20"></div>
                                    <div class="author_name">
                                        <h4 class="f_s_15 f_w_500 mb-0">{{ authUser['name'] }}</h4>
                                    </div>
                                    <div class="profile_info_iner">
                                        <div class="profile_info_details" @click.prevent="logout">
                                            <a href="#">
                                                {{ __(('variable.exit')) }}
                                            </a>
                                            <span class="f_s_15 f_w_500 mb-0 authUserSpan">{{ authUser['name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main_content_iner overly_inner ">
                <div class="container-fluid p-0 ">
                    <div class="">
                        <router-view :locale="lang" :authuser="authUser" />
                    </div>
                </div>
            </div>
            <div class="footer_part">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="footer_iner text-center">
                                <p>2024 © <a href="https://itr.am/"> <i class="ti-heart"></i> </a><a
                                    href="https://itr.am/">ITResources</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
import Multiselect from 'vue-multiselect';
import {PageName} from '../PageName';

export default {

    components: {
        Swal,
        Multiselect,
    },

    props: [
        'locale',
        'authuser',
    ],

    watch: {
        locale: function (newVal, oldVal) {
            this.lang = newVal;
        },
        authuser: function (newVal, oldVal) {
            this.authUser = newVal;
        },
        $route(to, from) {
            this.pageName = 'ITR';
        }
    },

    data() {
        return {
            lang: this.locale,
            authUser: this.authuser,
            pageName: 'CleanChoes',
        }
    },

    mounted() {

    },

    created() {
        PageName.$on('updatePageName', data => {
            this.pageName = data ? data : 'CleanChoes';
        });

    },

    methods: {

        getRout(name, param = '') {

            return getRout(name, param)
        },

        logout() {
            let self = this;
            axios.post(route('logout', {locale: self.lang}))
                .then(response => {
                    this.$router.go('Dashboard')
                })
                .catch(error => {
                    this.showCatchError(error);
                    // handle logout error
                })
            // window.location.href = this.getRout('login',{locale:this.lang});

        },

        showCatchError(err) {
            if (err.response && err.response.data) {
                Swal.fire({
                    icon: 'error',
                    title: `${err.response.data.message}`,
                    confirmButtonText: this.__('variable.ok')
                });
            }
        },

        setLanguage(language) {
            let self = this;
            axios.post(route('setLocale', {locale: this.lang, lang: language}))
                .then((data) => {
                    if (data && data.data && data.data.isSuccess) {
                        window.location.href = data.data.redirectUrl;
                    }
                })
                .catch((err) => {
                    self.showCatchError(err);
                });
        },
    },

    computed: {
        currentRouteName() {
            return this.$route.name;
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
.nav_div {
    display: flex;
    justify-content: space-around;
}

.languages_div {
    padding-left: 10px
}

.language_a {
    cursor: pointer;
}
</style>
