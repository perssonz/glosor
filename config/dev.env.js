'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')
/*
module.exports = {
        dev: {
                proxyTable: {
                        // proxy all requests starting with /cgi to jsonplaceholder
                        '/cgi': {
                                target: 'http://localhost/glosor/cgi',
                                changeOrigin: true,
                                pathRewrite: {
                                  '^/cgi': ''
                                }
                        }
                }
        }
}
*/

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"'
})
