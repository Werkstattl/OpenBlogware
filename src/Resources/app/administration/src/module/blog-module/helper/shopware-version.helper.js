/**
 * Check if the current shopware version is equal or higher than the given version
 *
 * @param version
 * @returns {boolean}
 */
function versionGTE(version) {
    const shopwareVersion = Shopware.Context.app.config.version;
    return versionCompare(shopwareVersion, version, '>=');
}

/**
 * Same as php version_compare
 * compares two "PHP-standardized" version number strings
 *
 * @link https://php.net/manual/en/function.version-compare.php
 * @param version1 First version number.
 * @param version2 Second version number.
 * @param operator If you specify the third optional operator
 * argument, you can test for a particular relationship.
 * This parameter is case-sensitive, so values should be lowercase.
 * @returns By default, returns
 * -1 if the first version is lower than the second,
 * 0 if they are equal, and
 * 1 if the second is lower.
 * When using the optional operator argument, the
 * function will return true if the relationship is the one specified
 * by the operator, false otherwise.
 */
function versionCompare(version1, version2, operator) {
    let index;
    let compare = 0;

    version1 = _prepVersion(version1);
    version2 = _prepVersion(version2);
    const maxLengthOfTwo = Math.max(version1.length, version2.length);

    for (index = 0; index < maxLengthOfTwo; index++) {
        if (version1[index] === version2[index]) {
            continue;
        }

        version1[index] = _numVersion(version1[index]);
        version2[index] = _numVersion(version2[index]);
        if (version1[index] < version2[index]) {
            compare = -1;
            break;
        } else if (version1[index] > version2[index]) {
            compare = 1;
            break;
        }
    }

    if (!operator) {
        return compare;
    }

    switch (operator) {
        case '>':
        case 'gt':
            return (compare > 0);
        case '>=':
        case 'ge':
            return (compare >= 0);
        case '<=':
        case 'le':
            return (compare <= 0);
        case '===':
        case '=':
        case 'eq':
            return (compare === 0);
        case '<>':
        case '!==':
        case 'ne':
            return (compare !== 0);
        case '':
        case '<':
        case 'lt':
            return (compare < 0);
        default:
            return null;
    }
}

/**
 * VERSION_PRIORITY maps textual PHP versions to negative, so they're less than 0.
 * PHP currently defines these as CASE-SENSITIVE. It is important to
 * leave these as negatives so that they can come before numerical versions
 * and as if no letters were there to begin with.
 * (1alpha is < 1 and < 1.1 but > 1dev1)
 * If a non-numerical value can't be mapped to this table, it receives
 * -7 as its value.
 */
const VERSION_PRIORITY = {
    dev: -6,
    alpha: -5,
    a: -5,
    beta: -4,
    b: -4,
    RC: -3,
    rc: -3,
    '#': -2,
    p: 1,
    pl: 1,
};

/**
 * This function will be called to prepare each version argument.
 * It replaces every _, -, and + with a dot.
 * It surrounds any non-sequence of numbers/dots with dots.
 * It replaces sequences of dots with a single dot.
 *   version_compare('4..0', '4.0') === 0
 * Important: A string of 0 length needs to be converted into a value
 * even less than an un-existing value in VERSION_PRIORITY (-7), hence [-8].
 * It's also important to not strip spaces because of this.
 *   version_compare('', ' ') === 1
 *
 * @private
 * @param version
 * @returns {number[]|string[]}
 */
function _prepVersion(version) {
    if (!version.length) {
        return [-8];
    }

    version = ('' + version).replace(/[_\-+]/g, '.');
    version = version.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');

    return version.split('.');
}

/**
 * This converts a version component to a number.
 * Empty component becomes 0.
 * Non-numerical component becomes a negative number.
 * Numerical component becomes itself as an integer.
 *
 * @private
 * @param version
 * @returns {number}
 */
function _numVersion(version) {
    if (!version) {
        return 0;
    }

    if (isNaN(version)) {
        return VERSION_PRIORITY[version] || -7;
    }

    return parseInt(version, 10);
}

export default {
    versionGTE,
    versionCompare,
};
