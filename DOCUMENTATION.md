## Installation

Simply copy the `Fetch` folder into `site/addons/`. That's it!

## Parameters

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `deep` | Boolean | `true` | Fetch nested data recursively, works for arrays as well as related content. |
| `nested` | Boolean | `false` | Add child pages to the page instead of the root of the response. |
| `depth` | Integer | `null` | Depth of nested page to fetch. Only works `nested`. `null` means unlimited. |
| `filter` | String | `null` | Filter `published` and `unpublished` data. |
| `taxonomy` | String | `null` | Filter data by a single or multiple taxonomies. |
| `locale` | String | `null` | Fetch data for a specific locale. |
| `limit` | Integer | `0` | Limit the total results returned. |
| `offset` | Integer | `0` | The number of items the results should by offset by. |
| `query` | String | `null` | The query to search for. |
| `index` | String | `null` | The search index to use during Search (requires a [Collection Index](https://docs.statamic.com/search#collection-indexes)). |
| `debug` | Boolean | `false` | Dump all data on the page (useful to check what data is available). |
| `api_key` | String | `null` | When `Enable API Key` is activated in the settings, make sure to add the `api_key` to every request.* |

\*Both `GET` and `POST` requests are supported; include the `api_key` in the url query string or in the body of the request. It is recommended to use `POST` requests over HTTPS to ensure your `api_key` remains secure.

## Settings

The settings page is accessed via `CP > Configure > Addons > Fetch`.

| Name | Type | Description |
|------|------|-------------|
| **Deep** | Boolean | Site default to 'go deep' when fetching data. |
| **Nested** | Boolean | Site default to 'nested' parameter when fetching data. |
| **Enable API Key** | Boolean | Whether to use the API Key for authentication. |
| **API Key** | String | Generate an API Key. Only used when `Enable API Key` is set to `true`. |
| **IP Whitelist** | Array | List of whitelisted IP addresses. Leave blank to allow any. |
| **Domain Whitelist** | Array | List of whitelisted Domains. Leave blank to allow any. |

_Please note that these Authentication settings are potentially **not** completely secure, it’s meant as a simple layer to stop ‘general’ access to the API endpoints. If you have any ideas on improvements, please open a PR!_

## Note

By default, Fetch will 'go deep' and find all nested data recursively within the dataset. This means that any related content (saved as an ID) will also be fetched and returned.

This behavior can be disabled via Fetch's settings (CP > Configure > Addons > Fetch). You can also enable/disable deep fetching per request via a query string/tag option (see below for further details). When disabled, only a shallow fetch will be performed; related data will simply be returned as its ID.

## Usage

### Types

* [**Collection**](#collection-examples): The Collection's slug.
* [**Entry**](#entry-examples): An Entry's ID or collection + slug.
* [**Page**](#pages-examples): A single Page's URI.
* [**Pages**](#pages-examples): All Pages or a comma-separated list of Page URIs.
* [**Global**](#globals-examples): A single Global slug.
* [**Globals**](#globals-examples): All Globals or a comma-separated list of Global slugs.
* [**Taxonomy**](#taxonomies-examples): A single Taxomony's ID.
* [**Taxonomies**](#taxonomies-examples): All Taxonomies.
* [**User**](#users-examples): A single User's username or email.
* [**Users**](#users-examples): All Users.
* [**Formset**](#formset-examples): A single Formset slug.
* [**Search**](#search-examples): A Search query.

### Parameter Examples

| Name | Example |
|------|---------|
| `deep` | URL: `http://domain.com/!/Fetch/collection/blog?deep=true` <br> Tag: `{{ fetch:blog deep="true" }}` |
| `nested` | URL: `http://domain.com/!/Fetch/collection/blog?nested=true` <br> Tag: `{{ fetch:blog nested="true" }}` |
| `filter` | URL: `http://domain.com/!/Fetch/collection/blog?filter=published` <br> Tag: `{{ fetch:blog filter="published" }}` |
| `taxonomy` | URL: `http://domain.com/!/Fetch/collection/blog?taxonomy=tags/news` <br> Tag: `{{ fetch:blog taxonomy="tags/news" }}` |
| `locale` | URL: `http://domain.com/!/Fetch/collection/blog?locale=nl` <br> Tag: `{{ fetch:blog locale="nl" }}` |
| `limit` | URL: `http://domain.com/!/Fetch/collection/blog?limit=5` <br> Tag: `{{ fetch:blog limit="5" }}` |
| `offset` | URL: `http://domain.com/!/Fetch/collection/blog?offset=3` <br> Tag: `{{ fetch:blog offset="3" }}` |
| `query` | URL: `http://domain.com/!/Fetch/collection/search?query=foo` <br> Tag: `{{ fetch:blog query="foo" }}` |
| `index` | URL: `http://domain.com/!/Fetch/collection/blog?query=foo&index=collections/news` <br> Tag: `{{ fetch:blog query="foo" index="collections/news" }}` |
| `debug` | URL: `http://domain.com/!/Fetch/collection/blog?debug=true` <br> Tag: `{{ fetch:blog debug="true" }}` |
| `api_key` | URL: `http://domain.com/!/Fetch/collection/blog?api_key=[YOUR_KEY_HERE]` <br> Tag: `N/A`|

### Collection Examples

**JS**

```javascript
axios.get('/!/Fetch/collection/blog').then(...);
```

**Tag**

Fetch all blog entries
```html
{{ fetch collection="blog" }}
```

Shorthand
```html
{{ fetch:blog }}
```

Example passing data into a Vue component
```html
<my-component :data='{{ fetch:blog }}'></my-component>
```

### Entry Examples

Entries can either be fetched by their ID or by their collection + slug.

**JS**

```javascript
axios.get('/!/Fetch/entry/a1880157-2b7e-4d7c-ac8f-01790d821312').then(...);
```
```javascript
axios.get('/!/Fetch/entry/blog/my-awesome-blog-post').then(...);
```

**Tag**

Fetch a single entry
```html
{{ fetch entry="a1880157-2b7e-4d7c-ac8f-01790d821312" }}
```
```html
{{ fetch entry="blog/my-awesome-blog-post" }}
```

Example passing data into a Vue component
```html
<my-component :data='{{ fetch entry="a1880157-2b7e-4d7c-ac8f-01790d821312" }}'></my-component>
```
```html
<my-component :data='{{ fetch entry="blog/my-awesome-blog-post" }}'></my-component>
```

### Page(s) Examples

**JS**

Fetch a single page
```javascript
axios.get('/!/Fetch/page/about').then(...);
```

Fetch all pages
```javascript
axios.get('/!/Fetch/pages').then(...);
```

Fetch multiple pages
```javascript
var pages = '/, /about, /contact-us';

axios.get('/!/Fetch/pages/?pages='+encodeURIComponent(pages)).then(...);
```

Fetch pages nested
```javascript
axios.get('/!/Fetch/pages/?nested=true&depth=5').then(...);
```

**Tag**

Fetch a single page
```html
{{ fetch page="/about" }}
```

Fetch all pages
```html
{{ fetch:pages }}
```

Fetch multiple pages
```html
{{ fetch pages="/,/about,/contact-us" }}
```

Example passing data into a Vue component
```html
<my-component :data='{{ fetch:pages }}'></my-component>
```

### Global(s) Examples

**JS**

Fetch a single global
```javascript
axios.get('/!/Fetch/global/opening_hours').then(...);
```

Fetch all globals
```javascript
axios.get('/!/Fetch/globals').then(...);
```

Fetch multiple globals
```javascript
var globals = 'general, contact_info, opening_hours';

axios.get('/!/Fetch/globals/?globals='+encodeURIComponent(globals)).then(...);
```

**Tag**

Fetch a single global
```html
{{ fetch global="opening_hours" }}
```

Fetch all globals
```html
{{ fetch:globals }}
```

Fetch multiple globals
```html
{{ fetch globals="general, contact_info, opening_hours" }}
```

Example passing data into a Vue component
```html
<my-component :data='{{ fetch:globals }}'></my-component>
```

### Taxomomies Examples

**JS**

Fetch a single taxonomy
```javascript
axios.get('/!/Fetch/taxonomy/categories').then(...);
```

Fetch all taxonomies
```javascript
axios.get('/!/Fetch/taxonomies').then(...);
```

**Tag**

Fetch a single taxonomy
```html
{{ fetch taxonomy="categories" }}
```

Fetch all taxonomies
```html
{{ fetch:taxonomies }}
```

### User(s) Examples

**JS**

Fetch a single user
```javascript
axios.get('/!/Fetch/user/admin').then(...);
```
```javascript
axios.get('/!/Fetch/user/admin@example.com').then(...);
```

Fetch all users
```javascript
axios.get('/!/Fetch/users').then(...);
```

**Tag**

Fetch a single user
```html
{{ fetch user="admin" }}
```
```html
{{ fetch user="admin@example.com" }}
```

Fetch all users
```html
{{ fetch:users }}
```

### Formset Examples

**JS**

Fetch formset data
```javascript
axios.get('/!/Fetch/formset/contact').then(...);
```

Perform Statamic Form submission using ajax
```javascript
const form = document.querySelector('#contact-form');

form.addEventListener('submit', e => {
  e.preventDefault();

  // First, fetch the formset's data
  axios.get('/!/Fetch/formset/contact').then(resp => {
    // Create a new FormData object from the form element
    const formData = new FormData(form);

    // Set the required encrypted params field
    // for Statamic to handle the submission
    formData.set('_params', resp.data.data.params);

    // Once `_params` is set, we can perform the POST request
    // Note the additional header being sent as part of the request
    axios.post('/!/Form/create', formData, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(response => {
      // Success!
      console.log(response.data);
    }).catch(error => {
      // Errors...
      console.log(error.response.data);
    });
  });
});
```

### Search Examples

**JS**

Fetch search results
```javascript
axios.get('/!/Fetch/search?query=[search_term]').then(...);
```

### Caching

Caching can be enabled from the settings page.
When enabled all endpoints will be remembered for as long as the cache TTL is set, or for the default 24 hours.
The following 3 headers are sent when caching is enabled:

Header | Value
---|---
Statamic-Fetch-Cache-Enabled | Boolean telling whether or not caching is enabled
Statamic-Fetch-Cache-Created-At | Timestamp of the moment the cache was created
Statamic-Fetch-Cache-TTL | Caching TTL in minutes

To manually clear the cache make a `GET` request to `/!/Fetch/clear-cache`