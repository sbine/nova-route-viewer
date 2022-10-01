<template>
    <div class="overflow-hidden overflow-x-auto relative">
        <table class="table w-full" cellpadding="0" cellspacing="0">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th
                    v-for="(field, index) in fields"
                    :key="index"
                    class="text-left px-6 py-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide"
                >
                    <SortableIcon
                        :uri-key="field.attribute"
                        @sort="sort(field.attribute)"
                    >
                        {{ __(field.label) }}
                    </SortableIcon>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(route, index) in routes"
                :key="index"
                :route="route"
                class="group"
            >
                <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    {{ route.uri }}
                </td>
                <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    {{ route.as }}
                </td>
                <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <span
                        v-for="(value, index) in route.methods"
                        :key="index"
                        :class="{
                            'px-2 py-1 text-xs dark:text-gray-600 font-semibold rounded mr-2': ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'].includes(value),
                            'bg-get': value === 'GET',
                            'bg-head': value === 'HEAD',
                            'bg-post': value === 'POST',
                            'bg-put': value === 'PUT',
                            'bg-patch': value === 'PATCH',
                            'bg-options': value === 'OPTIONS',
                            'bg-delete': value === 'DELETE',
                        }"
                    >
                        {{ value }}
                    </span>
                </td>
                <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    {{ route.action }}
                </td>
                <td class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <span
                        v-for="(value, index) in route.middleware"
                        :key="index"
                        class="px-2 py-1 text-xs dark:text-gray-600 font-semibold rounded mr-2"
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
        'bg-get',
        'bg-head',
        'bg-post',
        'bg-put',
        'bg-patch',
        'bg-delete',
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

<style>
.bg-get {
    background-color: #bef264;
}
.bg-head {
    background-color: #d1d5db;
}
.bg-post {
    background-color: #93c5fd;
}
.bg-put {
    background-color: #fde047;
}
.bg-patch {
    background-color: #67e8f9;
}
.bg-options {
    background-color: #5eead4;
}
.bg-delete {
    background-color: #fda4af;
}
</style>
