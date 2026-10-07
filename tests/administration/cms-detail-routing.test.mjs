import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { test } from 'node:test';

class Criteria {
    constructor(page, limit) {
        this.page = page;
        this.limit = limit;
        this.filters = [];
    }

    addFilter(filter) {
        this.filters.push(filter);
    }

    static equals(field, value) {
        return { field, value };
    }
}

const apiContext = {};
globalThis.Shopware = { Data: { Criteria }, Context: { api: apiContext } };

// Load the actual administration override without a Vite build.
const source = await readFile(new URL(
    '../../src/Resources/app/administration/src/module/blog-module/extension/sw-cms/page/sw-cms-detail/index.js',
    import.meta.url
), 'utf8');
const { default: override } = await import(
    `data:text/javascript;base64,${Buffer.from(source).toString('base64')}`
);

function createInstance(search, id = 'cms-page-a') {
    const calls = [];
    const instance = {
        ...override.data?.(),
        ...override.methods,
        $route: { name: 'sw.cms.detail', params: { id } },
        $router: { replace: (route) => calls.push({ redirect: route }) },
        $super: (method) => calls.push({ core: method }),
        repositoryFactory: {
            create(entity) {
                assert.equal(entity, 'werkl_blog_entry');
                return { search };
            },
        },
    };

    return { instance, calls };
}

function deferred() {
    let resolve;
    let reject;
    const promise = new Promise((resolvePromise, rejectPromise) => {
        resolve = resolvePromise;
        reject = rejectPromise;
    });
    return { promise, resolve, reject };
}

const result = (entry) => ({ first: () => entry });

test('a linked CMS page redirects to its blog entry', async () => {
    const { instance, calls } = createInstance(async (criteria, context) => {
        assert.equal(criteria.limit, 1);
        assert.deepEqual(criteria.filters, [{ field: 'cmsPageId', value: 'cms-page-a' }]);
        assert.equal(context, apiContext);
        return result({ id: 'blog-entry-a' });
    });

    await instance.createdComponent();
    assert.deepEqual(calls, [{
        redirect: { name: 'blog.module.detail', params: { id: 'blog-entry-a' } },
    }]);
});

test('an unlinked CMS page opens in the core editor', async () => {
    const { instance, calls } = createInstance(async () => result(null));
    await instance.createdComponent();
    assert.deepEqual(calls, [{ core: 'createdComponent' }]);
});

test('an API failure falls back to the core editor', async () => {
    const { instance, calls } = createInstance(async () => { throw new Error('API unavailable'); });
    await instance.createdComponent();
    assert.deepEqual(calls, [{ core: 'createdComponent' }]);
});

test('a route without a page ID never searches for a blog entry', async () => {
    const { instance, calls } = createInstance(() => assert.fail('Unexpected lookup'), null);
    await instance.createdComponent();
    assert.deepEqual(calls, [{ core: 'createdComponent' }]);
});

for (const entry of [{ id: 'blog-entry-a' }, null]) {
    const kind = entry ? 'linked' : 'unlinked';

    test(`a late ${kind} result cannot affect another route`, async () => {
        const response = deferred();
        const { instance, calls } = createInstance(() => response.promise);
        const pending = instance.createdComponent();
        instance.$route = { name: 'sw.product.detail', params: { id: 'product-b' } };
        response.resolve(result(entry));
        await pending;
        assert.deepEqual(calls, []);
    });

    test(`a late ${kind} result cannot affect an unmounted component`, async () => {
        const response = deferred();
        const { instance, calls } = createInstance(() => response.promise);
        const pending = instance.createdComponent();
        override.beforeUnmount?.call(instance);
        response.resolve(result(entry));
        await pending;
        assert.deepEqual(calls, []);
    });
}

test('a late API failure cannot initialize the editor after navigation', async () => {
    const response = deferred();
    const { instance, calls } = createInstance(() => response.promise);
    const pending = instance.createdComponent();
    instance.$route = { name: 'sw.product.index', params: {} };
    response.reject(new Error('API unavailable'));
    await pending;
    assert.deepEqual(calls, []);
});

test('a new route with the same ID cannot use the previous lookup', async () => {
    const response = deferred();
    const { instance, calls } = createInstance(() => response.promise);
    const pending = instance.createdComponent();
    instance.$route = { name: 'sw.cms.detail', params: { id: 'cms-page-a' } };
    response.resolve(result({ id: 'blog-entry-a' }));
    await pending;
    assert.deepEqual(calls, []);
});
