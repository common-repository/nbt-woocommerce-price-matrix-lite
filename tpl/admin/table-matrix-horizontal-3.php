<?php
$attribute_one = pm_attribute_label($_pm_attr['horizontal'][0], $product_id, $order_attributes);
$attribute_two = pm_attribute_label($_pm_attr['horizontal'][1], $product_id, $order_attributes);
$attribute_three = pm_attribute_label($_pm_attr['vertical'][0], $product_id, $order_attributes);

?>
<div class="table-responsive">
    <table class="pure-table price-matrix-table horizontal-3">
        <tbody>
            <tr>
                <td class="attr-name" rowspan="2"></td>
                <?php foreach ($attribute_one as $kat => $attr_one) :?>
                <td class="attr-name heading-center" colspan="<?php echo count($attribute_two);?>"><?php echo $attr_one->name;?></td>
                <?php endforeach;?>
            </tr>
            <?php
            end($attribute_one);
            $last_one = key($attribute_one);
            foreach ($attribute_one as $k_one => $attr_one) :
            if($k_one == 0):
                echo '<tr>';
            endif;?>
                <?php foreach ($attribute_two as $k_two => $attr_two) :?>
                <td class="attr-name"><?php echo $attr_two->name;?></td>
                <?php endforeach;?>
     
            <?php
            if($k_one == $last_one):
                echo '</tr>';
            endif;
            endforeach;?>

     
            <?php foreach ($attribute_three as $k_three => $attr_three) :?>
            <tr>
                <td class="attr-name"><?php echo $attr_three->name;?></td>
                <?php
                foreach ($attribute_one as $k_one => $attr_one) :
                    foreach ($attribute_two as $k_two => $attr_two) :
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
                    <?php endforeach;
                endforeach;?>

            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <input type="hidden" name="security" value="<?php echo wp_create_nonce( "_price_matrix_save" );?>" />
    <button type="button" class="button save_enter_price button-primary" style="margin-top: 15px;">Save</button>
    <span class="loading-wrap"><span class="enter-price-loading"></span></span>
</div>
