<template>
    <div class="my-component">
        <div> {{ message.text }} </div>
        <div><button @click="changeMessage">Click me</button></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: {
                type: 'greeting',
                text: 'How are you'
            }
        };
    },
    methods: {
        changeType() {
            this.message.type = "This is a NEW Type";
        },
        changeText() {
            this.message.text = 'This is NEW Text'
        }
    },
    computed: {
        getType(){
            return this.message.type
        },
        getText(){
            return this.message.text
        }
    },
    watch: {
        getType() {
            console.log('type changed')
        },
        getText(){
            console.log('text changed')
        }
    }
};
</script>

<style lang="scss" scoped>
.my-component {
    color: blueviolet;
}
</style>