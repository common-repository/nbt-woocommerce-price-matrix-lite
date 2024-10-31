<?php
$order_attributes = get_post_meta($product->get_id(), '_pm_order_attributes', true);
$attribute_one = pm_attribute_label($_pm_attr['vertical'][0], $product->get_id(), $order_attributes);
$attribute_two = pm_attribute_label($_pm_attr['vertical'][1], $product->get_id(), $order_attributes);
$attribute_three = pm_attribute_label($_pm_attr['horizontal'][0], $product->get_id(), $order_attributes);
$attribute_four = pm_attribute_label($_pm_attr['horizontal'][1], $product->get_id(), $order_attributes);
$default_attributes = $product->get_default_attributes();
?>
<div class="table-responsive">
    <table class="pure-table table-matrix-4">
        <tbody>

            <tr>
                <td class="attr-name" rowspan="2" colspan="2"></td>
                <?php foreach ($attribute_three as $kat => $attr_three) :?>
                <td class="attr-name heading-center" colspan="3"><?php echo $attr_three->name;?></td>
                <?php endforeach;?>
            </tr>
            <?php
            end($attribute_three);
            $last_three = key($attribute_three);
            foreach ($attribute_three as $k_three => $attr_three) :
            if($k_three == 0):
                echo '<tr>';
            endif;?>
                <?php foreach ($attribute_four as $k_four => $attr_four) :?>
                <td class="attr-name"><?php echo $attr_four->name;?></td>
                <?php endforeach;?>
     
            <?php
            if($k_three == $last_three):
                echo '</tr>';
            endif;
            endforeach;?>
            <?php
            foreach ($attribute_one as $key => $attr_one) :
                foreach ($attribute_two as $katw => $attr_two) :?>
                <tr>
                    <?php if($katw == 0):?>
                    <td class="attr-name first" rowspan="<?php echo count($attribute_two);?>"><?php echo $attr_one->name;?></td>
                    <?php endif;?>
                    <td class="attr-name"><?php echo $attr_two->name;?></td>
                    <?php foreach ($attribute_three as $kat => $attr_three):

                    $id = '';
                    foreach ($attribute_four as $kaf => $attr_four):
                        $hover_detail = '';
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
                            ),
                            array(
                                'name' => $attr_four->taxonomy,
                                'value' => $attr_four->slug
                            )
                        );

                        $id = md5($attr_one->taxonomy . $attr_one->slug . $attr_two->taxonomy . $attr_two->slug . $attr_three->taxonomy . $attr_three->slug . $attr_four->taxonomy . $attr_four->slug);
                        
                        if($deprived) {
                            $group_attr = array_merge($group_attr, $deprived);
                        }

                        $count_default = 0;
                        foreach( $group_attr as $k => $v ) {
                            
                            if( isset($default_attributes[$v['name']]) ) {
                                if( $default_attributes[$v['name']] == $v['value'] ) {
                                    $count_default += 1;
                                }
                            }
                        }
                        
                        $selected = '';
                        if( count($group_attr) == $count_default && ! isset($_POST['add-to-cart']) ) {
                            $selected = ' selected';
                        }

                        $variations = pm_attribute_price($group_attr, $product);

                        $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_one->taxonomy, $product->get_id()).'</td><td>'.$attr_one->name.'</td></tr>';
                        $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_two->taxonomy, $product->get_id()).'</td><td>'.$attr_two->name.'</td></tr>';
                        $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_three->taxonomy, $product->get_id()).'</td><td>'.$attr_three->name.'</td></tr>';
                        $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_four->taxonomy, $product->get_id()).'</td><td>'.$attr_four->name.'</td></tr>';
                        $hover_detail .= '<tr><td>'. __('Total', 'nbt-solution') .'</td><td class=&quot;total_price&quot;>'.htmlspecialchars($variations['price']).'</td></tr>';
                        
                        ?>
                        <td id="<?php echo 'pm-price-' . $variations['variation_id'];?>" class="pm-td-price tippy<?php echo $selected;?>" title="<table><?php echo $hover_detail;?></table>" data-attr="<?php echo htmlspecialchars(wp_json_encode( $group_attr ));?>" data-price="<?php echo isset($variations['final_price']) ? $variations['final_price'] : '';?>"><?php echo $variations['price'];?></td>
                    <?php endforeach;
                    endforeach;?>
                </tr>
                <?php endforeach;
            endforeach;
            ?>
        </tbody>
    </table>
    <?php
    $_pm_show = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_on'];
    if($_pm_show != 'default') {
        echo '<div class="wc-price-matrix-amount price" style="margin-top: 15px;font-size: 1.41575em;"></div>';
    }?>
</div>