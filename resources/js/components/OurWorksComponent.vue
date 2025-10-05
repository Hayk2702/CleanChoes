<template>
    <div>
        <div class="row justify-content-center" :style="{ 'pointer-events': isLoading ? 'none' : 'auto' }">
            <div>
                <b-row class="justify-content-end">
                    <b-col sm="6" md="6" class="d-flex table_navigate justify-content-end pb-1">
                        <b-button size="sm" class="addButton toggleTableBtm" @click="toggleTable">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-border-all" viewBox="0 0 16 16">
                                <path d="M0 0h16v16H0V0zm1 1v6.5h6.5V1H1zm7.5 0v6.5H15V1H8.5zM15 8.5H8.5V15H15V8.5zM7.5 15V8.5H1V15h6.5z"/>
                            </svg>
                        </b-button>

                        <div class="col-md-2 d-flex justify-content-end">
                            <a class="btn btnAdd" href="javascript:void(0)" @click="openAddModal">
                                {{ __(('variable.add')) }}
                            </a>
                        </div>
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
                                :items="getItems"
                                :fields="fields"
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                :apiUrl="getRout('ourworks.index',{locale : lang})"
                                :per-page="paginate.perPage"
                                :current-page="paginate.currentPage"
                                :striped="showTableLine"
                                :small="showTableLine"
                                :bordered="showTableLine"
                                :filter="filter"
                            >
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>{{ __(('variable.loaded')) }}</strong>
                                    </div>
                                </template>

                                <template #cell(image_path_left)="{ item }">
                                    <img v-if="item.image_path_left" :src="item.image_path_left" alt="left" style="max-height:60px; max-width:120px; object-fit:cover;">
                                </template>

                                <template #cell(image_path_right)="{ item }">
                                    <img v-if="item.image_path_right" :src="item.image_path_right" alt="right" style="max-height:60px; max-width:120px; object-fit:cover;">
                                </template>

                                <template #cell(actions)="{ item }">
                                    <button class="btn btn-danger p-1 a_position"
                                            @click="destroyItem(item.id)">
                                        <i class="bi bi-trash title_hov_top" :data-title="__(('variable.remove'))">
                                            <svg v-b-tooltip.hover
                                                 xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="red"
                                                 class="bi bi-trash" viewBox="0 0 16 16">
                                                <path
                                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd"
                                                      d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                            </svg>
                                        </i>
                                    </button>

                                </template>
                            </b-table>
                        </div>
                    </div>

                    <PaginationComponent
                        ref="PaginationComponent"
                        v-if="(items && items.length)"
                        :paginate="paginate"
                        @loadAfterChangePage="loadAfterChangePage"
                    />
                </div>

                <!-- Add Modal -->
                <b-modal v-model="show" :title="__('variable.add') + ' ' + ('OurWorks')" :header-text-variant="headerTextVariant" :header-bg-variant="headerBgVariant">
                    <b-container fluid>
                        <b-row class="mb-2">
                            <b-col class="blockInput">
                                <label class="mb-1">{{ __('variable.image') || 'Left image' }}</label>
                                <b-form-file @change="onLeftChange" :state="leftFile ? true : null" accept="image/*" browse-text="Choose"/>
                                <div v-if="leftPreview" class="mt-2">
                                    <img :src="leftPreview" style="max-width:200px; max-height:140px; object-fit:cover;">
                                </div>
                                <small v-if="errors['image_path_left']" class="error-msg">{{ errors['image_path_left'] }}</small>
                            </b-col>
                        </b-row>

                        <b-row class="mb-2">
                            <b-col class="blockInput">
                                <label class="mb-1">{{ __('variable.image') || 'Right image' }}</label>
                                <b-form-file @change="onRightChange" :state="rightFile ? true : null" accept="image/*" browse-text="Choose"/>
                                <div v-if="rightPreview" class="mt-2">
                                    <img :src="rightPreview" style="max-width:200px; max-height:140px; object-fit:cover;">
                                </div>
                                <small v-if="errors['image_path_right']" class="error-msg">{{ errors['image_path_right'] }}</small>
                            </b-col>
                        </b-row>
                    </b-container>

                    <template #modal-footer>
                        <b-button :disabled="buttonDisabled" variant="primary" @click="submit">
                            {{ __(('variable.add')) }}
                        </b-button>
                    </template>
                </b-modal>
            </div>
        </div>

        <div v-if="isLoading" class="loading-overflow">
            <vue-loading class="loading" type="balls" color="#d9544e" :size="{ width: '100px', height: '100px' }"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
import { VueLoading } from 'vue-loading-template';
import Multiselect from 'vue-multiselect';
import PaginationComponent from './PaginationComponent';
import SearchComponent from './SearchComponent';
import { PageName } from '../PageName';

export default {
    name: 'ourWorksComponent',
    components: { Swal, Multiselect, PaginationComponent, SearchComponent, VueLoading },
    props: ['locale','authuser'],

    data() {
        return {
            lang: this.locale,
            authUser: this.authuser,
            paginate: { perPage: 10, currentPage: 1, total: '', currentPageInput: '', lastPage: '' },
            errors: {},

            showTableLine: false,
            fields: [
                { key: 'id',               label: this.__('variable.id'), sortable: true },
                { key: 'image_path_left',  label: (this.__('variable.image')) + ' (L)', sortable: false },
                { key: 'image_path_right', label: (this.__('variable.image')) + ' (R)', sortable: false },
                { key: 'actions',          label: this.__('variable.action'), sortable: false },
            ],
            isBusy: false,
            sortBy: 'id',
            sortDesc: true,
            filter: [{ text: '', key: '' }],

            // add modal
            show: false,
            headerBgVariant: 'custom',
            headerTextVariant: 'white',
            buttonDisabled: false,

            leftFile: null,
            rightFile: null,
            leftPreview: '',
            rightPreview: '',

            items: [],

            isLoading: false,
        };
    },

    watch: {
        locale(v){ this.lang = v; },
        authuser(v){ this.authUser = v; },
        filter: { handler(){ this.paginate.currentPage = 1; }, deep: true },
    },

    mounted(){ PageName.$emit('updatePageName', 'OurWorks'); },

    methods: {
        getRout(param){ return getRout(param); },
        updateFilter(n){ this.filter = n; },
        toggleTable(){ this.showTableLine = !this.showTableLine; },
        openAddModal(){ this.resetForm(); this.show = true; },
        loadAfterChangePage(current, per){ if(current) this.paginate.currentPage = current; if(per) this.paginate.perPage = per; },

        // list
        getItems(ctx){
            this.isBusy = true;
            return axios.get(route('ourworks.index', { locale: this.lang }) + `?page=${ctx.currentPage}`, { params: ctx })
                .then(resp => {
                    const r = resp?.data || null;
                    if(r && r.data){
                        this.items = r.data;
                        this.paginate.rows = r.total;
                        this.paginate.total = r.total;
                        this.paginate.currentPage = r.current_page;
                        this.paginate.currentPageInput = this.paginate.currentPage;
                        this.paginate.lastPage = r.last_page;
                    }
                    this.isBusy = false;
                    return this.items;
                })
                .catch(err => { this.isBusy = false; this.showCatchError(err); });
        },

        // add
        onLeftChange(e){ const f = e.target.files?.[0]; this.leftFile = f || null; this.leftPreview = f ? URL.createObjectURL(f) : ''; },
        onRightChange(e){ const f = e.target.files?.[0]; this.rightFile = f || null; this.rightPreview = f ? URL.createObjectURL(f) : ''; },

        submit(){
            if(this.buttonDisabled) return;
            this.errors = {};
            if(!this.leftFile && !this.rightFile){
                this.errors.image_path_left = this.__('variable.required') || 'At least one image is required';
                return;
            }
            this.buttonDisabled = true;

            const form = new FormData();
            if(this.leftFile)  form.append('image_left', this.leftFile);
            if(this.rightFile) form.append('image_right', this.rightFile);

            axios.post(route('ourworks.store', { locale: this.lang }), form, { headers: { 'Content-Type':'multipart/form-data' }})
                .then(res => {
                    if(res?.data?.isSuccess){
                        this.show = false;
                        this.$refs.table && this.$refs.table.refresh();
                        Swal.fire({ title: this.__('variable.success'), icon: 'success', timer: 600, showConfirmButton: false });
                    }
                    this.buttonDisabled = false;
                })
                .catch(err => {
                    this.buttonDisabled = false;
                    if(err?.response?.data){
                        const errors = err.response.data;
                        Object.keys(errors).forEach(k => this.$set(this.errors, k, errors[k][0] || errors[k]));
                    }
                });
        },

        destroyItem(id){
            Swal.fire({
                title: this.__('variable.sure'), icon: 'warning', showCancelButton: true,
                confirmButtonText: this.__('variable.yes'), cancelButtonText: this.__('variable.no')
            }).then(r => {
                if(r.isConfirmed){
                    axios.delete(route('ourworks.destroy', { locale: this.lang, ourwork: id }))
                        .then(res => {
                            if(res?.data?.isSuccess){
                                if(this.paginate.perPage == 1){ this.paginate.currentPage--; }
                                this.$refs.table && this.$refs.table.refresh();
                                Swal.fire({ title: this.__('variable.success'), icon: 'success', timer: 500, showConfirmButton: false });
                            } else if(res?.data && res.data.message){
                                Swal.fire({ title: res.data.message, confirmButtonText: this.__('variable.ok') });
                            }
                        })
                        .catch(this.showCatchError);
                }
            });
        },

        resetForm(){
            this.errors = {};
            this.leftFile = null; this.rightFile = null;
            this.leftPreview = ''; this.rightPreview = '';
        },

        showCatchError(err){
            if(err?.response?.data){
                Swal.fire({ icon:'error', title: `${err.response.data.message}`, confirmButtonText: this.__('variable.ok') });
            }
        },
    }
}
</script>

<style scoped>
.error-border { border-color:#dc3545 !important; }
.error-msg { color:#dc3545; font-size:12px; }
</style>
