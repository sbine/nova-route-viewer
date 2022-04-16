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

            <div class="flex items-center ml-4">
                <checkbox
                    :checked="showNova"
                    @input="toggleNova"
                />
                <label
                    class="cursor-pointer ml-2"
                    @click="toggleNova"
                >
                    {{ __('Show Nova routes') }}
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
            this.isLoading = true
            Nova.request().get('/nova-vendor/route-viewer/routes').then(response => {
                if (response.data) {
                    this.routes = response.data;
                }
            });
            this.isLoading = false
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
