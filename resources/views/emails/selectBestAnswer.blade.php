{!! $translator->trans('duzhenye-best-answer.email.body.select', [
    '{recipient_display_name}' => $user->display_name,
    '{discussion_title}' => $blueprint->discussion->title,
    '{discussion_url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->discussion->id]),
]) !!}
