# Dead simple user permissions for Laravel

[![Build Status](https://travis-ci.org/tpg/deadbolt.svg?branch=master)](https://travis-ci.org/tpg/deadbolt)

> NOTE! Deadbolt is sparkly new. Don't use this in production yet. We're still figuring out things and it's going to change a lot in the early stages. Breaking changes between versions are likely.

## Why another authorization package?
Because we wanted something way simpler than the other solutions. We've used many of the current top authorization packages, and will continue to use them in the future, but in a number of cases they're just a little over the top.

Deadbolt is "dead" simple. you define your permissions in the config file, and you can assign them to your users (or any model you like). All you need to do is add a `permissions` column. No need for any additional migrations or complicated configurations.

Deadbolt is simple by design. If you need something more feature rich, take a look at Spatie's `laravel-permission` package [here](https://github.com/spatie/laravel-permission)

## Installation
Install the Deadbolt package through Composer:

```
composer require thepublicgood/deadbolt
```


## Getting Started
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

If you want to add permissions to other models, just follow the same steps.

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
Deadbolt can be used through the `Deadbolt` facade.

### The `User` instance
Deadbolt works by assigning permissions to a user. By `user`, we mean any model that has a `permissions` column in the database table. It doesn't have to be an actual user.
The `user()` method will return an instance of the `Deadbolt\User` class and is purely used to manipulate permissions on your models.

For example, to get a new `Deadbolt\User` instance from the currently logged user, you could do:

```php
$user = Deadbolt::user($request()->user());
```

### Permanence
Before continuing, just a note about permanence. Permissions are not permanent by default. Deadbolt will assign the permission set for the duration of the request or until the user is refreshed from the DB, but to make them permanent, you must call the `save()` method:

```php
Deadbolt::user($user)->save();

// or

$user->save();
```

You can check if a permission set is permanent by calling the `saved()` method:

```php
Deadbolt::user($user)->saved();
```

### Give a user permissions
Use the `give()` method to assign permissions to a user.

```php
$user = User::find(1);

Deadbolt::user($user)->give('articles.edit', 'articles.create', 'articles.delete');

// or you can pass in an array

$permissions = ['articles.edit', 'articles.delete'];
Deadbolt::user($user)->give($permissions);
```

You can also add ALL the defined permissions in one go by using the `super()` method:

```php
Deadbolt::user($user)->super();
```

Attempting to assign a permission that is not defined will throw a `NoSuchPermissionException`.

### Revoke a user permission
Revoke permissions using the `revoke()` method:

```php
Deadbolt::user($user)->revoke('articles.delete');
```

You can revoke ALL the users permissions by using the `revokeAll()` method:

```php
Deadbolt::user($user)->revokeAll();
```

## Testing Permissions
Once a user has been given permissions you can test them using the following methods:

### has
Check if a user has a single permission using the `has()` method:

```php
Deadbolt::user($user)->has('articles.create');
```

### hasAll
Check if a user has all of the specified permissions:

```php
Deadbolt::user($user)->hasAll('articles.create', 'articles.edit');
```

### hasAny
If you need to check if a user has at least one of the specified permissions, you can use the `hasAny` method:

```php
Deadbolt::user($user)->hasAny('articles.create', 'articles.edit', 'articles.delete');
```

### hasNone
If you need to make sure that a user does NOT have any of the specified permissions, then the `hasNone` method can be used:

```php
Deadbolt::user($user)->hasNone('articles.create', 'articles.edit');
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
        return Deadbolt::user($user)->has('articles.create');
    }
}
```

And you can test the policy with:

```php
$user->can('create', Article::class);
```

This is handy if a policy needs to test for more than one permission:

```php
public function update(User $user, Article $article)
{
    return Deadbolt::user($user)->hasAll('articles.create', 'articles.edit');
}
```

## Roles
Deadbolt roles are a simple solution to grouping permissions together in some form of logical collection. For example, you might have a role named `publisher` which needs to have the permissions `edit articles` and `publish articles` but not `create articles` or `delete articles`. When assigning a role to a user, the permissions within the role are assigned instead.

> It is considered bad practise to authorize a user based on their roles. Test for user permissions instead. Roles are a convenience tool used to group permissions together and those permissions can change at any time causing unexpected issues.

### Defining roles
Define roles in the `deadbolt.php` config file and assign the permissions you want in the role.

```php
return [

    'permissions' => [
        'create articles',
        'edit articles',
        'publish articles',
        'delete articls',
    ]
    
    'roles' => [
    
        'writer' => [
            'create articles',
            'edit articles',
            'delete articles',
        ],
        'publisher' => [
            'edit articles',
            'publish articles',
        ]
    ]

];
```

### Assigning roles
Assign roles to user with the `giveRoles` method:

```php
Deadbolt::user($user)->roles()->give('publisher');
```

The above will assign the `edit articles` and `publish articles` permissions.

### Revoking roles
Roles can be used to remove groups of permissions. However, be careful when doing so. Deadbolt will remove all the permissions associated with that role regardless of any other roles that may have been assigned to the same user.

```php
Deadbolt::user($user)->give('publisher', 'writer');
Deadbolt::user($user)->revoke('publisher');
```
The above will revoke the `edit articles` permission because it is in the `publisher` role. Even though the `writer` role, which has the same permission wasn't revoked.

### Getting a users roles
You can also check what roles a user has by the permissions they have. Users don't actually get assigned roles, but we can deduce the roles by their permissions.

```php
$roles = Deadbolt::user()->roles();
```

### Check if a user has a role
You can also check if a specific role is applied to a user:

```php
if (Deadbolt::user($user)->is('publisher')) {
    // ...
}
```
