<template>
    <div class="overflow-hidden overflow-x-auto relative">
        <table class="table w-full" cellpadding="0" cellspacing="0">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th
                    v-for="(field, index) in columns"
                    :key="index"
                    class="text-left px-6 py-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide"
                >
                    <SortableIcon
                        v-if="field.sortable"
                        :uri-key="field.attribute"
                        @sort="sort(field.attribute)"
                    >
                        {{ __(field.label) }}
                    </SortableIcon>
                    <span v-else>{{ __(field.label) }}</span>
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
                <td
                    v-for="(field, fIndex) in columns"
                    :key="fIndex"
                    class="px-6 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900"
                >
                    <template v-if="field.attribute === 'methods'">
                        <span
                            v-for="(value, mIndex) in route.methods"
                            :key="mIndex"
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
                    </template>
                    <template v-else-if="field.attribute === 'middleware'">
                        <span
                            v-for="(value, mwIndex) in route.middleware"
                            :key="mwIndex"
                            class="px-2 py-1 text-xs dark:text-gray-600 font-semibold rounded mr-2"
                            :class="style(value)"
                        >
                            {{ value }}
                        </span>
                    </template>
                    <template v-else>
                        {{ route[field.attribute] }}
                    </template>
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
        columns: {
            type: Array,
            required: true,
        },
        sort: {
            type: Function,
        }
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
