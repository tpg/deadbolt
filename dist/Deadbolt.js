export default class Deadbolt {
    constructor(permissions) {
        if (typeof permissions === 'string') {
            this._permissions = JSON.parse(permissions);
        }
        else {
            this._permissions = permissions;
        }
    }
    has(permission) {
        return this._permissions.includes(permission);
    }
    hasAll(permissions) {
        return permissions.filter(permission => !this._permissions.includes(permission)).length === 0;
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