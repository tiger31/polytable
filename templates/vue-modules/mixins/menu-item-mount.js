import store from "../polytable-store.js";
export default {
    mounted () {
        store.commit("add", { name : this.type, item : this.menuItem });
    }
}