<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div>
                <a
                    class="link-default"
                    target="_blank"
                    v-if="field.href"
                    :href="field.href"
                    :download="field.download ? field.download : null"
                >
                    {{ field.href }}
                </a>
                <p v-else>&mdash;</p>
            </div>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ["index", "resource", "resourceName", "resourceId", "field"],
};
</script>
