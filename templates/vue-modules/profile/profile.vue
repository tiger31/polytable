<template>
    <div id="profile">
        <profile-menu
                v-bind:modules-list="modulesList"
                v-bind:user-info="userInfo">
        </profile-menu>
        <div id="content">
            <div id="modules" v-for="modules in modulesList">

            </div>
        </div>
    </div>
</template>

<script>
    import ProfileMenu from "./profile-menu.vue";
    import axios from 'axios'

    const url = "/action.php";
    axios.defaults.headers.common['X-Requested-With'] = "XMLHttpRequest";

    export default {
        name: "profile",
        data : function() {
            return {
                profileData: {},
                userInfo: {}
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
                this.userInfo = response.data.user;
            }).catch(function (error) {
                console.error(error);
            })
        },
        components : {
            ProfileMenu
        },
        computed: {
            modulesList: function () {
                return Object.keys(this.profileData);
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../css/profile-dev.scss";
</style>