/*
iwptp customization notes

- iwptp_wc_price_calculator_params[] contains wc_price_calculator_params for each product, printed via PHP in <script> in product rows
- wc_price_calculator_params is passed into every function, relieving depence on a global wc_price_calculator_params
- pushed the instantly executable code inside 'iwptp_wc_mc_init_cart' focusing it on a single cart at a time
- changed every selector to constraint it to current cart
- modified price measurement + product addons integration code to make it independent of gloal data and work with form data
- this script must be loaded before iwptp as the latter depends on it
*/

var iwptp_wc_mc_init_cart;

jQuery( document ).ready( function( $ ) {

	/**
	 * Gets the price for the given measurement from the pricing rules
	 *
	 * @param float measurement the product measurement
	 * @return object the rule, if any
	 */
	function getPricingRule( measurement, wc_price_calculator_params ) {

		var foundRule = null;

		$.each( wc_price_calculator_params.pricing_rules, function( index, rule ) {
			if ( measurement >= parseFloat( rule.range_start ) && ( '' === rule.range_end || measurement <= rule.range_end ) ) {
				foundRule = rule;
				return false;
			}
		} );

		return foundRule;
  }

  iwptp_wc_mc_init_cart = function(){
    if( 'undefined' === typeof iwptp_wc_price_calculator_params ){
      return;
    }

    var $cart = $(this),
        product_id = $cart.closest('[data-iwptp-product-id]').attr('data-iwptp-product-id'),
        wc_price_calculator_params = iwptp_wc_price_calculator_params[product_id];

    /** Pricing Calculator ********************************************/


    if ( 'undefined' !== typeof wc_price_calculator_params && 'pricing' === wc_price_calculator_params.calculator_type ) {

      /**
       * if all required measurements are provided, calculate and display the total product price
       */
      $cart.bind( 'wc-measurement-price-calculator-update', function() {
        var totalMeasurement;

        // for each user-supplied measurement:  allow other plugins a chance to modify things
        $( '.amount_needed:input', $cart ).each( function( index, el ) {
          el      = $( el );
          var val = null;

          // don't standardize number inputs, given the decimal separator depends on the browser, not the site settings
          // see https://stackoverflow.com/questions/13412204/localization-of-input-type-number {BR 2017-12-05}
          if ( 'number' === $( this ).attr( 'type' ) ) {
            val = el.val();
          } else {
            val = standardizeInput( el.val(), wc_price_calculator_params );
          }

          var measurementValue = convertToFloat(val);

          el.trigger( 'wc-measurement-price-calculator-product-measurement-change', [measurementValue] );
        });

        // for each user-supplied measurement multiply it by the preceding ones to derive the Area or Volume
        $( '.amount_needed:input', $cart ).each( function( index, el ) {

          el      = $( el );
          var val = null;

          // don't standardize number inputs, given the decimal separator depends on the browser, not the site settings
          // see https://stackoverflow.com/questions/13412204/localization-of-input-type-number {BR 2017-12-05}
          if ( 'number' === $( this ).attr( 'type' ) ) {
            val = el.val();
          } else {
            val = standardizeInput( el.val(), wc_price_calculator_params );
          }

          var measurementValue = convertToFloat(val);

          // if no measurement value, or negative, we can't get a total measurement so break the loop
          if ( ! measurementValue || measurementValue < 0 ) {

            totalMeasurement = 0;
            return false;
          }

          // convert to the common measurement unit so as we multiply measurements together to derive an area or volume, we do so in a single known "common" unit
          measurementValue = convertUnits( measurementValue, el.data( 'unit' ), el.data( 'common-unit' ), wc_price_calculator_params );

          if ( 'area-linear' === wc_price_calculator_params.measurement_type ) {

            if ( ! totalMeasurement ) {
              // first or single measurement
              totalMeasurement = 2 * measurementValue;
            } else {
              // multiply to get either the area or volume measurement
              totalMeasurement += 2 * measurementValue;
            }

          } else if ( 'area-surface' === wc_price_calculator_params.measurement_type ) {

            if ( ! totalMeasurement ) {

              // calculate surface area only once
              var length = standardizeInput( $( '#length_needed', $cart ).val(), wc_price_calculator_params );
              length     = convertUnits( convertToFloat( length ), $( '#length_needed', $cart ).data( 'unit' ), $( '#length_needed', $cart ).data( 'common-unit' ), wc_price_calculator_params );

              var width = standardizeInput( $( '#width_needed', $cart ).val(), wc_price_calculator_params );
              width     = convertUnits( convertToFloat( width ), $( '#width_needed', $cart ).data( 'unit' ), $( '#width_needed', $cart ).data( 'common-unit' ), wc_price_calculator_params );

              var height = standardizeInput( $( '#height_needed', $cart ).val(), wc_price_calculator_params );
              height     = convertUnits( convertToFloat( height ), $( '#height_needed', $cart ).data( 'unit' ), $( '#height_needed', $cart ).data( 'common-unit' ), wc_price_calculator_params );

              totalMeasurement = 2 * ( length * width + width * height + length * height );
              return;
            }
          } else {
            if ( ! totalMeasurement ) {
              // first or single measurement
              totalMeasurement = measurementValue;
            } else {
              // multiply to get either the area or volume measurement
              totalMeasurement *= measurementValue;
            }
          }
        } );

        // now totalMeasurement is in 'product_total_measurement_common_unit', convert to pricing units
        totalMeasurement = convertUnits( totalMeasurement, wc_price_calculator_params.product_total_measurement_common_unit, wc_price_calculator_params.product_price_unit, wc_price_calculator_params );

        // is there a pricing rule which matches the customer-supplied measurement?
        if ( wc_price_calculator_params.pricing_rules ) {

          var rule = getPricingRule( totalMeasurement, wc_price_calculator_params );

          if ( rule ) {

            wc_price_calculator_params.product_price = parseFloat( rule.price );

            $( '.single_variation span.price', $cart ).html( rule.price_html );
          } else {

            wc_price_calculator_params.product_price = '';

            $( '.single_variation span.price', $cart ).html( '' );
          }
        }

        // set the measurement needed, so we can easily multiply (measurement needed) * (price per unit) to get the final product price on the backend
        $( '#_measurement_needed', $cart ).val( totalMeasurement );
        $( '#_measurement_needed_unit', $cart ).val( wc_price_calculator_params.product_price_unit );

        var price              = 0.0,
            price_overage      = 0.0,
            overage_percentage = parseFloat( wc_price_calculator_params.pricing_overage ),
            $price             = $( '.product_price', $cart ),
            $price_overage     = $( '.product_price_overage', $cart );

        if ( totalMeasurement ) {

          // calculate the price based on the total measurement
          price = wc_price_calculator_params.product_price * totalMeasurement;

          // check for a minimum price
          if ( wc_price_calculator_params.minimum_price > price ) {
            price = parseFloat( wc_price_calculator_params.minimum_price );
          }

          // calculate overage
          if ( overage_percentage > 0 ) {
            price_overage = price * overage_percentage;
            price += price_overage;

            // display price overage
            $price_overage.html( woocommerce_price( price_overage, wc_price_calculator_params ) );
          }

          // set the price
          $price.html( woocommerce_price( price, wc_price_calculator_params ) ).trigger( 'wc-measurement-price-calculator-product-price-change', [totalMeasurement, price] );

        } else {
          $price.html('').trigger( 'wc-measurement-price-calculator-product-price-change' );

          if ( overage_percentage > 0 ) {
            // clear overage
            $price_overage.html('');
          }
        }

        // display the total amount, in display units, if the "total amount" element is available
        var $measureTotalAmount = null;

        if ( $measureTotalAmount = $( '.wc-measurement-price-calculator-total-amount', $cart ) ) {

          var params = wc_price_calculator_params;

          var totalAmount = convertUnits( totalMeasurement, params.product_price_unit, $measureTotalAmount.data( 'unit' ), wc_price_calculator_params );

          totalAmount = parseFloat( totalMeasurement.toFixed( params.measurement_precision ) );

          $measureTotalAmount.text( number_format( totalAmount, params.measurement_precision, params.woocommerce_price_decimal_sep, params.woocommerce_price_thousand_sep ) );
        }

        // add support for WooCommerce Product Addons by feeding the calculated product price in and triggering the addons update
        if ( 'undefined' !== typeof woocommerce_addons_params && $cart.find( '#product-addons-total' ).length > 0 ) {

          var productPrice = '' === price ? 0 : price,
              $totals       = $cart.find( '#product-addons-total' );

          $totals.data( 'price', productPrice.toFixed( 2 ) );
          $cart.trigger( 'woocommerce-product-addons-update' );
        }
      } );

      // display pricing on page load if we can
      $cart.trigger( 'wc-measurement-price-calculator-update' );

      // pricing calculator measurement changed: update product pricing
      $( '.amount_needed:input', $cart ).on( 'keyup change mpc-change', function() {
        var $cart = $( this ).closest( 'form.cart' );
        $cart.trigger( 'wc-measurement-price-calculator-update' );

        // update_cookie( $cart );
      } ).first().trigger( 'mpc-change' );


      // called when a variable product is fully configured and the 'add to cart'
      //  button is displayed
      $( '.single_variation, .single_variation_wrap', $cart ).bind( 'show_variation', function( event, variation ) {

        var price        = parseFloat( variation.price );
        var minimumPrice = parseFloat( variation.minimum_price );

        wc_price_calculator_params.product_price = price; // set the current variation product price
        wc_price_calculator_params.minimum_price = minimumPrice;

        $cart.trigger( 'wc-measurement-price-calculator-show-variation', variation );
        $cart.trigger( 'wc-measurement-price-calculator-update' );

        $( '.variable_price_calculator', $cart ).show();
      });

    }


    /** Quantity calculator ********************************************/
    if ( 'undefined' !== typeof wc_price_calculator_params && 'quantity' === wc_price_calculator_params.calculator_type ) {

      /**
       * quantity changed, update the amount fields and total price
       */
      $cart.bind( 'wc-measurement-price-calculator-quantity-changed', function( event, quantity, updateAmountNeeded = true ) {

        if ( ! wc_price_calculator_params.product_measurement_value ) {
          return;
        }

        var fieldsToUpdate = [];

        if ( updateAmountNeeded ) {
          // update the amount needed/amount actual fields
          fieldsToUpdate = $( '.amount_needed, .amount_actual', $cart );
        } else {
          // update only the amount actual field (the event was triggered by changing the amount needed value)
          fieldsToUpdate = $( '.amount_actual', $cart );
        }

        fieldsToUpdate.each( function( index, el ) {

          el = $(el);

          // if we're dealing with more than one input, it's impossible to estimate the amounts needed based on quantity
          if ( $( '.amount_needed', $cart ).length > 1 ) {
            return;
          }

          // convert the product measurement value from product units to frontend display units
          var rawAmount = convertUnits( wc_price_calculator_params.product_measurement_value, wc_price_calculator_params.product_measurement_unit, el.data( 'unit' ), wc_price_calculator_params );
          // TODO: I'm not saying that rounding to two decimal places is the ideal/best solution, but it's a tough problem and hopefully reasonable for now
          var amount    = parseFloat( ( rawAmount * quantity ).toFixed( 2 ) );
          var params    = wc_price_calculator_params;

          // passing the value to an input field
          if ( el.is( 'input' ) ) {
            // unless we're dealing with numerical inputs, we may need to make sure that the correct separators are used or conversion issues may arise
            if ( 'number' !== el.attr( 'type' ) ) {
              // round number to two decimals places to be consistent with the rest of the use-cases
              el.val( number_format( amount, 2, params.woocommerce_price_decimal_sep, params.woocommerce_price_thousand_sep ) );
            } else {
              el.val( amount );
            }
          // passing the computed value to a calculated label value
          } else {
            // round number to two decimals places to be consistent with the rest of the use-cases
            el.text( number_format( amount, 2, params.woocommerce_price_decimal_sep, params.woocommerce_price_thousand_sep ) );
          }
        } );

        // set total price
        $( '.total_price', $cart ).html( woocommerce_price( quantity * wc_price_calculator_params.product_price, wc_price_calculator_params ) ).trigger( 'wc-measurement-price-calculator-quantity-total-price-change', [quantity, wc_price_calculator_params.product_price] );
      } );

      // Should show quantity discrepancy warning message.
      var show_warning = false;

      /**
       * "Compile" the product measurements down to a single value (dimension,
       * area, volume or weight) if enough measurements are provided by the customer, and
       * update the quantity, total price and actual amount fields
       */
      $cart.bind( 'wc-measurement-price-calculator-update', function() {

        if ( ! wc_price_calculator_params.product_measurement_value ) {
          return;
        }

        var totalMeasurement;

        // for each user-supplied measurement multiply it by the preceding ones to derive the Area or Volume
        $( 'input.amount_needed', $cart ).each( function( index, el ) {
          el = $( el );

          var val = standardizeInput( el.val(), wc_price_calculator_params );
          var measurementValue = convertToFloat(val);

          // if no measurement value, or negative, we can't get a total measurement so break the loop
          if ( ! measurementValue || measurementValue < 0 ) {
            totalMeasurement = 0;
            return false;
          }

          // convert to the common measurement unit so as we multiply measurements together to dervice an area or volume, we do so in a single known "common" unit
          measurementValue = convertUnits( measurementValue, el.data( 'unit' ), el.data( 'common-unit' ), wc_price_calculator_params );

          if ( ! totalMeasurement ) {
            // first or single measurement
            totalMeasurement = measurementValue;
          } else {
            // multiply to get either the area or volume measurement
            totalMeasurement *= measurementValue;
          }
        });

        if ( totalMeasurement ) {
          // convert the product measurement to total measurement units

          var productMeasurement = convertUnits( wc_price_calculator_params.product_measurement_value, wc_price_calculator_params.product_measurement_unit, wc_price_calculator_params.product_total_measurement_common_unit, wc_price_calculator_params );

          // determine the quantity based on the amount of product needed / amount of product in a quantity of 1
          //  note that we toFixed() to limit the amount of precision used since there's the chance of getting
          //  a value like 1.0000003932 when converting between different systems of measurement, and we wouldn't want to make that '2'
          var quantity = Math.ceil( ( totalMeasurement / productMeasurement ).toFixed( wc_price_calculator_params.measurement_precision ) );

          if ( quantity < parseFloat( wc_price_calculator_params.quantity_range_min_value ) ) {
            quantity = parseFloat( wc_price_calculator_params.quantity_range_min_value );
          }

          if ( parseFloat( wc_price_calculator_params.quantity_range_max_value ) && quantity > parseFloat( wc_price_calculator_params.quantity_range_max_value ) ) {
            quantity = parseFloat( wc_price_calculator_params.quantity_range_max_value );
            show_warning = true;
          } else {
            show_warning = false;
          }

          // update the quantity
          $( 'input[name=quantity]', $cart ).val( quantity );

          // update the amount actual fields
          $( '.amount_actual', $cart ).each( function( index, el ) {
            el = $( el );

            // convert the product measurement value from product units to frontend display units
            var amount = convertUnits(wc_price_calculator_params.product_measurement_value, wc_price_calculator_params.product_measurement_unit, el.data('unit'), wc_price_calculator_params);
            // TODO: I'm not saying that rounding to two decimal places is the ideal/best solution, but it's a tough problem and hopefully reasonable for now
            amount = parseFloat( (amount * quantity ).toFixed( 2 ) );

            if ( el.is( 'input' ) ) {
              el.val( amount );
            } else {
              el.text( amount );
            }
          });

          if ( show_warning ) {
            $( '#stock-discrepancy-warning', $cart ).remove();
            $cart.after( '<p id="stock-discrepancy-warning" class="woocommerce-error">' + wc_price_calculator_params.stock_warning + '</p>' );
          } else {
            $( '#stock-discrepancy-warning', $cart ).remove();
          }

          // update the total price
          $( '.total_price', $cart ).html( woocommerce_price( quantity * wc_price_calculator_params.product_price, wc_price_calculator_params ) ).trigger( 'wc-measurement-price-calculator-total-price-change', [quantity, wc_price_calculator_params.product_price] );
        }
      } );

      var $amount_needed_input = $( '.amount_needed:input', $cart );

      // pricing calculator measurement changed: update product quantity
      $amount_needed_input.on( 'keyup change mpc-change', function() {

        var $cart = $( this ).closest( 'form.cart' );

        $cart.trigger( 'wc-measurement-price-calculator-update' );

        update_cookie( $cart );

      } );

      // pricing calculator quantity changed: update actual amount
      $amount_needed_input.on('blur mpc-change', function() {

        var $cart = $( this ).closest( 'form.cart' ),
            quantity = $( 'input[name=quantity]' ).val();

        $cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [quantity, false] );
      });

      // update actual amount on page load (will use correct separators)	
      $amount_needed_input.trigger( 'mpc-change' )      

      // user typed a new quantity (change which we bind to below, only fires when the quantity field loses focus)
      $( 'input[name=quantity]', $cart ).on( 'change mpc-change', function( evt ) {
        var $cart = $( this ).closest( 'form.cart' );
        $cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [evt.target.value] );

        update_cookie( $cart );
      } ).trigger( 'change' );

      // called when a variable product is fully configured and the 'add to cart' button is displayed
      $( '.single_variation, .single_variation_wrap', $cart ).bind( 'show_variation', function( event, variation ) {

        wc_price_calculator_params.product_price             = parseFloat(variation.price);  // set the current variation product price
        wc_price_calculator_params.product_measurement_value = parseFloat(variation.product_measurement_value);
        wc_price_calculator_params.product_measurement_unit  = variation.product_measurement_unit;

        if ( variation.product_measurement_value ) {

          if ( $( 'input.amount_needed', $cart ).length > 0 ) {

            if ( ! $('input.amount_needed', $cart ).val() ) {

              // first time a variation is selected, no amount needed, so set the amount actual/total price based on the starting quantity
              $cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [ $('input[name=quantity]', $cart).val() ] );

            } else {

              // measurement inputs, so update the quantity, price for the current product
              $cart.trigger( 'wc-measurement-price-calculator-update' );
            }

          } else {
            // otherwise no measurement inputs, so just update the amount actual
            $cart.trigger( 'wc-measurement-price-calculator-quantity-changed', [ $('input[name=quantity]', $cart).val() ] );
          }

          $( '.variable_price_calculator', $cart ).show();
        } else {
          // variation does not have all required physical attributes defined, so hide the calculator
          $( '.variable_price_calculator', $cart ).hide();
        }
      });
    }
  }

	/** Core PHP Function Ports ********************************************/


	/**
	 * http://phpjs.org/functions/number_format/
	 */
	function number_format( number, decimals, dec_point, thousands_sep ) {

		// Strip all characters but numerical ones.
		number = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );

		var n = ! isFinite( +number ) ? 0 : +number,
			prec = ! isFinite( +decimals ) ? 0 : Math.abs( decimals ),
			sep = ( typeof thousands_sep === 'undefined' ) ? ',' : thousands_sep,
			dec = ( typeof dec_point === 'undefined' ) ? '.' : dec_point,
			s = '',

			toFixedFix = function ( n, prec ) {

				// instead of multiplying a float by the power, as in the original implementation
				// of http://phpjs.org/functions/number_format/, we shift the decimal point's position
				// in the string - this works around an issue where something like 1.275 * 100 = 127.49999999999
				var k = Math.pow( 10, prec ),
					s = '' + n,
					p = s.indexOf( '.' );

				// there be a decimal point in the number!
				if ( p > -1 ) {

					p += prec; // shift the decimal point's position by the precision amount - this is effectively the same as n * pow, without the floating point errors
					s = s.replace( '.', '' );

					var leading_zeroes = '';

					// check for leading zeroes and break them off
					// before using .startsWith though, we need to add a polyfill to support IE *sigh*
					if ( ! String.prototype.startsWith ) {
						String.prototype.startsWith = function( searchString, position ) {
							position = ! isNaN( parseInt( position ) ) ? position : 0;
							return this.indexOf( searchString, position ) === position;
						};
					}
					if ( s.startsWith( '0' ) ) {
						leading_zeroes = s.slice( 0, s.length - parseFloat( s ).toString().length );
					}

					var original = s; // stash the current value right now, we may need to do some rounding with it
					s = s.slice( 0, p );

					// check the number we're just about to cut off - if >= 5, we should be rounding the value of s up before restoring the decimal
					var round = original.slice( p, p+1 );
					if ( round >= 5 ) {
						s = parseFloat( s );
						s += 1;
						s = '' + s;

						// restore any leading zeros to the rounded number
						s = leading_zeroes + s;
					}

					// before we use this, be sure our initial slice has the number of digits we expect
					// we may need to add trailing zeros to have the right number of digits
					if ( s.length < p ) {
						// how many zeroes do we need to add? multiple by that power of 10
						var zeroes = p - s.length;
						s *= Math.pow( 10, zeroes );
						s = Number( s, 10 );
					} else {
						s = Number( [ s, '.', s.slice( p ) ].join( '' ), 10 );
					}

					return '' + ( Math.round( s ) / k );
				}

				// fall back to the original toFixed()
				return n.toFixed( prec );
			};

		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = ( prec ? toFixedFix( n, prec ) : '' + Math.round( n ) ).split( '.' );

		if ( s[0].length > 3 ) {
			s[0] = s[0].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
		}

		if ( ( s[1] || '' ).length < prec ) {
			s[1] = s[1] || '';
			s[1] += new Array( prec - s[1].length + 1 ).join( '0' );
		}

		return s.join(dec);
	}


	/**
	 * http://phpjs.org/functions/preg_quote/
	 */
	function preg_quote( str, delimiter ) {
		return ( str + '' ).replace( new RegExp( '[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + ( delimiter || '' ) + '-]', 'g' ), '\\$&' );
	}


	/** Custom PHP Function Ports ********************************************/


	/**
	 * Convert value from the current unit to a new unit
	 *
	 * @param numeric value the value in fromUnit units
	 * @param string fromUnit the unit that value is in
	 * @param string toUnit the unit to convert to
	 * @return numeric value in toUnit untis
	 */
	function convertUnits( value, fromUnit, toUnit, wc_price_calculator_params ) {

		// fromUnit to its corresponding standard unit
		if ( 'undefined' !== typeof( wc_price_calculator_params.unit_normalize_table[ fromUnit ] ) ) {

			if ( 'undefined' !== typeof( wc_price_calculator_params.unit_normalize_table[ fromUnit ].inverse ) && wc_price_calculator_params.unit_normalize_table[ fromUnit ].inverse ) {
				value /= wc_price_calculator_params.unit_normalize_table[ fromUnit ].factor;
			} else {
				value *= wc_price_calculator_params.unit_normalize_table[ fromUnit ].factor;
			}

			fromUnit = wc_price_calculator_params.unit_normalize_table[ fromUnit ].unit;
		}

		// standard unit to toUnit
		if ( 'undefined' !== typeof( wc_price_calculator_params.unit_conversion_table[ fromUnit ] ) && 'undefined' !== typeof( wc_price_calculator_params.unit_conversion_table[ fromUnit ][ toUnit ] ) ) {

			if ( 'undefined' !== typeof( wc_price_calculator_params.unit_conversion_table[ fromUnit ][ toUnit ].inverse ) && wc_price_calculator_params.unit_conversion_table[ fromUnit ][ toUnit ].inverse ) {
				value /= wc_price_calculator_params.unit_conversion_table[ fromUnit ][ toUnit ].factor;
			} else {
				value *= wc_price_calculator_params.unit_conversion_table[ fromUnit ][ toUnit ].factor;
			}
		}

		return value;
	}


	/**
	 * Convert a string with a possible (mixed) fraction to a float
	 *
	 * @param numeric value the value in fromUnit units
	 * @param string fromUnit the unit that value is in
	 * @param string toUnit the unit to convert to
	 * @return numeric value in toUnit untis
	 *
	 * @param string str the value to convert
	 * @return float returns the converted float
	 */
	function convertToFloat( str ) {

		var matches;

		if ( matches = str.match( /(\d+)\s+(\d+)\/(\d+)/ ) ) {
			return matches[3] !== 0 ? parseFloat( matches[1] ) + ( matches[2] / matches[3] ) : parseFloat( matches[1] );
		}

		if ( matches = str.match( /(\d+)\/(\d+)/ ) ) {
			return matches[2] !== 0 ? matches[1] / matches[2] : 0;
		}

		return '' === str ? 0 : parseFloat( str );
	}


	/**
	 * Standardizes a customer input to allow for other decimal separators and remove thousand separators.
	 *
	 * This isn't a great way to do this as it doesn't guarantee what the customer will use,
	 * but 'tis the best we can do without trying to accept every number input form {BR 2017-11-07}
	 *
	 * @since 3.12.5
	 *
	 * @param val {string|int|float} the customer input value
	 * @return {string} the standard number-formatted value
	 */
	function standardizeInput( val, wc_price_calculator_params ) {

		var thousandSeparator = $.trim( wc_price_calculator_params.woocommerce_price_thousand_sep ).toString(),
		    decimalSeparator  = $.trim( wc_price_calculator_params.woocommerce_price_decimal_sep ).toString();

		if ( ! val || null === val ) {
			val = '';
		} else if (  ! isNaN( val ) ) {
			val = val.toString();
		}

		if ( val.length > 0 ) {

			// remove thousands separators, but escape it if it's a decimal so we don't replace everything (#blameBeka)
			if ( '.' === thousandSeparator ) {
				val = val.replace( /\./g, '' );
			} else {
				val = val.replace( new RegExp( thousandSeparator, 'g' ), '' );
			}

			// allow for other decimal separators; THERE CAN BE ONLY ONE so replace the first we find
			val = val.replace( decimalSeparator, '.' );
		}

		return val;
	}


	/** WooCommerce Function Ports ********************************************/


	/**
	 * Returns the price formatted according to the WooCommerce settings
	 */
	function woocommerce_price( price, wc_price_calculator_params ) {

		var formatted_price = '';

		var num_decimals    = wc_price_calculator_params.woocommerce_price_num_decimals;
		var currency_pos    = wc_price_calculator_params.woocommerce_currency_pos;
		var currency_symbol = wc_price_calculator_params.woocommerce_currency_symbol;

		price = number_format( price, num_decimals, wc_price_calculator_params.woocommerce_price_decimal_sep, wc_price_calculator_params.woocommerce_price_thousand_sep );

		if ( 'yes' === wc_price_calculator_params.woocommerce_price_trim_zeros && num_decimals > 0 ) {
			price = woocommerce_trim_zeros( price, wc_price_calculator_params );
		}

		switch ( currency_pos ) {
			case 'left' :
				formatted_price = '<span class="amount">' + currency_symbol + price + '</span>';
				break;
			case 'right' :
				formatted_price = '<span class="amount">' + price + currency_symbol + '</span>';
				break;
			case 'left_space' :
				formatted_price = '<span class="amount">' + currency_symbol + '&nbsp;' + price + '</span>';
				break;
			case 'right_space' :
				formatted_price = '<span class="amount">' + price + '&nbsp;' + currency_symbol + '</span>';
				break;
		}

		return formatted_price;
	}


	/**
	 * Trim trailing zeros off prices.
	 */
	function woocommerce_trim_zeros( price, wc_price_calculator_params ) {
		return price.replace( new RegExp( preg_quote( wc_price_calculator_params.woocommerce_price_decimal_sep, '/' ) + '0+$' ), '' );
	}

});
