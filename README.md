![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1584709250/preview-blog_nn8mcq.jpg)

# Shopware 6 Blog Plugin
After the plugin installation you can find the entity if you hop to `content -> blog`.

![](https://sbp-plugin-images.s3.eu-west-1.amazonaws.com/phpZoe2dS)
*Blog listing view*

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1608026832/Bildschirmfoto_2020-12-15_um_12.06.25_ahbgze.png)
*Blog overview page with categories*

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1608026833/Bildschirmfoto_2020-12-15_um_12.06.31_tz3qql.png)
*blog detail view*

### Configuration
The plugin makes use of two CMS Elements which are part of two different CMS Layouts.
During the plugin installation those two CMS pages will be created for you:
* Blog Listing Page which contains a Blog Detail element
* Blog Detail Page which contains a Blog Listing element

Within the plugin configuration the **Blog Detail Page ID** is assigned,
so Shopware knows which CMS Page to use for the detail page.

#### Menu entry
You need to create a new category within your category tree
and assign the **Blog Listing** CMS Page.

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1602580652/Bildschirmfoto_2020-10-13_um_12.16.54_nmtgdw.png)
*Category entry*

After this you will see all blog articles within your menu/category entry in the storefront.

### CMS Listing Element
Currently you can only set the number of posts showing per page for the pagination.

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1602580706/Bildschirmfoto_2020-10-13_um_12.18.22_bdghy1.png)
*CMS Listing element configuration*

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1602581049/Bildschirmfoto_2020-10-13_um_12.23.42_popsgs.png)
*Pagination within the storefront*

### SEO Url
Within the `Settings > SEO` page you can define the structure of the URL to your blog detail page
where you can also select from all available variables.

![](https://res.cloudinary.com/dtgdh7noz/image/upload/v1602580850/Bildschirmfoto_2020-10-13_um_12.20.25_xxnrro.png)
*SEO URL template*

## RSS Feed
For access **RSS Feed** url you can use this path `/blog/rss`
Example(`http://Your-domain/blog/rss`)
