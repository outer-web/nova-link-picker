<template>
    <div>
        <a
            class="link-default"
            target="_blank"
            v-if="field.href"
            :href="field.href"
            :download="field.download ? field.download : null"
        >
            {{ href }}
        </a>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
export default {
    props: ["resourceName", "field"],
    computed: {
        href() {
            if (this.field.abbreviated) {
                return this.field.href.substring(0, 20) + "...";
            }

            return this.field.href;
        },
    },
};
</script>
