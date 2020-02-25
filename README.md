# Deadbolt

**Dead simple user permissions for Laravel**

[![Build Status](https://travis-ci.org/tpg/deadbolt.svg?branch=master)](https://travis-ci.org/tpg/deadbolt)

## Installation
Install the Deadbolt package through Composer:

```
composer require thepublicgood/deadbolt
```


## Quick Start
Deadbolt works by setting permissions in a JSON array in a column on your users table. It doesn't care much about authentication and such, as long as it can fetch an array of permissions.

To get started quickly, add a nullable string/text column to your users table:

```php
Schema::create('users', function (Blueprint $table) {
    // ...
    $table->string('permissions')->nullable();
});
```

Depending on the number of permissions you have, you might need to increase the size of the column.

Add the `Deadbolted` trait to your user model:

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TPG\Deadbolt\Traits\Deadbolted;

class User extends Authenticatable
{
    use Notifiable, Deadbolted;
    
    // ...
}
```

That's it. If you want to add permissions to other models, just follow the same steps.

## Configuration
Make sure you publish the Deadbolt config file with:

```
php ./artisan vendor:publish --tag=deadbolt
```

This will put a `deadbolt.php` file in your config directory. Define your permissions by adding them to the `permissions` array:

```php
return [

    'permissions' => [
        'articles.create',
        'articles.edit',
        'articles.delete',
    ],

];
```

You can name your permissions anything you like. There is no requirement to follow the dot notation like this example. You could do something like this:

```php
return [

    'permissions' => [
        'create articles',
        'edit articles',
        'delete articles',
    ],

];
```

## Usage
Deadbolt can be accessed through the `deadbolt()` method on your user model. This will return an instance of the `Permissions` class.

### Permanence
Permissions are not permanent by default. Deadbolt will retain the permission set for the duration of the request or until the user is refreshed from the DB. At any time, you can make the permission changes permanent by calling the `makePermanent()` method:

```php
$user->deadbolt()->makePermanent();
```

You can check if a permission set is permanent by calling the `isPermanent()` method:

```php
$user->deadbolt()->isPermanent();
```

### Give a user permissions
Use the `give()` method to assign permissions to a user.

```php
$user = User::find(1);

$user->deadbolt()->give('articles.edit', 'articles.create', 'articles.delete');

// or...

$user->deadbolt()->give(['articles.edit', 'articles.create']);
```

You can also add ALL the defined permissions in one go by using the `super()` method:

```php
$user->deadbolt()->super();
```

Attempting to assign a permission that is not defined will throw a `NoSuchPermissionException`.

### Revoke a user permission
Revoke permissions using the `revoke()` or `revokeAll()` methods:

```php
$user->deadbolt()->revoke('articles.delete');

// To remove ALL permissions...
$user->deadbolt()->revokeAll();
```

## Asserting
Once a user has been given permissions you can test them using the following methods:

### has
Check if a user has a single permission using the `has()` method:

```php
$user->deadbolt()->has('articles.create');
```

### hasAll
Check if a user has all of the specified permissions:

```php
$user->deadbolt()->hasAll('articles.create', 'articles.edit');
```

You can also pass an array to the `hasAll` method if you prefer:

```php
$user->deadblt()->hasAll(['articles.create', 'articles.edit']);
```

### hasAny
If you need to check if a user has at least one of the specified permissions, you can use the `hasAny` method:

```php
$user->deadbolt()->hasAny('articles.create', 'articles.edit');
```

### hasNone
If you need to make sure that a user does NOT have any of the specified permissions, then the `hasNone` method can be used:

```php
$user->deadbolt()->hasNone('articles.delete', 'articles.destroy');
```

## Using Laravel Policies
Laravel policies are a great way to deal with user abilities associated with your different models, and deadbolt works perfectly with policies. You can read the Laravel documentation about policies [here](https://laravel.com/docs/6.x/authorization#creating-policies).

Once you have a policy in place, you can do something like this:

```php
<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->deadbolt()->has('articles.create');
    }
}
```

And you can test the policy with:

```php
$user->can('create', Article::class);
```

Or in a controller method using `$this->authorize()`:
```php
public function update(Request $request, Article $article)
{
    $this->authorize('update', $article);
}
```

This is handy if a policy needs to test for more than one permission:

```php
public function update(User $user, Article $article)
{
    return $user->deadbolt()->hasAll('articles.create', 'articles.edit');
}
```
