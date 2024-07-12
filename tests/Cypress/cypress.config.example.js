const { defineConfig } = require('cypress')

module.exports = defineConfig({
  e2e: {
    experimentalSessionAndOrigin: true,
  },
  responseTimeout: 80000,
  env: {
    user: 'admin',
    pass: 'shopware',
    salesChannelName: 'Storefront',
    admin: '/admin',
    apiPath: 'api',
    locale: 'en-GB',
    shopwareRoot: "../../../../..",
    localUsage: false,
    usePercy: false,
    minAuthTokenLifetime: 60,
    acceptLanguage: 'en-GB,en;q=0.5',
    expectedVersion: '6.6.',
    grepOmitFiltered: true,
    grepFilterSpecs: true,
  },
})