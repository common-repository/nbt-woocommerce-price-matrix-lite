<?php
$attribute_one = pm_attribute_label($_pm_attr['vertical'][0], $product_id, $order_attributes);
$attribute_two = pm_attribute_label($_pm_attr['vertical'][1], $product_id, $order_attributes);
$attribute_three = pm_attribute_label($_pm_attr['horizontal'][0], $product_id, $order_attributes);

?>
<div class="table-responsive">
    <table class="pure-table price-matrix-table vertical-3">
        <tbody>
            <tr>
                <td class="attr-name" colspan="2"></td>
                <?php foreach ($attribute_three as $kat => $attr_three) :?>
                <td class="attr-name heading-center"><?php echo $attr_three->name;?></td>
                <?php endforeach;?>
            </tr>
            <?php
            foreach ($attribute_one as $key => $attr_one) :
                foreach ($attribute_two as $katw => $attr_two) :?>
                <tr>
                    <?php if($katw == 0):?>
                    <td class="attr-name first" rowspan="<?php echo count($attribute_two);?>"><?php echo $attr_one->name;?></td>
                    <?php endif;?>
                    <td class="attr-name"><?php echo $attr_two->name;?></td>
                    <?php foreach ($attribute_three as $kat => $attr_three):
                    $group_attr = array(
                        array(
                            'name' => $attr_one->taxonomy,
                            'value' => $attr_one->slug
                        ),
                        array(
                            'name' => $attr_two->taxonomy,
                            'value' => $attr_two->slug
                        ),
                        array(
                            'name' => $attr_three->taxonomy,
                            'value' => $attr_three->slug
                        )
                    );

                    if( ! empty($deprived) ) {
                        $group_attr = array_merge($group_attr, $deprived);
                    }
                    
                    ?>
                    <td class="price" data-attr="<?php echo htmlspecialchars(wp_json_encode( $group_attr ));?>"><div class="wrap"><div style="margin: 10px 5px;" class="" contenteditable="false"><?php echo pm_attribute_price($group_attr, $product, true);?></div></div></td>
                    <?php endforeach;?>
                </tr>
                <?php endforeach;
            endforeach;
            ?>
        </tbody>
    </table>

    <input type="hidden" name="security" value="<?php echo wp_create_nonce( "_price_matrix_save" );?>" />
    <button type="button" class="button save_enter_price button-primary" style="margin-top: 15px;">Save</button>
    <span class="loading-wrap"><span class="enter-price-loading"></span></span>
</div>
