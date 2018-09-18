<?php

namespace App\Transformer;

use App\Models\Post;
use League\Fractal\TransformerAbstract;


class PostTransformer extends TransformerAbstract
{
    public function transform(Post $post)
    {

        //dd($post->tags);

        return [
            'id' => $post->id,
            'title' => $post->title,
            'text' => $post->text,
            'tags' => $post->tags
        ];
    }
}