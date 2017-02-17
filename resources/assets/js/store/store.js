import Vue from 'vue'
import Vuex from 'vuex'
import * as actions from './actions'
import * as getters from './getters'
import mutations from './mutations'
import sidenav from './modules/sidenav'
import users from './modules/users'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	state: {
		currentView: ''
	},
	getters,
	mutations,
	actions,
	modules: {
		sidenav,
		users
	},
	strict: debug
})
