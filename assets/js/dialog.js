
var app = app || {};

app.dialog = {
  callback : null,
  dialog : '#dialog',
  dialog_active : 'has_dialog',

  init : function() {
    this.bindClickListener();
  },

  bindClickListener : function() {
    $(dialog).find('.confirm').click(this.confirmListener);
    $(dialog).find('.cancel').click(this.cancelListener);
  },

  cancelListener : function() {
    app.dialog.close();
    app.dialog.callback(false);
  },

  close : function() {
    $('body').removeClass(this.dialog_active);
  },

  confirmListener : function() {
    app.dialog.close();
    app.dialog.callback(true);
  },

  open : function(data) {
    if (!data.callback) {
      console.error('Error: No callback given for the dialog.');
      return;
    }

    if (data.title) {
      $(this.dialog).find('.title').text(data.title);
    }

    if (data.message) {
      $(this.dialog).find('.message').text(data.message);
    }

    if (data.confirm) {
      $(this.dialog).find('.confirm').text(data.confirm);
    }

    if (data.cancel) {
      $(this.dialog).find('.cancel').text(data.cancel);
    }

    $('body').addClass(this.dialog_active);

    app.dialog.callback = data.callback;
  }
};
