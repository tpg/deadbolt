![Run Tests](https://github.com/tpg/deadbolt/workflows/Run%20Tests/badge.svg)

# Dead simple user permissions for Laravel

## Why another authorization package?

Because I wanted something way simpler than the other solutions. I've used many of the current top authorization packages, and will continue to use them in the future, but in a number of cases they're just a little over the top.

Deadbolt is "dead" simple. you define your permissions in the config file, and you can assign them to your users (or any model you like). All you need to do is add a `permissions` column. No need for any additional migrations or complicated configurations.

Deadbolt is simple by design. If you need something more feature rich, take a look at Spatie's `laravel-permission` package [here](https://github.com/spatie/laravel-permission).

## Installation

Install the Deadbolt package through Composer:

```
composer require thepublicgood/deadbolt
```

## Getting Started

Deadbolt works by setting permissions in a JSON array in a column on your users table.

To get started quickly, add a nullable string column to your users table:

```php
Schema::create('users', function (Blueprint $table) {
    // ...
    $table->string('permissions')->nullable();
});
```

Depending on the number of permissions you have, you might need to increase the size of the column.

If you want to add permissions to other models, just make sure you have a `permissions` column in the database table.

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

You can also describe your permissions which is handy when you need to show a list of permissions and you want to include something a little more friendly in a user interface.

```php
return [

    'permissions' => [
        'articles.create' => 'Create articles',
        'articles.edit' => 'Edit articles',
        'articles.delete' => 'Delete articles',
    ],

];
```

You don't need to describe ALL your permissions. Deadbolt will be able to figure out when permissions have descriptions and which don't.

## Usage

Deadbolt can be used through the `Deadbolt` facade.

### Getting the defined permissions

You can easily grab a list of permissions:

```php
$permissions = Deadbolt::permissions();
```

This will return an array of permission names. If you also want the descriptions included with the array of permission names, you can use the `describe` method:

```php
$permissions = Deadbolt::describe();

// You can also filter the description array;

$permissions = Deadbolt::describe('articles.create', 'articles.edit');
```

### The `User` instance

Deadbolt works by assigning permissions to a user. By `user`, we mean any model that has a `permissions` column in the database table. It doesn't have to be an actual user.
The `user()` method will return an instance of the `Deadbolt\\User` class and is purely used to manipulate permissions on your models.

For example, to get a new `Deadbolt\\User` instance from the currently logged user, you could do:

```php
$deadbolt = Deadbolt::user($request()->user());
```

### Permanence

Before continuing, just a note about permanence. Permissions are not permanent by default. Deadbolt will assign the permission set for the duration of the request or until the user is refreshed from the DB, but to make them permanent, you must call the `save()` method:

```php
Deadbolt::user($user)->save();

// since changes are made directly to the model, you can also do...

$user->save();
```

You can check if a permission set is permanent by calling the `saved()` method:

```php
Deadbolt::user($user)->saved();
```

This simply compares the in-memory model to the original. If they're different, the `saved()` method will return false.

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

### Syncing permissions

There is also a `sync` method which will sync the permissions on the user with the permissions provided. Permissions that are currently assigned to the user will be removed. This is a convenience method and performs the same tasks as `revokeAll` and `give` in that order.

```php
Deadbolt::user($user)->sync('articles.edit', 'articles.delete');
```

## Testing permissions

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

## Laravel Policies

Laravel policies are a great way to deal with user abilities associated with your different models, and deadbolt works perfectly with policies. You can read the Laravel documentation about policies [here](https://laravel.com/docs/6.x/authorization#creating-policies).

Once you have a policy in place, you can do something like this:

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

## Groups

> The groups system will be removed from version 2. I feel that the groups feature makes Deadbolt more complex than it was intended, so I'm going back to the basics and looking at what Deadbolt was supposed to be. Groups don't offer the same value as proper roles. Deadbolt is meant to be a simple permissions library and groups don't fit well with that idea.

Deadbolt groups are a simple solution to grouping permissions together in some form of logical collection. For example, you might have a group named `publisher` which contains the permissions `edit articles` and `publish articles` but not `create articles` or `delete articles`. When assigning a group to a user, the permissions within the group are assigned instead.

It is considered bad practice to authorize a user based on groups. You should test for user permissions instead. Groups are a convenience tool used to collect permissions together and those permissions can change at any time causing unexpected issues. Deadbolt groups are not assigned to users but rather the permissions they collect. It's important to note that if the permissions in a group change, the users who were originally assigned that group will not gain the changed permissions.

Although they may appear similar, Deadbolt groups are not roles. If you're looking for a way to implement proper roles, there is a wiki article [here](https://github.com/tpg/deadbolt/wiki/2.-Roles) about how Deadbolt can still be used to do so. Otherwise, you may need to look for an alternative solution.

### Defining groups

Define groups in the `deadbolt.php` config file and assign the permissions you want in the group.

```php
return [

    'permissions' => [
        'create articles',
        'edit articles',
        'publish articles',
        'delete articls',
    ]

    'groups' => [

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

### Assigning groups

There's no need for another API just to manage groups. You can assign groups to user with the `give` method in the same way you assign permissions:

```php
Deadbolt::user($user)->give('publisher');
```

Deadbolt will figure out if the name is a permission or a group (permissions take precidence) and will assign the permissions from that group.

The above will assign the `edit articles` and `publish articles` permissions.

### Revoking groups

In the same way you use the `give` method to assign both groups and permissions, you can use the same `revoke` method to remove them.

However, be careful when revoking groups. Deadbolt will remove all the permissions associated with that group regardless of any other groups that may have been assigned to the same user. For example:

```php
Deadbolt::user($user)->give('publisher', 'writer');
Deadbolt::user($user)->revoke('publisher');
```

The above will revoke the `edit articles` permission because it is in the `publisher` group. Even though the `writer` group, which has the same permission wasn't revoked.

### Mixing permissions and groups

Both the `give` and `revoke` methods will accept group and permission names in one go. For example, if you need to assign the `publisher` group, but also give a user the `articles.delete` permission, you can do so by calling `give` once:

```php
Deadbolt::user($user)->give('publisher', 'articles.delete');
```

Likewize, if you need to revoke the a group as well as an additional permission:

```php
Deadbolt::user($user)->revoke('publisher', 'articles.delete');
```

### Getting a users groups

You can also check what groups a user has by the permissions they have. Users don't actually get assigned groups, but we can deduce the groups by their permissions.

```php
$groups = Deadbolt::user()->groups();
```

### Check if a user has a group

You can also check if a specific group is applied to a user using the `is` method:

```php
if (Deadbolt::user($user)->is('publisher')) {
    // ...
}
```

## Multiple users

Deadbolt allows you to modify the permissions of multiple users at the same time. The `Deadbolt` facade provides access to a `users` method that takes a collection of users (can be a `Collection` instance or an array, etc). The `users` method returns a `UserCollection` instance that provides a few simple methods for working with all the users in the collection at the same time. The same methods work on the collection. For example, you can give all the users the same set of permissions in one go:

```php
$users = User::all();

Deadbolt::users($users)->give('articles.create');
```

You can revoke permissions in the same way:

```php
Deadbolt::users($users)->revoke('articles.create');
```

The `UserCollection` class also provides some handy methods for testing permissions on the colection. Test test that all the users have the specified permissions, you can use the `allHave` method:

```php
Deadbolt::users($users)->allHave('articles.edit');
```

Or if you need to test that any of the users have a specified permission regardless of if the others have it or not, you can use the `anyHave` method:

```php
Deadbolt::users($users)->anyHave('articles.edit');
```

There is also a `noneHave` method to test if none of the users have the specified permissions:

```php
Deadbolt::users($users)->noneHave('articles.edit');
```

## Drivers

Deadbolt is designed for simplicity, but sometimes you might need something just a little more flexible. Deadbolt uses a simple driver system for sourcing groups and permissions, so you can provide your own custom implementations. This can be handy if you really do want to store your permissions in your database.

Deadbolt includes an `ArrayDriver` by default that sources permissions and groups from the `deadbolt` config. If you want to use a custom driver you can do so by passing a new driver instance to the `driver` method before calling `user()`:

```php
$driver = new DatabaseDriver($config);
Deadbolt::driver($driver))->user($user)->give('...');
```

If you don't want to call the `driver` method everytime you use Deadbolt, then you can se the custom driver in the `deadbolt` config file:

```php
return [

    'driver' => \App\Drivers\DatabaseDriver::class,

];
```

The deadbolt config file will always be passed to the constructor of the custom driver in this case, so you can use it to include any custom config you have.

### Writing custom drivers

All custom drivers MUST implement `Drivers\\Contracts\\DriverInterface` which requires that a `permissions()` and a `groups()` method exists.

The `permissions` method must return an array of permission names, and a `groups` method must return the an array of groups, each an array of permissions.

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

    public function permissions(...$groups): array
    {
        // return an array of permission names filtered by `$groups`
        // Descriptions MUST be included, or null, even if not set.
        return [
            'articles.create' => 'Create articles',
            'articles.edit' => null,
            'articles.delete' => null,
        ];
    }

    public function groups(): array
    {
        //return an array of permissions keyed by group names.
        return [
            'publisher' => [
                'articles.edit',
                'articles.publish',
            ],
            'writer' => [
                'articles.create',
                'articles.edit',
                'articles.delete',
            ],
        ];
    }
}
```

How the permissions and groups are sourced is up to you. You could created a `DatabaseDriver` or even an `HttpDriver`. A simple example of a custom `DatabaseDeadboltDriver` can be found [here](https://github.com/tpg/deadbolt/wiki/3.-Custom-Database-Driver).
