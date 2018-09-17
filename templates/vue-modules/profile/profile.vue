<template>
    <div id="profile">
        <profile-menu
                v-bind:modules-list="componentsList">
        </profile-menu>
        <div id="content">
            <div id="components" v-if="dataLoaded" v-for="component in componentsList">
                <profile-dyn-component :data="profileData[component]" :type="component"></profile-dyn-component>
            </div>
        </div>
    </div>
</template>

<script>
    import store from "../polytable-store.js";
    import ProfileMenu from "./profile-menu.vue";
    import axios from 'axios'
    import ProfileDynComponent from "./profile-dyn-component.vue";

    const url = "/action.php";
    axios.defaults.headers.common['X-Requested-With'] = "XMLHttpRequest";

    export default {
        name: "profile",
        store,
        data : function() {
            return {
                dataLoaded : false,
                profileData: {},
            }
        },
        created: function () {
            axios.get(url, {
                params : {
                    action : "profile",
                },
                xsrfCookieName : "X-CSRF-TOKEN"
            }).then(response => {
                this.profileData = response.data.data;
                this.dataLoaded = true;
                store.commit("update", response.data.user);
            }).catch(function (error) {
                console.error(error);
            })
        },
        components : {
            ProfileDynComponent,
            ProfileMenu
        },
        computed: {
            componentsList: function () {
                return Object.keys(this.profileData);
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../css/profile-dev.scss";
</style>