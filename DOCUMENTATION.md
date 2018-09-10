## Installation

Simply copy the `Fetch` folder into `site/addons/`. That's it!

## Parameters

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `deep` | Boolean | `true` | Fetch nested data recursively, works for arrays as well as related content. |
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

## Usage

### Types

* [**Collection**](#collection-examples): The Collection's slug.
* [**Page**](#pages-examples): A single page's URI.
* [**Pages**](#pages-examples): All pages or a comma-separated list of page URIs.
* [**Global**](#globals-examples): A single global slug.
* [**Globals**](#globals-examples): All globals or a comma-separated list of global slugs.
* [**Search**](#search-examples): A search query.

### Parameter Examples

| Name | Example |
|------|---------|
| `deep` | URL: `http://domain.com/!/Fetch/collection/blog?deep=true` <br> Tag: `{{ fetch:blog deep="true" }}` |
| `filter` | URL: `http://domain.com/!/Fetch/collection/blog?filter=published` <br> Tag: `{{ fetch:blog filter="published" }}` |
| `taxonomy` | URL: `http://domain.com/!/Fetch/collection/blog?taxonomy=tags/news` <br> Tag: `{{ fetch:blog taxonomy="tags/news" }}` |
| `locale` | URL: `http://domain.com/!/Fetch/collection/blog?locale=nl` <br> Tag: `{{ fetch:blog locale="nl" }}` |
| `limit` | URL: `http://domain.com/!/Fetch/collection/blog?limit=5` <br> Tag: `{{ fetch:blog limit="5" }}` |
| `offset` | URL: `http://domain.com/!/Fetch/collection/blog?offset=3` <br> Tag: `{{ fetch:blog offset="3" }}` |
| `query` | URL: `http://domain.com/!/Fetch/collection/search?query=foo` <br> Tag: `{{ fetch:blog query="foo" }}` |
| `index` | URL: `http://domain.com/!/Fetch/collection/blog?query=foo&index=collections/news` <br> Tag: `{{ fetch:blog query="foo" index="collections/news" }}` |
| `debug` | URL: `http://domain.com/!/Fetch/collection/blog?debug=true` <br> Tag: `{{ fetch:blog debug="true" }}` |

### Collection Examples

**GET** request using Axios

```javascript
axios.get('/!/Fetch/collection/blog').then(...);
```

**POST** request using Guzzle + API Key

```php
$client = new GuzzleHttp\Client();

$response = $client->post('https://domain.com/!/Fetch/collection/blog', ['api_key' => 'YOUR_KEY_HERE']);

if ($response->getStatusCode() == 200) {
    $data = collect(json_decode($response->getBody(), true));
} else {
    // Handle errors
}

return $data;
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

### Page(s) Examples

**GET** request using Axios

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

**POST** request using Axios + API Key

```javascript
axios.post('/!/Fetch/page/about', {api_key: 'YOUR_KEY_HERE'}).then(...);
```

**POST** request using Guzzle + API Key

```php
$client = new GuzzleHttp\Client();

$params = [
    'api_key' => 'YOUR_KEY_HERE',
    'pages' => ['/', '/about', '/contact-us']
];

$response = $client->post('https://domain.com/!/Fetch/pages', $params);

if ($response->getStatusCode() == 200) {
    $data = collect(json_decode($response->getBody(), true));
} else {
    // Handle errors
}

return $data;
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

By default, Fetch will 'go deep' and find all nested data recursively within the dataset. This means that any related content (saved as an ID) will also be fetched and returned.

This behavior can be disabled via Fetch's settings (CP > Configure > Addons > Fetch). You can also enable/disable deep fetching per request via a query string/tag option (see below for further details). When disabled, only a shallow fetch will be performed; related data will simply be returned as its ID.

### Global(s) Examples

**GET** request using Axios

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

**POST** request using Axios + API Key

```javascript
axios.post('/!/Fetch/globals/opening_hours', {api_key: 'YOUR_KEY_HERE'}).then(...);
```

**POST** request using Guzzle + API Key

```php
$client = new GuzzleHttp\Client();

$params = [
    'api_key' => 'YOUR_KEY_HERE',
    'globals' => ['general', 'contact_info', 'opening_hours']
];

$response = $client->post('https://domain.com/!/Fetch/globals', $params);

if ($response->getStatusCode() == 200) {
    $data = collect(json_decode($response->getBody(), true));
} else {
    // Handle errors
}

return $data;
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

By default, Fetch will 'go deep' and find all nested data recursively within the dataset. This means that any related content (saved as an ID) will also be fetched and returned.

This behavior can be disabled via Fetch's settings (CP > Configure > Addons > Fetch). You can also enable/disable deep fetching per request via a query string/tag option (see below for further details). When disabled, only a shallow fetch will be performed; related data will simply be returned as its ID.

## Settings

The settings page is accessed via `CP > Configure > Addons > Fetch`.

| Name | Type | Description |
|------|------|-------------|
| **Deep** | Boolean | Site default to 'go deep' when fetching data. |
| **Enable API Key** | Boolean | Whether to use the API Key for authentication. |
| **API Key** | String | Generate an API Key. Only used when `Enable API Key` is set to `true`. |
| **IP Whitelist** | Array | List of whitelisted IP addresses. Leave blank to allow any. |
| **Domain Whitelist** | Array | List of whitelisted Domains. Leave blank to allow any. |

_Please note that these Authentication settings are potentially **not** completely secure, it’s meant as a simple layer to stop ‘general’ access to the API endpoints. If you have any ideas on improvements, please open a PR!_
