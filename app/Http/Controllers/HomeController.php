<?php

namespace App\Http\Controllers;

use App\Shortener;
use Hashids\Hashids;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{
    /**
     * Home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('home');
    }

    /**
     * Redirect to the original url from short url.
     *
     * @param string $hash
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shortUrlRedirect($hash)
    {
        $id = (new Hashids('', 6))->decode($hash);

        if (empty($id)) {
            throw new NotFoundHttpException;
        }

        $short = Shortener::findOrFail($id[0]);

        return Redirect::to($short->getAttribute('url'));
    }
}
