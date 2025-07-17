export function isRole(user, ...roles) {
    if (!user || !user.role) return false;
    return roles.includes(user.role.name || user.role);
}