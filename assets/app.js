/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

import 'bootstrap'

import 'owl.carousel';


var closesIcon = document.querySelectorAll('.xd-message .fa fa-close');

closesIcon.forEach(function(closeIcon) {
  closeIcon.addEventListener('click', function() {
    this.parentNode.parentNode.classList.add('hide');
  });
});
