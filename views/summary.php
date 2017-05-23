
<?php

  foreach ($this->tables as $table) {
    $turnover = $table['turnover']; ?>
    <h3><?php echo $table['title']; ?></h3>
    <table class="value_list">
      <thead>
        <tr>
          <td class="text">Category</td>
          <td class="number">Netto</td>
          <td class="number">7%</td>
          <td class="number">19%</td>
          <td class="number">Brutto</td>
        </tr>
      </thead>

      <tfoot>
        <tr class="turnover">
          <td></td>
          <td class="number
            <?php echo $turnover['netto'] < 0 ? 'negative' : ''; ?>">
            <?php echo sprintf('%01.2f €', $turnover['netto']); ?>
          </td>
          <td class="number
            <?php echo $turnover['tax_7'] < 0 ? 'negative' : ''; ?>">
            <?php echo sprintf('%01.2f €', $turnover['tax_7']); ?>
          </td>
          <td class="number
            <?php echo $turnover['tax_19'] < 0 ? 'negative' : ''; ?>">
            <?php echo sprintf('%01.2f €', $turnover['tax_19']); ?>
          </td>
          <td class="number
            <?php echo $turnover['brutto'] < 0 ? 'negative' : ''; ?>">
            <?php echo sprintf('%01.2f €', $turnover['brutto']); ?>
          </td>
        </tr>
      </tfoot>

      <tbody>
        <?php foreach ($table['types'] as $type => $value) { ?>
          <tr <?php echo $value['is_income'] ? ' class="income"' : ''; ?>>
            <td class="text">
              <?php echo $type; ?>
            </td>
            <td class="number">
              <?php echo sprintf('%01.2f €', $value['netto']); ?>
            </td>
            <td class="number">
              <?php echo sprintf('%01.2f €', $value['tax_7']); ?>
            </td>
            <td class="number">
              <?php echo sprintf('%01.2f €', $value['tax_19']); ?>
            </td>
            <td class="number">
              <?php echo sprintf('%01.2f €', $value['brutto']); ?>
            </td>
          </tr>
        <?php } ?>

      </tbody>
    </table>
  <?php }

  $this->income_chart->show();
  $this->expense_chart->show();

?>
