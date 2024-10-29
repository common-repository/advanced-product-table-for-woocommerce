<?php
add_filter('iwptp_element', 'iwptp_condition__evaluate', 100, 1);
function iwptp_condition__evaluate( $element ){
  if( 
    ! empty( $element['condition'] ) && 
    ! iwptp_condition( $element['condition'] ) ){
    return false;
  }

  return $element;
}

function iwptp_condition( $condition ){
  // no condition was set
  $no_condition = true;
  $conditions = array(
    'product_type_enabled',
    'custom_field_enabled',
    'attribute_enabled',
    'category_enabled',
    'stock_enabled',
    'price_enabled',
    'store_timings_enabled',
    'user_role_enabled',
  );
  foreach( $conditions as $i ){
    if( ! empty( $condition[$i] ) ){
      $no_condition = false;
    }
  }

  if( $no_condition ){
    return true;
  }

  // evaluate condition
  if( empty( $condition['action'] ) ){
    $condition['action'] = 'show';
  }

  $action = empty( $condition['action'] ) ? 'show' : $condition['action'];

  // $result = iwptp_condition_helper( $condition ); // boolean
  extract( iwptp_condition_helper( $condition ) );

  // return display value (true: show | false: hide)
  switch ($action) {
    case 'hide': // hide element if ALL checks are met
      if( ! $unmet_condition ){ // no unmet condition...
        return false; // ...so hide element
      }else{
        return true;
      }

      break;
    
    case 'hide_any':  // hide element if ANY checks are met
      if( $met_condition ){ // atleast one met condition...
        return false; // ...so hide element
      }else{
        return true;
      }

      break;

    case 'show_any':  // show element if ANY checks are met
      if( $met_condition ){ // atleast one met condition...
        return true; // ...so show element
      }else{
        return false;
      }
      
      break;

    default: // show element if ALL checks are met
      if( ! $unmet_condition ){ // no unmet condition...
        return true; // ...so show element
      }else{
        return false;
      }

      break;
  }

  if( $condition['action'] === 'show' ){ // Show if ALL conditions are met
    if( $result ){
      return true;
    }

  }else{ // Hide if ANY condition is met
    if( ! $result ){
      return false;
    }else{
      return true;
    }

  }

  return false;
}

function iwptp_condition_helper( $condition ){

  global $product;

  extract( $condition );
  $unmet_condition = false;
  $met_condition = false;

  // product type

  // if( 
  //   ! empty( $product_type_enabled ) && 
  //   ! empty( $product_type ) && 
  //   ! in_array( $product->get_type(), $product_type ) 
  // ){
  // 	return false;
  // }

  if( 
    ! empty( $product_type_enabled ) &&
    ! empty( $product_type )
  ){
    if( in_array( $product->get_type(), $product_type ) ){
      $met_condition = true;
    }else{
      $unmet_condition = true;
    }
    
  }  

  // user roles
  if( 
    ! empty( $user_role_enabled ) && 
    ! empty( $user_role ) 
  ){

    if( ! is_user_logged_in() ){
      $current_user_role = array('_visitor');

    }else{
      $user = wp_get_current_user();
      $roles = ( array ) $user->roles;
      $current_user_role = $roles;

    }

    if( array_intersect( $current_user_role, $user_role ) ){
      $met_condition = true;

    }else{
      $unmet_condition = true;

    }
    
  }

  // custom field condition
  if( 
    ! empty( $custom_field_enabled ) && 
    ! empty( $custom_field ) 
  ){

    if( ! isset( $custom_field_value ) ){
      $custom_field_value = '';
    }

    $custom_field_value = trim( $custom_field_value );

    $val = trim( get_post_meta( $product->get_id(), $custom_field, true ) );

    $_unmet_condition = false;

    // no val permitted
    if( $custom_field_value == '-' ){
      if( $val !== '' ){
        $_unmet_condition = true;
      }

    // any value permitted
    }else if( $custom_field_value === '' ){
      if( $val === '' ){
        $_unmet_condition = true;
      }

    }else{

      $arr = array_map( 'trim', explode( '||',  $custom_field_value ) );
      $arr2 = array_map( 'trim', explode( '-',  $custom_field_value ) );

      // range
      if( count( $arr2 ) == 2 ){

        if( ! ( (float)$arr2[0] <= (float)$val && (float)$val <= (float)$arr2[1] ) ){
          $_unmet_condition = true;
        }

      }else{

        // multi/single
        if( ! in_array( $val, $arr ) ){
          $_unmet_condition = true;
        }

      }

    }

    if( $_unmet_condition ){
      $unmet_condition = true;

    }else{
      $met_condition = true;      

    }

  }

  // attribute condition
  if( ! empty( $attribute_enabled ) && ! empty( $attribute ) ){

    $_unmet_condition = false;    

    if( $product->get_type() == 'variation' ){
      $terms = array( get_post_meta( $product->get_id(), 'attribute_pa_' . $attribute, true ) );

    }else{
      $terms = get_the_terms( $product->get_id(), 'pa_' . $attribute );
    }

    if( empty( $attribute_term ) ){
      $attribute_term = '';
    }

    // no term permitted
    if( $attribute_term == '-' ){
      if( $terms ){
        $_unmet_condition = true;
      }

    // any value permitted
    }else if( ! $attribute_term ){
      if( ! $terms ){
        $_unmet_condition = true;
      }

    }else{

      if( ! $terms ){
        $_unmet_condition = true;

      }else{

        $arr = array_map( 'trim', explode( '||',  $attribute_term ) );

        if( $product->get_type() == 'variation' ){
          $term_slugs = $terms;
  
        }else{
          $term_slugs = [];
          foreach( $terms as $term ){
            $term_slugs[] = $term->slug;
          }
  
        }
  
        if( ! count( array_intersect( $arr, $term_slugs ) ) ){
          $_unmet_condition = true;
        }        

      }

    }

    if( $_unmet_condition ){
      $unmet_condition = true;

    }else{
      $met_condition = true;      

    }

  }

  // category condition
  if( ! empty( $category_enabled ) && ! empty( $category ) ){

    $_unmet_condition = false;    

    $terms = get_the_terms( $product->get_id(), 'product_cat' );

    if( empty( $category ) ){
      $category = '';
    }

    if( ! $terms ){
      $_unmet_condition = true;
    }

    $arr = [];
    
    foreach( explode( '||',  $category ) as $item ){
      $arr[] = sanitize_title( $item );
    }

    $term_slugs = [];
    foreach( $terms as $term ){
      $term_slugs[] = $term->slug;
    }

    if( ! count( array_intersect( $arr, $term_slugs ) ) ){
      $_unmet_condition = true;
    }

    if( $_unmet_condition ){
      $unmet_condition = true;

    }else{
      $met_condition = true;      

    }    

  }

  // stock condition
  if( 
    ! empty( $stock_enabled ) && 
    isset( $stock ) 
  ){

    $_unmet_condition = false;

    $stock = trim( $stock );

    if( in_array( $stock, array( 'instock', 'outofstock', 'onbackorder' ) ) ){
      if( $product->get_stock_status() !== strtolower( $stock ) ){
        $_unmet_condition = true;
      }

    }else{

      if( 
        $product->get_manage_stock() == 'yes' ||
        (
          $product->get_manage_stock() == 'parent' &&
          $product->get_type() == 'variation' &&
          $product->get_stock_status() == 'instock'
        )
      ){
        $val = $product->get_stock_quantity();  

      }else{
        switch ( $product->get_stock_status() ) {
          case 'instock':
          case 'onbackorder':
            $val = 1e9;
            break;
          
          default: // outofstock
            $val = 0;
            break;

        }

      }

      $arr = array_map( 'trim', explode( '||',  $stock ) );
      $arr2 = array_map( 'trim', explode( ' - ',  $stock ) );
  
      // range
      if( count( $arr2 ) == 2 ){
  
        if( ! ( (float)$arr2[0] <= (float)$val && (float)$val <= (float)$arr2[1] ) ){
          $_unmet_condition = true;
        }
  
      }else{
  
        // multi/single
        if( ! in_array( $val, $arr ) ){
          $_unmet_condition = true;
        }
  
      }

    }

    if( $_unmet_condition ){
      $unmet_condition = true;

    }else{
      $met_condition = true;      

    }        

  }

  // price condition
  if( 
    ! empty( $price_enabled )
  ){

    $_unmet_condition = false;    

    if( 
      $product->get_type() == 'variable' &&
      isset( $price ) &&
      trim( $price ) === "0"
    ){

      $max_price = apply_filters('iwptp_product_get_highest_price', $product->get_variation_price('max'), $product);
      $min_price = apply_filters('iwptp_product_get_lowest_price', $product->get_variation_price('min'), $product);

      if( 
        $max_price ||
        $min_price
      ){
        $_unmet_condition = true;
      }

    }else if( in_array( $product->get_type(), array('variable', 'grouped') ) ){
      $_unmet_condition = true;

    }else{

      $val = apply_filters('iwptp_product_get_price', $product->get_price(), $product);

      $_permitted_vals = array_map( 'floatval', array_map( 'trim', explode( '||',  $price ) ) ); // permitted vals
      $_permitted_range = array_map( 'floatval', array_map( 'trim', explode( '-',  $price ) ) ); // permitted range
  
      if( count( $_permitted_range ) == 2 ){
        $permitted_vals = false;
        $permitted_range =& $_permitted_range;

        if( 
          ! (
              (float) $permitted_range[0] <= (float) $val && 
              (float) $val <= (float) $permitted_range[1] ) 
          ){
          $_unmet_condition = true;
        }
  
      }else{
        $permitted_vals =& $_permitted_vals;
        $permitted_range = false;  
        
        if( ! in_array( $val, $permitted_vals ) ){
          $_unmet_condition = true;
        }
  
      }      

    }

    if( $_unmet_condition ){
      $unmet_condition = true;

    }else{
      $met_condition = true;      

    }

  }

  // store timings condition

  if( 
    ! empty( $store_timings_enabled ) &&
    ! empty( trim( $store_timings ) )
  ){
    $store_timings_exploded = preg_split( '/\r\n|[\r\n]/', trim( $store_timings ) );
    $store_timings_arr = [];
    
    foreach( $store_timings_exploded as $rule ){
      $rule_exploded = array_map( 'trim', explode( ':', $rule ) );
      $day = strtolower( $rule_exploded[0] );
      $timing_slots = $rule_exploded[1];
      
      $store_timings_arr[$day] = [];
    
      if( false !== strpos( $timing_slots, 'open_all_day' ) ){
        $store_timings_arr[$day] = '[open_all_day]';
        continue;
      }
    
      if( false !== strpos( $timing_slots, 'closed_all_day' ) ){
        $store_timings_arr[$day] = '[closed_all_day]';
        continue;
      }
      
      $timing_slots_arr = [];
      $timing_slots_exploded = array_map( 'trim', explode( '|', $timing_slots ) );
      foreach( $timing_slots_exploded as $timing_slot ){
        $timing_slot_exploded = array_map( 'trim', explode( '-', $timing_slot ) );
        $timing_slots_arr[] = array( $timing_slot_exploded[0], $timing_slot_exploded[1] );
      }
    
      $store_timings_arr[$day] = $timing_slots_arr;
    }
  
    if( empty( $timezone ) ){
      $timezone = 'UTC+0';
    }
  
    if( 
      substr( $timezone, 0, 4 ) == 'UTC+' ||
      substr( $timezone, 0, 4 ) == 'UTC-'
    ){
      $dec = substr( $timezone, 4 );
      $seconds = ($dec * 3600);
      $hours = floor($dec);
      $seconds -= $hours * 3600;
      $minutes = floor($seconds / 60);
      $timezone = $timezone[3] . ( ((strlen($hours) < 2) ? "0{$hours}" : $hours) . ((strlen($minutes) < 2) ? "0{$minutes}" : $minutes));
    }
  
    $date = new DateTime(null, new DateTimeZone( $timezone ));
    $timestamp = $date->getTimestamp() +  $date->getOffset();
 
    
    $day = strtolower( gmdate('F j, Y', $timestamp) ); // date October 1, 2020

    if( empty( $store_timings_arr[$day] ) ){
      $day = strtolower( gmdate('l', $timestamp) ); // week day
    }

    $hours = gmdate('Hi', $timestamp);
  
    $open = false;	

    if( ! empty( $store_timings_arr[$day] ) ){
      if( $store_timings_arr[$day] == '[closed_all_day]' ){
        $open = false;

      }else if( $store_timings_arr[$day] == '[open_all_day]' ){
        $open = true;

      }else {
        foreach( $store_timings_arr[$day] as $set ){
          if( 
            $hours >= $set[0] &&
            $hours <=  $set[1]
          ){
            $open = true;
            break;
          }
        }
 
      }

    }
    
    if( ! $open ){
      $unmet_condition = true;

    }else{
      $met_condition = true;

    }

  }

  return array(
    'unmet_condition'=> $unmet_condition,
    'met_condition'=> $met_condition,
  );

}