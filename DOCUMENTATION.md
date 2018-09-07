## Installation

Simply copy the `Fetch` folder into `site/addons/`. That's it!

## Usage

### Types

* [**Collection**](#collection-examples): The Collection's slug (eg: `blog`).
* [**Page**](#pages-examples): A single page's URI (eg: `/about`).
* [**Pages**](#pages-examples): All pages or a comma-separated list of page URIs (eg: `/,/about,/contact-us`).
* [**Global**](#globals-examples): A single global slug (eg: `opening_hours`).
* [**Globals**](#globals-examples): All globals or a comma-separated list of global slugs (eg: `general,contact_info,opening_hours`).

### Collection Examples

**GET** request using Vue Resource

```javascript
this.$http.get('/!/Fetch/collection/blog').then(successCallback, errorCallback);
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

**GET** request using Vue Resource

Fetch a single page
```javascript
this.$http.get('/!/Fetch/page/about').then(successCallback, errorCallback);
```

Fetch all pages
```javascript
this.$http.get('/!/Fetch/pages').then(successCallback, errorCallback);
```

Fetch multiple pages
```javascript
var pages = '/, /about, /contact-us';

this.$http.get('/!/Fetch/pages/?pages='+encodeURIComponent(pages)).then(successCallback, errorCallback);
```

**POST** request using Vue Resource + API Key

```javascript
this.$http.post('/!/Fetch/page/about', {api_key: 'YOUR_KEY_HERE'}).then(successCallback, errorCallback);
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

**GET** request using Vue Resource

Fetch a single global
```javascript
this.$http.get('/!/Fetch/global/opening_hours').then(successCallback, errorCallback);
```

Fetch all globals
```javascript
this.$http.get('/!/Fetch/globals').then(successCallback, errorCallback);
```

Fetch multiple globals
```javascript
var globals = 'general, contact_info, opening_hours';

this.$http.get('/!/Fetch/globals/?globals='+encodeURIComponent(globals)).then(successCallback, errorCallback);
```

**POST** request using Vue Resource + API Key

```javascript
this.$http.post('/!/Fetch/globals/opening_hours', {api_key: 'YOUR_KEY_HERE'}).then(successCallback, errorCallback);
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

* **Deep** (boolean): Site default to 'go deep' when fetching data.
* **Enable API Key** (boolean): Whether to use the API Key for authentication.
* **API Key** (string): Generate an API Key. Only used when `Enable API Key` is set to true.
* **IP Whitelist** (array): Add a list of IP addresses that are whitelisted to make requests. Leave blank to allow any.
* **Domain Whitelist** (array): Add a list of Domains that are whitelisted to make requests. Leave blank to allow any.

_Please note that these Authentication options are **not** 100% secure, it’s meant as a simple layer to stop ‘general’ access to the API endpoints. You cannot hold Fetch, or me, accountable for any leaked data._

## Options

* **deep** (boolean) [ *Default: true* ]: Fetch nested data recursively, works for arrays as well as related content.
  * Example URL param: `http://domain.com/!/Fetch/collection/blog?deep=true`.
  * Example Tag option: `{{ fetch:blog deep="true" }}`.
* **filter** (string) [ *Default: null* ]: Optionally filter `published` and `unpublished` content.
  * Example URL param: `http://domain.com/!/Fetch/collection/blog?filter=published`.
  * Example Tag option: `{{ fetch:blog filter="published" }}`.
* **locale** (string) [ *Default: default_locale* ]: Fetch data for a specific locale.
  * Example URL param: `http://domain.com/!/Fetch/collection/blog?locale=nl`.
  * Example Tag option: `{{ fetch:blog locale="nl" }}`.
* **debug** (boolean) [ *Default: false* ]: Dump all data on the page (useful to check what data is available).
  * Example URL param: `http://domain.com/!/Fetch/collection/blog?debug=true`.
  * Example Tag option: `{{ fetch:blog debug="true" }}`.
* **api_key** (string): When `Enable API Key` is activated in the settings, make sure to add the `api_key` to every request.
  * Both `GET` and `POST` requests are supported; just include the `api_key` in the url query string or in the body of the request and the data will be returned.
  * It is recommended to use `POST` requests over **HTTPS** to ensure your `api_key` remains secure.
