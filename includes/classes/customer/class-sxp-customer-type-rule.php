<?php
/**
 * SaleXpresso
 *
 * @package SaleXpresso\Customer
 * @version 1.0.0
 * @since   SaleXpresso v1.0.0
 */

namespace SaleXpresso\Customer;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

/**
 * Class SXP_Customer_Type_Rule
 *
 * @package SaleXpresso\Customer
 */

class SXP_Customer_Type_Rule {
	/**
	 * SXP_Customer_Type_Rule constructor.
	 */
	public function __construct() {
		?>
			<div class="sxp-customer-type-rule-wrapper">
				<div class="sxp-customer-type-name">
					<h4 class="sxp-customer-type-section-header">Customer Type Name</h4>
					<div class="sxp-customer-type-section-title-container">
						Distributor
					</div>
				</div><!-- end .sxp-customer-type-name -->
				<div class="sxp-customer-type-rule-container">
					<h4 class="sxp-customer-type-section-header">Create Rule</h4>

					<div class="sxp-customer-type-rules-wrapper">
						<div class="sxp-customer-type-rules">
							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount" selected>Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
								<select class="sxp-custom-select">
									<option value="equal">=</option>
									<option value="not-equal">!=</option>
									<option value="greater-than">></option>
									<option value="less-than"><</option>
									<option value="greater-than-or-equal">>=</option>
									<option value="less-than-or-equal"><=</option>
								</select>
								<input type="number" value="5000" class="sxp-custom-number">
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity" selected>Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
								<select class="sxp-custom-select">
									<option value="min-max">min-max</option>
								</select>
								<div class="sxp-form-group">
									<label for="min-quantity">min</label>
									<input type="number" name="min-quantity" value="50">
								</div><!-- sxp-form-group -->
								<div class="sxp-form-group">
									<label for="max-quantity">max</label>
									<input type="number" name="max-quantity" value="2000">
								</div><!-- sxp-form-group -->
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase" selected>Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
								<select class="sxp-custom-select">
									<option value="equal">=</option>
									<option value="not-equal">!=</option>
									<option value="greater-than">></option>
									<option value="less-than"><</option>
									<option value="greater-than-or-equal">>=</option>
									<option value="less-than-or-equal"><=</option>
								</select>
								<input type="number" value="600" class="sxp-custom-number">
							</div><!-- end .sxp-customer-type-single-rule -->
							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value" selected>Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code" selected>Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
								<select class="sxp-custom-select">
									<Option class="">JIGCL586</Option>
									<Option class="">"HEHGK98"</Option>
									<Option class="">9798KJLJ</Option>
									<Option class="">JIGCL586</Option>
								</select>
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought" selected>Product Bought</option>
									<option value="tagged-width">Tagged width</option>
								</select>
							</div><!-- end .sxp-customer-type-single-rule -->

							<div class="sxp-customer-type-single-rule">
								<select class="sxp-custom-select">
									<option value="purchase-ammount">Purchase Amount</option>
									<option value="purchase-quantity">Purchase Quantity</option>
									<option value="recurring-purchase">Recurring Purchase</option>
									<option value="customer-life-time-value">Customer's Life time value</option>
									<option value="coupon-code">Coupon code</option>
									<option value="product-bought">Product Bought</option>
									<option value="tagged-width" selected>Tagged width</option>
								</select>
								<select class="sxp-custom-select">
									<option value="equal">=</option>
									<option value="not-equal">!=</option>
									<option value="greater-than">></option>
									<option value="less-than"><</option>
									<option value="greater-than-or-equal">>=</option>
									<option value="less-than-or-equal"><=</option>
								</select>
								<select class="sxp-custom-select">
									<option value="no-tag">No tags</option>
									<option value="no-tag">tag 1</option>
									<option value="no-tag">tag 2</option>
								</select>
							</div><!-- end .sxp-customer-type-single-rule -->
						</div><!-- end .sxp-customer-type-rules -->
					</div><!-- end .sxp-customer-type-rules-wrapper -->
					<div class="sxp-customer-type-rule-add-btn">
						<a href="#">Add Condition</a>
					</div>
				</div><!-- end .sxp-customer-type-rule-container -->
				<div class="sxp-customer-type-rule-save-wrapper">
					<a href="#">Cancel</a>
					<a href="#">Save New Customer Type</a>
				</div>
			</div><!-- end .sxp-customer-type-rule-wrapper -->
		<?php
	}
}