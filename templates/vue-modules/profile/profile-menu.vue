<template>
    <div id="menu">
        <menu-item>
            <a href="/"><img src="assets/images/logo3.png"/></a>
        </menu-item>
        <menu-item class="card">
            <div class="avatar"><img v-bind:src="src"/></div>
            <div class="name">{{user['login']}}</div>
            <div class="post">{{user['post']}}</div>
        </menu-item>
        <menu-item v-for="(value, key) in items" :key="key">
            <i v-bind:class="value.icon" class="ui icon"></i>
            {{value.text}}
        </menu-item>
        <menu-item>
            <i class="ui icon sign out alternate"></i>
            Выход
        </menu-item>
    </div>
</template>

<script>
    import userMixin from "../mixins/user.js";
    import store from "../polytable-store.js";
    import menuItem from "./profile-menu-item.vue";
    export default {
        name: "profile-menu",
        store,
        mixins: [userMixin],
        computed : {
            src : function () {
                return `data/image/64/${this.user['id']}.png`
            },
            items () {
                return store.state.menu.items;
            }
        },
        components : {
            menuItem
        }
    }
</script>
