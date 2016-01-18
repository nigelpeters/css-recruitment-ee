

<tr class="odd seed_option_<?=$channel_id?>_category">
    <th scope="row" colspan="2">
    	There are <?=count( $option['values']['groups'] )?> category groups. You can choose a range for selection for each group
    </th>
</tr>


<tr class="odd seed_option_<?=$channel_id?>_category">
    <th scope="row">
        Minimum Categories for the [ CATEGORY gROUP NAME 1 ]
    </th>
    <td>
        There are 3 category groups

        <?='<pre>'.print_R($option,1).'</pre>'?>

         <!--<?php foreach( $option['values'][ 'groups' ] as $group ) : 

            $show_header = TRUE;

                
                foreach( $group as $cat ) :
                    if( $show_header ) : ?>
                        <h3><?=$cat['3']?></h3>
                    <?php $show_header = FALSE; endif; ?>

                 
                <?php endforeach; 

            endforeach; ?>


        <?php echo('<pre> - '.$channel_id.' - '.print_R($option,1).'</pre>') ?>
        
        Populate categories in the [ one ] group<br/>
        Populate categories in the [ two ] group -->

        
    </td>
</tr>
