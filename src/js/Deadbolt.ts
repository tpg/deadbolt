import {User} from '@/User';

export default class Deadbolt {

    readonly _permissions: string[];

    constructor(user: User, column: string = 'permissions') {
        if (typeof user.permissions === 'string') {
            this._permissions = JSON.parse(user[column]);
        } else {
            this._permissions = user[column];
        }
    }

    has (permission: string): boolean {
        return this._permissions.includes(permission);
    }

    hasAll (permissions: string[]): boolean {
        return this._permissions.filter(permission => !permissions.includes(permission)).length === 0;
    }

    hasAny (permissions: string[]): boolean {
        return this._permissions.filter(permission => permissions.includes(permission)).length > 0;
    }

    hasNone (permissions: string[]): boolean {
        return !this.hasAny(permissions);
    }

    all(): string[] {
        return this._permissions;
    }
}
