/**
 * @file
 * Provides Intersection Observer API AJAX helper.
 *
 * Blazy IO works fine with AJAX, until using VIS, or alike. Adds a helper.
 */

(function (Drupal) {

  'use strict';

  var _blazy = Drupal.blazy || {};
  var _ajaxSuccess;
  var _revTimer;

  if (_blazy.isIo()) {
    _ajaxSuccess = Drupal.Ajax.prototype.success;

    // Overrides Drupal.Ajax.prototype.success to re-observe new AJAX contents.
    Drupal.Ajax.prototype.success = function (response, status) {
      var me = Drupal.blazy;

      window.clearTimeout(_revTimer);
      // DOM ready fix. Be sure Views "Use field template" is disabled.
      _revTimer = window.setTimeout(function () {
        me.init.revalidate(true);
      }, 100);

      _ajaxSuccess.apply(this, arguments);
    };

  }

})(Drupal);
