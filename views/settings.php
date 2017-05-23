
<h3>Manage customers</h3>
<table class="value_list">
  <thead>
    <tr>
      <td class="text">Name</td>
      <td class="text">Color</td>
      <td class="actions"></td>
    </tr>
  </thead>
  <tbody>
    <?php if (count($this->customers)) { ?>
      <?php foreach ($this->customers as $customer) { ?>
        <tr>
          <td><?php echo $customer->getName(); ?></td>
          <td class="color">
            <?php if ($customer->getColor()) { ?>
              <span class="color" style="background-color: <?php echo $customer->getColor(); ?>"></span>
              <?php } ?>
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

    <?php } else { ?>
      <tr>
        <td colspan="3">No customers yet.</td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<h3>Manage categories</h3>
<table class="value_list">
  <thead>
    <tr>
      <td class="text">Name</td>
      <td class="text">Is Income</td>
      <td class="text">Type</td>
      <td class="actions"></td>
    </tr>
  </thead>
  <tbody>
    <?php if (count($this->categories)) { ?>
      <?php foreach ($this->categories as $category) { ?>
        <tr>
          <td><?php echo $category->getName(); ?></td>
          <td>
            <?php if ($category->isIncome()) { ?>
              yes
            <?php } else { ?>
              no
            <?php } ?>
          </td>
          <td><?php echo $category->getName(); ?></td>
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

    <?php } else { ?>
      <tr>
        <td colspan="3">No categories yet.</td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<h3 class="mt2">Other Settings</h3>

<form action="/settings" class="settings" method="post">
  <fieldset>
    <label>
      <span>Change language</span>
      <select name="language">
        <option value="en">English</option>
        <option value="de">German</option>
      </select>
    </label>
    <input type="submit" value="Save and reload" name="save_language">
  </fieldset>

  <fieldset>
    <label>
      <span>Change currency</span>
      <select name="language">
        <option value="usd">US-Dollar $</option>
        <option value="eur">Euro €</option>
      </select>
    </label>
    <input type="submit" value="Save and reload" name="save_currency">
  </fieldset>
</form>
