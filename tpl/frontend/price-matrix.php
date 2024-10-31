<?php
$order_attributes = get_post_meta($product->get_id(), '_pm_order_attributes', true);
$attribute_one = pm_attribute_label($_pm_attr['vertical'][0], $product->get_id(), $order_attributes);
$attribute_two = pm_attribute_label($_pm_attr['horizontal'][0], $product->get_id(), $order_attributes);
$default_attributes = $product->get_default_attributes();


?>
<div class="table-responsive<?php if($khuyet){ echo ' hide';}?>">
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
                <td class="attr-name" style="padding-left: 20px;"><?php echo $attr_two->name;?></td>
                <?php
                $id = '';
                foreach ($attribute_one as $kat => $attr_one) :
                    $hover_detail = '';
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

                    if($deprived){
                        $group_attr = array_merge($group_attr, $deprived);
                    }

                    $id = md5($attr_one->taxonomy . $attr_one->slug . $attr_two->taxonomy . $attr_two->slug);

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

                    $variations = pm_attribute_price( $group_attr, $product );

                    $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_one->taxonomy, $product->get_id()).'</td><td>'.$attr_one->name.'</td></tr>';
                    $hover_detail .= '<tr><td>'.pm_attribute_tax($attr_two->taxonomy, $product->get_id()).'</td><td>'.$attr_two->name.'</td></tr>';
                    $hover_detail .= '<tr><td>'. __('Total', 'nbt-solution') .'</td><td class=&quot;total_price&quot;>'.htmlspecialchars($variations['price']).'</td></tr>';
                    
                    
                    ?>
                    <td id="<?php echo 'pm-price-' . $variations['variation_id'];?>" style="text-align: center" class="pm-td-price tippy<?php echo $selected;?>" title="<table><?php echo $hover_detail;?></table>" data-attr="<?php echo htmlspecialchars(wp_json_encode( $group_attr ));?>" data-price="<?php echo isset($variations['final_price']) ? $variations['final_price'] : '';?>"><span class="pm-price-wrap<?php echo $show_regular_price;?>"><?php echo $variations['price'];?></span></td>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <?php
    $_pm_show = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_on'];
    if($_pm_show != 'default'){
        echo '<div class="wc-price-matrix-amount price" style="margin-top: 15px;font-size: 1.41575em;"></div>';
    }?>
</div>