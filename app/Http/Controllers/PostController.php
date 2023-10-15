<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class PostController extends Controller
{

    public function home(): View
    {
        $posts = Post::where('is_published', true)->paginate(env('PAGINATE_NUM'));
        $categories = Category::all();
        $tags = Tag::all();

        return view('home', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

     /**
     * Display search result
     */
    public function search(Request $request): View
    {
        $key = $request->input('q');
        $posts = Post::where('title', 'like', "%{$key}%")->orderBy('id', 'desc')->paginate(env('PAGINATE_NUM'));
        $categories = Category::all();
        $tags = Tag::all();

        return view('search', [
            'key' => $key,
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Get the data from the request
        $title = $request->input('title');
        $content = $request->input('content');

        if ($request->input('is_published') == 'on') {
            $is_published = true;
        } else {
            $is_published = false;
        }

        // Create a new Post instance and put the requested data to the corresponding column
        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->is_published = $is_published;

        // Save the cover image
        $path = $request->file('cover')->store('cover', 'public');
        $post->cover = $path;

        // Set user
        $user = Auth::user();
        $post->user()->associate($user);

        // Set category
        $category = Category::find($request->input('category'));
        $post->category()->associate($category);

        // Save post
        $post->save();

        //Set tags
        $tags = $request->input('tags');

        foreach ($tags as $tag) {
            $post->tags()->attach($tag);
        }

        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
