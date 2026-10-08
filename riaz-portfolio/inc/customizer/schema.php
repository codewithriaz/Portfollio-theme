<?php
/**
 * Single source of truth for every theme option.
 *
 * The Customizer controls, sanitization, selective-refresh partials and the
 * defaults returned by riaz_mod() are all generated from this schema, so a new
 * option only has to be declared once.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Full option schema: panels, sections and fields.
 *
 * @return array
 */
function riaz_customizer_schema() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$icon_rows = array(
		'icon' => array(
			'type'  => 'icon',
			'label' => __( 'Icon', 'riaz-portfolio' ),
		),
		'text' => array(
			'type'  => 'text',
			'label' => __( 'Label', 'riaz-portfolio' ),
		),
	);

	$heading_fields = static function ( $prefix, $defaults, $extra = array() ) {
		return array_merge(
			array(
				$prefix . '_eyebrow'   => array(
					'type'    => 'text',
					'label'   => __( 'Eyebrow label', 'riaz-portfolio' ),
					'default' => $defaults[0],
				),
				$prefix . '_title'     => array(
					'type'    => 'text',
					'label'   => __( 'Title', 'riaz-portfolio' ),
					'default' => $defaults[1],
				),
				$prefix . '_highlight' => array(
					'type'        => 'text',
					'label'       => __( 'Title highlight', 'riaz-portfolio' ),
					'description' => __( 'Shown after the title in the accent gradient.', 'riaz-portfolio' ),
					'default'     => $defaults[2],
				),
				$prefix . '_text'      => array(
					'type'    => 'textarea',
					'label'   => __( 'Description', 'riaz-portfolio' ),
					'default' => $defaults[3],
				),
			),
			$extra
		);
	};

	$manage = static function ( $post_type, $label ) {
		if ( ! post_type_exists( $post_type ) ) {
			return array(
				'type'        => 'info',
				'label'       => $label,
				'description' => __( 'Activate the bundled Riaz Portfolio Core plugin (Appearance → Riaz Portfolio) to add and manage these items.', 'riaz-portfolio' ),
			);
		}
		return array(
			'type'        => 'info',
			'label'       => $label,
			'description' => sprintf(
				/* translators: %s: admin URL. */
				__( 'Items are managed as posts. <a href="%s" target="_blank">Open the list in a new tab</a>.', 'riaz-portfolio' ),
				esc_url( admin_url( 'edit.php?post_type=' . $post_type ) )
			),
		);
	};

	$schema = array(
		'panels'   => array(
			'riaz_home'    => array(
				'title'       => __( 'Homepage Sections', 'riaz-portfolio' ),
				'description' => __( 'Edit, reorder and hide every section of the one-page homepage. Changes preview live.', 'riaz-portfolio' ),
				'priority'    => 22,
			),
			'riaz_options' => array(
				'title'       => __( 'Theme Options', 'riaz-portfolio' ),
				'description' => __( 'Brand colors, typography, header, footer, contact details, social links, layout and performance.', 'riaz-portfolio' ),
				'priority'    => 23,
			),
		),
		'sections' => array(

			/* ---------- Homepage ---------- */
			'riaz_section_order'   => array(
				'panel'  => 'riaz_home',
				'title'  => __( 'Section Order & Visibility', 'riaz-portfolio' ),
				'fields' => array(
					'home_sections'     => array(
						'type'        => 'sortable',
						'label'       => __( 'Homepage sections', 'riaz-portfolio' ),
						'description' => __( 'Drag to reorder. Untick to hide. Sections without content (for example Portfolio with no projects yet) are skipped automatically.', 'riaz-portfolio' ),
						'default'     => riaz_default_section_order(),
						'transport'   => 'refresh',
					),
					'home_use_sections' => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show these sections when the homepage displays "Your latest posts"', 'riaz-portfolio' ),
						'description' => __( 'If you choose a static front page in Settings → Reading and give it content (blocks, Elementor or Divi), that content is shown instead.', 'riaz-portfolio' ),
						'default'     => true,
						'transport'   => 'refresh',
					),
				),
			),
			'riaz_hero'            => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Hero', 'riaz-portfolio' ),
				'partial' => 'hero',
				'fields'  => array(
					'hero_eyebrow'     => array(
						'type'    => 'text',
						'label'   => __( 'Eyebrow label', 'riaz-portfolio' ),
						'default' => __( 'Your partner in digital growth', 'riaz-portfolio' ),
					),
					'hero_title'       => array(
						'type'        => 'text',
						'label'       => __( 'Headline', 'riaz-portfolio' ),
						'description' => __( 'This is the page H1.', 'riaz-portfolio' ),
						'default'     => __( 'Digital growth strategy you can', 'riaz-portfolio' ),
					),
					'hero_highlight'   => array(
						'type'    => 'text',
						'label'   => __( 'Headline highlight', 'riaz-portfolio' ),
						'default' => __( 'count on', 'riaz-portfolio' ),
					),
					'hero_text'        => array(
						'type'    => 'textarea',
						'label'   => __( 'Description', 'riaz-portfolio' ),
						'default' => __( 'Helping businesses make informed digital decisions through clear strategy, search visibility, and useful content.', 'riaz-portfolio' ),
					),
					'hero_btn1_label'  => array(
						'type'    => 'text',
						'label'   => __( 'Primary button label', 'riaz-portfolio' ),
						'default' => __( 'Explore the approach', 'riaz-portfolio' ),
					),
					'hero_btn1_url'    => array(
						'type'    => 'url',
						'label'   => __( 'Primary button link', 'riaz-portfolio' ),
						'default' => '#approach',
					),
					'hero_btn2_label'  => array(
						'type'    => 'text',
						'label'   => __( 'Secondary button label', 'riaz-portfolio' ),
						'default' => __( 'View services', 'riaz-portfolio' ),
					),
					'hero_btn2_url'    => array(
						'type'    => 'url',
						'label'   => __( 'Secondary button link', 'riaz-portfolio' ),
						'default' => '#services',
					),
					'hero_focus'       => array(
						'type'      => 'repeater',
						'label'     => __( 'Focus points', 'riaz-portfolio' ),
						'fields'    => $icon_rows,
						'max'       => 5,
						'add_label' => __( 'Add focus point', 'riaz-portfolio' ),
						'default'   => array(
							array(
								'icon' => 'search',
								'text' => __( 'Search visibility', 'riaz-portfolio' ),
							),
							array(
								'icon' => 'file-text',
								'text' => __( 'Useful content', 'riaz-portfolio' ),
							),
							array(
								'icon' => 'compass',
								'text' => __( 'Clear strategy', 'riaz-portfolio' ),
							),
						),
					),
					'hero_image'       => array(
						'type'        => 'image',
						'label'       => __( 'Portrait', 'riaz-portfolio' ),
						'description' => __( 'A cut-out portrait (transparent PNG/WebP) looks best. Recommended ~800×780px.', 'riaz-portfolio' ),
						'default'     => 0,
					),
					'hero_note_show'   => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show floating note', 'riaz-portfolio' ),
						'default' => true,
					),
					'hero_note_title'  => array(
						'type'    => 'text',
						'label'   => __( 'Floating note title', 'riaz-portfolio' ),
						'default' => __( 'Growth, with purpose.', 'riaz-portfolio' ),
					),
					'hero_note_text'   => array(
						'type'    => 'text',
						'label'   => __( 'Floating note text', 'riaz-portfolio' ),
						'default' => __( 'Built around your goals', 'riaz-portfolio' ),
					),
					'hero_badge_show'  => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show name badge', 'riaz-portfolio' ),
						'default' => true,
					),
					'hero_badge_title' => array(
						'type'    => 'text',
						'label'   => __( 'Name badge title', 'riaz-portfolio' ),
						'default' => 'Riaz Ahmed',
					),
					'hero_badge_text'  => array(
						'type'    => 'text',
						'label'   => __( 'Name badge subtitle', 'riaz-portfolio' ),
						'default' => __( 'Digital Growth Partner', 'riaz-portfolio' ),
					),
				),
			),
			'riaz_marquee'         => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Who I Help (Marquee)', 'riaz-portfolio' ),
				'partial' => 'marquee',
				'fields'  => array(
					'marquee_label' => array(
						'type'    => 'text',
						'label'   => __( 'Label', 'riaz-portfolio' ),
						'default' => __( 'Who I help', 'riaz-portfolio' ),
					),
					'marquee_items' => array(
						'type'      => 'repeater',
						'label'     => __( 'Scrolling items', 'riaz-portfolio' ),
						'fields'    => array(
							'text' => array(
								'type'  => 'text',
								'label' => __( 'Label', 'riaz-portfolio' ),
							),
						),
						'max'       => 16,
						'add_label' => __( 'Add item', 'riaz-portfolio' ),
						'default'   => array(
							array( 'text' => __( 'Small businesses', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Startups', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Online stores', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Service providers', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Personal brands', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Content creators', 'riaz-portfolio' ) ),
						),
					),
					'marquee_speed' => array(
						'type'        => 'range',
						'label'       => __( 'Loop duration (seconds)', 'riaz-portfolio' ),
						'description' => __( 'Higher is slower.', 'riaz-portfolio' ),
						'default'     => 34,
						'min'         => 10,
						'max'         => 90,
						'step'        => 1,
					),
				),
			),
			'riaz_about'           => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'About', 'riaz-portfolio' ),
				'partial' => 'about',
				'fields'  => array(
					'about_eyebrow'    => array(
						'type'    => 'text',
						'label'   => __( 'Eyebrow label', 'riaz-portfolio' ),
						'default' => __( 'About me', 'riaz-portfolio' ),
					),
					'about_title'      => array(
						'type'    => 'text',
						'label'   => __( 'Title', 'riaz-portfolio' ),
						'default' => __( 'Good growth begins with', 'riaz-portfolio' ),
					),
					'about_highlight'  => array(
						'type'    => 'text',
						'label'   => __( 'Title highlight', 'riaz-portfolio' ),
						'default' => __( 'the right questions.', 'riaz-portfolio' ),
					),
					'about_lead'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Lead paragraph', 'riaz-portfolio' ),
						'default' => __( "I'm Riaz Ahmed. I believe the strongest digital presence is built with intention, not noise. My focus is helping businesses make sense of their opportunities, show up more clearly online, and take the next step with confidence.", 'riaz-portfolio' ),
					),
					'about_text'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Supporting paragraph', 'riaz-portfolio' ),
						'default' => __( 'From search visibility to content direction, the work starts by understanding your audience and ends with a plan you can actually move forward with.', 'riaz-portfolio' ),
					),
					'about_points'     => array(
						'type'      => 'repeater',
						'label'     => __( 'Checklist', 'riaz-portfolio' ),
						'fields'    => array(
							'text' => array(
								'type'  => 'text',
								'label' => __( 'Point', 'riaz-portfolio' ),
							),
						),
						'max'       => 8,
						'add_label' => __( 'Add point', 'riaz-portfolio' ),
						'default'   => array(
							array( 'text' => __( 'Goal-led priorities', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Search-first thinking', 'riaz-portfolio' ) ),
							array( 'text' => __( 'People-first content', 'riaz-portfolio' ) ),
							array( 'text' => __( 'Measurable next steps', 'riaz-portfolio' ) ),
						),
					),
					'about_image'      => array(
						'type'        => 'image',
						'label'       => __( 'Photo', 'riaz-portfolio' ),
						'description' => __( 'Portrait orientation (4:5) works best, e.g. 880×1100px.', 'riaz-portfolio' ),
						'default'     => 0,
					),
					'about_name'       => array(
						'type'    => 'text',
						'label'   => __( 'Photo name tag', 'riaz-portfolio' ),
						'default' => 'Riaz Ahmed',
					),
					'about_role'       => array(
						'type'    => 'text',
						'label'   => __( 'Photo role tag', 'riaz-portfolio' ),
						'default' => __( 'Digital Growth Partner', 'riaz-portfolio' ),
					),
					'about_badge_show' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show rotating badge', 'riaz-portfolio' ),
						'default' => true,
					),
					'about_badge_text' => array(
						'type'        => 'text',
						'label'       => __( 'Rotating badge text', 'riaz-portfolio' ),
						'description' => __( 'Around 40 characters fits the circle best.', 'riaz-portfolio' ),
						'default'     => __( 'DIGITAL GROWTH • SEO • CONTENT • STRATEGY •', 'riaz-portfolio' ),
					),
					'about_btn_label'  => array(
						'type'    => 'text',
						'label'   => __( 'Button label', 'riaz-portfolio' ),
						'default' => __( 'How I work', 'riaz-portfolio' ),
					),
					'about_btn_url'    => array(
						'type'    => 'url',
						'label'   => __( 'Button link', 'riaz-portfolio' ),
						'default' => '#approach',
					),
					'about_link_label' => array(
						'type'    => 'text',
						'label'   => __( 'Text link label', 'riaz-portfolio' ),
						'default' => __( 'See services', 'riaz-portfolio' ),
					),
					'about_link_url'   => array(
						'type'    => 'url',
						'label'   => __( 'Text link', 'riaz-portfolio' ),
						'default' => '#services',
					),
				),
			),
			'riaz_stats'           => array(
				'panel'       => 'riaz_home',
				'title'       => __( 'Statistics', 'riaz-portfolio' ),
				'description' => __( 'Only add numbers you can verify. The section is hidden until at least one statistic exists.', 'riaz-portfolio' ),
				'partial'     => 'stats',
				'fields'      => $heading_fields(
					'stats',
					array( __( 'By the numbers', 'riaz-portfolio' ), __( 'Progress you can', 'riaz-portfolio' ), __( 'measure', 'riaz-portfolio' ), '' ),
					array(
						'stats_items' => array(
							'type'      => 'repeater',
							'label'     => __( 'Statistics', 'riaz-portfolio' ),
							'fields'    => array(
								'value'  => array(
									'type'  => 'text',
									'label' => __( 'Number (e.g. 120 or 4.9)', 'riaz-portfolio' ),
								),
								'prefix' => array(
									'type'  => 'text',
									'label' => __( 'Prefix (e.g. $)', 'riaz-portfolio' ),
								),
								'suffix' => array(
									'type'  => 'text',
									'label' => __( 'Suffix (e.g. + or %)', 'riaz-portfolio' ),
								),
								'label'  => array(
									'type'  => 'text',
									'label' => __( 'Label', 'riaz-portfolio' ),
								),
							),
							'max'       => 8,
							'add_label' => __( 'Add statistic', 'riaz-portfolio' ),
							'default'   => array(),
						),
					)
				),
			),
			'riaz_services'        => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Services', 'riaz-portfolio' ),
				'partial' => 'services',
				'fields'  => $heading_fields(
					'services',
					array(
						__( 'Services', 'riaz-portfolio' ),
						__( 'Services', 'riaz-portfolio' ),
						__( 'offered', 'riaz-portfolio' ),
						__( 'Practical digital thinking tailored to meet your unique needs and goals.', 'riaz-portfolio' ),
					),
					array(
						'services_manage'     => $manage( 'riaz_service', __( 'Service cards', 'riaz-portfolio' ) ),
						'services_link_label' => array(
							'type'    => 'text',
							'label'   => __( 'Header link label', 'riaz-portfolio' ),
							'default' => __( 'Discuss your goals', 'riaz-portfolio' ),
						),
						'services_link_url'   => array(
							'type'    => 'url',
							'label'   => __( 'Header link', 'riaz-portfolio' ),
							'default' => '#contact',
						),
						'services_card_label' => array(
							'type'    => 'text',
							'label'   => __( 'Card link label', 'riaz-portfolio' ),
							'default' => __( 'Learn more', 'riaz-portfolio' ),
						),
						'services_card_url'   => array(
							'type'        => 'url',
							'label'       => __( 'Default card link', 'riaz-portfolio' ),
							'description' => __( 'Used when a service has no page content and no custom link.', 'riaz-portfolio' ),
							'default'     => '#contact',
						),
						'services_count'      => array(
							'type'    => 'number',
							'label'   => __( 'Number of services', 'riaz-portfolio' ),
							'default' => 6,
							'min'     => 1,
							'max'     => 24,
						),
					)
				),
			),
			'riaz_approach'        => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Approach / Process', 'riaz-portfolio' ),
				'partial' => 'approach',
				'fields'  => $heading_fields(
					'approach',
					array(
						__( 'A considered approach', 'riaz-portfolio' ),
						__( 'Less guesswork.', 'riaz-portfolio' ),
						__( 'More direction.', 'riaz-portfolio' ),
						__( 'A straightforward process keeps the work focused, collaborative, and connected to what success means for you.', 'riaz-portfolio' ),
					),
					array(
						'approach_steps' => array(
							'type'      => 'repeater',
							'label'     => __( 'Steps', 'riaz-portfolio' ),
							'fields'    => array(
								'icon'  => array(
									'type'  => 'icon',
									'label' => __( 'Icon', 'riaz-portfolio' ),
								),
								'title' => array(
									'type'  => 'text',
									'label' => __( 'Title', 'riaz-portfolio' ),
								),
								'text'  => array(
									'type'  => 'textarea',
									'label' => __( 'Description', 'riaz-portfolio' ),
								),
							),
							'max'       => 6,
							'add_label' => __( 'Add step', 'riaz-portfolio' ),
							'default'   => array(
								array(
									'icon'  => 'search',
									'title' => __( 'Understand', 'riaz-portfolio' ),
									'text'  => __( 'Get clear on your audience, goals, and what is holding growth back.', 'riaz-portfolio' ),
								),
								array(
									'icon'  => 'target',
									'title' => __( 'Prioritize', 'riaz-portfolio' ),
									'text'  => __( 'Find the highest-impact opportunities and shape a focused roadmap.', 'riaz-portfolio' ),
								),
								array(
									'icon'  => 'sparkles',
									'title' => __( 'Improve', 'riaz-portfolio' ),
									'text'  => __( 'Put thoughtful, measurable improvements into action, step by step.', 'riaz-portfolio' ),
								),
								array(
									'icon'  => 'lightbulb',
									'title' => __( 'Learn', 'riaz-portfolio' ),
									'text'  => __( 'Review what is working, share the insight, and keep building momentum.', 'riaz-portfolio' ),
								),
							),
						),
					)
				),
			),
			'riaz_portfolio'       => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Portfolio', 'riaz-portfolio' ),
				'partial' => 'portfolio',
				'fields'  => $heading_fields(
					'portfolio',
					array(
						__( 'Portfolio', 'riaz-portfolio' ),
						__( 'Selected', 'riaz-portfolio' ),
						__( 'work', 'riaz-portfolio' ),
						__( 'A look at recent projects and the thinking behind them.', 'riaz-portfolio' ),
					),
					array(
						'portfolio_manage'     => $manage( 'riaz_portfolio', __( 'Projects', 'riaz-portfolio' ) ),
						'portfolio_count'      => array(
							'type'    => 'number',
							'label'   => __( 'Number of projects', 'riaz-portfolio' ),
							'default' => 6,
							'min'     => 1,
							'max'     => 24,
						),
						'portfolio_filters'    => array(
							'type'    => 'checkbox',
							'label'   => __( 'Show category filter', 'riaz-portfolio' ),
							'default' => true,
						),
						'portfolio_link_label' => array(
							'type'    => 'text',
							'label'   => __( '"View all" label', 'riaz-portfolio' ),
							'default' => __( 'View all projects', 'riaz-portfolio' ),
						),
					)
				),
			),
			'riaz_case_studies'    => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Case Studies', 'riaz-portfolio' ),
				'partial' => 'case_studies',
				'fields'  => $heading_fields(
					'cases',
					array(
						__( 'Case studies', 'riaz-portfolio' ),
						__( 'Stories of', 'riaz-portfolio' ),
						__( 'steady growth', 'riaz-portfolio' ),
						__( 'How clear strategy turned into practical, measurable progress.', 'riaz-portfolio' ),
					),
					array(
						'cases_manage'     => $manage( 'riaz_case_study', __( 'Case studies', 'riaz-portfolio' ) ),
						'cases_count'      => array(
							'type'    => 'number',
							'label'   => __( 'Number of case studies', 'riaz-portfolio' ),
							'default' => 3,
							'min'     => 1,
							'max'     => 12,
						),
						'cases_link_label' => array(
							'type'    => 'text',
							'label'   => __( '"View all" label', 'riaz-portfolio' ),
							'default' => __( 'All case studies', 'riaz-portfolio' ),
						),
					)
				),
			),
			'riaz_testimonials'    => array(
				'panel'       => 'riaz_home',
				'title'       => __( 'Testimonials', 'riaz-portfolio' ),
				'description' => __( 'Only publish real testimonials you have permission to use. Video testimonials use YouTube and load only when played.', 'riaz-portfolio' ),
				'partial'     => 'testimonials',
				'fields'      => $heading_fields(
					'testimonials',
					array(
						__( 'Testimonials', 'riaz-portfolio' ),
						__( 'Hear what clients', 'riaz-portfolio' ),
						__( 'are saying', 'riaz-portfolio' ),
						__( 'Real conversations about working together.', 'riaz-portfolio' ),
					),
					array(
						'testimonials_manage' => $manage( 'riaz_testimonial', __( 'Testimonials', 'riaz-portfolio' ) ),
						'testimonials_count'  => array(
							'type'    => 'number',
							'label'   => __( 'Maximum testimonials', 'riaz-portfolio' ),
							'default' => 12,
							'min'     => 1,
							'max'     => 40,
						),
					)
				),
			),
			'riaz_team'            => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Team', 'riaz-portfolio' ),
				'partial' => 'team',
				'fields'  => $heading_fields(
					'team',
					array(
						__( 'Team', 'riaz-portfolio' ),
						__( 'The people', 'riaz-portfolio' ),
						__( 'behind the work', 'riaz-portfolio' ),
						__( 'A small, focused team that cares about doing things properly.', 'riaz-portfolio' ),
					),
					array(
						'team_manage' => $manage( 'riaz_team', __( 'Team members', 'riaz-portfolio' ) ),
						'team_count'  => array(
							'type'    => 'number',
							'label'   => __( 'Number of members', 'riaz-portfolio' ),
							'default' => 8,
							'min'     => 1,
							'max'     => 24,
						),
					)
				),
			),
			'riaz_pricing'         => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'Pricing', 'riaz-portfolio' ),
				'partial' => 'pricing',
				'fields'  => $heading_fields(
					'pricing',
					array(
						__( 'Pricing', 'riaz-portfolio' ),
						__( 'Simple, transparent', 'riaz-portfolio' ),
						__( 'packages', 'riaz-portfolio' ),
						__( 'Choose a starting point. Every engagement is shaped around your goals.', 'riaz-portfolio' ),
					),
					array(
						'pricing_manage' => $manage( 'riaz_pricing', __( 'Pricing plans', 'riaz-portfolio' ) ),
						'pricing_note'   => array(
							'type'    => 'text',
							'label'   => __( 'Note below the plans', 'riaz-portfolio' ),
							'default' => '',
						),
					)
				),
			),
			'riaz_faq'             => array(
				'panel'   => 'riaz_home',
				'title'   => __( 'FAQ', 'riaz-portfolio' ),
				'partial' => 'faq',
				'fields'  => $heading_fields(
					'faq',
					array(
						__( 'FAQ', 'riaz-portfolio' ),
						__( 'Frequently', 'riaz-portfolio' ),
						__( 'asked questions', 'riaz-portfolio' ),
						__( 'Quick answers to common questions about working together.', 'riaz-portfolio' ),
					),
					array(
						'faq_manage'     => $manage( 'riaz_faq', __( 'Questions', 'riaz-portfolio' ) ),
						'faq_help_title' => array(
							'type'    => 'text',
							'label'   => __( 'Help box title', 'riaz-portfolio' ),
							'default' => __( 'Still have a question?', 'riaz-portfolio' ),
						),
						'faq_help_label' => array(
							'type'    => 'text',
							'label'   => __( 'Help box link label', 'riaz-portfolio' ),
							'default' => __( "Let's talk", 'riaz-portfolio' ),
						),
						'faq_help_url'   => array(
							'type'    => 'url',
							'label'   => __( 'Help box link', 'riaz-portfolio' ),
							'default' => '#contact',
						),
						'faq_first_open' => array(
							'type'    => 'checkbox',
							'label'   => __( 'Open the first question by default', 'riaz-portfolio' ),
							'default' => true,
						),
						'faq_count'      => array(
							'type'    => 'number',
							'label'   => __( 'Maximum questions', 'riaz-portfolio' ),
							'default' => 12,
							'min'     => 1,
							'max'     => 50,
						),
					)
				),
			),
			'riaz_cta'             => array(
				'panel'       => 'riaz_home',
				'title'       => __( 'Call To Action', 'riaz-portfolio' ),
				'description' => __( 'The closing banner. It also appears at the end of single project, case study and service pages.', 'riaz-portfolio' ),
				'partial'     => 'cta',
				'fields'      => array(
					'cta_eyebrow'    => array(
						'type'    => 'text',
						'label'   => __( 'Eyebrow label', 'riaz-portfolio' ),
						'default' => __( 'Your next chapter', 'riaz-portfolio' ),
					),
					'cta_title'      => array(
						'type'    => 'text',
						'label'   => __( 'Title', 'riaz-portfolio' ),
						'default' => __( 'Ready to grow', 'riaz-portfolio' ),
					),
					'cta_highlight'  => array(
						'type'    => 'text',
						'label'   => __( 'Title highlight', 'riaz-portfolio' ),
						'default' => __( 'with purpose?', 'riaz-portfolio' ),
					),
					'cta_text'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Description', 'riaz-portfolio' ),
						'default' => __( "Let's start with a conversation about what you're building and where you want to take it.", 'riaz-portfolio' ),
					),
					'cta_btn_label'  => array(
						'type'    => 'text',
						'label'   => __( 'Button label', 'riaz-portfolio' ),
						'default' => __( 'Explore the approach', 'riaz-portfolio' ),
					),
					'cta_btn_url'    => array(
						'type'        => 'url',
						'label'       => __( 'Button link', 'riaz-portfolio' ),
						'description' => __( 'Tip: use mailto:you@example.com or a booking link.', 'riaz-portfolio' ),
						'default'     => '#approach',
					),
					'cta_btn2_label' => array(
						'type'    => 'text',
						'label'   => __( 'Second button label (optional)', 'riaz-portfolio' ),
						'default' => '',
					),
					'cta_btn2_url'   => array(
						'type'    => 'url',
						'label'   => __( 'Second button link', 'riaz-portfolio' ),
						'default' => '',
					),
					'cta_image_show' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show photo', 'riaz-portfolio' ),
						'default' => true,
					),
					'cta_image'      => array(
						'type'        => 'image',
						'label'       => __( 'Photo', 'riaz-portfolio' ),
						'description' => __( 'Defaults to the hero portrait.', 'riaz-portfolio' ),
						'default'     => 0,
					),
					'cta_on_singles' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show on single project, case study and service pages', 'riaz-portfolio' ),
						'default' => true,
					),
				),
			),
			'riaz_contact_section' => array(
				'panel'       => 'riaz_home',
				'title'       => __( 'Contact Section', 'riaz-portfolio' ),
				'description' => __( 'Contact details and a form. Enable it under Section Order & Visibility. Details come from Theme Options → Contact Details.', 'riaz-portfolio' ),
				'partial'     => 'contact',
				'fields'      => $heading_fields(
					'contact',
					array(
						__( 'Get in touch', 'riaz-portfolio' ),
						__( "Let's start a", 'riaz-portfolio' ),
						__( 'conversation', 'riaz-portfolio' ),
						__( 'Tell me a little about your business and where you want to take it.', 'riaz-portfolio' ),
					)
				),
			),

			/* ---------- Theme options ---------- */
			'riaz_colors'          => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Brand Colors', 'riaz-portfolio' ),
				'fields' => array(
					'color_primary'    => array(
						'type'      => 'color',
						'label'     => __( 'Primary', 'riaz-portfolio' ),
						'default'   => '#1761cf',
						'css_var'   => '--rp-primary',
						'transport' => 'postMessage',
					),
					'color_accent'     => array(
						'type'      => 'color',
						'label'     => __( 'Accent', 'riaz-portfolio' ),
						'default'   => '#2f80ff',
						'css_var'   => '--rp-accent',
						'transport' => 'postMessage',
					),
					'color_light'      => array(
						'type'      => 'color',
						'label'     => __( 'Light accent (on dark backgrounds)', 'riaz-portfolio' ),
						'default'   => '#8cc0ff',
						'css_var'   => '--rp-accent-light',
						'transport' => 'postMessage',
					),
					'color_dark'       => array(
						'type'      => 'color',
						'label'     => __( 'Dark (hero, process)', 'riaz-portfolio' ),
						'default'   => '#0a2a66',
						'css_var'   => '--rp-dark',
						'transport' => 'postMessage',
					),
					'color_darker'     => array(
						'type'      => 'color',
						'label'     => __( 'Darkest (footer, gradients)', 'riaz-portfolio' ),
						'default'   => '#050f2c',
						'css_var'   => '--rp-darker',
						'transport' => 'postMessage',
					),
					'color_text'       => array(
						'type'      => 'color',
						'label'     => __( 'Text', 'riaz-portfolio' ),
						'default'   => '#0b1630',
						'css_var'   => '--rp-ink',
						'transport' => 'postMessage',
					),
					'color_text_muted' => array(
						'type'      => 'color',
						'label'     => __( 'Muted text', 'riaz-portfolio' ),
						'default'   => '#56627a',
						'css_var'   => '--rp-ink-soft',
						'transport' => 'postMessage',
					),
					'color_surface'    => array(
						'type'      => 'color',
						'label'     => __( 'Section background tint', 'riaz-portfolio' ),
						'default'   => '#f5f8fd',
						'css_var'   => '--rp-surface',
						'transport' => 'postMessage',
					),
				),
			),
			'riaz_typography'      => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Typography', 'riaz-portfolio' ),
				'fields' => array(
					'font_body'      => array(
						'type'        => 'select',
						'label'       => __( 'Body font', 'riaz-portfolio' ),
						'description' => __( 'Roboto is self-hosted. Other fonts load from Google Fonts.', 'riaz-portfolio' ),
						'default'     => 'roboto',
						'choices'     => riaz_font_choices(),
					),
					'font_heading'   => array(
						'type'    => 'select',
						'label'   => __( 'Heading font', 'riaz-portfolio' ),
						'default' => 'inherit',
						'choices' => array_merge( array( 'inherit' => __( 'Same as body', 'riaz-portfolio' ) ), riaz_font_choices() ),
					),
					'font_size'      => array(
						'type'    => 'range',
						'label'   => __( 'Base font size (px)', 'riaz-portfolio' ),
						'default' => 16,
						'min'     => 14,
						'max'     => 20,
						'step'    => 1,
					),
					'heading_weight' => array(
						'type'    => 'select',
						'label'   => __( 'Heading weight', 'riaz-portfolio' ),
						'default' => '700',
						'choices' => array(
							'500' => __( 'Medium (500)', 'riaz-portfolio' ),
							'600' => __( 'Semi-bold (600)', 'riaz-portfolio' ),
							'700' => __( 'Bold (700)', 'riaz-portfolio' ),
							'800' => __( 'Extra-bold (800)', 'riaz-portfolio' ),
						),
					),
				),
			),
			'riaz_header'          => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Header', 'riaz-portfolio' ),
				'fields' => array(
					'header_logo_info' => array(
						'type'        => 'info',
						'label'       => __( 'Logo', 'riaz-portfolio' ),
						'description' => __( 'Upload your logo in Site Identity. Without a logo the site title is shown.', 'riaz-portfolio' ),
					),
					'logo_width'       => array(
						'type'    => 'range',
						'label'   => __( 'Logo width (px)', 'riaz-portfolio' ),
						'default' => 136,
						'min'     => 60,
						'max'     => 260,
						'step'    => 2,
					),
					'logo_pill'        => array(
						'type'        => 'checkbox',
						'label'       => __( 'White pill behind the logo', 'riaz-portfolio' ),
						'description' => __( 'Keeps dark logos readable over the dark hero.', 'riaz-portfolio' ),
						'default'     => true,
					),
					'header_sticky'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Sticky header', 'riaz-portfolio' ),
						'default' => true,
					),
					'header_cta_show'  => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show header button', 'riaz-portfolio' ),
						'default' => true,
					),
					'header_cta_label' => array(
						'type'    => 'text',
						'label'   => __( 'Header button label', 'riaz-portfolio' ),
						'default' => __( 'Contact', 'riaz-portfolio' ),
					),
					'header_cta_url'   => array(
						'type'    => 'url',
						'label'   => __( 'Header button link', 'riaz-portfolio' ),
						'default' => '#contact',
					),
				),
			),
			'riaz_footer'          => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Footer', 'riaz-portfolio' ),
				'fields' => array(
					'footer_brand'         => array(
						'type'        => 'text',
						'label'       => __( 'Footer brand name', 'riaz-portfolio' ),
						'description' => __( 'Leave empty to use the site title.', 'riaz-portfolio' ),
						'default'     => '',
					),
					'footer_about'         => array(
						'type'    => 'textarea',
						'label'   => __( 'Footer description', 'riaz-portfolio' ),
						'default' => __( 'Your partner in digital growth. Strategy, search visibility, and useful content, built with intention.', 'riaz-portfolio' ),
					),
					'footer_col1_title'    => array(
						'type'        => 'text',
						'label'       => __( 'Column 1 title', 'riaz-portfolio' ),
						'description' => __( 'Links come from the "Footer — Column 1" menu.', 'riaz-portfolio' ),
						'default'     => __( 'Explore', 'riaz-portfolio' ),
					),
					'footer_col2_title'    => array(
						'type'        => 'text',
						'label'       => __( 'Column 2 title', 'riaz-portfolio' ),
						'description' => __( 'Links come from the "Footer — Column 2" menu, or your services.', 'riaz-portfolio' ),
						'default'     => __( 'Focus', 'riaz-portfolio' ),
					),
					'footer_col3_title'    => array(
						'type'    => 'text',
						'label'   => __( 'Contact column title', 'riaz-portfolio' ),
						'default' => __( 'Connect', 'riaz-portfolio' ),
					),
					'footer_copyright'     => array(
						'type'        => 'text',
						'label'       => __( 'Copyright text', 'riaz-portfolio' ),
						'description' => __( 'Use {year} and {site} as placeholders.', 'riaz-portfolio' ),
						'default'     => __( '© {year} {site}. All rights reserved.', 'riaz-portfolio' ),
					),
					'footer_back_to_top'   => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show "Back to top" link', 'riaz-portfolio' ),
						'default' => true,
					),
					'footer_wordmark'      => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show large wordmark', 'riaz-portfolio' ),
						'default' => true,
					),
					'footer_wordmark_text' => array(
						'type'        => 'text',
						'label'       => __( 'Wordmark text', 'riaz-portfolio' ),
						'description' => __( 'Leave empty to use the brand name.', 'riaz-portfolio' ),
						'default'     => '',
					),
				),
			),
			'riaz_contact'         => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Contact Details', 'riaz-portfolio' ),
				'fields' => array(
					'contact_email'          => array(
						'type'    => 'email',
						'label'   => __( 'Email', 'riaz-portfolio' ),
						'default' => '',
					),
					'contact_phone'          => array(
						'type'    => 'text',
						'label'   => __( 'Phone', 'riaz-portfolio' ),
						'default' => '',
					),
					'contact_address'        => array(
						'type'    => 'textarea',
						'label'   => __( 'Address / location', 'riaz-portfolio' ),
						'default' => '',
					),
					'contact_hours'          => array(
						'type'    => 'text',
						'label'   => __( 'Availability', 'riaz-portfolio' ),
						'default' => '',
					),
					'contact_form_shortcode' => array(
						'type'        => 'text',
						'label'       => __( 'Form plugin shortcode (optional)', 'riaz-portfolio' ),
						'description' => __( 'Paste a Contact Form 7, WPForms, Fluent Forms or Gravity Forms shortcode to use it instead of the built-in form (which needs the Riaz Portfolio Core plugin).', 'riaz-portfolio' ),
						'default'     => '',
					),
					'contact_form_builtin'   => array(
						'type'    => 'checkbox',
						'label'   => __( 'Use the built-in contact form', 'riaz-portfolio' ),
						'default' => true,
					),
					'contact_form_to'        => array(
						'type'        => 'email',
						'label'       => __( 'Send form messages to', 'riaz-portfolio' ),
						'description' => __( 'Defaults to the contact email, then the site admin email.', 'riaz-portfolio' ),
						'default'     => '',
					),
					'contact_form_note'      => array(
						'type'    => 'text',
						'label'   => __( 'Note under the form', 'riaz-portfolio' ),
						'default' => __( 'Your details are only used to reply to your message.', 'riaz-portfolio' ),
					),
				),
			),
			'riaz_social'          => array(
				'panel'       => 'riaz_options',
				'title'       => __( 'Social Links', 'riaz-portfolio' ),
				'description' => __( 'Profiles appear in the footer and contact section, and are added to the schema "sameAs" list.', 'riaz-portfolio' ),
				'fields'      => riaz_social_fields(),
			),
			'riaz_layout'          => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Layout & Animation', 'riaz-portfolio' ),
				'fields' => array(
					'container_width'    => array(
						'type'    => 'range',
						'label'   => __( 'Content width (px)', 'riaz-portfolio' ),
						'default' => 1280,
						'min'     => 960,
						'max'     => 1520,
						'step'    => 20,
					),
					'section_spacing'    => array(
						'type'    => 'select',
						'label'   => __( 'Section spacing', 'riaz-portfolio' ),
						'default' => 'normal',
						'choices' => array(
							'compact'  => __( 'Compact', 'riaz-portfolio' ),
							'normal'   => __( 'Normal', 'riaz-portfolio' ),
							'spacious' => __( 'Spacious', 'riaz-portfolio' ),
						),
					),
					'anim_enable'        => array(
						'type'        => 'checkbox',
						'label'       => __( 'Enable animations', 'riaz-portfolio' ),
						'description' => __( 'Scroll reveals, floating cards, marquee and glows. Visitors who prefer reduced motion never see them.', 'riaz-portfolio' ),
						'default'     => true,
					),
					'cursor_enable'      => array(
						'type'        => 'checkbox',
						'label'       => __( 'Custom animated cursor', 'riaz-portfolio' ),
						'description' => __( 'Desktop mouse users only.', 'riaz-portfolio' ),
						'default'     => true,
					),
					'progress_enable'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Scroll progress bar', 'riaz-portfolio' ),
						'default' => true,
					),
					'banner_enable'      => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show the page banner on inner pages', 'riaz-portfolio' ),
						'description' => __( 'Can also be turned off per page in the editor.', 'riaz-portfolio' ),
						'default'     => true,
					),
					'breadcrumbs_enable' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show breadcrumbs', 'riaz-portfolio' ),
						'default' => true,
					),
				),
			),
			'riaz_blog'            => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Blog', 'riaz-portfolio' ),
				'fields' => array(
					'blog_eyebrow'    => array(
						'type'    => 'text',
						'label'   => __( 'Blog banner label', 'riaz-portfolio' ),
						'default' => __( 'Insights', 'riaz-portfolio' ),
					),
					'blog_text'       => array(
						'type'    => 'textarea',
						'label'   => __( 'Blog banner description', 'riaz-portfolio' ),
						'default' => __( 'Notes on digital strategy, search visibility, and content that connects.', 'riaz-portfolio' ),
					),
					'blog_layout'     => array(
						'type'    => 'select',
						'label'   => __( 'Archive layout', 'riaz-portfolio' ),
						'default' => 'grid',
						'choices' => array(
							'grid' => __( 'Grid', 'riaz-portfolio' ),
							'list' => __( 'List', 'riaz-portfolio' ),
						),
					),
					'blog_sidebar'    => array(
						'type'        => 'checkbox',
						'label'       => __( 'Show sidebar on blog pages', 'riaz-portfolio' ),
						'description' => __( 'Uses the "Blog Sidebar" widget area when it has widgets.', 'riaz-portfolio' ),
						'default'     => false,
					),
					'blog_share'      => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show share links on posts', 'riaz-portfolio' ),
						'default' => true,
					),
					'blog_author_box' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show author box on posts', 'riaz-portfolio' ),
						'default' => true,
					),
					'blog_related'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show related posts', 'riaz-portfolio' ),
						'default' => true,
					),
				),
			),
			'riaz_performance'     => array(
				'panel'  => 'riaz_options',
				'title'  => __( 'Performance', 'riaz-portfolio' ),
				'fields' => array(
					'perf_block_css'    => array(
						'type'    => 'checkbox',
						'label'   => __( 'Load block styles only for blocks on the page', 'riaz-portfolio' ),
						'default' => true,
					),
					'perf_preload_font' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Preload the main font file', 'riaz-portfolio' ),
						'default' => true,
					),
				),
			),
		),
	);

	/**
	 * Filter the full Customizer schema so child themes can add or change options.
	 *
	 * @param array $schema Schema array.
	 */
	$schema = apply_filters( 'riaz_customizer_schema', $schema );

	// Only cache once translations are available.
	if ( did_action( 'init' ) ) {
		$cache = $schema;
	}
	return $schema;
}

/**
 * Flat map of setting key => field definition.
 *
 * @return array
 */
function riaz_customizer_fields() {
	static $fields = null;
	if ( null !== $fields ) {
		return $fields;
	}
	$flat = array();
	foreach ( riaz_customizer_schema()['sections'] as $section_id => $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$field['section'] = $section_id;
			$field['partial'] = isset( $section['partial'] ) ? $section['partial'] : '';
			$flat[ $key ]     = $field;
		}
	}
	if ( did_action( 'init' ) ) {
		$fields = $flat;
	}
	return $flat;
}

/**
 * Default value for a setting. Repeaters and the section list are stored as JSON.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function riaz_default( $key ) {
	$fields = riaz_customizer_fields();
	if ( ! isset( $fields[ $key ] ) || ! array_key_exists( 'default', $fields[ $key ] ) ) {
		return '';
	}
	$default = $fields[ $key ]['default'];
	if ( in_array( $fields[ $key ]['type'], array( 'repeater', 'sortable' ), true ) ) {
		return wp_json_encode( $default );
	}
	return $default;
}

/**
 * Fonts offered in the typography options.
 *
 * @return array<string,string>
 */
function riaz_font_choices() {
	$choices = array( 'roboto' => 'Roboto ' . __( '(self-hosted)', 'riaz-portfolio' ) );
	foreach ( riaz_google_fonts() as $slug => $font ) {
		$choices[ $slug ] = $font['name'];
	}
	$choices['system'] = __( 'System UI (no download)', 'riaz-portfolio' );
	return $choices;
}

/**
 * Google Fonts available as alternatives, with the axis spec requested.
 *
 * @return array<string,array{name:string,spec:string,fallback:string}>
 */
function riaz_google_fonts() {
	return array(
		'inter'             => array(
			'name'     => 'Inter',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'dm-sans'           => array(
			'name'     => 'DM Sans',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'manrope'           => array(
			'name'     => 'Manrope',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'plus-jakarta-sans' => array(
			'name'     => 'Plus Jakarta Sans',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'outfit'            => array(
			'name'     => 'Outfit',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'sora'              => array(
			'name'     => 'Sora',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'space-grotesk'     => array(
			'name'     => 'Space Grotesk',
			'spec'     => 'wght@300..700',
			'fallback' => 'sans-serif',
		),
		'montserrat'        => array(
			'name'     => 'Montserrat',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'poppins'           => array(
			'name'     => 'Poppins',
			'spec'     => 'wght@300;400;500;600;700;800',
			'fallback' => 'sans-serif',
		),
		'open-sans'         => array(
			'name'     => 'Open Sans',
			'spec'     => 'wght@300..800',
			'fallback' => 'sans-serif',
		),
		'lora'              => array(
			'name'     => 'Lora',
			'spec'     => 'wght@400..700',
			'fallback' => 'serif',
		),
		'playfair-display'  => array(
			'name'     => 'Playfair Display',
			'spec'     => 'wght@400..800',
			'fallback' => 'serif',
		),
	);
}

/**
 * Social link URL fields.
 *
 * @return array
 */
function riaz_social_fields() {
	$fields = array();
	foreach ( riaz_social_networks() as $network => $label ) {
		$fields[ 'social_' . $network ] = array(
			'type'    => 'url',
			'label'   => $label,
			'default' => '',
		);
	}
	return $fields;
}
