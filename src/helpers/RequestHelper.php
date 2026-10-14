<?php
namespace cloudgrayau\csp\helpers;

use CraftCms\Cms\Cms;
use Illuminate\Http\Request;

class RequestHelper {

  public static function isCp(Request $request): bool {
    $route = $request->route();
    if ($route && in_array('craft.cp', $route->gatherMiddleware(), true)) {
      return true;
    }
    $trigger = trim((string) Cms::config()->cpTrigger, '/');
    if ($trigger === '') {
      return false;
    }
    $path = trim($request->path(), '/');
    return $path === $trigger || str_starts_with($path, $trigger . '/');
  }

}