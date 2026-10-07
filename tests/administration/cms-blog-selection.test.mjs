import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { test } from 'node:test';

class Criteria {
    setIds(ids) {
        this.ids = ids;
    }
}

class EntityCollection extends Map {
    constructor(route, entity, context, criteria, entities = []) {
        super(entities.map((item) => [item.id, item]));
        this.source = route;
        this.entity = entity;
        this.context = context;
    }

    getIds() {
        return [...this.keys()];
    }
}

const apiContext = {};
const registry = {};
globalThis.Shopware = {
    Context: { api: apiContext },
    Data: { Criteria, EntityCollection },
    Mixin: { getByName: () => ({}) },
    Component: { register() {} },
    Service: () => ({
        registerCmsElement: (element) => { registry[element.name] = element; },
        getCollectFunction: () => () => {},
    }),
};

// Exercise the actual registrations and component methods without a Vite build.
async function loadModule(path) {
    const source = await readFile(new URL(
        '../../src/Resources/app/administration/src/module/blog-module/elements/' + path,
        import.meta.url,
    ), 'utf8');
    const script = 'const template = "";\n' + source.replace(/^import .*;\n/gm, '');
    return import(`data:text/javascript;base64,${Buffer.from(script).toString('base64')}`);
}

await loadModule('blog/index.js');
await loadModule('blog-newest-listing/index.js');
const { default: blog } = await loadModule('blog/config/index.js');
const { default: newest } = await loadModule('blog-newest-listing/config/index.js');

function createInstance(component, type, config = registry[type].defaultConfig) {
    const calls = [];
    const repository = (entity) => ({
        route: entity,
        schema: { entity },
        async search(criteria) {
            calls.push({ entity, ids: [...criteria.ids] });
            return collection(entity, criteria.ids);
        },
    });
    const instance = {
        ...component.data(),
        ...component.methods,
        element: {
            config: structuredClone(config),
            translated: { config: structuredClone(config) },
            data: {},
        },
        blogCategoryRepository: repository('werkl_blog_category'),
        tagRepository: repository('tag'),
        // The CMS mixin preserves existing field values, including legacy nulls.
        initElementConfig() {},
        initElementData() {},
    };
    return { instance, calls };
}

function collection(entity, ids) {
    return new EntityCollection(entity, entity, apiContext, new Criteria(), ids.map((id) => ({ id })));
}

for (const [type, component] of [['blog', blog], ['blog-newest-listing', newest]]) {
    test(`${type}: a new element opens with empty selections`, async () => {
        const { instance, calls } = createInstance(component, type);
        await instance.createdComponent();
        assert.deepEqual(instance.blogCategoryCollection.getIds(), []);
        if (type === 'blog') {
            assert.deepEqual(instance.tagCollection.getIds(), []);
        }
        assert.deepEqual(calls, []);
    });

    test(`${type}: existing null selections open without blocking initialization`, async () => {
        const config = structuredClone(registry[type].defaultConfig);
        config.blogCategories.value = null;
        if (type === 'blog') {
            config.blogTags.value = null;
        }
        const { instance, calls } = createInstance(component, type, config);
        await instance.createdComponent();
        assert.deepEqual(instance.blogCategoryCollection.getIds(), []);
        if (type === 'blog') {
            assert.deepEqual(instance.tagCollection.getIds(), []);
        }
        assert.deepEqual(calls, []);
    });

    test(`${type}: category changes preserve config metadata and survive reopening`, async () => {
        const config = structuredClone(registry[type].defaultConfig);
        config.blogCategories.value = [];
        if (type === 'blog') {
            config.blogTags.value = [];
        }
        const { instance } = createInstance(component, type, config);
        await instance.createdComponent();
        const categoryField = structuredClone(instance.element.config.blogCategories);
        instance.blogCategoryCollection = collection('werkl_blog_category', ['category-a', 'category-b']);
        instance.onBlogCategoriesChange();

        const expected = { ...categoryField, value: ['category-a', 'category-b'] };
        assert.deepEqual(instance.element.config.blogCategories, expected);
        assert.deepEqual(instance.element.translated.config.blogCategories, expected);
        assert.equal(instance.element.data.blogCategories, instance.blogCategoryCollection);

        const saved = JSON.parse(JSON.stringify(instance.element.config));
        const { instance: reopened, calls } = createInstance(component, type, saved);
        await reopened.createdComponent();
        assert.deepEqual(reopened.blogCategoryCollection.getIds(), ['category-a', 'category-b']);
        assert.deepEqual(calls, [{ entity: 'werkl_blog_category', ids: ['category-a', 'category-b'] }]);

        reopened.blogCategoryCollection.clear();
        reopened.onBlogCategoriesChange();
        assert.deepEqual(reopened.element.config.blogCategories, categoryField);
        assert.deepEqual(reopened.element.translated.config.blogCategories, categoryField);
    });

    test(`${type}: category config still updates without preview or translated data`, () => {
        const { instance } = createInstance(component, type);
        delete instance.element.data;
        delete instance.element.translated;
        instance.blogCategoryCollection = collection('werkl_blog_category', ['category-a']);
        instance.onBlogCategoriesChange();
        assert.deepEqual(instance.element.config.blogCategories.value, ['category-a']);
    });
}

test('blog: stored tags still load when categories are null', async () => {
    const config = structuredClone(registry.blog.defaultConfig);
    config.blogCategories.value = null;
    config.blogTags.value = ['tag-a', 'tag-b'];
    const { instance, calls } = createInstance(blog, 'blog', config);
    await instance.createdComponent();
    assert.deepEqual(instance.tagCollection.getIds(), ['tag-a', 'tag-b']);
    assert.deepEqual(calls, [{ entity: 'tag', ids: ['tag-a', 'tag-b'] }]);
});

test('blog: tag update payload persists IDs and metadata through save and reopen', async () => {
    const config = structuredClone(registry.blog.defaultConfig);
    config.blogCategories.value = [];
    config.blogTags.value = [];
    const { instance } = createInstance(blog, 'blog', config);
    await instance.createdComponent();
    const tagField = structuredClone(instance.element.config.blogTags);
    const selected = collection('tag', ['tag-a', 'tag-b']);
    instance.onTagsChange(selected);

    const expected = { ...tagField, value: ['tag-a', 'tag-b'] };
    assert.equal(instance.tagCollection, selected);
    assert.deepEqual(instance.element.config.blogTags, expected);
    assert.deepEqual(instance.element.translated.config.blogTags, expected);
    assert.equal(instance.element.data.blogTags, selected);

    const saved = JSON.parse(JSON.stringify(instance.element.config));
    const { instance: reopened, calls } = createInstance(blog, 'blog', saved);
    await reopened.createdComponent();
    assert.deepEqual(reopened.tagCollection.getIds(), ['tag-a', 'tag-b']);
    assert.deepEqual(calls, [{ entity: 'tag', ids: ['tag-a', 'tag-b'] }]);

    reopened.onTagsChange(collection('tag', []));
    assert.deepEqual(reopened.element.config.blogTags, tagField);
    assert.deepEqual(reopened.element.translated.config.blogTags, tagField);
});

test('blog: tag config still updates without preview or translated data', () => {
    const { instance } = createInstance(blog, 'blog');
    delete instance.element.data;
    delete instance.element.translated;
    instance.onTagsChange(collection('tag', ['tag-a']));
    assert.deepEqual(instance.element.config.blogTags.value, ['tag-a']);
});
