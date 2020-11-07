<?php

namespace FacebookAnonymousPublisher\Shortener\Controllers;

use FacebookAnonymousPublisher\Shortener\Facades\Shortener;

class ShortenerController
{
    /**
     * Redirect to the origin url.
     *
     * @param string $url
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($url)
    {
        return redirect(Shortener::decode($url));
    }
}
