<?php
namespace App\Policies; use App\User; use Illuminate\Auth\Access\HandlesAuthorization; class UserPolicy { use HandlesAuthorization; public function __construct() { } public function admin($sp264a55) { } public function merchant($sp264a55) { } public function before($sp264a55, $sp7d9a16) { return true; } }