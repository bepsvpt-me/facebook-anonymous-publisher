<?php

namespace App\Http\Controllers\Dashboard;

use App\Block;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlockWordRequest;
use Flash;
use Redirect;

class BlockWordController extends Controller
{
    /**
     * Get the block words list.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $words = Block::where('type', 'keyword')->get();

        return view('dashboard.block-words.index', compact('words'));
    }

    /**
     * Create new block word.
     *
     * @param BlockWordRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlockWordRequest $request)
    {
        Block::create([
            'type' => 'keyword',
            'value' => $request->input('value'),
        ]);

        return Redirect::route('dashboard.block-words.index');
    }

    /**
     * Delete the specific block word.
     *
     * @param string $value
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($value)
    {
        $word = Block::where('type', 'keyword')->where('value', $value)->firstOrFail();

        $word->delete();

        Flash::success('刪除成功');

        return Redirect::route('dashboard.block-words.index');
    }
}
