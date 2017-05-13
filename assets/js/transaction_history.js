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
    var transaction_id = $(this).closest('tr').attr('data-transaction-id');
    // TODO add confirmation dialog
    app.transaction_history.deleteTransaction(transaction_id);
  },


  deleteTransaction : function(transaction_id) {
    $.post({
      url: 'api.php',
      data: {
        'action' : 'delete_transaction',
        'transaction_id' : transaction_id
      },
      success: function(data) {
        if (data == 'success') {
          $('tr[data-transaction-id=' + transaction_id + ']')
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
  },

  doneButtonClick : function() {
    $(this).closest('tr').removeClass('edit_active');
  },

  editButtonClick : function() {
    $(app.transaction_history.table).find('.edit_active').removeClass('edit_active');
    $(this).closest('tr').addClass('edit_active');
  }
};
