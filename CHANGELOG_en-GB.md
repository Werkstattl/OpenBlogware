# 1.5.8
* Fix error when run bin/console dal:validation [#97](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/97/files)
* Fix SERP Information and Preview Picture for shopware 6.4.9.0 or later [#100](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/100/files)

# 1.5.7
* Fixed compiled administration file

# 1.5.6
* Integrated ACL [#84](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/84/files)
* Sitemap generation [#78](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/78/files)
* Fixed wrong slug in Umlaute [#83](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/83/files)
* Fixed duplicate Slug [#76](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/76/files)
* Added a plugin option to make the blog search optional [#77](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/77/files)
* RSS Feed - URL: `/blog/rss` [#79](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/79/files)
* Fixed a "back" icon [#80](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/80/files)
* Change `createdAt` to `publishDate` [#81](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/81/files)
* Added SEO Canonical-Link [#82](https://github.com/ChristopherDosin/Shopware-6-Blog-Plugin/pull/82/files)

# 1.5.5
* change access to global Shopware object [wrongspot](https://github.com/wrongspot)
* set navigation id by active navigation [Drumm3r](https://github.com/Drumm3r)

# 1.5.4
* Fixed blog pro loading

# 1.5.3
* Added missing compiled files

# 1.5.2
* Blog Pro compability

# 1.5.1
* Extended the core search, to be able to search also for blog entries
* Fixed bug for the PWA usage

# 1.5.0
* Added ApiAware flags & store-api controller for usage in PWA and Elasticsearch [Drumm3r](https://github.com/Drumm3r)
* Implemented custom fields [gRoberts84](https://github.com/gRoberts84)
* Fixed Bug which caused that you can't create a blog category anymore

# 1.4.1
* fixed migration issue with the salutation if the default `not_specified` was deleted

# 1.4.0
* Added Shopware 6.4 compability

# 1.3.1
* Added DESC sorting to admin blog listing

# 1.3.0
* Added enable/disable filter for author & category to the blog listing element
* Added enable/disable filter for author & category to the blog detail element
* Added a new `publishedAt` date field for a blog entry
* Added back button to blog detail page
* Centered blog detail post with `col-md-8 offset-md-2`

# 1.2.13
* Added category slug in seo url template

# 1.2.12
* fixed single select element if it's `null`

# 1.2.11
* Listing show author is optional, enabled or disabled by the plugin configuration
* Added `sas_blog_card_footer` and `sas_blog_card_footer_author` twig block
* Fixed empty placeholder author image styling

# 1.2.10
* Storefront date locale fix
* Added author to the storefront
* Overhauled general styling of the listing

# 1.2.9
* Teaser thumbnail size adjusted

# 1.2.8
* Fixed an issue with structure data of the author
* replaced "read more" title name with the actual article title

# 1.2.7
* Fixed error if you have a blog listing + blog detail element on the same CMS page
* Removed unnecessary twig filter
* moved single select data resolver to new folder

# 1.2.6
* Improved single select CMS element & block

#1.2.5
* Adjusted date output in blog box template

# 1.2.4
* Added the possibility to translate categories in non-system language

# 1.2.3
* fixed and improved language selection while creating an entry, because an entry has always to be 
in the system language first, before you can switch to another language

# 1.2.2
* fixed category translated issue in twig template

# 1.2.1
* removed editor.js
* added missing description translation

# 1.2.0
* Replaced editor.js with Shopware's default text editor
* Added `Blog Single Select` CMS element, to show a single blog entry card
* Added counter for meta title and description
* Fixed JSON-LD SEO template
* Prepared plugin base for the Blog PRO version

# 1.1.5
* Added missing German author translations

# 1.1.4
* Added author entity
* Added category entity

# 1.1.3
* changed `SalesChannelCmsPageLoader` to `SalesChannelCmsPageLoaderInterface` in `BlogController`

# 1.1.2
* fixed Blog Plugin kills all meta-Infos from all other pages #20
* fixed Stay on blogpost when saving #14
* fixed Error 500 when using «Raw HTML» in Editor #9

# 1.1.1
* fixed license to MIT

# 1.1.0
* Added teaser image within listing
* fixed error messages if title or slug is empty
* added publish date to listing card

# 1.0.0
* First release in Store
