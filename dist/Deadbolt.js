export default class Deadbolt {
    constructor(user, column = 'permissions') {
        if (typeof user.permissions === 'string') {
            this._permissions = JSON.parse(user[column]);
        }
        else {
            this._permissions = user[column];
        }
    }
    has(permission) {
        return this._permissions.includes(permission);
    }
    hasAll(permissions) {
        return this._permissions.filter(permission => !permissions.includes(permission)).length === 0;
    }
    hasAny(permissions) {
        return this._permissions.filter(permission => permissions.includes(permission)).length > 0;
    }
    hasNone(permissions) {
        return !this.hasAny(permissions);
    }
    all() {
        return this._permissions;
    }
}
//# sourceMappingURL=Deadbolt.js.map