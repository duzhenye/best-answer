import type Discussion from 'flarum/common/models/Discussion';
import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';

export default class BestAnswerInDiscussionNotification extends Notification {
  icon() {
    return 'fas fa-check';
  }

  href() {
    const notification = this.attrs.notification;
    const discussion = notification.subject() as Discussion;

    return app.route.discussion(discussion);
  }

  content() {
    const user = this.attrs.notification.fromUser();
    return app.translator.trans('duzhenye-best-answer.forum.notification.best_answer_in_discussion', {
      user: user,
    });
  }

  excerpt() {
    return null;
  }
}