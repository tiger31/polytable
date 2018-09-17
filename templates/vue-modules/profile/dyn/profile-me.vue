<template>
    <div class="module">me</div>
</template>

<script>
    export default {
        name : "profile-me",
        props: ['data'],
        data : function () {
            return {
                menuItem : {
                    icon : "user",
                    test : "Учетная запись"
                }
            }
        }
    }
</script>

