<template>
    <div>
        <heading class="mb-6">Route Viewer</heading>

        <div class="flex justify-between">
            <div class="relative h-9 flex items-center mb-6">
                <icon type="search" class="absolute ml-3 text-70" />

                <input
                    class="appearance-none form-control form-input w-search pl-search"
                    placeholder="Search"
                    type="search"
                    v-model="search"
                >
            </div>
        </div>

        <card>
            <div class="overflow-hidden overflow-x-auto relative">
                <resource-table
                    resource-name="routes"
                    :resources="routesAsResources"
                    singular-name="route"
                    ref="resourceTable"
                    @order="order"
                />
            </div>
        </card>
    </div>
</template>

<script>
    import {
        Filterable,
        Paginatable,
        PerPageable,
    } from 'laravel-nova';

export default {
    mixins: [
        Filterable,
        Paginatable,
        PerPageable,
    ],
    data() {
        return {
            routes: [],
            search: '',
            sortOrder: -1
        }
    },
    mounted() {
        this.getRoutes();
    },
    methods: {
        getRoutes() {
            Nova.request().get('/nova-vendor/route-viewer/routes').then(response => {
                this.routes = response.data;
            })
        },
        order(event) {
            this.sortOrder *= -1;

            this.routes.sort((route1, route2) => {
                let comparison = 0;
                if (route1[event.attribute] < route2[event.attribute]) {
                    comparison = -1;
                }
                if (route1[event.attribute] > route2[event.attribute]) {
                    comparison = 1;
                }

                return comparison * this.sortOrder;
            });
        },
    },
    computed: {
        routesAsResources() {
            return this.routes.filter(route => {
                let regex = new RegExp('(' + this.search + ')','i');
                for (let key in route) {
                    return regex.test(route[key]);
                }
                return Object.values(route)
            }).map(route => {
                return {
                    fields: [
                        {
                            indexName: 'Route',
                            attribute: 'uri',
                            value: route.uri,
                            sortable: true,
                            component: 'text-field',
                            textAlign: 'left',
                        },
                        {
                            indexName: 'Name',
                            attribute: 'as',
                            value: route.as,
                            sortable: true,
                            component: 'text-field',
                            textAlign: 'left',
                        },
                        {
                            indexName: 'Methods',
                            attribute: 'methods',
                            value: route.methods,
                            sortable: true,
                            component: 'text-field',
                            textAlign: 'left',
                        },
                        {
                            indexName: 'Action',
                            attribute: 'action',
                            value: route.action,
                            sortable: true,
                            component: 'text-field',
                            textAlign: 'left',
                        },
                        {
                            indexName: 'Middleware',
                            attribute: 'middleware',
                            value: route.middleware,
                            sortable: true,
                            component: 'text-field',
                            textAlign: 'left',
                        },
                    ],
                    id: [],
                };
            });
        }
    }
}
</script>

<style>
    /* Scoped Styles */
</style>
