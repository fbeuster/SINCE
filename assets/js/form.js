var app = app || {};

app.combo = {
  init : function() {
    $('input.combo').on('change paste keyup', this.inputChangeListener);
    $('input.combo').on('focusout', this.inputFocusoutListener);
    $('input.combo').parent().addClass('combo');
  },

  attachDropdown : function($input, $list) {
    $input.after($list);
  },

  detachDropdown : function($input) {
    $input.next('.suggestions').remove();
  },

  getValuesList : function(search_term) {
    // todo here
    // - make api request with limited result number (5)

    var item_value  = search_term + search_term;

    var $li         = $('<li></li>')
                        .attr('title', item_value)
                        .text(item_value);

    var $ul         = $('<ul></ul>')
                        .addClass('suggestions')
                        .append($li);

    $li.on('mousedown', app.combo.itemClickListener);

    return $ul;
  },

  inputChangeListener : function() {
    var search_term = this.value.trim();

    app.combo.detachDropdown($(this));

    if (search_term != '') {
      var $list = app.combo.getValuesList(search_term);

      app.combo.attachDropdown($(this), $list);
    }
  },

  inputFocusoutListener : function(event) {
    app.combo.detachDropdown($(this));
  },

  itemClickListener : function(event) {
    event.preventDefault();
    var $input = $(this).closest('td.combo').children('input.combo');
    $input.val($(this).text());

    $input.blur();
  }
};
