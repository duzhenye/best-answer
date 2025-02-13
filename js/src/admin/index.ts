import app from 'flarum/admin/app';
import addBestAnswerCountSort from '../common/addBestAnswerCountSort';
import BestAnswerSettingsPage from './components/BestAnswerSettingsPage';

export { default as extend } from './extend';

app.initializers.add(
  'duzhenye-best-answer',
  () => {
    app.extensionData
      .for('duzhenye-best-answer')
      .registerPage(BestAnswerSettingsPage)
      .registerPermission(
        {
          icon: 'far fa-comment',
          label: app.translator.trans('duzhenye-best-answer.admin.permissions.best_answer'),
          permission: 'discussion.selectBestAnswerOwnDiscussion',
        },
        'reply'
      )
      .registerPermission(
        {
          icon: 'far fa-comment',
          label: app.translator.trans('duzhenye-best-answer.admin.permissions.best_answer_not_own_discussion'),
          permission: 'discussion.selectBestAnswerNotOwnDiscussion',
        },
        'reply'
      );

    addBestAnswerCountSort();
  },
  5
);