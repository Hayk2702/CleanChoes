<template>
    <div class="parent_search">
        <div class="icon_search">
            <i v-if="filters.length < 6" class="bi bi-plus-square-fill btnAddFilter" @click="addFilter">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                     class="bi bi-plus-square-fill" viewBox="0 0 16 16">
                    <path
                        d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0z"/>
                </svg>
            </i>
        </div>
        <div class="child_search">
            <div class="child_search_div" v-for="(filter,i) in filters">
                <b-col class="child_search_div_b_col">
                    <div class="searchDiv">
                        <multiselect
                            v-model="filter.key"
                            :options="options.map(item => item = {value: item.key, text : item.label})"
                            :multiple="false"
                            label="text"
                            track-by="value"
                            :selectedLabel="__(('variable.selected'))"
                            :selectLabel="__(('variable.select'))"
                            :deselectLabel="__(('variable.remove'))"
                            :placeholder="__(('variable.filter_by'))"
                            :showNoResults="false"
                            @input="updateFilter"
                            class="search_multi_select_custom"
                        >
                        </multiselect>
                        <select v-if="showAction" class="search_select" v-model="filter.action">
                            <option>LIKE</option>
                            <option>=</option>
                            <option>!=</option>
                            <option>></option>
                            <option><</option>
                            <option>>=</option>
                            <option><=</option>
                        </select>
                        <template
                            v-if="filter && filter.key && filter.key.value && datepickerStrings.indexOf(filter.key.value) !== -1">
                            <datepicker class="searchDpCustom"
                                        v-model="filter.text"
                                        :format="'dd-MM-yyyy'"
                                        :lang="translate"
                                        :editable="true"
                                        :placeholder="__(('variable.search'))"
                                        @change="updateFilter"
                            ></datepicker>
                        </template>
                        <template v-else>
                            <b-input
                                class="searchInput"
                                v-model="(filter.text instanceof Date) ? (filter.text = '') : filter.text"
                                @change="updateFilter"
                                :placeholder="__(('variable.search'))"
                            ></b-input>
                        </template>
                        <i class="bi bi-dash-square-fill btnRemoveFilter" v-if="filters.length > 1"
                           @click="removeFilter(i)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                 class="bi bi-dash-square-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm2.5 7.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1z"/>
                            </svg>
                        </i>
                    </div>
                    <a v-if="i < filters.length - 1 && showCondition" href="javascript:void(0)" class="condition_a"
                       @click="changeCondition(filter)">
                        {{ filter.condition == "AND" ? __('variable.and') : __('variable.or') }}</a>
                </b-col>
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import Datepicker from 'vue2-datepicker-mask';

export default {
    name: "SearchComponent",

    components: {
        Multiselect,
        Datepicker

    },

    props: [
        'keys',
        'isCondition',
        'isAction',
        'dateKeys',
    ],

    watch: {
        keys: function (newVal, oldVal) {
            this.options = newVal;
        },

        dateKeys: function (newVal, oldVal) {
            this.datepickerStrings = newVal;
        },

        isCondition: function (newVal, oldVal) {
            this.showCondition = newVal;
        },

        isAction: function (newVal, oldVal) {
            this.showAction = newVal;
        },
    },

    data() {

        return {
            //Data
            options: this.keys,
            showCondition: this.isCondition,
            showAction: this.isAction,
            datepickerStrings: this.dateKeys ? this.dateKeys : [],
            filters: [
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                },
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                },
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                }
            ],
            translate: {
                days: [this.__('variable.sunday'), this.__('variable.monday'), this.__('variable.tuesday'), this.__('variable.wednesday'), this.__('variable.thursday'), this.__('variable.friday'), this.__('variable.saturday')],
                months: [this.__('variable.jan'), this.__('variable.feb'), this.__('variable.mar'), this.__('variable.apr'), this.__('variable.my'), this.__('variable.jun'), this.__('variable.jul'), this.__('variable.aug'), this.__('variable.sep'), this.__('variable.oct'), this.__('variable.nov'), this.__('variable.dec')],
                pickers: [this.__('variable.next_seven_days'), this.__('variable.next_thirty_days'), this.__('variable.last_seven_days'), this.__('variable.last_thirty_days')],
                placeholder: {
                    date: this.__('variable.select_date'),
                }
            },
            //DATA
        }
    },

    mounted() {
        // this.updateFilter();
    },

    created() {

    },

    methods: {

        updateFilter() {
            this.$emit("filter-updated", this.filters);
        },

        addFilter() {
            if (this.filters.length >= 6) {
                return
            }

            this.filters.push({text: '', key: '', condition: 'AND', action: 'LIKE',})
        },

        removeFilter(id) {
            if (this.filters.length === 3) {
                this.filters[id].text = '';
                this.filters[id].key = '';
                this.filters[id].condition = 'AND';
                this.filters[id].action = 'LIKE';
                return
            }
            this.filters.splice(id, 1);
            this.updateFilter();
        },

        resetFilter() {
            this.filters = [
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                },
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                },
                {
                    text: '',
                    key: '',
                    condition: 'AND',
                    action: 'LIKE',
                },

            ];
        },

        changeCondition(item) {
            if (!item || !item.condition) {
                return
            }
            if (item.condition === "AND") {
                item.condition = "OR";
            } else {
                item.condition = "AND";
            }
        },
    }
}
</script>

<style scoped>



</style>
