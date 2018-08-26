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
                <table class="table w-full" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th v-for="field in fields" class="text-left">
                            <sortable-icon
                                    @sort="sortBy(field)"
                                    :resource-name="resourceName"
                                    :uri-key="field.attribute"
                            >
                                {{ field.label }}
                            </sortable-icon>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(route, index) in filteredRoutes" :key="index">
                        <td v-for="field in fields">
                            <span class="whitespace-no-wrap text-left">
                                {{ route[field.attribute] }}
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            fields: [
                {
                    label: 'Route',
                    attribute: 'uri',
                },
                {
                    label: 'Name',
                    attribute: 'as',
                },
                {
                    label: 'Methods',
                    attribute: 'methods',
                },
                {
                    label: 'Action',
                    attribute: 'action',
                },
                {
                    label: 'Middleware',
                    attribute: 'middleware',
                }
            ],
            routes: [],
            search: '',
            sort: {
                field: '',
                order: -1,
            },
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
            this.sort.field = field.attribute;
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
    },
    computed: {
        filteredRoutes() {
            if (! this.search.length) {
                return this.routes;
            }

            return this.routes.filter(route => {
                let regex = new RegExp('(' + this.search + ')','i');
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
        }
    }
}
</script>

<style>
</style>
