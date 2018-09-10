# Fetch

**Access content directly as JSON using URL endpoints or via a simple tag.**

This addon will automatically make any of your collections, pages & globals accessible via a URL endpoint, `GET` / `POST` request, or via a simple tag.

Accessing your content via URL endpoints can be very useful when you need to fetch your site's data dynamically using Ajax or when you need access from another domain.

## Getting started

To get started and for a list of available parameters, check out the [docs](https://statamic.com/marketplace/addons/fetch/docs).

## Example

Say we have a simple collection of blog entries and a single entry looks like:

```yaml
title: My Awesome Blog Post
id: f1f92ae6-00a8-4626-a1fc-de900a7f9203
content: Lorem ipsum aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.
categories:
  - statamic
  - addons
images:
  - /assets/img/cool-stuff.jpg
  - /assets/img/pretty-flowers.jpg
```

The returned data would look like:

```json
[
    {
        "title": "My Awesome Blog Post",
        "id": "7285536e-c383-46fc-8b09-8ae117446d85",
        "content": "Lorem ipsum aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.",
        "categories": [
            {
                "title": "Statamic",
                "id": "categories/statamic",
                "slug": "statamic",
                ...
            },
            {
                "title": "Awesome Addons",
                "id": "categories/addons",
                "slug": "addons",
                ...
            }
        ],
        "images": [
            "http://domain.com/assets/img/cool-stuff.jpg",
            "http://domain.com/assets/img/pretty-flowers.jpg"
        ],
        "slug": "my-awesome-blog-post",
        "url": "/blog/2016-11-09-my-awesome-blog-post",
        "permalink": "http://domain.com/blog/2016-11-09-my-awesome-blog-post",
        ...
    },
    ...
]
```

*Note: a lot more data is returned, this is simply a summary for the example.*
