export class Deadbolt {
    _permissions;
    constructor(permissions) {
        this._permissions = permissions;
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