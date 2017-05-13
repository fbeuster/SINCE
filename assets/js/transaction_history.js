var app = app || {};

app.transaction_history = {
  table : '',

  init : function(table) {
    this.table = table;
    this.bindClickListener();
  },

  bindClickListener : function() {
    $('span.button.cancel').click(this.cancelButtonClick);
    $('span.button.done').click(this.doneButtonClick);
    $('span.button.mode_edit').click(this.editButtonClick);
  },

  cancelButtonClick : function() {
    $(this).closest('tr').removeClass('edit_active');
  },

  doneButtonClick : function() {
    $(this).closest('tr').removeClass('edit_active');
  },

  editButtonClick : function() {
    $(app.transaction_history.table).find('.edit_active').removeClass('edit_active');
    $(this).closest('tr').addClass('edit_active');
  }
};
