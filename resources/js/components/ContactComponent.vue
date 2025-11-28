<template>
    <div>
        <div>

        </div>

        <div class="row justify-content-center" :style="{ 'pointer-events': isLoading ? 'none' : 'auto' }">
            <div>
                <b-row class="justify-content-end">
                    <b-col sm="5" md="5" class="d-flex table_navigate justify-content-end pb-1">
                        <b-button
                            size="sm"
                            class="addButton toggleTableBtm"
                            @click="toggleTable"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-border-all" viewBox="0 0 16 16">
                                <path
                                    d="M0 0h16v16H0V0zm1 1v6.5h6.5V1H1zm7.5 0v6.5H15V1H8.5zM15 8.5H8.5V15H15V8.5zM7.5 15V8.5H1V15h6.5z"/>
                            </svg>
                        </b-button>

                    </b-col>
                </b-row>

                <div class="parent_card_pagination">
                    <div class="card">
                        <div>
                            <b-table
                                :class="showTableLine ? 'table_line': 'table_grid'"
                                :stacked="!showTableLine"
                                :responsive="showTableLine"
                                ref="table"
                                :busy="isBusy"
                                :hover="showTableLine"
                                :items="getData"
                                :fields="fields"
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                :apiUrl="getRout('contacts.index',{locale : lang})"
                                :per-page="paginate.perPage"
                                :current-page="paginate.currentPage"
                                :striped="showTableLine"
                                :small="showTableLine"
                                :bordered="showTableLine"
                                :filter="filter"
                                @row-clicked="showAddOrEditForm($event, 'edit')"
                            >
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>{{ __(('variable.loaded')) }}</strong>
                                    </div>
                                </template>

                                <template v-if="!isLoading" #cell(actions)="row">
                                    <a class="btn btn-inverse-warning p-1 a_position"
                                       href="javascript:void(0)"
                                       @click="showAddOrEditForm(row.item, 'edit')">
                                        <i class="bi bi-pencil title_hov_top" :data-title="__(('variable.edit'))">
                                            <svg v-b-tooltip.hover xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path
                                                    d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </i>
                                    </a>
                                </template>
                            </b-table>
                        </div>
                    </div>

                    <PaginationComponent
                        ref="PaginationComponent"
                        v-if="Object.keys(contacts).length > 0"
                        :paginate="paginate"
                        @loadAfterChangePage="loadAfterChangePage"
                    />
                </div>

                <b-modal
                    class="addModal"
                    v-model="show"
                    :header-text-variant="headerTextVariant"
                    :header-bg-variant="headerBgVariant"
                >
                    <template #modal-title>
                        <b-row>
                            <b-col sm="10" md="10" class="title_popup fs-15 lh-2">
                                {{
                                ((addOrEdit == 'add') ? __(('variable.add')) : __(('variable.edit')))
                                + " " + __(('variable.contact'))
                                }}
                            </b-col>
                        </b-row>
                    </template>

                    <b-container fluid>
                        <!-- telegram -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.telegram')">
                                <b-form-input
                                    :class="[errors['telegram'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.telegram"
                                    type="text"
                                    :placeholder="__('variable.telegram')"
                                />
                                <small v-if="errors['telegram']" class="error-msg">{{ errors['telegram'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- whatsapp -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.whatsapp')">
                                <b-form-input
                                    :class="[errors['whatsapp'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.whatsapp"
                                    type="text"
                                    :placeholder="__('variable.whatsapp')"
                                />
                                <small v-if="errors['whatsapp']" class="error-msg">{{ errors['whatsapp'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- address -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.address')">
                                <b-form-input
                                    :class="[errors['address'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.address"
                                    type="text"
                                    :placeholder="__('variable.address')"
                                />
                                <small v-if="errors['address']" class="error-msg">{{ errors['address'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- email -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.email')">
                                <b-form-input
                                    :class="[errors['email'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.email"
                                    type="email"
                                    :placeholder="__('variable.email')"
                                />
                                <small v-if="errors['email']" class="error-msg">{{ errors['email'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- phone -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.phone')">
                                <b-form-input
                                    :class="[errors['phone'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.phone"
                                    type="text"
                                    :placeholder="__('variable.phone')"
                                />
                                <small v-if="errors['phone']" class="error-msg">{{ errors['phone'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- facebook -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.facebook')">
                                <b-form-input
                                    :class="[errors['facebook'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.facebook"
                                    type="text"
                                    :placeholder="__('variable.facebook')"
                                />
                                <small v-if="errors['facebook']" class="error-msg">{{ errors['facebook'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- instagram -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.instagram')">
                                <b-form-input
                                    :class="[errors['instagram'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.instagram"
                                    type="text"
                                    :placeholder="__('variable.instagram')"
                                />
                                <small v-if="errors['instagram']" class="error-msg">{{ errors['instagram'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- youtube -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.youtube')">
                                <b-form-input
                                    :class="[errors['youtube'] ? 'error-border': '', 'addFormInputs']"
                                    v-model="contact.youtube"
                                    type="text"
                                    :placeholder="__('variable.youtube')"
                                />
                                <small v-if="errors['youtube']" class="error-msg">{{ errors['youtube'] }}</small>
                            </b-col>
                        </b-row>

                        <!-- lat / lng -->
                        <b-row class="mb-1 bRowPosition">
                            <b-col cols="6" class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.lat')">
                                <b-form-input
                                    :class="[errors['lat'] ? 'error-border': '', 'addFormInputs']"
                                    v-model.number="contact.lat"
                                    type="number"
                                    step="0.00000001"
                                    :placeholder="__('variable.lat')"
                                />
                                <small v-if="errors['lat']" class="error-msg">{{ errors['lat'] }}</small>
                            </b-col>
                            <b-col cols="6" class='blockInput title_hov_center' v-b-tooltip.hover :data-title="__('variable.lng')">
                                <b-form-input
                                    :class="[errors['lng'] ? 'error-border': '', 'addFormInputs']"
                                    v-model.number="contact.lng"
                                    type="number"
                                    step="0.00000001"
                                    :placeholder="__('variable.lng')"
                                />
                                <small v-if="errors['lng']" class="error-msg">{{ errors['lng'] }}</small>
                            </b-col>
                        </b-row>
                    </b-container>

                    <template #modal-footer>
                        <div class="w-100 handle_button">
                            <b-button
                                :disabled="buttonDisabled"
                                size="sm"
                                class="float-right addButton"
                                @click="sendData()"
                            >
                                {{ ((addOrEdit == 'add') ? __(('variable.add')) : __(('variable.edit'))) }}
                            </b-button>
                        </div>
                    </template>
                </b-modal>
            </div>
        </div>

        <div v-if="isLoading" class="loading-overflow">
            <vue-loading class="loading" type="balls" color="#d9544e"
                         :size="{ width: '100px', height: '100px' }"></vue-loading>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
import { VueLoading } from 'vue-loading-template'
import Multiselect from 'vue-multiselect';
import PaginationComponent from "./PaginationComponent";
import SearchComponent from "./SearchComponent";
import { PageName } from '../PageName';

export default {
    name: "contactsComponent",

    components: {
        Swal,
        Multiselect,
        PaginationComponent,
        SearchComponent,
        VueLoading,
    },

    props: ['locale','authuser'],

    data() {
        return {
            // default
            lang: this.locale,
            authUser: this.authuser,
            paginate: {
                perPage: 10,
                currentPage: 1,
                total: '',
                currentPageInput: '',
                lastPage: '',
            },
            errors: {},
            // table
            showTableLine: false,
            fields: [
                { key: 'address',   label: this.__('variable.address'),   sortable: true },
                { key: 'phone',     label: this.__('variable.phone'),     sortable: true },
                { key: 'email',     label: this.__('variable.email'),     sortable: true },
                { key: 'telegram',  label: 'Telegram',                    sortable: true },
                { key: 'whatsapp',  label: 'WhatsApp',                    sortable: true },
                { key: 'facebook',  label: 'Facebook',                    sortable: true },
                { key: 'instagram', label: 'Instagram',                   sortable: true },
                { key: 'youtube',   label: 'YouTube',                     sortable: true },
                { key: 'lat',       label: 'Lat',                         sortable: true },
                { key: 'lng',       label: 'Lng',                         sortable: true },

            ],
            isBusy: false,
            total: '',
            sortBy: 'id',
            sortDesc: false,
            filter: [{ text: '', key: '' }],

            // modal
            show: false,
            headerBgVariant: 'custom',
            headerTextVariant: 'white',

            // data
            contacts: [],
            contact: {
                telegram: '',
                whatsapp: '',
                address: '',
                email: '',
                phone: '',
                facebook: '',
                instagram: '',
                youtube: '',
                lat: null,
                lng: null,
            },
            addOrEdit: 'add',
            buttonDisabled: false,
            isLoading: false,
        }
    },

    watch: {
        locale(newVal){ this.lang = newVal; },
        authuser(newVal){ this.authUser = newVal; },
        filter: {
            handler(){ this.paginate.currentPage = 1; },
            deep: true
        }
    },

    mounted() {
        PageName.$emit('updatePageName', this.__('variable.contact')); // set page title
    },

    methods: {
        updateFilter(newFilter) { this.filter = newFilter; },
        toggleTable() { this.showTableLine = !this.showTableLine; },

        loadAfterChangePage(currentPage, perPage) {
            if (currentPage) this.paginate.currentPage = currentPage;
            if (perPage) this.paginate.perPage = perPage;
        },

        getData(data) {
            this.isBusy = true;
            const self = this;
            return axios
                .get(route('contacts.index', { locale: self.lang }) + `?page=${data.currentPage}`, { params: data })
                .then((resp) => {
                    if (resp && resp.data && resp.data.data) {
                        self.contacts = resp.data.data;
                        self.paginate.rows = resp.data.total;
                        self.paginate.total = resp.data.total;
                        self.paginate.currentPage = resp.data.current_page;
                        self.paginate.currentPageInput = self.paginate.currentPage;
                        self.paginate.lastPage = resp.data.last_page;
                        self.isBusy = false;
                        return self.contacts;
                    }
                })
                .catch((err) => {
                    self.isBusy = false;
                    self.showCatchError(err);
                });
        },

        sendData() {
            if (this.buttonDisabled) return;
            this.buttonDisabled = true;

            const payload = { ...this.contact };
            this.errors = {};

            axios.post(route('contacts.store', { locale: this.lang }), payload)
                .then((res) => {
                    if (res && res.data && res.data.isSuccess) {
                        this.show = false;
                        this.resetData(false); // keep pagination
                        this.$refs.table && this.$refs.table.refresh();
                        Swal.fire({
                            title: this.__('variable.success'),
                            icon: 'success',
                            timer: 600,
                            showConfirmButton: false
                        });
                    }
                    this.buttonDisabled = false;
                })
                .catch((err) => {
                    if (err && err.response && err.response.data) {
                        const errors = err.response.data;
                        for (let k in errors) {
                            this.$set(this.errors, k, errors[k][0]);
                        }
                    }
                    this.buttonDisabled = false;
                });
        },

        showAddOrEditForm(obj = '', addOrEdit = 'add') {
            this.errors = {};
            this.addOrEdit = addOrEdit;

            // if there’s already a contact in the list, prefilling for edit helps
            // backend stores/updates the first record anyway
            this.contact = {
                telegram:  obj ? obj.telegram  : (this.contacts[0]?.telegram  || ''),
                whatsapp:  obj ? obj.whatsapp  : (this.contacts[0]?.whatsapp  || ''),
                address:   obj ? obj.address   : (this.contacts[0]?.address   || ''),
                email:     obj ? obj.email     : (this.contacts[0]?.email     || ''),
                phone:     obj ? obj.phone     : (this.contacts[0]?.phone     || ''),
                facebook:  obj ? obj.facebook  : (this.contacts[0]?.facebook  || ''),
                instagram: obj ? obj.instagram : (this.contacts[0]?.instagram || ''),
                youtube:   obj ? obj.youtube   : (this.contacts[0]?.youtube   || ''),
                lat:       obj ? obj.lat       : (this.contacts[0]?.lat       ?? null),
                lng:       obj ? obj.lng       : (this.contacts[0]?.lng       ?? null),
            };

            this.show = true;
        },

        resetData(resetPaging = true) {
            this.errors = {};
            this.contacts = [];
            this.contact = {
                telegram: '',
                whatsapp: '',
                address: '',
                email: '',
                phone: '',
                facebook: '',
                instagram: '',
                youtube: '',
                lat: null,
                lng: null,
            };
            this.addOrEdit = 'add';
            this.buttonDisabled = false;
            this.show = false;

            if (resetPaging) {
                this.paginate.currentPage = 1;
            }
        },

        getRout(param) { return getRout(param); },

        showCatchError(err) {
            if (err?.response?.data) {
                Swal.fire({
                    icon: 'error',
                    title: `${err.response.data.message}`,
                    confirmButtonText: this.__('variable.ok')
                });
            }
        },
    }
}
</script>
