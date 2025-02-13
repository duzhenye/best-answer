import { override } from 'flarum/common/extend';

export default () => {
  const DuzhenyeUserDirectory = require('@duzhenye-user-directory');

  if (!DuzhenyeUserDirectory) return;

  override(DuzhenyeUserDirectory.SortMap.prototype, 'sortMap', (map) => ({
    ...map(),
    most_best_answers: '-bestAnswerCount',
    least_best_answers: 'bestAnswerCount',
  }));
};