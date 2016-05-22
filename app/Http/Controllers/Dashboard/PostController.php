<?php

namespace App\Http\Controllers\Dashboard;

use App\Config;
use App\Http\Controllers\Controller;
use App\Post;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Flash;
use Redirect;

class PostController extends Controller
{
    /**
     * Get the post list.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::latest('id')->paginate(null, [
            'id', 'content', 'fbid', 'user_agent', 'ip', 'created_at',
        ]);

        return view('dashboard.posts.index', compact('posts'));
    }

    /**
     * Delete the specific post.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id, ['id', 'fbid']);

        $fb = new Facebook(Config::getConfig('facebook-service'));

        try {
            $response = $fb->delete($post->getAttribute('fbid'));

            if ($response->getDecodedBody()['success']) {
                $post->delete();

                Flash::success('刪除成功');
            }
        } catch (FacebookSDKException $e) {
            Flash::error('刪除失敗，文章不存在或沒有權限刪除');
        }

        return Redirect::route('dashboard.index');
    }
}
