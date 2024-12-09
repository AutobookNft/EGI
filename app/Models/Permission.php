<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Permission::findOrCreate('manage_roles');
Permission::findOrCreate('manage_icons');

$adminRole = Role::findOrCreate('admin');
$adminRole->givePermissionTo(['manage_roles', 'manage_icons']);

$editorRole = Role::findOrCreate('editor');
$editorRole->givePermissionTo(['manage_icons']); // L'editor può solo gestire le icone
