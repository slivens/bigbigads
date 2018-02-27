<?php

namespace App\Traits;
use Response;

trait Redirectable {
    public function handleRedirect(string $url) {
        if (request()->expectsJson()) {
            return Response::json(['redirectTo' => $url]);
        }
        return redirect($url);
    }
}
