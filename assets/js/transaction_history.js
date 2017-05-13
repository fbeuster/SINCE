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
    $(this).closest('tr').removeClass('edit_active');
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
    $(app.transaction_history.table).find('.edit_active').removeClass('edit_active');
    $(this).closest('tr').addClass('edit_active');
  }
};
