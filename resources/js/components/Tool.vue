<template>
    <div>
        <Head :title="__('Route Viewer')" />

        <Heading class="mb-6">Route Viewer</Heading>

        <div class="flex items-center mb-6">
            <div class="relative h-9 flex items-center">
                <icon type="search" class="absolute ml-3 text-70" />

                <input
                    v-model="search"
                    class="pl-10 pr-2 form-control focus:outline-none form-input-bordered dark:bg-gray-900 form-select"
                    :placeholder="__('Search')"
                    type="search"
                >
            </div>

            <div class="relative h-9 flex items-center ml-3 mr-1">
                <label for="http-method-filter" class="cursor-pointer ml-2">{{ __('Filter by HTTP Method') }}</label>
                <select id="http-method-filter" v-model="selectedHttpMethod" class="border rounded p-1">
                    <option value="all">All</option>
                    <option value="GET">GET</option>
                    <option value="HEAD">HEAD</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="OPTIONS">OPTIONS</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>

            <div
                v-for="filter in filters"
                :key="filter.label"
                class="flex items-center ml-3"
            >
                <checkbox
                    :checked="filterState[filter.label] || false"
                    @input="toggleFilter(filter.label)"
                />
                <label
                    class="cursor-pointer ml-2"
                    @click="toggleFilter(filter.label)"
                >
                    {{ __('Show :name routes', { name: filter.label }) }}
                </label>
            </div>

            <span class="ml-auto">
                <button
                    class="bg-primary-500 shadow px-6 py-2 rounded text-white dark:text-gray-900 cursor-pointer text-sm font-bold hover:bg-primary-400"
                    @click="getRoutes()"
                >
                    {{ __('Refresh') }}
                </button>
            </span>
        </div>

        <LoadingView :loading="isLoading">
            <Card>
                <RouteTable
                    :routes="filteredRoutes"
                    :columns="columns"
                    :sort="sortBy"
                />
            </Card>
        </LoadingView>
    </div>
</template>

<script>
import RouteTable from './RouteTable';

export default {
    metaInfo() {
      return {
        title: 'Route Viewer'
      }
    },
    components: { RouteTable },

    data() {
        return {
            isLoading: true,
            routes: [],
            columns: [],
            filters: [],
            filterState: {},
            search: '',
            sort: {
                field: '',
                order: -1,
            },
            selectedHttpMethod: 'all',
        }
    },

    mounted() {
        this.getRoutes();
    },

    methods: {
        getRoutes() {
            this.isLoading = true;

            Nova.request().get('/nova-vendor/route-viewer/routes')
                .then(response => {
                    if (response.data) {
                        this.columns = response.data.columns;
                        this.routes = response.data.routes;

                        if (response.data.filters) {
                            this.filters = response.data.filters;
                            const newState = {};
                            this.filters.forEach(filter => {
                                // Preserve existing toggle state on refresh, otherwise use default
                                newState[filter.label] = filter.label in this.filterState
                                    ? this.filterState[filter.label]
                                    : ! filter.hiddenByDefault;
                            });
                            this.filterState = newState;
                        }
                    }
                })
                .catch(error => Nova.error(error.message))
                .finally(() => {
                    this.isLoading = false;
                });
        },

        sortBy(field) {
            this.sort.field = field;
            this.sort.order *= -1;

            this.routes.sort((route1, route2) => {
                let comparison = 0;
                if (route1[this.sort.field] < route2[this.sort.field]) {
                    comparison = -1;
                }
                if (route1[this.sort.field] > route2[this.sort.field]) {
                    comparison = 1;
                }

                return comparison * this.sort.order;
            });
        },

        toggleFilter(label) {
            this.filterState = {
                ...this.filterState,
                [label]: ! this.filterState[label],
            };
        },
    },

    computed: {
        filteredRoutes() {
            if (! this.search.length) {
                return this.visibleRoutes;
            }

            const regex = this.searchRegex;
            // User input is not a valid regular expression, show no results
            if (! regex) {
                return {};
            }

            return this.visibleRoutes.filter(route => {
                let matchesSearch = false;

                for (let key in route) {
                    if (key === 'matchedFilters') continue;

                    if (Array.isArray(route[key])) {
                        route[key].forEach(property => {
                            if (regex.test(property)) {
                                matchesSearch = true;
                            }
                        });
                    }
                    else if (regex.test(route[key])) {
                        matchesSearch = true;
                    }
                }

                return matchesSearch;
            });
        },

        visibleRoutes() {
            let filteredRoutes = this.routes;

            if (this.selectedHttpMethod !== 'all') {
                filteredRoutes = filteredRoutes.filter(route => route.methods && route.methods.includes(this.selectedHttpMethod));
            }

            // Apply custom filters: hide routes matching a filter whose toggle is off
            filteredRoutes = filteredRoutes.filter(route => {
                if (! route.matchedFilters || route.matchedFilters.length === 0) {
                    return true;
                }

                return route.matchedFilters.every(label => this.filterState[label]);
            });

            return filteredRoutes;
        },

        searchRegex() {
            try {
                return new RegExp('(' + this.search + ')','i');
            } catch (e) {
                return false;
            }
        }
    }
}
</script>
