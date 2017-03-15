<?php

namespace App\Activities;

use App\Models\Activity;
use App\Models\User;
use App\Models\Topic;
use Carbon\Carbon;

class BaseActivity
{
    public function removeBy($causer, $indentifier)
    {
        Activity::where('causer', $causer)
                ->where('indentifier', $indentifier)
                ->where('type', class_basename(get_class($this)))
                ->delete();
    }

    public function addTopicActivity(User $user, Topic $topic, $extra_data = [], $indentifier = null)
    {
        $causer      = 'u' . $user->id;
        $indentifier = $indentifier ?: 't' . $topic->id;
        $data = array_merge([
                'topic_type' => $topic->isArticle() ? 'article' : 'topic',
                'topic_link' => $topic->link(),
                'topic_title' => $topic->title,
                'topic_category_id' => $topic->category->id,
                'topic_category_name' => $topic->category->name,
            ], $extra_data);

        $this->addActivity($causer, $user, $indentifier, $data);
    }

    public function addActivity($causer, $user, $indentifier, $data)
    {
        $type = class_basename(get_class($this));

        $activities[] = [
            'causer'      => $causer,
            'user_id'     => $user->id,
            'type'        => $type,
            'indentifier' => $indentifier,
            'data'        => serialize($data),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ];

        Activity::insert($activities);

    }
}
