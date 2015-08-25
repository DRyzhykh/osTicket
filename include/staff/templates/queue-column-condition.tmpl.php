<?php
// Calling convention:
//
// $field - field for the condition (Ticket / Last Update)
// $properties - currently-configured properties for the condition
// $condition - <QueueColumnCondition> instance for this condition
?>
<div class="condition">
  <div class="pull-right">
    <a href="#" onclick="javascript: $(this).closest('.condition').remove();
      "><i class="icon-trash"></i></a>
  </div>
  <?php echo $field->get('label'); ?>
  <div class="advanced-search">
<?php
$name = $field->get('name');
$parts = SavedSearch::getSearchField($field, $name);
// Drop the search checkbox field
unset($parts["{$name}+search"]);
foreach ($parts as $name=>$F) {
    if (substr($name, -7) == '+method')
        // XXX: Hack
        unset($F->ht['visibility']);
}
$form = new SimpleForm($parts);
foreach ($form->getFields() as $F) { ?>
    <fieldset id="field<?php echo $F->getWidget()->id;
        ?>" <?php
            $class = array();
            @list($name, $sub) = explode('+', $F->get('name'), 2);
            if (!$F->isVisible()) $class[] = "hidden";
            if ($sub === 'method')
                $class[] = "adv-search-method";
            elseif ($F->get('__searchval__'))
                $class[] = "adv-search-val";
            if ($class)
                echo 'class="'.implode(' ', $class).'"';
            ?>>
        <?php echo $F->render(); ?>
        <?php foreach ($F->errors() as $E) {
            ?><div class="error"><?php echo $E; ?></div><?php
        } ?>
    </fieldset>
<?php } ?>

    <div class="properties" style="margin-left: 25px; margin-top: 10px">
<?php foreach ($condition->getProperties() as $prop=>$v) {
    include 'queue-column-condition-prop.tmpl.php';
} ?>
      <div style="margin-top: 10px">
        <i class="icon-plus-sign"></i>
        <select onchange="javascript:
        var $this = $(this),
            selected = $this.find(':selected'),
            container = $this.closest('.properties');
        $.ajax({
          url: 'ajax.php/queue/condition/addProperty',
          data: { prop: selected.val() },
          dataType: 'html',
          success: function(html) {
            $(html).insertBefore(container);
            selected.prop('disabled', true);
          }
        });
        ">
          <option>— <?php echo __('Add a property'); ?> —</option>
<?php foreach (array_keys(QueueColumnConditionProperty::$properties) as $p) {
    echo sprintf('<option value="%s">%s</option>', $p, mb_convert_case($p, MB_CASE_TITLE));
} ?>
        </select>
      </div>
    </div>
  </div>
</div>