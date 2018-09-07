# Fetch

**Access Collections and Pages directly as JSON using GET/POST requests or via a simple tag.**

This addon will automatically make any of your collections & pages accessible via a URL endpoint, `GET` / `POST` request, or via a simple tag.

Accessing your collection entries & pages data via URL endpoints can be very useful when you need to fetch your site's data dynamically using XHR (XMLHttpRequest) or when you need access from another domain.

## Getting started

To get started and for a list of available options, check out the [docs](https://statamic.com/marketplace/addons/fetch/docs).

## Example

Say we have a simple collection of blog entries and a single entry looks like:

```yaml
title: My Awesome Blog Post
id: f1f92ae6-00a8-4626-a1fc-de900a7f9203
content: Lorem ipsum aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.
categories:
  - bdc2086c-961b-4b84-8c65-da0e6823e6e9
  - e13a11ca-aaa2-4e7b-83ec-15e904f918a8
images:
  - 74a39e87-16c2-4052-a028-26f00baca541
  - edc7fd1e-ca3a-4528-b4b7-f3be6ad87d8a
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
                "id": "bdc2086c-961b-4b84-8c65-da0e6823e6e9"
            },
            {
                "title": "Awesome Addons",
                "id": "e13a11ca-aaa2-4e7b-83ec-15e904f918a8"
            },
        ],
        "images": [
            "http://domain.com/img/id/74a39e87-16c2-4052-a028-26f00baca541"
            "http://domain.com/img/id/edc7fd1e-ca3a-4528-b4b7-f3be6ad87d8a"
        ],
        "slug": "my-awesome-blog-post",
        "url": "/blog/2016-11-09-my-awesome-blog-post",
        "permalink": "http://domain.com/blog/2016-11-09-my-awesome-blog-post",
        ...
    },
    ...
]
```

*(Please note: there's actually a lot more data that is returned, this is simply a summary for the example)*
