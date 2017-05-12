var app = app || {};

app.combo = {
  init : function() {
    $('input.combo').on('change paste keyup', this.inputChangeListener);
    $('input.combo').on('focusout', this.inputFocusoutListener);
    $('input.combo').parent().addClass('combo');
  },

  attachDropdown : function($input, search_term) {
    $.post({
      url: 'api.php',
      data: {
        'action' : 'autocomplete',
        'field' : $input[0].name,
        'search_term' : search_term
      },
      success: function(data) {
        if (data != '') {
          var suggestions = JSON.parse(data);
          var $ul         = $('<ul></ul>')
                              .addClass('suggestions');

          for (var i = 0; i < suggestions.length; i++) {
            var $li = $('<li></li>')
                        .attr('title', suggestions[i])
                        .text(suggestions[i])
                        .appendTo($ul);
            $li.on('mousedown', app.combo.itemClickListener);
          }

          $input.after($ul);
        }
      }
    });
  },

  detachDropdown : function($input) {
    $input.next('.suggestions').remove();
  },

  inputChangeListener : function() {
    var search_term = this.value.trim();

    app.combo.detachDropdown($(this));

    if (search_term != '' && $(this).is(':focus')) {
      app.combo.attachDropdown($(this), search_term);
    }
  },

  inputFocusoutListener : function(event) {
    app.combo.detachDropdown($(this));
  },

  itemClickListener : function(event) {
    event.preventDefault();
    var $input = $(this).closest('label.combo').children('input.combo');
    $input.val($(this).text());

    $input.blur();
  }
};
