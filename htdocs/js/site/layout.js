$(document).ready(function() {
  var $secondaryMenu = $('.secondary-menu__current-item');

  if (!$secondaryMenu) {
    return;
  }

  var $list = $secondaryMenu.next();
  $list.addClass('hidden');

  $secondaryMenu.on('click', function(event) {
    event.preventDefault();

    $list.toggleClass('hidden');
  });
});
