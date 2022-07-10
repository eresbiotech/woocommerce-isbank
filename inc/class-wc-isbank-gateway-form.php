<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Isbank_Gateway_Form {

	public static function init_form( $args ) {
		$form     = new WC_Payment_Gateway_CC();
		$form->id = $args['form_id'];

		$order      = new WC_Order( $args['order_id'] );
		$return_url = get_home_url() . "/wc-api/wc_gateway_isbank";
		$amount     = $order->order_total;
		$rnd        = microtime();
		$instalment_enabled = $args['instalment_enabled'];
		$taksit     = '';
		$hashstr    = $args['client_id'] . $args['order_id'] . $amount . $return_url . $return_url . 'Auth' . $taksit . $rnd . $args['store_key'];
		$hash       = base64_encode( pack( 'H*', sha1( $hashstr ) ) );

		$form_css = 'wc-isbank-checkout woocommerce-checkout';
		$form_css = apply_filters( 'woocoomerce_isbank_css', $form_css );

		wp_enqueue_script( 'wc-credit-card-form' );
		ob_start();
		?>
        <form action="<?php echo $args['action_url']; ?>" class="<?php echo $form_css; ?>" method="post">
            <div id="payment" class="woocommerce-checkout-payment">
                <ul class="wc_payment_methods payment_methods methods">
                    <li class="wc_payment_method payment_method_cod">
                        <div class="payment_box payment_method_isbank">
                            <fieldset id="wc-isbank-cc-form" class='wc-credit-card-form wc-payment-form'>

                                <p class="form-row form-row-wide">
                                    <label for="isbank-card-number">
                                        <?php echo __( 'Card Number', 'wc-isbank' ); ?>
                                        <span class="required">*</span>
                                    </label>

                                    <input id="isbank-card-number" class="input-text wc-credit-card-form-card-number"
                                           inputmode="numeric" autocomplete="cc-number" autocorrect="no"
                                           autocapitalize="no" spellcheck="no" type="tel"
                                           placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;"
                                           name="pan"/>
                                </p>

                                <p class="form-row form-row-first">
                                    <label for="isbank-card-expiry">
                                        <?php echo __( 'Expiry Date', 'wc-isbank' ); ?>
                                        <span class="required">*</span>
                                    </label>

                                    <input id="isbank-card-expiry"
                                           class="input-text wc-credit-card-form-card-expiry"
                                           inputmode="numeric" autocomplete="cc-exp" autocorrect="no"
                                           autocapitalize="no" spellcheck="no" type="tel" placeholder="MM/YYYY"
                                           name="isbank-card-expiry"/>
                                </p>
                                <p class="form-row form-row-last">
                                    <label for="isbank-card-cvc">
                                        <?php echo __( 'CVV/CVC', 'wc-isbank' ); ?>
                                        <span class="required">*</span>
                                    </label>

                                    <input id="isbank-card-cvc" class="input-text wc-credit-card-form-card-cvc"
                                           inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no"
                                           spellcheck="no" type="tel" maxlength="4" placeholder="CVC"
                                           name="isbank-card-cvc" style="width:75px"/>
				</p> 
				<?php if($instalment_enabled === true):?>
                                <p class="form-row form-row-wide">
                                    <label for="isbank-installment">
                                        <?php echo __( 'Installment', 'wc-isbank' ); ?>
                                        <span class="required">*</span>
                                    </label>
				   <select id="isbank-installment" class="input-text wc-credit-card-form-installment"
                                           placeholder="Taksit Istemiyorum"
					   name="taksit">
					<option value="">Tek Cekim</option>
					<option value="2">2 Taksit</option>
					<option value="3">3 Taksit</option>
					<option value="4">4 Taksit</option>
					<option value="5">5 Taksit</option>
					<option value="6">6 Taksit</option>
					<option value="7">7 Taksit</option>
					<option value="8">8 Taksit</option>
					<option value="9">9 Taksit</option>
					<option value="10">10 Taksit</option>
					<option value="11">11 Taksit</option>
					<option value="12">12 Taksit</option>
				   </select>
				</p>
				<?php else: ?>
                                <input type="hidden" name="taksit" value=""/>
				<?php endif; ?>
                                <div class="clear"></div>

                                <input type="hidden" name="clientid" value="<?php echo $args['client_id'] ?>"/>
                                <input type="hidden" name="amount" value="<?php echo $amount; ?>"/>
                                <input type="hidden" name="oid" value="<?php echo $args['order_id']; ?>"/>
                                <input type="hidden" name="okUrl" value="<?php echo $return_url; ?>"/>
                                <input type="hidden" name="failUrl" value="<?php echo $return_url; ?>"/>
                                <input type="hidden" name="rnd" value="<?php echo $rnd; ?>"/>
				<input type="hidden" name="hash" value="<?php echo $hash;?>"/>
                                <input type="hidden" name="storetype" value="3D"/>
                                <input type="hidden" name="lang" value="en"/>
                                <input type="hidden" name="islemtipi" value="Auth"/>
				<input type="hidden" name="currency" value="<?php echo $args['currency'];?>"/>

                            </fieldset>
                        </div>
                    </li>
                    <input type="submit" class="button alt" value="<?php echo __( 'Place Order', 'wc-isbank' ); ?>"/>
                </ul>
            </div>
        </form>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	public static function validate_fields() {
		if ( empty( $_POST['pan'] ) ||
		     empty( $_POST['card_expiry'] ) ||
		     empty( $_POST['card_cvc'] ) ) {

			echo json_encode( array(
				'result' => 'failure',
				'msg'    => __( 'All fields are required.', 'wc-isbank' )
			) );
			wp_die();
		}

		$expiry_date = $_POST['card_expiry'];
		$expiry_date = explode( ' / ', $expiry_date );

		if ( strlen( $expiry_date[1] ) < 4 ) {
			echo json_encode( array(
				'result' => 'failure',
				'msg'    => __( 'Expire date must be 4 digit.', 'wc-isbank' )
			) );
			wp_die();
		}

		if ( ( $expiry_date[0] < date( 'm' ) && $expiry_date[1] == date( 'Y' ) ) || $expiry_date[1] < date( 'Y' ) ) {

			echo json_encode( array(
				'result' => 'failure',
				'msg'    => __( 'Its not possible to make payment with an expired card.', 'wc-isbank' )
			) );
			wp_die();
		}

		$hashstr = $_POST['hashstr'];
		$instalment =$_POST['instalment'];
		$hashstr = str_replace('##TAKSIT##',$instalment,$hashstr);


		echo json_encode( array(
			'result' => 'success',
//			'hash'=>$hash
		) );
		wp_die();
	}
}
