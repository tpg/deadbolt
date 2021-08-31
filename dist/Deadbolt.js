export default class Deadbolt {
    constructor(user) {
        if (typeof user.permissions === 'string') {
            this._permissions = JSON.parse(user.permissions);
        }
        else {
            this._permissions = user.permissions;
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