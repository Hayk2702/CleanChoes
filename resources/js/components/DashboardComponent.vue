<template>
    <div>
        <div>
            <div id="sidebar_menu" class="metismenu" style="    display: flex;
    flex-direction: row;
    flex-wrap: wrap;">

            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2/dist/sweetalert2.js'
import 'sweetalert2/src/sweetalert2.scss'
import Multiselect from 'vue-multiselect'
import PaginationComponent from "./PaginationComponent";
import {PageName} from '../PageName';
import axios from "axios";

export default {

    components: {
        Swal,
        Multiselect,
        PaginationComponent,

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

    },

    data() {
        return {
            lang: this.locale,
            authUser: this.authuser,
        }
    },

    mounted() {
        PageName.$emit('updatePageName', this.__('variable.home'));

    },

    created() {
    },

    methods: {

        getRout(name, param = '') {

            return getRout(name, param)
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
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>
