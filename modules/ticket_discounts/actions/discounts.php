<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2015 Scil (http://scil.coop)
//    Philippe Pary
//
//    This file is part of Pastèque.
//
//    Pastèque is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Pastèque is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Pastèque.  If not, see <http://www.gnu.org/licenses/>.

// discounts action

namespace TicketProducts;

$message = NULL;
$error = NULL;

if (isset($_POST['delete-dis'])) {
    if (\Pasteque\DiscountsService::deleteDis($_POST['delete-dis'])) {
        $message = \i18n("Changes saved");
    } else {
        $error = \i18n("Unable to save changes");
    }
}

$discounts = \Pasteque\DiscountsService::getAll();
?>

<h1><?php \pi18n("Discounts", PLUGIN_NAME); ?></h1>

<?php \Pasteque\tpl_msg_box($message, $error); ?>

<?php \Pasteque\tpl_btn('btn', \Pasteque\get_module_url_action(PLUGIN_NAME, "discount_edit"),
        \i18n('Add a discount campain', PLUGIN_NAME), 'img/btn_add.png');?>
<?php \Pasteque\tpl_btn('btn', \Pasteque\get_module_url_action(PLUGIN_NAME, "discountsManagement"),
        \i18n('Import discounts', PLUGIN_NAME), 'img/btn_add.png');?>

<p><?php \i18n("%d discounts", PLUGIN_NAME, count($discounts)); ?></p>

<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php \pi18n("Discount.label"); ?></th>
                        <th><?php \pi18n("Discount.startDate"); ?></th>
                        <th><?php \pi18n("Discount.endDate"); ?></th>
                        <th><?php \pi18n("Discount.rate"); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php

function printDiscount($printDiscount) {
    ?><tr class="row">
            <td><? echo $printDiscount->label; ?></td>
            <td><? echo $printDiscount->startDate; ?></td>
            <td><? echo $printDiscount->endDate; ?></td>
            <td><? echo $printDiscount->rate; ?></td>
            <td class="edition">
                    <?php \Pasteque\tpl_btn("edition", \Pasteque\get_module_url_action(PLUGIN_NAME,
                            'discount_edit', array("id" => $printDiscount->id)), "",
                            'img/edit.png', \i18n('Edit'), \i18n('Edit'));
                    ?>
            <form action="<?php echo \Pasteque\get_current_url(); ?>" method="post"><?php \Pasteque\form_delete("dis", $printDiscount->id, \Pasteque\get_template_url() . 'img/delete.png') ?></form></td>
</tr><?
}
foreach($discounts as $discount) {
     printDiscount($discount);
}
?>
    </tbody>
</table>
<?php
if (count($discounts) == 0) {
?>
<div class="alert"><?php \pi18n("No discount campain found", PLUGIN_NAME); ?></div>
<?php
}
?>
