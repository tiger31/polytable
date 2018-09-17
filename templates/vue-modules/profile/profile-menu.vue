<template>
    <div id="menu">
        <div class="menu-item">
            <a href="/"><img src="assets/images/logo3.png"/></a>
        </div>
        <div class="menu-item card">
            <div class="avatar"><img v-bind:src="src"/></div>
            <div class="name">{{userInfo.login}}</div>
            <div class="post">{{userInfo.post}}</div>
        </div>
        <div class="menu-item" v-for="module in modulesList">
            {{module}}
        </div>
    </div>
</template>

<script>
    export default {
        name: "profile-menu",
        props: ['modules-list', 'user-info'],
        computed : {
            src : function () {
                return `data/image/64/${this.userInfo.id}.png`
            }
        }
    }
</script>
