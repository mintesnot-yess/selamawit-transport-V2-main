<?php

use App\Models\Log;

if (!function_exists("log_action")) {
    function log_action($action, $userId = null)
    {
        return Log::create([
            "user_id" => $userId ?? auth()->id(),
            "action" => $action,
            "ip_address" => request()->ip(),
        ]);
    }
}
// --------example
// static::created(function ($model) {
//     log_action('Created '.class_basename($model).' #'.$model->id);
// });

// static::updated(function ($model) {
//     log_action('Updated '.class_basename($model).' #'.$model->id);
// });

// static::deleted(function ($model) {
//     log_action('Deleted '.class_basename($model).' #'.$model->id);
// });
