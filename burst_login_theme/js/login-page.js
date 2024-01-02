$('.form-item input').on('focusin focusout', function() {
  var $this = $(this);
  $this.parent().toggleClass('small-form-label', !!$this.val().length || $this.is(':focus'))
});

$(document).on('click', 'a', function(event) {
  var $a = $(event.target);
  var path = $a.attr('href');
  var $tab = $('[data-tab="' + path + '"]');
  if ($tab.length) {
    event.preventDefault();
    $('[data-tab]').each(function() {
      var $this = $(this);
      $this.toggle($this.data('tab') === path);
    })
    history.replaceState({}, '', path);
  }
});
