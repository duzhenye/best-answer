import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import SolvedFilter from '../components/SolvedFilter';

export default function extendIndexPage() {
  extend(IndexPage.prototype, 'sidebarItems', function (items) {
    const tag = this.currentTag();

    if (!tag?.isQnA?.()) return;

    const canStartDiscussion = app.forum.attribute('canStartDiscussion') || !app.session.user;
    const cta = items.get('newDiscussion');
    cta.children = app.translator.trans(
      canStartDiscussion ? 'duzhenye-best-answer.forum.index.ask_question' : 'duzhenye-best-answer.forum.index.cannot_ask_question'
    );

    if (items.has('startDiscussion')) {
      items.setContent('startDiscussion', cta);
    }
  });

  extend(IndexPage.prototype, 'viewItems', function (items) {
    items.add('solved-filter', <SolvedFilter />);
  });
}