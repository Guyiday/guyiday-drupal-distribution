/**
 * @file
 * JavaScript behaviors iFramed Paragraph Edit Forms.
 */

/* eslint-disable */
(function($, Drupal) {
  /**
   * Send message on submit.
   *
   * @type {Drupal~behavior}
   *
   * @see https://www.drupal.org/project/webform/issues/3068998
   */
  Drupal.behaviors.inlineEditingForm = {
    attach(context) {
      $('[type="submit"]', context).each(function() {
        const $element = $(this);

        $element.on('click', function() {
          window.parent.postMessage({ type: 'SUBMIT_FORM' }, '*');
        });
      });
    },
  };
})(jQuery, Drupal);
