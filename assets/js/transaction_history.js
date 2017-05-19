var app = app || {};

app.transaction_history = {
  table : '',

  init : function(table) {
    this.table = table;
    this.bindClickListener();
  },

  bindClickListener : function() {
    $('span.button.cancel').click(this.cancelButtonClick);
    $('span.button.delete').click(this.deleteButtonClick);
    $('span.button.done').click(this.doneButtonClick);
    $('span.button.mode_edit').click(this.editButtonClick);
  },

  cancelButtonClick : function() {
    app.transaction_history.clearEditMode();
  },

  clearEditMode : function() {
    var $row = $(app.transaction_history.table).find('.edit_active');

    $row.find('td:not(.actions)').each(function unwrapInput(){
      if ($(this).hasClass('date')) {
        var date    = new Date($(this).attr('data-value')),
            options = {year: 'numeric', month: '2-digit', day: '2-digit' };
        $(this).text( date.toLocaleString('de-DE', options) );

      } else if($(this).hasClass('number')) {
        $(this).text( parseFloat($(this).attr('data-value')).toFixed(2) + ' €' );

      } else {
        $(this).text( $(this).attr('data-value') );
      }
    });

    $row.removeClass('edit_active');
  },

  deleteButtonClick : function() {
    app.transaction_history.transaction_id = $(this).closest('tr').attr('data-transaction-id');
    app.transaction_history.deleteTransaction();
  },

  deleteConfirm : function(confirmed) {
    if (confirmed) {
      $.post({
        url: 'api.php',
        data: {
          'action' : 'delete_transaction',
          'transaction_id' : app.transaction_history.transaction_id
        },
        success: function(data) {
          if (data == 'success') {
            $('tr[data-transaction-id=' + app.transaction_history.transaction_id + ']')
              .find('td')
              .wrapInner('<div></div>')
              .parent()
              .find('td > div')
              .slideUp(400, function(){
                $(this).parent().parent().remove();
              });
          }
        }
      });
    }
  },

  deleteTransaction : function() {
    var $tr         = $('tr[data-transaction-id=' + app.transaction_history.transaction_id + ']'),
        customer    = $tr.find('td.customer').text(),
        date        = $tr.find('td.date').text(),
        description = $tr.find('td.description').text(),
        transaction = '"' + description + '" from ' + customer;

    app.dialog.open({
      'callback' : this.deleteConfirm,
      'title' : 'Warning!',
      'message' : 'Do you want to delete the transaction ' + transaction + '?'
    });
  },

  doneButtonClick : function() {
    $(this).closest('tr').removeClass('edit_active');
  },

  editButtonClick : function() {
    app.transaction_history.clearEditMode();
    app.transaction_history.startEditMode($(this).closest('tr'));
  },

  keyListener : function(event) {
    switch(event.which) {
      case app.utils.keys.ESCAPE:
        app.transaction_history.clearEditMode();
        break;

      default:
        break;
    }
  },

  startEditMode : function($row) {
    $(document).on('keydown', app.transaction_history.keyListener);

    $row.addClass('edit_active');

    $row.find('td:not(.actions)').each(function wrapInput(){
      var $input = $('<input></input>');

      if ($(this).hasClass('date')) {
        $input.attr({
          'placeholder' : 'YYYY-MM-DD',
          'type' : 'date'
        });

      } else if($(this).hasClass('number')) {
        $input.attr('type', 'number');

      } else {
        $input.attr('type', 'text');
        $input.addClass('combo');
        $(this).addClass('combo');
      }

      $input.attr({
        'name'  : $(this).attr('data-name'),
        'title' : $(this).text(),
        'value' : $(this).attr('data-value')
      });
      $input.css('width', $(this).width());

      $(this).html($input);
    });
  }
};
