<template>
    <div>
        <heading class="mb-6">Route Viewer</heading>

        <div class="flex justify-between">
            <div class="relative h-9 flex items-center mb-6">
                <icon type="search" class="absolute ml-3 text-70" />

                <input v-model="search"
                       class="appearance-none form-control form-input w-search pl-search"
                       placeholder="Search"
                       type="search"
                >
            </div>

            <div class="flex items-center mb-6 ml-6">
                <checkbox :checked="showNova"
                          @input="toggleNova"
                />
                <label class="cursor-pointer pl-2"
                       @click="toggleNova"
                >
                    Show Nova routes
                </label>
            </div>

            <span class="ml-auto mb-6">
                <button @click="getRoutes()"
                        class="btn btn-default btn-primary"
                >
                    Refresh
                </button>
            </span>
        </div>

        <card>
            <route-table :routes="filteredRoutes"
                         :sort="sortBy"
            ></route-table>
        </card>
    </div>
</template>

<script>
import RouteTable from './RouteTable';

export default {
    components: { RouteTable },
    data() {
        return {
            routes: [],
            search: '',
            sort: {
                field: '',
                order: -1,
            },
            showNova: false,
        }
    },
    mounted() {
        this.getRoutes();
    },
    methods: {
        getRoutes() {
            Nova.request().get('/nova-vendor/route-viewer/routes').then(response => {
                this.routes = response.data;
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
        toggleNova() {
            this.showNova = ! this.showNova;
        }
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
            if (this.showNova) {
                return this.routes;
            }

            return this.routes.filter(route => {
                return (! route.action.length || route.action.indexOf('Laravel\\Nova') !== 0)
                    && (! route.as.length || route.as.indexOf('nova') !== 0)
                    && ! route.middleware.includes('nova');
            });
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

<style>
</style>
