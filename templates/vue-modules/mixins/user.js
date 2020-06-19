import store from "../polytable-store.js";
export default {
    computed : {
        user () {
            return store.state.user.user;
        }
    }
}