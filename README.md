# Fetch

**Access your collection entries as JSON directly using URL endpoints or via a simple tag.**

This addon will automatically make any of your collections accessible via a URL endpoint, or via a simple tag.

Accessing your collection entries via URL endpoints can be very useful when you need to fetch your site's data dynamically using XHR (XMLHttpRequest) or when you need access from another domain.

## Installation

Simply copy the `Fetch` folder into `site/addons/`. That's it!

## Usage

By default, Fetch will 'go deep' and find all nested data recursively within your entries. This means that any related content (saved as an ID) will also be fetched and returned.

This behavior can be disabled via Fetch's settings (CP > Configure > Addons > Fetch). You can also enable/disable deep fetching per request via a query string/tag option (see below for further details). When disabled, only a shallow fetch will be performed; related data will simply be returned as its ID.
  
## Settings

The settings page is accessed via `CP > Configure > Addons > Fetch`.

* **Deep** (boolean): Site default to 'go deep' when fetching data.
* **Enable API Key** (boolean): Whether to use the API Key for authentication.
* **API Key** (string): Generate an API Key. Only used when `Enable API Key` is set to true.
* **IP Whitelist** (array): Add a list of IP addresses that are whitelisted to make requests. Leave blank to allow any.

## Options

* **deep** (boolean) [ *Default: true* ]: Fetch nested data recursively, works for arrays as well as related content.
  * URL param: `http://domain.com/!/Fetch/collection/blog?deep=true`.
  * Tag option: `{{ fetch:blog deep="true" }}`.
* **debug** (boolean) [ *Default: false* ]: Dump all data on the page (useful to check what data is available).
  * URL param: `http://domain.com/!/Fetch/collection/blog?debug=true`.
  * Tag option: `{{ fetch:blog debug="true" }}`.
* **api_key** (string): When `Enable API Key` is activated in the settings, make sure to add it to every query.
  * URL param: `http://domain.com/!/Fetch/collection/blog?api_key=[YOUR_KEY_HERE]`.

## Example

Now, say we have a simple collection of blog entries and a single entry looks like:

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

### URL Endpoint

All the blog entries will be accessible via a GET request to `http://domain.com/!/Fetch/collection/blog`.

### Tag

Using the tag, all the blog entries will be output as JSON using `{{ fetch:blog }}` or `{{ fetch collection="blog" }}`.

When using a frontend framework such as Vue, you sometimes need to pass data directly into a Vue component, this is where Fetch's tag comes in handy...

```html
<my-component :data="{{ fetch:blog }}"></my-component>
```

## Disclaimer

This addon was created for personal use and may not work for every use case, therefore support for every site cannot be guaranteed. Feel free to fork this repo or submit a pull request if you have any ideas on improving Fetch.
