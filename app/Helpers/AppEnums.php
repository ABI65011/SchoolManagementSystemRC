<?php

namespace App\Helpers;

enum UserRoles: string
{
    case Super = "Super";
    case Admin = "Admin";
    case Staff = "Staff";
    case Student = "Student";
    case Parent = "Parent";
    case User = "User";
}
