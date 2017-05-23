
<table class="value_list" id="transaction_history">
  <thead>
    <tr>
      <td class="date">Date</td>
      <td class="text">Customer</td>
      <td class="text">Description</td>
      <td class="number">Netto</td>
      <td class="number">7%</td>
      <td class="number">19%</td>
      <td class="number">Brutto</td>
      <td class="text">Category</td>
      <td class="actions"></td>
    </tr>
  </thead>
  <tbody>

    <?php if (!count($this->transactions)) { ?>
      <tr>
        <td colspan="8">No transactions</td>
      </tr>

    <?php } else { ?>
      <?php foreach ($this->transactions as $transaction) { ?>
        <tr data-transaction-id="'.$transaction['id'].'">
          <td class="date" data-name="date"
              data-value="<?php echo $transaction['date']; ?>">
            <?php echo date('d.m.Y', strtotime($transaction['date'])); ?>
          </td>
          <td class="text customer" data-name="customer"
              data-value="<?php echo $transaction['customer']; ?>">
            <?php echo $transaction['customer']; ?>
          </td>
          <td class="text description" data-name="description"
              data-value="<?php echo $transaction['description']; ?>">
            <?php echo $transaction['description']; ?>
          </td>
          <td class="number" data-name="netto"
              data-value="<?php echo $transaction['netto']; ?>">
            <?php echo sprintf('%01.2f €', $transaction['netto']); ?>
          </td>
          <td class="number" data-name="tax_7"
              data-value="<?php echo $transaction['tax_7']; ?>">
            <?php echo sprintf('%01.2f €', $transaction['tax_7']); ?>
          </td>
          <td class="number" data-name="tax_19"
              data-value="<?php echo $transaction['tax_19']; ?>">
            <?php echo sprintf('%01.2f €', $transaction['tax_19']); ?>
          </td>
          <td class="number" data-name="brutto"
              data-value="<?php echo $transaction['brutto']; ?>">
            <?php echo sprintf('%01.2f €', $transaction['brutto']); ?>
          </td>
          <td class="text" data-name="category"
              data-value="<?php echo $transaction['category']; ?>">
            <?php echo $transaction['category']; ?>
          </td>
          <td class="actions">
            <span class="button mode_edit material-icons"
                  title="Edit transaction">
              mode_edit
            </span>
            <span class="button delete material-icons"
                  title="Delete transaction">
              delete
            </span>
            <span class="button done material-icons"
                  title="Save changes">
              done
            </span>
            <span class="button cancel material-icons"
                  title="Cancel">
              cancel
            </span>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>

  </tbody>
</table>

<?php $this->form->show(); ?>
