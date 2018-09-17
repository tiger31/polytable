import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const user = {
    state : {
        user : {}
    },
    mutations : {
        update (state, user) {
            state.user = Object.assign({}, state.user, user);
        }
    }
};

const menuItems = {
    state : {
        items : {}
    },
    mutations : {
        add (state, payload) {
           Vue.set(state.items, payload.name, payload.item);
        },
        remove (state, name) {
            delete state.items[name];
        }
    }
};

export default new Vuex.Store({
    modules : {
        user : user,
        menu : menuItems
    }
});