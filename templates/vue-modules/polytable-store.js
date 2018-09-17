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
        add (state, name, item) {
            state[name] = item;
        },
        remove (state, name) {
            delete state[name];
        }
    }
};

export default new Vuex.Store({
    modules : {
        user : user,
        menu : menuItems
    }
});