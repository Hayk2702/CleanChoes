<template>
    <div>
        <div>
            <SearchComponent
                ref="SearchComponent"
                @filter-updated="updateFilter"
                :keys="fields.filter(f => f.key !== 'actions')"
                :isCondition="true"
                :isAction="true"
            />
        </div>

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
                            <a class="btn btnAdd" href="javascript:void(0)" @click="showCategoryForm()">
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
                                :items="getCategories"
                                :fields="fields"
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                :apiUrl="getRout('categories.index',{locale : lang})"
                                :per-page="paginate.perPage"
                                :current-page="paginate.currentPage"
                                :striped="showTableLine"
                                :small="showTableLine"
                                :bordered="showTableLine"
                                :filter="filter"
                                @row-clicked="showCategoryForm($event, 'edit')"
                            >
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>{{ __(('variable.loaded')) }}</strong>
                                    </div>
                                </template>

                                <template #cell(image_path)="{ item }">
                                    <img v-if="item.image_path" :src="item.image_path" alt="img" style="max-height:40px; max-width:80px; object-fit:cover;"/>
                                </template>

                                <template #cell(description)="{ item }">
                                    <span>{{ truncate(stripTags(item.description), 80) }}</span>
                                </template>

                                <template v-if="!isLoading" #cell(actions)="row">
                                    <div class="d-flex">

                                        <a class="btn btn-inverse-warning  p-1 a_position"
                                           href="javascript:void(0)" @click="showCategoryForm(row.item, 'edit')">
                                            <i class="bi bi-pencil title_hov_top" :data-title="__(('variable.edit'))">
                                                <svg v-b-tooltip.hover
                                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     fill="currentColor"
                                                     class="bi bi-pencil" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                                </svg>
                                            </i>
                                        </a>

                                        <a class="btn btn-inverse-warning  p-1 a_position"
                                           href="javascript:void(0)" @click="openServices(row.item)">
                                           <span>{{__(('variable.manage'))}}</span>
                                        </a>

                                        <button class="btn btn-danger p-1 a_position"
                                                @click="destroyCategory(row.item.id)">
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

                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>

                    <PaginationComponent
                        ref="PaginationComponent"
                        v-if="(categories && categories.length)"
                        :paginate="paginate"
                        @loadAfterChangePage="loadAfterChangePage"
                    />
                </div>

                <!-- Category Add/Edit Modal -->
                <b-modal class="addModal" v-model="show" :header-text-variant="headerTextVariant" :header-bg-variant="headerBgVariant">
                    <template #modal-title>
                        <b-row>
                            <b-col sm="10" md="10" class="title_popup fs-15 lh-2">
                                {{ (addOrEdit === 'add' ? __(('variable.add')) : __(('variable.edit'))) + ' ' + (__('variable.category') || 'Category') }}
                            </b-col>
                        </b-row>
                    </template>

                    <b-container fluid>
                        <b-row class="mb-1 bRowPosition">
                            <b-col class="blockInput title_hov_center" v-b-tooltip.hover :data-title="__('variable.name')">
                                <b-form-input :class="[errors['name'] ? 'error-border': '', 'addFormInputs']" v-model="category.name" type="text" :placeholder="__('variable.name')"/>
                                <small v-if="errors['name']" class="error-msg">{{ errors['name'] }}</small>
                            </b-col>
                        </b-row>

                        <b-row class="mb-1 bRowPosition">
                            <b-col class="blockInput title_hov_center" v-b-tooltip.hover :data-title="__('variable.description')">
                                <ckeditor
                                    :editor="editor"
                                    v-model="category.description"
                                    :config="editorConfig"
                                />
                                <small v-if="errors['description']" class="error-msg">{{ errors['description'] }}</small>
                            </b-col>
                        </b-row>

                        <b-row class="mb-1 bRowPosition">
                            <b-col class="blockInput title_hov_center" v-b-tooltip.hover :data-title="__('variable.image') || 'Image'">
                                <b-form-file @change="onImageChange" :state="imageFile ? true : null" accept="image/*" browse-text="Choose"/>
                                <div v-if="previewUrl" class="mt-2">
                                    <img :src="previewUrl" style="max-width:160px; max-height:120px; object-fit:cover;"/>
                                </div>
                                <small v-if="errors['image_path']" class="error-msg">{{ errors['image_path'] }}</small>
                            </b-col>
                        </b-row>
                    </b-container>

                    <template #modal-footer>
                        <div class="w-100 handle_button">
                            <b-button :disabled="buttonDisabled" size="sm" class="float-right addButton" @click="submitCategory">
                                {{ (addOrEdit === 'add' ? __(('variable.add')) : __(('variable.edit'))) }}
                            </b-button>
                        </div>
                    </template>
                </b-modal>

                <!-- Services Modal (by Category) -->
                <b-modal v-model="servicesModal" size="lg" :title="(__('variable.services') || 'Services') + ' — ' + (currentCategory?.name || '')">
                    <b-container fluid>
                        <b-row class="mb-2">
                            <b-col class="d-flex justify-content-end">
                                <b-button size="sm" variant="primary" @click="showServiceForm()">{{ __(('variable.add')) }}</b-button>
                            </b-col>
                        </b-row>

                        <b-table :items="services" :fields="serviceFields" small bordered responsive :busy="servicesBusy">
                            <template #cell(price)="{ item }">
                                <span>{{ formatPrice(item.price) }}</span>
                            </template>
                            <template #cell(actions)="{ item }">
                                <button class="btn btn-inverse-warning p-1 mr-1" @click="showServiceForm(item, 'edit')">{{ __(('variable.edit')) }}</button>
                                <button class="btn btn-danger p-1" style="color: black" @click="destroyService(item.id)">{{ __(('variable.remove')) }}</button>
                            </template>
                        </b-table>
                    </b-container>

                    <template #modal-footer>
                        <b-button variant="secondary" @click="servicesModal=false">{{ __('variable.close') || 'Close' }}</b-button>
                    </template>
                </b-modal>

                <!-- Service Add/Edit Modal -->
                <b-modal v-model="serviceFormModal" :title="(serviceAddOrEdit==='add' ? __(('variable.add')) : __(('variable.edit'))) + ' ' + (__('variable.service')||'Service')">
                    <b-container fluid>
                        <b-row class="mb-1 bRowPosition">
                            <b-col>
                                <b-form-input v-model="serviceForm.name" :placeholder="__('variable.name')"/>
                                <small v-if="serviceErrors['name']" class="error-msg">{{ serviceErrors['name'] }}</small>
                            </b-col>
                        </b-row>
                        <b-row class="mb-1 bRowPosition">
                            <b-col>
                                <b-form-input v-model.number="serviceForm.price" type="number" min="0" step="1" :placeholder="__('variable.price') || 'Price'"/>
                                <small v-if="serviceErrors['price']" class="error-msg">{{ serviceErrors['price'] }}</small>
                            </b-col>
                        </b-row>
                    </b-container>

                    <template #modal-footer>
                        <b-button :disabled="serviceButtonDisabled" variant="primary" @click="submitService">{{ (serviceAddOrEdit==='add' ? __(('variable.add')) : __(('variable.edit'))) }}</b-button>
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
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
export default {
    name: 'categoriesComponent',
    components: { Swal, Multiselect, PaginationComponent, SearchComponent, VueLoading ,ckeditor: CKEditor.component},
    props: ['locale','authuser'],

    data() {
        return {
            editor: ClassicEditor,
            editorConfig: {
                toolbar: [
                    'heading','|','bold','italic','underline','link','bulletedList','numberedList','blockQuote',
                    '|','undo','redo','removeFormat'
                ]
            },
            lang: this.locale,
            authUser: this.authuser,
            paginate: { perPage: 10, currentPage: 1, total: '', currentPageInput: '', lastPage: '' },
            errors: {},

            showTableLine: false,
            fields: [
                { key: 'id',          label: this.__('variable.id'),          sortable: true },
                { key: 'name',        label: this.__('variable.name'),        sortable: true },
                { key: 'image_path',  label: this.__('variable.image')||'Image', sortable: false },
                { key: 'description', label: this.__('variable.description'), sortable: true },
                { key: 'actions',     label: this.__('variable.action'),      sortable: false }
            ],
            isBusy: false,
            sortBy: 'id',
            sortDesc: false,
            filter: [{ text: '', key: '' }],

            // category modal
            show: false,
            headerBgVariant: 'custom',
            headerTextVariant: 'white',
            addOrEdit: 'add',
            buttonDisabled: false,

            categories: [],
            category: { id: '', name: '', description: '', image_path: '' },
            imageFile: null,
            previewUrl: '',

            // services
            servicesModal: false,
            currentCategory: null,
            services: [],
            servicesBusy: false,
            serviceFields: [
                { key: 'id',    label: 'ID',    sortable: true },
                { key: 'name',  label: this.__('variable.name'), sortable: true },
                { key: 'price', label: this.__('variable.price')||'Price', sortable: true },
                { key: 'actions', label: this.__('variable.action'), sortable: false },
            ],
            serviceFormModal: false,
            serviceForm: { id: '', name: '', price: null },
            serviceAddOrEdit: 'add',
            serviceErrors: {},
            serviceButtonDisabled: false,

            isLoading: false,
        };
    },

    watch: {
        locale(val){ this.lang = val; },
        authuser(val){ this.authUser = val; },
        filter: { handler(){ this.paginate.currentPage = 1; }, deep: true },
    },

    mounted(){ PageName.$emit('updatePageName', this.__('variable.categories') || 'Categories'); },

    methods: {
        getRout(param){ return getRout(param); },
        truncate(txt, len){ if(!txt) return ''; return txt.length>len ? txt.substr(0,len)+'…' : txt; },
        updateFilter(newFilter){ this.filter = newFilter; },
        toggleTable(){ this.showTableLine = !this.showTableLine; },
        loadAfterChangePage(currentPage, perPage){ if(currentPage) this.paginate.currentPage = currentPage; if(perPage) this.paginate.perPage = perPage; },

        // --- Categories list
        getCategories(data){
            this.isBusy = true;
            return axios.get(route('categories.index', { locale: this.lang }) + `?page=${data.currentPage}`, { params: data })
                .then(resp => {
                    const r = resp && resp.data ? resp.data : null;
                    if(r && r.data){
                        this.categories = r.data;
                        this.paginate.rows = r.total;
                        this.paginate.total = r.total;
                        this.paginate.currentPage = r.current_page;
                        this.paginate.currentPageInput = this.paginate.currentPage;
                        this.paginate.lastPage = r.last_page;
                    }
                    this.isBusy = false;
                    return this.categories;
                })
                .catch(err => { this.isBusy = false; this.showCatchError(err); });
        },

        // --- Category form
        showCategoryForm(obj = '', addOrEdit = 'add'){
            this.errors = {};
            this.addOrEdit = addOrEdit;
            this.category = {
                id: obj ? obj.id : '',
                name: obj ? obj.name : '',
                description: obj ? obj.description : '',
                image_path: obj ? obj.image_path : ''
            };
            this.imageFile = null;
            this.previewUrl = obj && obj.image_path ? obj.image_path : '';
            this.show = true;
        },

        onImageChange(e){
            const f = e.target.files && e.target.files[0];
            this.imageFile = f || null;
            this.previewUrl = f ? URL.createObjectURL(f) : '';
        },

        submitCategory(){
            if(this.buttonDisabled) return; this.buttonDisabled = true;
            this.errors = {};

            const form = new FormData();
            if(this.category.id) form.append('id', this.category.id);
            form.append('name', this.category.name || '');
            form.append('description', this.category.description || '');
            if(this.imageFile) form.append('image', this.imageFile);

            axios.post(route('categories.store', { locale: this.lang }), form, { headers: { 'Content-Type': 'multipart/form-data' }})
                .then(res => {
                    if(res && res.data && res.data.isSuccess){
                        this.show = false; this.buttonDisabled = false;
                        this.$refs.table && this.$refs.table.refresh();
                        Swal.fire({ title: this.__('variable.success'), icon: 'success', timer: 600, showConfirmButton: false });
                    } else { this.buttonDisabled = false; }
                })
                .catch(err => {
                    this.buttonDisabled = false;
                    if(err && err.response && err.response.data){
                        const errors = err.response.data;
                        Object.keys(errors).forEach(k => this.$set(this.errors, k, errors[k][0]));
                    }
                });
        },

        destroyCategory(id){
            Swal.fire({
                title: this.__('variable.sure'), text: '', icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: this.__('variable.yes'), cancelButtonText: this.__('variable.no')
            }).then(result => {
                if(result.isConfirmed){
                    axios.delete(route('categories.destroy', { locale: this.lang, category: id }))
                        .then(res => {
                            if(res && res.data && res.data.isSuccess){
                                if(this.paginate.perPage == 1){ this.paginate.currentPage--; }
                                this.$refs.table && this.$refs.table.refresh();
                                Swal.fire({ title: this.__('variable.success'), icon: 'success', timer: 500, showConfirmButton: false });
                            } else if(res && res.data && !res.data.isSuccess && res.data.message){
                                Swal.fire({ title: res.data.message, confirmButtonText: this.__('variable.ok') });
                            }
                        })
                        .catch(this.showCatchError);
                }
            });
        },

        // --- Services by category
        openServices(cat){ this.currentCategory = cat; this.fetchServices(); this.servicesModal = true; },

        fetchServices(){
            if(!this.currentCategory) return;
            this.servicesBusy = true;
            axios.get(route('services.index', { locale: this.lang, category: this.currentCategory.id }))
                .then(res => { this.services = (res && res.data && res.data.data) ? res.data.data : []; this.servicesBusy = false; })
                .catch(err => { this.servicesBusy = false; this.showCatchError(err); });
        },

        showServiceForm(obj = '', addOrEdit = 'add'){
            this.serviceErrors = {}; this.serviceAddOrEdit = addOrEdit;
            this.serviceForm = { id: obj ? obj.id : '', name: obj ? obj.name : '', price: obj ? obj.price : null };
            this.serviceFormModal = true;
        },

        submitService(){
            if(this.serviceButtonDisabled) return; this.serviceButtonDisabled = true;
            this.serviceErrors = {};
            const payload = { ...this.serviceForm, category_id: this.currentCategory.id };
            axios.post(route('services.store', { locale: this.lang }), payload)
                .then(res => {
                    if(res && res.data && res.data.isSuccess){ this.serviceFormModal = false; this.fetchServices(); }
                    this.serviceButtonDisabled = false;
                })
                .catch(err => {
                    this.serviceButtonDisabled = false;
                    if(err && err.response && err.response.data){
                        const errors = err.response.data;
                        Object.keys(errors).forEach(k => this.$set(this.serviceErrors, k, errors[k][0]));
                    }
                });
        },

        destroyService(id){
            Swal.fire({ title: this.__('variable.sure'), icon: 'warning', showCancelButton: true, confirmButtonText: this.__('variable.yes'), cancelButtonText: this.__('variable.no') })
                .then(result => {
                    if(result.isConfirmed){
                        axios.delete(route('services.destroy', { locale: this.lang, service: id }))
                            .then(res => { if(res && res.data && res.data.isSuccess){ this.fetchServices(); } })
                            .catch(this.showCatchError);
                    }
                });
        },

        stripTags(html) {
            if (!html) return '';
            const div = document.createElement('div');
            div.innerHTML = html;
            return (div.textContent || div.innerText || '').trim();
        },
        truncate(txt, len){ if(!txt) return ''; return txt.length>len ? txt.substr(0,len)+'…' : txt; },

        // helpers
        formatPrice(v){ if(v === null || v === undefined) return ''; return new Intl.NumberFormat().format(v); },
        showCatchError(err){ if(err?.response?.data){ Swal.fire({ icon:'error', title: `${err.response.data.message}`, confirmButtonText: this.__('variable.ok') }); } },
    }
}
</script>

<style scoped>
.error-border { border-color:#dc3545 !important; }
.error-msg { color:#dc3545; font-size:12px; }
</style>
