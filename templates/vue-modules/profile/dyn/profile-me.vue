<template>
    <div class="component" v-bind:id="type">
        <h1>УЧЕТНАЯ ЗАПИСЬ</h1>
        <div id="image_box">
            <img :src="imageSrc"/>
            <div class="login">{{data.login}}</div>
            <div class="post">{{data.title}}</div>
            <div class="group"><a :href="groupLink">{{data.group}}</a></div>
        </div>
        <h2>ЛИЧНЫЕ ДАННЫЕ</h2>
        <div class="form">
            <div class="input">
                <label for="user-email">Электронная почта</label>
                <div class="input_icon"><i class="ui icon envelope"></i></div>
                <input name="email" id="user-email" class="labeled" type="email" maxlength="64" disabled>
            </div>
        </div>
    </div>
</template>

<script>
    import menuItem from "../../mixins/menu-item-mount.js"
    export default {
        name : "profile-me",
        props: ['data', 'type'],
        data () {
            return {
                menuItem : {
                    icon : "user",
                    text : "Учетная запись"
                }
            }
        },
        computed : {
            groupLink () {
                return `https://polytable.ru/groups.php?id=${this.data.group}`
            },
            imageSrc () {
                return `/data/image/64/${this.data.id}.png`
            }
        },
        mixins:[menuItem]
    }
</script>

