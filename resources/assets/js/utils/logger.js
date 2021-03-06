import _ from 'lodash'
import Vue from 'vue'
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import { isDev, isDebug, envValue } from 'js/utils/env'

export default class Logger {
	// Log levels as per https://tools.ietf.org/html/rfc5424
	static get EMERGENCY() { return 'emergency'; }
	static get ALERT()     { return 'alert'; }
	static get CRITICAL()  { return 'critical'; }
	static get ERROR()     { return 'error'; }
	static get WARNING()   { return 'warning'; }
	static get NOTICE()    { return 'notice'; }
	static get INFO()      { return 'info'; }
	static get DEBUG()     { return 'debug'; }

	static get LEVELS() {
		return {
			'emergency' : 0,
			'alert'     : 1,
			'critical'  : 2,
			'error'     : 3,
			'warning'   : 4,
			'notice'    : 5,
			'info'      : 6,
			'debug'     : 7,
		}
	}

	constructor(options = {}) {
		if (this.useExternal()) {
			Raven
				.config(envValue('SENTRY_DSN_VUE_PUB'))
				.setTagsContext({
					env: envValue('appEnv'),
				})
				.addPlugin(RavenVue, Vue)
				.install()
		}

		this.level     = envValue('APP_LOG_LEVEL')
		this.levelCode = Logger.LEVELS[this.level]
	}

	useExternal() {
		return !isDev()
	}

	log(level, message) {
		if (Logger.LEVELS[level] <= this.levelCode) {
			if (this.useExternal()) {
				Raven.captureMessage(message, { level })
			}

			if (isDebug()) {
				this.consolePrint(level, message)
			}
		}
	}

	capture(exception) {
		if (this.useExternal()) {
			Raven.captureException(exception)
		}

		if (isDebug()) {
			this.error(exception.message, exception.stack)
		}
	}

	/**
	 * Requires at least 2 arguments - level and header.
	 * If 2 arguments are passed, it treats the 1st one as a log level,
	 * and the 2nd one as a message.
	 * If more arguments are passed, the 2nd one is treated
	 * as a header of a group.
	 * @return {void}
	 */
	consolePrint(level, messages = []) {
		const len = messages.length

		if (len < 1) {
			return undefined
		}

		if (!Logger.LEVELS.hasOwnProperty(level)) {
			level = 'debug'
		}

		let levelCode = Logger.LEVELS[level],
			header = `${_.upperCase(level)}: ${messages[0]}`

		if (levelCode <= Logger.LEVELS.error) {
			console.error(header)
		} else if (levelCode <= Logger.LEVELS.warning) {
			console.warn(header)
		} else if (levelCode <= Logger.LEVELS.info) {
			console.info(header)
		} else {
			console.debug(header)
		}

		if (len > 1) {
			console.groupCollapsed(`Context for ${header}`)
			for (let i = 1; i < len; i++) {
				console.log(messages[i])
			}
			console.groupEnd()
		}
	}

	emergency(...args) {
		this.log(Logger.EMERGENCY, args)
	}

	alert(...args) {
		this.log(Logger.ALERT, args)
	}

	critical(...args) {
		this.log(Logger.CRITICAL, args)
	}

	error(...args) {
		this.log(Logger.ERROR, args)
	}

	warning(...args) {
		this.log(Logger.WARNING, args)
	}

	notice(...args) {
		this.log(Logger.NOTICE, args)
	}

	info(...args) {
		this.log(Logger.INFO, args)
	}

	debug(...args) {
		this.log(Logger.DEBUG, args)
	}
}
