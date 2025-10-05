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
                                :apiUrl="getRout('reviews.index',{locale : lang})"
                                :per-page="paginate.perPage"
                                :current-page="paginate.currentPage"
                                :striped="showTableLine"
                                :small="showTableLine"
                                :bordered="showTableLine"
                            >
                                <template #table-busy>
                                    <div class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>{{ __(('variable.loaded')) }}</strong>
                                    </div>
                                </template>

                                <template #cell(comment)="{ item }">
                                    <span>{{ truncate(item.comment, 120) }}</span>
                                </template>

                                <template #cell(rate)="{ item }">
                                    <b-badge variant="warning" style="color: black">{{ item.rate }}/5</b-badge>
                                </template>

                                <template #cell(status)="{ item }">
                                    <b-badge :variant="item.status ? 'success' : 'secondary'" style="color: black">
                                        {{ item.status ? (__('variable.published') || 'Published') : (__('variable.unpublished') || 'Unpublished') }}
                                    </b-badge>
                                </template>

                                <template #cell(actions)="{ item }">
                                    <b-button
                                        size="sm"
                                        :variant="item.status ? 'outline-secondary' : 'outline-success'"
                                        class="mr-2"
                                        @click="toggleStatus(item)"
                                    >
                                        {{ item.status ? (__('variable.unpublish') || 'Unpublish') : (__('variable.publish') || 'Publish') }}
                                    </b-button>
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
            </div>
        </div>

        <div v-if="isLoading" class="loading-overflow">
            <vue-loading class="loading" type="balls" color="#d9544e" :size="{ width: '100px', height: '100px' }"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { VueLoading } from 'vue-loading-template';
import PaginationComponent from './PaginationComponent';
import { PageName } from '../PageName';

export default {
    name: 'reviewsComponent',
    components: { PaginationComponent, VueLoading },
    props: ['locale','authuser'],

    data() {
        return {
            lang: this.locale,
            authUser: this.authuser,
            paginate: { perPage: 10, currentPage: 1, total: '', currentPageInput: '', lastPage: '' },
            isBusy: false,
            isLoading: false,

            showTableLine: false,
            // default sort: id desc (server also enforces)
            sortBy: 'id',
            sortDesc: true,

            fields: [
                { key: 'id',       label: this.__('variable.id'), sortable: true },
                { key: 'name',     label: this.__('variable.name'), sortable: true },
                { key: 'email',    label: this.__('variable.email'), sortable: true },
                { key: 'rate',     label: this.__('variable.rate') || 'Rate', sortable: true },
                { key: 'comment',  label: this.__('variable.comment') || 'Comment', sortable: false },
                { key: 'status',   label: this.__('variable.status') || 'Status', sortable: true },
                { key: 'actions',  label: this.__('variable.action'), sortable: false },
            ],

            items: [],
        };
    },

    watch: {
        locale(v){ this.lang = v; },
        authuser(v){ this.authUser = v; },
    },

    mounted(){
        PageName.$emit('updatePageName', this.__('variable.reviews') || 'Reviews');
    },

    methods: {
        getRout(param){ return getRout(param); },
        toggleTable(){ this.showTableLine = !this.showTableLine; },
        loadAfterChangePage(current, per){ if(current) this.paginate.currentPage = current; if(per) this.paginate.perPage = per; },

        truncate(txt, len){ if(!txt) return ''; return txt.length>len ? txt.substr(0,len)+'…' : txt; },

        getItems(ctx){
            this.isBusy = true;
            const params = {
                perPage: ctx.perPage,
                currentPage: ctx.currentPage,
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            };
            return axios.get(route('reviews.index', { locale: this.lang }) + `?page=${ctx.currentPage}`, { params })
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
                .catch(() => { this.isBusy = false; });
        },

        toggleStatus(item){
            const id = item.id;
            axios.post(route('reviews.toggle', { locale: this.lang, review: id }))
                .then(res => {
                    if (res?.data?.isSuccess) {
                        // update row locally for snappier UX
                        item.status = !!res.data.status;
                    }
                })
                .catch(() => {});
        },
    }
}
</script>

<style scoped>
/* optional styling */
</style>
