<?php
/**
 * SaleXpresso
 *
 * @package SaleXpresso\Customer
 * @version 1.0.0
 * @since   SaleXpresso v1.0.0
 */

namespace SaleXpresso\Customer;

use SaleXpresso\SXP_List_Table;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomerReportDataStore;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

/**
 * Class SXP_Customer_List_Table
 *
 * @package SaleXpresso\Customer
 */
class SXP_Customer_List_Table extends SXP_List_Table {
	
	/**
	 * SXP_Customer_List_Table constructor.
	 *
	 * @param array|string $args List table params.
	 */
	public function __construct( $args = array() ) {
		
		parent::__construct(
			[
				'singular' => __( 'Customer', 'salexpresso' ),
				'plural'   => __( 'Customers', 'salexpresso' ),
				'ajax'     => false,
				'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
				'tab'      => '',
				'tfoot'    => false,
			]
		);
		
		$this->items = [];
		$this->prepare_items();
	}
	
	public function prepare_items() {
		// using wc report api data store.
		$reportStore = new CustomerReportDataStore();
		$per_page    = $this->get_items_per_page( 'customers_per_page' );
		$order_by    = 'date_last_order';
		$sort_order  = 'DESC';
		
		// set sorting.
		if ( isset( $_REQUEST['orderby'] ) ) {
			$order_by = sanitize_text_field( $_REQUEST['orderby'] );
		}
		
		if ( isset( $_REQUEST['order'] ) ) {
			$sort_order = 'asc' === strtolower( $_REQUEST['order'] ) ? 'ASC' : 'DESC';
		}
		
		$data = $reportStore->get_data( [
			'per_page'     => $per_page,
			'page'         => $this->get_pagenum(),
			'order'        => $sort_order,
			'orderby'      => $order_by, // date_registered
			'order_before' => TimeInterval::default_before(),
			'order_after'  => TimeInterval::default_after(),
			'fields'       => '*',
			/*'status_is'     => '',
			'status_is_not' => [ 'trash','pending','failed','cancelled' ],*/
		] );
		
		if ( ! is_wp_error( $data ) ) {
			$this->items = $data->data;
			$this->set_pagination_args( [
				'total_items' => $data->total,
				'total_pages' => $data->pages,
				'per_page'    => $per_page,
			] );
		}
	}
	
	public function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="customer_%1$s">%2$s</label>' .
			'<input type="checkbox" name="users[]" id="customer_%1$s" class="%3$s" value="%1$s" />',
			$item['id'],
			/* translators: %s: User Display Name. */
			sprintf( __( 'Select %s', 'salexpresso' ), $item['name'] ),
			'select-customer'
		);
	}
	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				$address = WC()->countries->get_formatted_address(
					[
						'state'   => $item['state'],
						'country' => $item['country'],
					],
					' '
				);
				$profile_tab = '#';
				if ( $item['user_id'] ) {
					$profile_tab = esc_url_raw( admin_url( 'admin.php?page=sxp-customer&tab=customer-profile&customer=' . $item['user_id'] ) );
				}
				
				return sprintf(
					'<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">%s</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<a href="%s">
									<p class="sxp-customer-desc-details-name">%s</p>
								</a>
								<p class="sxp-customer-desc-details-location">%s</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->',
					sprintf( '<a href="%s">%s</a>', $profile_tab, get_avatar( $item['email'], 40, '', $item['name'] ) ),
					$profile_tab,
					$item['name'],
					$address
				);
				break;
			case 'customers-type':
				$type = sxp_get_user_types( $item['user_id'] );
				if ( ! empty( $type ) && ! is_wp_error( $type ) ) {
					$type = $type[0];
					$color = sxp_get_term_background_color( $type );
					
					return sprintf( '<a href="#%s"  style="background: %s">%s</a>', esc_url( $type->term_id ), esc_attr( $color ), esc_html( $type->name ) );
				}
				break;
			case 'customer-tag':
				$output = '<ul class="sxp-tag-list">';
				if ( $item['user_id'] ) {
					$tags = sxp_get_user_tags( $item['user_id'] );
					if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
						$output .= sprintf( '<li><a href="#%s">%s</a></li>', esc_url( $tags[0]->term_id ), esc_html( $tags[0]->name ) );
						array_shift( $tags );
						if ( ! empty( $tags ) ) {
							$output .= sprintf( '<li><a href="#">%d</a></li>', count( $tags ) );
						}
					}
				} else {
					$output .= sprintf( '<li><a href="#">%s</a></li>', esc_html_x( 'Guest', 'Guest Customer', 'salexpresso' ) );
				}
				$output .= '</ul>';
				return $output;
				break;
			case 'orders_count':
				return $item['orders_count'];
				break;
			case 'total_spend':
				return wc_price( $item['total_spend'], [ 'ex_tax_label' => false ] );
				break;
			case 'date_last_order':
				if ( '0000-00-00 00:00:00' === $item['date_last_order'] ) {
					$t_time = '';
					$h_time = $t_time;
				} else {
					$time      = wc_string_to_timestamp( $item['date_last_order'] );
					$t_time    = date( __( 'Y/m/d g:i:s a' ), $time );
					$time_diff = time() - $time;
					
					if ( $time && $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
						/* translators: %s: Human-readable time difference. */
						$h_time = sprintf( __( '%s ago', 'salexpresso' ),
							human_time_diff( $time ) );
					} else {
						$h_time = date( __( 'Y/m/d' ), $time );
					}
				}
				
				return '<span title="' . $t_time . '">' . $h_time . '</span>';
				break;
			default:
				$output = '';
				break;
		}
		
		return apply_filters( "manage_{$this->screen->id}_{$column_name}_column_content", $output, $item );
	}
	
	public function get_columns() {
		return [
			'cb'              => '<input type="checkbox" />',
			'name'            => __( 'Customers', 'salexpresso' ),
			'customers-type'  => __( 'Customers Type', 'salexpresso' ),
			'customer-tag'    => __( 'Customers Tag', 'salexpresso' ),
			'orders_count'    => __( 'Orders', 'salexpresso' ),
			'total_spend'     => __( 'Revenue', 'salexpresso' ),
			'date_last_order' => __( 'Last Order', 'salexpresso' ),
		];
	}
	
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'name'            => [ 'name' ],
			'orders_count'    => [ 'orders_count' ],
			'total_spend'     => [ 'total_spend' ],
			'date_last_order' => [ 'date_last_order', true ],
		];
	}
	
	protected function get_table_classes() {
		return [
			'sxp-table',
			'widefat',
			$this->_args['plural'],
			$this->_args['tab'],
			$this->screen_id,
		];
	}
	
	public function old() {
		?>
		<div class="sxp-customer-list-wrapper">
			<div class="sxp-customer-top-wrapper">
				<div class="sxp-customer-search">
					<label for="sxp-customer-search"
					       class="screen-reader-text"><?php __( 'Search Customer',
							'salexpresso' ); ?></label>
					<input type="text" id="sxp-customer-search" placeholder="Search Customers">
				</div><!-- end .sxp-customer-search -->
				<div class="sxp-customer-btn-wrapper">
					<a href="#" class="sxp-customer-type-btn sxp-btn sxp-btn-default"><i
								data-feather="plus"></i> Customer Type Rules</a>
					<a href="#" class="sxp-customer-add-btn sxp-btn sxp-btn-primary"><i
								data-feather="plus"></i> Add New Customer</a>
				</div>
			</div><!-- end .sxp-customer-top-wrapper -->
			<table class="wp-list-table widefat sxp-table">
				<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>
					<th scope="col" id="sxp-customer-customers"
					    class="manage-column column-categories"><a href="#">Customers</a></th>
					<th scope="col" id="sxp-customer"
					    class="manage-column column-title column-primary sortable desc"><a href="#">Customer
							Type</a></th>
					<th scope="col" id="sxp-customer-tag" class="manage-column column-author"><a
								href="#">Customer Tag</a></th>
					<th scope="col" id="sxp-customer-order" class="manage-column column-categories">
						<a href="#">Orders</a></th>
					<th scope="col" id="sxp-customer-revenue"
					    class="manage-column column-categories"><a href="#">Revenue</a></th>
					<th scope="col" id="sxp-customer-last-order"
					    class="manage-column column-categories"><a href="#">Last Order</a></th>
				</tr>
				</thead>
				<tbody id="the-list">
				<tr id="sxp-customer-list-1" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer1.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Wendy Bell</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background: #FFD0D0">VIP</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">Holiday Campaign</a></li>
								<li><a href="">+2</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">799</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$6910.60
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				<tr id="sxp-customer-list-2" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer2.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Jane Nguyen</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background: #E3FFDA">Gold</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">New Year</a></li>
								<li><a href="">+2</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">727</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$3535.92
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				<tr id="sxp-customer-list-3" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer3.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Jane Nguyen</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background: #FFCFB5">Gold</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">Sports Lover</a></li>
								<li><a href="">+4</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">727</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$3535.92
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				<tr id="sxp-customer-list-4" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer4.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Jane Nguyen</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background:  #FFCFB5">Gold</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">Birthday</a></li>
								<li><a href="">+2</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">727</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$3535.92
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				<tr id="sxp-customer-list-5" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer5.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Jane Nguyen</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background: #DAE4FF">Gold</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">Doctor</a></li>
								<li><a href="">+5</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">727</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$3535.92
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				<tr id="sxp-customer-list-6" class="sxp-customer-list">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1"></label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
					</th>
					<td class="title column-title has-row-actions column-primary page-title sxp-customers-column"
					    data-colname="sxp-customer-customers">
						<div class="sxp-customer-desc">
							<div class="sxp-customer-desc-thumbnail">
								<img src="<?php echo esc_url( sxp_get_plugin_uri( 'assets/images/customers/customer6.png' ) ); ?>"
								     alt="Customer Thumbnail">
							</div><!-- end .sxp-customer-desc-thumbnail -->
							<div class="sxp-customer-desc-details">
								<p class="sxp-customer-desc-details-name">Jane Nguyen</p>
								<p class="sxp-customer-desc-details-location">Vermont</p>
							</div><!-- end .sxp-customer-desc-detaisl -->
						</div><!-- end .sxp-customer-desc -->
						<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="sxp-customer-name" data-colname="Customer Type"><a href="#"
					                                                              style="background: #CFFFF4">Gold</a>
					</td>
					<td class="sxp-customer-tag-column" data-colname="Customer Tag">
						<div class="sxp-customer-tag-container">
							<ul class="sxp-tag-list">
								<li><a href="#">New Year</a></li>
								<li><a href="">+2</a></li>
							</ul>
						</div><!-- end .sxp-customer-compaign-container -->
					</td>
					<td class="sxp-customer-assigned-column" data-colname="Customer Order">727</td>
					<td class="sxp-customer-revenue-column" data-colname="Customer Revenue">
						$3535.92
					</td>
					<td class="sxp-customer-last-order-column" data-colname="Last Order">23 days
						ago
					</td>
				</tr><!-- end .sxp-customer-list -->
				
				</tbody>
			</table><!-- end .sxp-customer-table -->
			<div class="sxp-pagination-wrapper">
				<ul class="sxp-pagination">
					<li><a href="#"><img alt="arrow-left.svg"
					                     src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTE0IDhMMyA4IiBzdHJva2U9IiM3RDdEQjMiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+CjxwYXRoIGQ9Ik02IDEyTDIgOEw2IDQiIHN0cm9rZT0iIzdEN0RCMyIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg=="/></a>
					</li>
					<li><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li>...</li>
					<li><a href="#">5</a></li>
					<li><a href="#">6</a></li>
					<li><a href="#">7</a></li>
					<li><a href="#">8</a></li>
					<li><a href="#"><img alt="arrow-right.svg"
					                     src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIgOEwxMyA4IiBzdHJva2U9IiM3RDdEQjMiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+CjxwYXRoIGQ9Ik0xMCA0TDE0IDhMMTAgMTIiIHN0cm9rZT0iIzdEN0RCMyIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg=="/></a>
					</li>
				</ul>
			</div><!-- end .sxp-paginaation-wrapper -->
			<div class="sxp-bottom-wrapper">
				<div class="sxp-selected-container">
					<div class="sxp-row-select">
						<a href="#" class="sxp-remove-select"><img
									src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzYiIGhlaWdodD0iMzYiIHZpZXdCb3g9IjAgMCAzNiAzNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIyIDE0TDE0IDIyIiBzdHJva2U9IiM3RDdEQjMiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+CjxwYXRoIGQ9Ik0xNCAxNEwyMiAyMiIgc3Ryb2tlPSIjN0Q3REIzIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K"
									alt="Remove selection"></a>
						<a href="#" class="sxp-selected">2 Rows Selected</a>
					</div>
					<div class="sxp-remove-customer">
						<a href="#">Delete</a>
					</div>
				</div><!-- end .sxp-selected-container -->
			</div><!-- end .sxp-bottom-wrapper -->
		</div><!-- end .sxp-customer-list-wrapper -->
		<?php
	}
}

// End of file class-sxp-customer-list-table.php.
