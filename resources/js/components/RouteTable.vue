<template>
    <div class="overflow-hidden overflow-x-auto relative">
        <table class="table w-full" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th
                    v-for="(field, index) in fields"
                    :key="index"
                    class="text-left"
                >
                    <sortable-icon
                        :resource-name="resourceName"
                        :uri-key="field.attribute"
                        @sort="sort(field.attribute)"
                    >
                        {{ __(field.label) }}
                    </sortable-icon>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(route, index) in routes"
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
                    <span
                        v-for="(value, index) in route.methods"
                        :key="index"
                        :class="{
                            'px-2 py-1 text-xs font-semibold rounded mr-2': ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(value),
                            'bg-success text-white': value === 'GET',
                            'bg-80 text-white': value === 'HEAD',
                            'bg-primary-dark text-white': value === 'POST',
                            'bg-warning text-90': value === 'PUT',
                            'bg-info text-white': value === 'PATCH',
                            'bg-danger text-white': value === 'DELETE',
                        }"
                    >
                        {{ value }}
                    </span>
                </td>
                <td class="whitespace-no-wrap text-left">
                    {{ route.action }}
                </td>
                <td class="whitespace-no-wrap text-left">
                    <span
                        v-for="(value, index) in route.middleware"
                        :key="index"
                        class="px-2 py-1 text-xs font-semibold rounded mr-2"
                        :class="style(value)"
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
const StyleGenerator = (() => {
    let styleIndex = 0;
    let usedStyles = {};
    const styles = [
        'bg-success text-white',
        'bg-80 text-white',
        'bg-primary-dark text-white',
        'bg-warning text-90',
        'bg-info text-white',
        'bg-danger text-white',
    ];

    return {
        generate: function(value) {
            if (styleIndex >= styles.length) {
                styleIndex = 0;
            }

            if (! usedStyles.hasOwnProperty(value)) {
                usedStyles[value] = styles[styleIndex];
            }

            styleIndex++;

            return usedStyles[value];
        }
    };
})();

export default {
    props: {
        routes: {
            type: Array,
            required: true,
        },
        sort: {
            type: Function,
        }
    },

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

    methods: {
        style(value) {
            return StyleGenerator.generate(value);
        }
    }
}
</script>
