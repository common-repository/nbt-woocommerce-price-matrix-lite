<?php

$attribute_one = pm_attribute_label($_pm_attr['vertical'][0], $product_id, $order_attributes);
$attribute_two = pm_attribute_label($_pm_attr['horizontal'][0], $product_id, $order_attributes);
?>

<div class="table-responsive">
    <table class="pure-table price-matrix-table">
        <tbody>
            <tr>
                <td class="attr-name"></td>
                <?php foreach ($attribute_one as $kat => $attr_one) :?>
                <td class="attr-name heading-center"><?php echo $attr_one->name;?></td>
                <?php endforeach;?>
            </tr>
            <?php foreach ($attribute_two as $k_two => $attr_two) :?>
            <tr>
                <td class="attr-name"><?php echo $attr_two->name;?></td>
                <?php foreach ($attribute_one as $kat => $attr_one) :

                    $group_attr = array(
                        array(
                            'name' => $attr_one->taxonomy,
                            'value' => $attr_one->slug
                        ),
                        array(
                            'name' => $attr_two->taxonomy,
                            'value' => $attr_two->slug
                        )
                    );

                    if( ! empty($deprived) ) {
                        $group_attr = array_merge($group_attr, $deprived);
                    }

                    if( ! $_price = pm_attribute_price($group_attr, $product, true) ) {
                        $_price = '';
                    }
                ?>
                    <td class="price" data-attr="<?php echo htmlspecialchars(wp_json_encode( $group_attr ));?>"><div class="wrap"><div style="margin: 10px 5px;" class="" contenteditable="false"><?php echo $_price;?></div></div></td>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <input type="hidden" name="security" value="<?php echo wp_create_nonce( "_price_matrix_save" );?>" />
    <button type="button" class="button save_enter_price button-primary" style="margin-top: 15px;">Save</button>
    <span class="loading-wrap"><span class="enter-price-loading"></span></span>
</div>
