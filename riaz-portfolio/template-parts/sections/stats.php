<?php
/**
 * Section: Statistics with count-up numbers.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'stats' ) );
$riaz_items = array_values(
	array_filter(
		riaz_mod_list( 'stats_items' ),
		static function ( $row ) {
			return ! empty( $row['value'] ) || ! empty( $row['label'] );
		}
	)
);

if ( ! $riaz_items ) {
	riaz_admin_placeholder(
		__( 'Statistics', 'riaz-portfolio' ),
		__( 'Add verified numbers in Homepage Sections → Statistics to show this section.', 'riaz-portfolio' ),
		admin_url( 'customize.php?autofocus[section]=riaz_stats' ),
		__( 'Add statistics', 'riaz-portfolio' )
	);
	return;
}
?>
<section class="stats-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'stats' ); ?>>
	<div class="section-shell">
		<div data-reveal><?php riaz_section_heading( riaz_heading_args( 'stats' ) ); ?></div>
		<ul class="stats-grid" data-stagger style="--stats-cols:<?php echo esc_attr( min( 4, count( $riaz_items ) ) ); ?>">
			<?php foreach ( $riaz_items as $riaz_item ) : ?>
				<?php
				$riaz_value  = isset( $riaz_item['value'] ) ? $riaz_item['value'] : '';
				$riaz_number = is_numeric( str_replace( ',', '', $riaz_value ) ) ? str_replace( ',', '', $riaz_value ) : '';
				?>
				<?php
				$riaz_prefix = isset( $riaz_item['prefix'] ) ? $riaz_item['prefix'] : '';
				$riaz_suffix = isset( $riaz_item['suffix'] ) ? $riaz_item['suffix'] : '';
				?>
				<li class="stat-card lift" data-reveal-item>
					<span class="stat-value text-accent" aria-hidden="true"><?php echo esc_html( $riaz_prefix ); ?><span<?php echo '' !== $riaz_number ? ' data-count="' . esc_attr( $riaz_number ) . '"' : ''; ?>><?php echo esc_html( $riaz_value ); ?></span><?php echo esc_html( $riaz_suffix ); ?></span>
					<span class="screen-reader-text"><?php echo esc_html( $riaz_prefix . $riaz_value . $riaz_suffix ); ?></span>
					<?php if ( ! empty( $riaz_item['label'] ) ) : ?>
						<span class="stat-label"><?php echo esc_html( $riaz_item['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
