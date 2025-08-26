// ✅ bonne import
import { startStimulusApp } from '@symfony/stimulus-bridge';
import PullquoteController from './controllers/pullquote_controller.js';

// (option “bridge” + auto-loader lazy)
const app = startStimulusApp(
  require.context('@symfony/stimulus-bridge/lazy-controller-loader!', true, /\.(j|t)sx?$/)
);

// on enregistre notre contrôleur custom
app.register('pullquote', PullquoteController);


// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
