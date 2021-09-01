import Deadbolt from "../../dist/Deadbolt.js";
import * as assert from "assert";

console.log(Deadbolt);

const user = {
    permissions: [
        'articles.create',
        'articles.delete',
    ],
}

const permissions = new Deadbolt(user);

it('"has" should return true for an existing permission', () => {
    assert.equal(permissions.has('articles.create'), true);
});

it('"has" should return false for a non-existing permission', () => {
    assert.equal(permissions.has('articles.edit'), false);
})

it('"hasAny" should return true if just one permission exists', () => {
    assert.equal(permissions.hasAny(['articles.edit', 'articles.delete']), true);
})

it('"hasAny" should return false if no permissions exist', () => {
    assert.equal(permissions.hasAny(['articles.edit', 'articles.update']), false);
})

it('"hasAll" should return true if all of the permissions exist', () => {
    assert.equal(permissions.hasAll(['articles.create', 'articles.delete']), true);
})

it('"hasAll" should return false if just one permission is missing', () => {
    assert.equal(permissions.hasAll(['articles.create', 'articles.delete', 'articles.edit']), false);
})

it ('"hasNone" should return true if all the permissions are missing', () => {
    assert.equal(permissions.hasNone(['articles.update', 'articles.edit']), true);
})

it ('"hasNone" should return false if one of the permissions exists', () => {
    assert.equal(permissions.hasNone(['articles.edit', 'articles.create']), false);
})
