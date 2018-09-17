<template>
    <component :is="component" :data="data" v-if="component"></component>
</template>

<script>
    export default {
        name: "profile-dyn-component",
        props: ['data', 'type'],
        data() {
            return {
                component: null,
            }
        },
        computed: {
            loader() {
                if (!this.type) {
                    return null
                }
                return () => import(`./dyn/${this.type}.vue`)
            },
        },
        mounted() {
            this.loader()
                .then(() => {
                    this.component = () => this.loader()
                })
                .catch(() => {
                    this.component = () => import('./dyn/default.vue');
                })
        },
    }
</script>

<style scoped>

</style>