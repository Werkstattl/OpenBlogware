const { join, resolve } = require('path');

module.exports = () => {
    return {
        resolve: {
            alias: {
                '@slugify': resolve(
                    join(__dirname, '..', 'node_modules', 'slugify')
                )
            }
        }
    };
}

