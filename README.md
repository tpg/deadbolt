
[![Tests](https://github.com/tpg/deadbolt/actions/workflows/tests.yml/badge.svg)](https://github.com/tpg/deadbolt/actions/workflows/tests.yml)

## Why another authorization package?

There are plenty of authorization packages around. But I wanted something that was way simpler than what was on offer and something that I could use easily with my current stack which includes plenty of JavaScript. I've used many of the current top authorisation packages, and will likely use them in the future, but in some cases they're just a little over the top.

Deadbolt is "dead" simple. It's in the name. You define your permissions in the config file (just so you have some source of truth), and you can assign them to your users. The only required database change is a `permissions` column on your `users` table. No need for any additional migrations or complicated configurations.

Deadbolt is simple by design. If you need something more feature rich, there are plenty of other choices. If this doesn't fit the bill, then my go to package is Spatie's [laravel-permission](https://github.com/spatie/laravel-permission) package.

## Requirements

Version 3 requires PHP 8.1 or later and Laravel 10 or later. If you're still using older versions then you'll need to stick with version 2.2. 

## Installation

Deadbolt can be installed via Composer:

```bash
composer require thepublicgood/deadbolt
```

## Getting Started

Deadbolt works by setting permissions in a JSON array in a column on your users table. So before you can use Deadbolt, you'll need to add that column. Deadbolt comes with a simple Artisan command that will do this for you:

```bash
php ./artisan deadbolt:install
```

This will do two things...

1. Create a new migration named `add_deadbolt_permissions_column`,
2. Place a copy of the Deadbolt config at `config/deadbolt.php`.

You can alter the migration if you need to, but the default will add a column named `permissions` to the `users` table.

You can now define your permissions in the `deadbolt.php` config file. That's it.

The `deadbolt:install` command is only available if the `deadbolt.php` file does not exist in the `config` directory. However, if you need to, you can always get the same result by running:

```bash
php ./artisan vendor:publish --provider=TPG\\Deadbolt\\DeadboltServiceProvider
```

## Permissions

Permissions are defined in the `deadbolt.php` config file,  in the appropriately named `$permissions` array. Permissions can be named anything you like, for example:

```php
$permissions = [
	'Create Articles',
	'Edit Articles',
	'Delete Articles',
];
```

However, naming permissions this way could lead to errors later on. So Deadbolt provides a way to create simpler permissions names and provide a description for each permission:

```php
$permissions = [
	'articles.create' => 'Create Articles',
	'articles.edit' => 'Edit Articles',
	'articles.delete' => 'Delete Articles',
];
```

The point of defining your permissions here is to create a single source of truth for your permissions. Assigning a permission to a user that does not exist in this array will throw an exception. Similarly, checking if a user has a permission that does not exist will also throw an exception.

## Working with permissions

### The `Deadbolt` facade

Deadbolt provides a Laravel facade named `Deadbolt`. Anything that Deadbolt can do can be handled through the use of this facade.

### Getting the defined permissions

You can easily grab a list of permissions:

```php
$permissions = Deadbolt::all();

/*
[
	'articles.create',
	'articles.edit',
	'articles.delete',
]
*/
```

This will return an array of permission names. If you also want the descriptions you defined for each permission, you can use the describe method:

```php
$permissions = Deadbolt::describe();

/*
[
	'articles.create' => 'Create Articles',
	'articles.edit' => 'Edit Articles',
	'articles.delete' => 'Delete Articles',
]
*/
```

You can also use the describe method to get the description for just one permission, or a sub-set of permissions:

```php
$permission = Deadbolt::describe('articles.create');
// $permission = 'Create Articles';

$permissions = Deadbolt::describe(['articles.create', 'articles.edit']);

/*
[
	'articles.create' => 'Create Articles',
	'articles.edit' => 'Edit Articles',
]
*/
```

### Assigning permissions

Deadbolt uses the word "User" to mean any model that has permissions that returns array of assigned permissions. This could be any Laravel model that has a "permissions" attribute, but it doesn't have to your actual `User` model. It could be `Role` model, or an `Organisation` model, for example.

To work with permissions on a "user" Deadbolt provides a `user()` method on the `Permissions` facade to which you need to pass your Laravel model:

```php
$deadbolt = Deadbolt::user($request->user());
```

There are two main methods you can use to assign permissions. The `give()` method can be used to assign specific permissions, and the `super()` method is a quick way to assign ALL permissions.

```php
// Give a single permission
Deadbolt::user($user)->give('articles.create');

// Give muliple permissions
Deadbolt::user($user)->give('articles.create', 'articles.edit');

// Give an array of permissions
Deadbolt::user($user)->give($arrayOfPermissions);
```

The `super()` method is really just a shortcut for `give(Deadbolt::all())`:

```php
Deadbolt::user($user)->super();
```

If you attempt to assign a non-existent permission you'll get a `NoSuchPermissionException`.

```php
Deadbolt::user($user)->give('articles.publish');
// Throws a NoSuchPermissionException.
```

You can always get an array of permissions that are currently assigned to the user using the `all` method:

```php
$permissions = Deadbolt::user($user)->all();
```

### Taking permissions away

You can take permissions away from a user with the `revoke` method. It works in much the same way as `give`:

```php
// Revoke a single permission
Deadbolt::user($user)->revoke('articles.edit');

// Revoke multiple permissions
Deadbolt::user($user)->revoke('articles.edit', 'articles.delete');

// Revoke an array of permissions
Deadbolt::user($user)->revoke($arrayOfArticles);
```

Again, trying to revoke a permission that is not defined will throw a `NoSuchPermissionException`, however attempting to a permission DOES exist but not assigned to the user, the `revoke` method will do nothing.

In addition there is also a `revokeAll` method which is simply remove all permissions currently assigned to the user.

```php
Deadbolt::user($user)->revokeAll();
```

### Syncing permissions

Sometimes it can be useful to synchronise a users permissions. You can do this with the `sync` method, which will revoke permissions NOT in the passed array and assign permissions that are not already assigned:

```php
Deadbolt::user($user)->sync($arrayOfPermissions);
```

Essentially, this is the same as doing `revokeAll()->give($arrayOfPermissions)`.

## Testing for permissions

Now that you have users with permissions, you need to be able to test for those permissions. Deadbolt provides a simple set of methods for this.

### has

Use the `has` method to check if a user has *ALL* of the specified permissions:

```php
// Check if a user has a permission
Deadbolt::user($user)->has('articles.create');

// Check that a user has ALL of the permissions
Deadbolt::user($user)->has('articles.create', 'articles.edit');
```

### any

Use the `any` method to check if a user has ANY of the permissions specified:

```php
// Will be true even if only one of the permissions is assigned.
Deadbolt::user($user)->any('articles.edit', 'articles.delete');
```

### none

Use the `none` method to ensure that a user has NONE of the permissions specified:

```php
// Will be false if the user has any of the specified permissions
Deadbolt::user($user)->none('articles.create', 'articles.delete');
```

## Multiple users

Deadbolt also allows you to deal with permissions across multiple users at the same time. By using the `users` method on the `Deadbolt` facade, you can use the same set of methods to work with more than one user at a time by passing a collection of user models:

```php
// Give all the users a permission
Deadbolt::users($users)->give('articles.edit');

// Remove the specified permisssions from all users.
Deadbolt::users($users)->revoke('articles.delete');
```

For testing permissions there are special set of methods specifically for testing across multiple users.

### have

Use the `have` method to test that all the users have the specified permissions:

```php
// All the users MUST HAVE all of the permissions
Deadbolt::users($users)->have($arrayOfPermissions);
```

### dontHave

Use the `dontHave` method to ensure that NONE of the users have the specified permissions:

```php
Deadbolt::users($users)->dontHave($arrayOfPermissions);
```

### any

## The `HasPermissions` Trait

Deadbolt also comes with a simple `HasPermissions` trait which you can add to your `User` model (or whichever model is given permissions. It works by simply doing the `Deadbolt::user($user)` part for you. To get started, simply add the `HasPermissions` trait to your model:

```php
class User extends Authenticatable
{
    use HasPermissions;

    //...
}
```

Now you have access to Deadbolt directly on the user model through the `permissions()` method:

```php
$user = User::find(1);

// Give a permission
$user->permissions()->give('articles.edit');

// Or revoke a permission
$user->permissions()->revoke('articles.edit');

// Or test for a permission
$canEdit = $user->permissions()->has('articles.edit');
```

The `HasPermissions` trait is optional and there is no requirement for you to use it instead of using the `Deadbolt` facade directly. Either way is correct and you can choose whichever feels better.

## Laravel Policies

Laravel policies are a great way to deal with user abilities associated with your different models, and Deadbolt works perfectly with Laravel policies. You can read the Laravel documentation about policies [here](https://laravel.com/docs/authorization#creating-policies).

A simple policy that uses Deadbolt could look something like this:

```php
<?php

namespace App\\Policies;

use App\\User;
use Illuminate\\Auth\\Access\\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return Deadbolt::user($user)->has('articles.create');

		// Or if you're using the HasPermissions trait:
		return $user->permissions()->has('articles.create');
    }
}
```

And you can test the policy with:

```php
$user->can('create', Article::class);
```

And if a policy needs to test for more than one permission:

```php
public function update(User $user, Article $article)
{
    return Deadbolt::user($user)->hasAll('articles.create', 'articles.edit');

	// Or if you're using the HasPermissions trait:
	return $user->hasAll('articles.create', 'articles.edit');
}
```

Policies can be used to test abilities like this:

```php
$user->can('update', $article);
```

## Drivers

Deadbolt is designed for simplicity, but sometimes you might need something just a little more flexible. Deadbolt uses a simple driver system for sourcing permissions, so it's easy to provide your own custom implementations. This can be handy if you really do want to store your permissions in your database, for example.

Deadbolt includes an `ArrayDriver` by default that sources permissions from the default `deadbolt.php` config file. If you want to write a custom driver you can do so by passing a new driver instance to the driver method before calling `user()`:

```php
$driver = new DatabaseDriver($config);
Deadbolt::driver($driver))->user($user)->give('...');
```

It's annoying to call the `driver` method every time you use Deadbolt, so you can set the custom driver in the config file:

```php
return [

    'driver' => \App\Drivers\DatabaseDriver::class,

];
```

### Custom Drivers

If, for example, you need to source a list of permissions from your database, you can write your own driver. A custom driver class needs to implement `Drivers\Contacts\DriverInterface` and the only requirement is to implement a `permissions` method.

The `permissions` method must return an array of permission names:

```php
<?php

namespace App\\Drivers;

use Illuminate\\Support\\Arr;
use Illuminate\\Support\\Facades\\DB;
use TPG\\Deadbolt\\Drivers\\Contracts\\DriverInterface;

class DatabaseDriver implements DriverInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function permissions(): array
    {
        // return an array of permission names filtered by `$groups`
        // Descriptions MUST be included, or null, even if not set.
        return [
            'articles.create' => 'Create articles',
            'articles.edit' => null,
            'articles.delete' => null,
        ];        
    }
}
```

How the `permissions` method sources permissions is up to you. It could a database request, or even a remote API request.

## JavaScript Helper
Deadbolt includes a simple JavaScript helper that provides some simple tools for checking permissions. You'll need to pass the user permissions to your front-end. You can import the `Deadbolt` class into your JavaScript component. If you're using Vue you could do something like this:

```javascript
import Deadbolt from '../../vendor/thepublicgood/deadbolt/dist/Deadbolt';

export default {
  props: {
    user: Object,   // The object that has the "permissions" column
  },
  setup (props) {
    
    const permissions = new Deadbolt(props.user.permissions);
    
    // Check if the user has a permission
    permissions.has('articles.create');
    
    // Check if the user has any permission
    permissions.hasAny(['articles.create', 'articles.edit']);
    
    // Check if the user has all permissions
    permissions.hasAll(['articles.edit', 'articles.delete']);
    
    // Check if the user has no permissions
    permissions.hasNone(['articles.edit', 'articles.delete']);
  }
}
```

## Upgrading from version 1

If you're upgrading Deadbolt from version that is already used on a project, then you should be able to simply replace Deadbolt 1 with Deadbolt 2. However, if you have used the "groups" feature Deadbolt 1 you will need to look at an alternative since groups are no longer available as of Deadbolt 2.

In addition, if you are using a custom Driver, the removal of groups will affect the method signatures. The original `groups` method has been removed, and the `permissions` method no longer takes any parameters.

Deadbolt 2 only supports PHP 7.3 and up, and Laravel 7.0 and up.
