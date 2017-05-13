
var app = app || {};

app.dialog = {
  callback : null,
  dialog : '#dialog',
  dialog_active : 'has_dialog',

  cancelListener : function() {
    app.dialog.close();
    app.dialog.callback(false);
  },

  close : function() {
    $(document).off('keydown', this.keyListener);
    $('body').removeClass(this.dialog_active);
  },

  confirmListener : function() {
    app.dialog.close();
    app.dialog.callback(true);
  },

  keyListener : function(event) {
    switch(event.which) {
      case app.utils.keys.ENTER:
        app.dialog.confirmListener();
        break;

      case app.utils.keys.ESCAPE:
        app.dialog.cancelListener();
        break;

      case app.utils.keys.ARROW_DOWN:
      case app.utils.keys.ARROW_LEFT:
      case app.utils.keys.ARROW_RIGHT:
      case app.utils.keys.ARROW_UP:
      case app.utils.keys.SPACE:
        return false;

      default:
        break;
    }
  },

  open : function(data) {
    if (!data.callback) {
      console.error('Error: No callback given for the dialog.');
      return;
    }

    $(document).on('keydown', this.keyListener);
    $(dialog).find('.confirm').click(this.confirmListener);
    $(dialog).find('.cancel').click(this.cancelListener);

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
