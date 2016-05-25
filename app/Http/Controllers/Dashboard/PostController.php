<?php

namespace App\Http\Controllers\Dashboard;

use App\Block;
use App\Config;
use App\Http\Controllers\Controller;
use App\Post;
use Cache;
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
        $posts = Post::withTrashed()->latest('id')->paginate(null, [
            'id', 'content', 'fbid', 'user_agent', 'ip', 'created_at', 'deleted_at',
        ]);

        return view('dashboard.posts.index', compact('posts'));
    }

    /**
     * Block the poster ip.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block($id)
    {
        $post = Post::withTrashed()->findOrFail($id, ['id', 'ip']);

        if (! Block::where('type', 'ip')->where('value', $post->getAttribute('ip'))->exists()) {
            Block::create([
                'type' => 'ip',
                'value' => $post->getAttribute('ip'),
            ]);
        }

        Cache::forget('blacklist-ip');

        Flash::success('封鎖成功');

        return Redirect::route('dashboard.posts.index');
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

        $config = Config::getConfig('facebook-service');

        $fb = new Facebook($config);

        try {
            $response = $fb->delete($config['page_id'].'_'.$post->getAttribute('fbid'));

            if ($response->getDecodedBody()['success']) {
                Flash::success('刪除成功');
            }
        } catch (FacebookSDKException $e) {
            Flash::error('刪除失敗，文章不存在或沒有權限刪除');
        }

        $post->delete();

        return Redirect::route('dashboard.posts.index');
    }
}
