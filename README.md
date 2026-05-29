# Laravel RestAPI Package (`laravue2/rest-api`)

A powerful and flexible package for Laravel that automates the creation of robust RESTful APIs. It facilitates advanced filtering, dynamic field selection, relationship eager loading and counting, pagination, API versioning, and Hashids integration out-of-the-box with minimal configuration.

---

## ⚡ Key Features

* **Ready-to-Use CRUD Controllers**: Automated and customizable `index`, `show`, `store`, `update`, and `destroy` endpoints.
* **SQL-like Advanced Filtering**: Compare and filter query results directly from the URL, supporting complex logical queries (`and`, `or`, and parenthesis `()`).
* **Dynamic Field Selection (`fields`)**: Allows clients to specify exactly which columns and relations (including nested relations) to return in the JSON payload.
* **Inline Limits on Relations**: Restrict and paginate child relations (e.g., `HasMany`, `BelongsToMany`) inline directly via the `fields` parameter.
* **Dynamic Relation Counting (`with_count`)**: Eagerly count related records on demand using a simple URL query parameter.
* **Dedicated Relation Endpoints**: Automatic sub-routing for querying specific relation collections directly (`GET /resource/{id}/{relation}`).
* **Controller Hook Methods**: Leverage lifecycle hooks such as `storing`, `stored`, `updating`, `updated`, etc., to inject custom business logic.
* **Native Hashids Integration**: Automatically encodes and decodes model IDs to protect database primary keys from enumeration attacks.
* **CORS & API Versioning**: Global CORS headers control and a custom router setup with automatic version prefixing (e.g., `api/v1`).

---

## ⚙️ Installation & Configuration

### 1. Register the Service Provider
If you are using an older version of Laravel without package auto-discovery, add the Service Provider in your `config/app.php` file:

```php
'providers' => [
    // ...
    Laravue2\RestAPI\Providers\ApiServiceProvider::class,
];
```

### 2. Publish the Configuration File
Run the following command to publish the package configuration to `config/api.php`:

```bash
php artisan vendor:publish --provider="Laravue2\RestAPI\Providers\ApiServiceProvider"
```

The configuration file contains the following keys:
* `defaultLimit`: Default pagination size when no limit is provided (10).
* `maxLimit`: Maximum allowed pagination size to prevent database overload (1000).
* `cors`: Enable or disable CORS response headers (true/false).
* `cors_headers`: List of headers allowed in CORS preflight requests.
* `prefix`: Global prefix for all API routes (e.g., `api`).
* `default_version`: Default fallback API version (e.g., `v1`). Set `null` to disable versions.

---

## 🏗️ Basic Usage Guide

### 1. Define the Model (`ApiModel`)
Your Eloquent models must extend `Laravue2\RestAPI\ApiModel` instead of Laravel's base Model class:

```php
namespace App\Models;

use Laravue2\RestAPI\ApiModel;

class Product extends ApiModel
{
    // Database columns to return by default if the "fields" parameter is omitted
    protected $default = ['id', 'name', 'price', 'status'];

    // Database columns allowed to be filtered in URL query parameters
    protected $filterable = ['id', 'name', 'price', 'status', 'category_id'];
    
    // Standard Eloquent relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 2. Define the Controller (`ApiController`)
Your controllers must extend `Laravue2\RestAPI\ApiController`. Simply define the target model property:

```php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use Laravue2\RestAPI\ApiController;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends ApiController
{
    protected $model = Product::class;

    // Optional: Attach FormRequest classes for validation during CRUD actions
    protected $storeRequest = StoreProductRequest::class;
    protected $updateRequest = UpdateProductRequest::class;
}
```

### 3. Register the Routes
The package provides an `ApiRouter` helper that automatically registers restful resource endpoints along with a relation helper endpoint.

In your `routes/api.php` file:

```php
use Laravue2\RestAPI\Facades\ApiRoute;

// Define versioned routes
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::resource('products', 'ProductController');
}
```

This registers the following routes under the hood:
* `GET /api/v1/products` (index)
* `POST /api/v1/products` (store)
* `GET /api/v1/products/{id}` (show)
* `PUT /api/v1/products/{id}` (update)
* `DELETE /api/v1/products/{id}` (destroy)
* `GET /api/v1/products/{id}/{relation}` (relation - Endpoint for querying child relationship lists)

---

## 🔍 Consuming the API (URL Query Parameters)

Clients can customize response structures dynamically using query strings in the URL.

### A. Dynamic Field Selection & Relations (`fields`)
Select specific table columns to return, and eagerly load nested relationships using curly braces `{}`.

* **Basic Example:** Retrieve only `id` and `name`:
  ```http
  GET /api/v1/products?fields=id,name
  ```
* **With Eager Loading:** Retrieve products and their categories:
  ```http
  GET /api/v1/products?fields=id,name,category{id,name}
  ```
* **Multi-level Eager Loading:** Retrieve product with category and warehouse detail:
  ```http
  GET /api/v1/products?fields=id,name,category{id,name},warehouse{id,name}
  ```

#### Inline Limits and Offsets on Child Relations
You can control the pagination and ordering of a `HasMany` or `BelongsToMany` relation directly inside the `fields` query string:
```http
GET /api/v1/suppliers?fields=id,name,supplied_products.limit(5).offset(0).order(chronological){id,name}
```

---

### B. Advanced Filtering (`filters`)
Construct complex search queries using the `filters` URL parameter.

**Supported Operators:**
* `eq` (Equals / `=`): `name eq "Laptop"`
* `ne` (Not Equals / `!=`): `status ne "disabled"`
* `gt` (Greater Than / `>`): `price gt 150`
* `ge` (Greater or Equal / `>=`): `price ge 100`
* `lt` (Less Than / `<`): `price lt 50`
* `le` (Less or Equal / `<=`): `price le 50`
* `lk` (Like): `name lk "%phone%"`
* `in` (In array): `status in (enabled,pending)` or `id in (M6q8Pvqz,AdWNDqgV)`

**Complex Logical Grouping:**
Combine conditions using `and`, `or`, and parenthesis `()` to dictate operational precedence:
```http
GET /api/v1/products?filters=(status eq "enabled" or status eq "pending") and price lt 100
```

---

### C. Ordering Results (`order`)
Use the `order` parameter followed by the target column name and direction (`asc` or `desc`):
```http
GET /api/v1/products?order=price desc,name asc
```

---

### D. Global Pagination (`limit` & `offset`)
Control pagination sizes and page offsets for the root request:
```http
GET /api/v1/products?limit=15&offset=30
```

---

### E. Dynamic Relation Counting (`with_count`)
Retrieve the count of related records without the overhead of loading every model in that relationship.

```http
GET /api/v1/suppliers?fields=id,name&with_count=suppliedProducts,orders
```

#### Integrating with Laravel API Resources
When `with_count` is passed in the URL, Eloquent appends a `{relation}_count` attribute to the resulting model. You can output this conditionally in your **JsonResource** using `whenCounted`:

```php
public function toArray($request)
{
    return [
        'id' => $this->xid,
        'name' => $this->name,
        
        // This will only be present in the final JSON response when requested in the URL
        'supplied_products_count' => $this->whenCounted('supplied_products'),
    ];
}
```

---

### F. Dedicated Relation Endpoints
If you do not need information about the parent resource and only want to fetch its related items, query the relationship endpoint directly:

```http
GET /api/v1/suppliers/M6q8Pvqz/supplied_products?limit=10&offset=0
```
*(This returns the collection of products associated with the supplier whose decoded ID is `M6q8Pvqz`).*

---

## 🪝 Controller Lifecycle Hooks

You can intercept actions within your custom controllers by overriding any of the following lifecycle hook methods:

```php
class ProductController extends ApiController
{
    protected $model = Product::class;

    // Called before the model is saved (Creation)
    protected function storing($object)
    {
        $object->created_by = auth()->id();
        return $object;
    }

    // Called after the model has been saved (Creation)
    protected function stored($object)
    {
        // Trigger emails, push notifications, log actions, etc.
    }

    // Called before the model is updated
    protected function updating($object)
    {
        return $object;
    }

    // Called after the model has been updated
    protected function updated($object)
    {
        // ...
    }

    // Called before the model is deleted
    protected function destroying($object)
    {
        return $object;
    }

    // Called after the model has been deleted
    protected function destroyed($object)
    {
        // ...
    }
}
```

---

## 🔒 Security & Hashids Integration

The package integrates `Vinkla\Hashids` to prevent database auto-increment ID exposure in the API layer:

1. **CRUD Resource Routes**: The controller automatically decodes incoming `{id}` hashids before querying records in `show`, `update`, and `destroy` endpoints.
2. **Hashable Filter Fields (`hashable`)**: If you filter on primary keys or foreign keys, specify the column names in the `hashable` parameter. The parser decodes these hashids to their numeric database counterparts automatically before executing queries:
   ```http
   GET /api/v1/products?filters=category_id eq "M6q8Pvqz"&hashable=category_id
   ```
   *(The query parser will decode `M6q8Pvqz` to the integer ID to build the SQL query).*
