var app = app || {};

app.combo = {
  init : function() {
    $('body').on('change paste keyup', 'input.combo', this.inputChangeListener);
    $('body').on('keydown', 'input.combo', this.keydownListener);
    $('body').on('focusout', 'input.combo', this.inputFocusoutListener);
    $('input.combo').parent().addClass('combo');

    if ($('.form.success').length > 0) {
      setTimeout(function(){
        $('div.form.success').animate({
          'height' : 0,
          'margin-top' : 0
        }, 400, function(){
          $('div.form.success').remove();
        });
      }, 5000);
    }
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

  bindListeners : function() {

  },

  detachDropdown : function($input) {
    $input.next('.suggestions').remove();
  },

  handleControlKey : function(event, $input) {
    // do we actually have suggestions?
    if ($input.next('.suggestions').length > 0 &&
        $input.next('.suggestions').children('li').length > 0) {
      var $suggestions = $input.next('.suggestions');

      // handle arrow down press
      if (event.which == app.utils.keys.ARROW_DOWN) {
        var $selected = $suggestions.find('li.selected');

        if ($selected.length > 0) {
          if (!$selected.is(':last-child')) {
            $selected.next().addClass('selected');
            $selected.removeClass('selected');
          }

        } else {
          $suggestions.find('li:first-child').addClass('selected');
        }

      // handle arrow up press
      } else if (event.which == app.utils.keys.ARROW_UP) {
        var $selected = $suggestions.find('li.selected');

        if ($selected.length > 0) {
          if (!$selected.is(':first-child')) {
            $selected.prev().addClass('selected');
          }

          $selected.removeClass('selected');
        }

      // handle tab and enter press
      } else if ( event.which == app.utils.keys.ENTER ||
                  event.which == app.utils.keys.TAB) {
        var $selected = $suggestions.find('li.selected');

        $input.val($selected.text());
        $input.blur();
        $input.focus();

      } else {
      }
    }
  },

  inputChangeListener : function(event) {
    var control_keys = [app.utils.keys.ALT,
                        app.utils.keys.ARROW_DOWN,
                        app.utils.keys.ARROW_LEFT,
                        app.utils.keys.ARROW_RIGHT,
                        app.utils.keys.ARROW_UP,
                        app.utils.keys.CAPS,
                        app.utils.keys.CTRL,
                        app.utils.keys.ENTER,
                        app.utils.keys.SHIFT,
                        app.utils.keys.TAB ];

    // do we have a control keypress?
    if (event.type == 'keyup' && control_keys.indexOf(event.which) > -1) {
      app.combo.handleControlKey(event, $(this));

    // no special key, just search then
    } else {
      var search_term = this.value.trim();

      app.combo.detachDropdown($(this));

      if (search_term != '' && $(this).is(':focus')) {
        app.combo.attachDropdown($(this), search_term);
      }
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
  },

  keydownListener : function(event) {
    var control_keys = [app.utils.keys.ARROW_DOWN,
                        app.utils.keys.ARROW_UP,
                        app.utils.keys.ENTER,
                        app.utils.keys.TAB ];

    if (event.type == 'keydown' &&
        control_keys.indexOf(event.which) > -1 &&
        $(this).next('.suggestions').length > 0) {
      event.preventDefault();
    }
  }
};
