<template>
    <div class="overflow-hidden overflow-x-auto relative">
        <table class="table w-full" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th v-for="field in fields" class="text-left">
                    <sortable-icon
                        @sort="sort(field.attribute)"
                        :resource-name="resourceName"
                        :uri-key="field.attribute"
                    >
                        {{ field.label }}
                    </sortable-icon>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(route, index) in routes"
                :key="index"
                :route="route"
            >
                <td class="whitespace-no-wrap text-left">
                    {{ route.uri }}
                </td>
                <td class="whitespace-no-wrap text-left">
                    {{ route.as }}
                </td>
                <td class="whitespace-no-wrap text-left">
                    <span v-for="value in route.methods"
                          :class="{
                            'px-2 py-1 text-white text-xs font-semibold rounded mr-2': ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(value),
                            'bg-success': value === 'GET',
                            'bg-80': value === 'HEAD',
                            'bg-primary-dark': value === 'POST',
                            'bg-warning': value === 'PUT',
                            'bg-info': value === 'PATCH',
                            'bg-danger': value === 'DELETE',
                        }"
                    >
                        {{ value }}
                    </span>
                </td>
                <td class="whitespace-no-wrap text-left">
                    {{ route.action }}
                </td>
                <td class="whitespace-no-wrap text-left">
                    <span v-for="value in route.middleware"
                          class="px-2 py-1 text-white text-xs font-semibold rounded mr-2"
                          :class="{
                            'bg-primary-dark': ! value.toLowerCase().includes('nova'),
                            'bg-80': value.toLowerCase().includes('nova'),
                          }"
                    >
                        {{ value }}
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
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
        };
    },
    props: {
        routes: {
            type: Array,
            required: true,
        },
        sort: {
            type: Function,
        }
    }
}
</script>
