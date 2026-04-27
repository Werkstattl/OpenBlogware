const { Mixin } = Shopware;

Mixin.register('version-compare', {
    methods: {
        compareVersion(
            targetVersion,
            comparison = 'up',
            currentVersion = Shopware.Context.app.config.version,
            versionLength = 4
        ) {
            const normalizeVersion = function (version) {
                const core = String(version || '').split('-')[0];
                const parts = core.split('.').map((part) => parseInt(part, 10));

                while (parts.length < versionLength) {
                    parts.push(0);
                }

                return parts
                    .slice(0, versionLength)
                    .map((part) => (Number.isFinite(part) ? part : 0));
            };

            const target = normalizeVersion(targetVersion);
            const current = normalizeVersion(currentVersion);

            for (let i = 0; i < versionLength; i++) {
                if (current[i] === target[i]) {
                    continue;
                }

                if (comparison === 'up') {
                    return current[i] > target[i];
                } else if (comparison === 'down') {
                    return current[i] < target[i];
                } else {
                    throw new Error(
                        `Unknown comparison type "${comparison}". Use "up" or "down".`
                    );
                }
            }

            return true;
        },
    },
});
