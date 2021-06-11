<?php
/**
 * Class that adds the metabox for Custom Layouts custom post type.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin
 */

/**
 * Class Hestia_Layouts_Metabox
 */
class Hestia_Layouts_Metabox {

	/**
	 * Custom layouts location.
	 *
	 * @var array
	 */
	private $layouts;

	/**
	 * Root rules.
	 *
	 * @var array
	 */
	private $root_ruleset;

	/**
	 * End rules.
	 *
	 * @var array
	 */
	private $end_ruleset;

	/**
	 * Ruleset map.
	 *
	 * @var array
	 */
	private $ruleset_map;

	/**
	 * Conditional display instance.
	 *
	 * @var Hestia_Conditional_Display
	 */
	private $conditional_display = null;

	/**
	 * Conditional logic value.
	 *
	 * @var string
	 */
	private $conditional_logic_value;

	/**
	 * Availabele dynamic tags ma[.
	 *
	 * @var array
	 */
	public static $magic_tags = array(
		'archive_taxonomy' => array(
			'category' => array( '{title}', '{description}' ),
			'post_tag' => array( '{title}', '{description}' ),
		),
		'archive_type'     => array(
			'author' => array( '{author}', '{author_description}', '{author_avatar}' ),
			'date'   => array( '{date}' ),
		),
	);

	/**
	 * Setup class properties.
	 */
	public function setup_props() {
		$this->conditional_display = new Hestia_Conditional_Display();
		$this->layouts             = array(
			'header'      => __( 'Header', 'hestia-pro' ),
			'page_header' => __( 'Page Header', 'hestia-pro' ),
			'footer'      => __( 'Footer', 'hestia-pro' ),
			'hooks'       => __( 'Hooks', 'hestia-pro' ),
			'not_found'   => __( '404 Page', 'hestia-pro' ),
		);

		if ( defined( 'PWA_VERSION' ) ) {
			$this->layouts['offline']      = __( 'Offline Page', 'hestia-pro' );
			$this->layouts['server_error'] = __( 'Internal Server Error Page', 'hestia-pro' );
		}

		$this->root_ruleset = $this->conditional_display->get_root_ruleset();
		$this->end_ruleset  = $this->conditional_display->get_end_ruleset();
		$this->ruleset_map  = $this->conditional_display->get_ruleset_map();
	}

	/**
	 * Initialize function.
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'init', array( $this, 'setup_props' ), 999 );
		add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post_data' ) );
	}

	/**
	 * Create meta box.
	 */
	public function create_meta_box() {
		$post_type = get_post_type();
		if ( $post_type !== 'hestia_layouts' ) {
			return;
		}
		add_meta_box(
			'custom-layouts-settings',
			__( 'Custom Layout Settings', 'hestia-pro' ),
			array( $this, 'meta_box_markup' ),
			$post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Save meta fields.
	 *
	 * @param int $post_id Post id.
	 */
	public function save_post_data( $post_id ) {
		$this->save_layout( $post_id, $_POST );
		$this->save_hook( $post_id, $_POST );
		$this->save_priority( $post_id, $_POST );
		$this->save_conditional_rules( $post_id, $_POST );
	}

	/**
	 * Save layout meta option.
	 *
	 * @param int   $post_id Post id.
	 * @param array $post    Post array.
	 *
	 * @return bool
	 */
	private function save_layout( $post_id, $post ) {
		if ( ! array_key_exists( 'hestia-custom-layout', $post ) ) {
			return false;
		}

		$choices = array( 'header', 'footer', 'hooks', 'page_header', 'not_found', 'offline', 'server_error' );
		if ( ! in_array( $post['hestia-custom-layout'], $choices, true ) ) {
			return false;
		}
		update_post_meta(
			$post_id,
			'custom-layout-options-layout',
			$post['hestia-custom-layout']
		);

		return true;
	}

	/**
	 * Save hook meta option.
	 *
	 * @param int   $post_id Post id.
	 * @param array $post    Post array.
	 *
	 * @return bool
	 */
	private function save_hook( $post_id, $post ) {
		if ( ! array_key_exists( 'hestia-custom-hook', $post ) ) {
			return false;
		}

		$hooks           = Hestia_Custom_Layouts_Module::$hooks;
		$available_hooks = array();
		foreach ( $hooks as $list_of_hooks ) {
			$available_hooks = array_merge( $available_hooks, $list_of_hooks );
		}
		if ( ! in_array( $post['hestia-custom-hook'], $available_hooks, true ) ) {
			return false;
		}

		update_post_meta(
			$post_id,
			'custom-layout-options-hook',
			$post['hestia-custom-hook']
		);

		return true;
	}

	/**
	 * Save priority meta option.
	 *
	 * @param int   $post_id Post id.
	 * @param array $post    Post array.
	 *
	 * @return bool
	 */
	private function save_priority( $post_id, $post ) {
		if ( ! array_key_exists( 'hestia-custom-priority', $post ) ) {
			return false;
		}
		update_post_meta(
			$post_id,
			'custom-layout-options-priority',
			(int) $post['hestia-custom-priority']
		);

		return true;
	}

	/**
	 * Save the conditional rules.
	 *
	 * @param int   $post_id post ID.
	 * @param array $post    $_POST variables.
	 */
	private function save_conditional_rules( $post_id, $post ) {
		if ( empty( $post['custom-layout-conditional-logic'] ) ) {
			return;
		}
		update_post_meta(
			$post_id,
			'custom-layout-conditional-logic',
			$post['custom-layout-conditional-logic']
		);
	}

	/**
	 * Meta box HTML.
	 *
	 * @param \WP_Post $post Post.
	 */
	public function meta_box_markup( $post ) {
		$this->conditional_logic_value = $this->get_conditional_logic_value( $post );
		$is_header_layout              = get_post_meta( $post->ID, 'header-layout', true );
		$layout                        = get_post_meta( $post->ID, 'custom-layout-options-layout', true );
		echo '<table class="hestia-custom-layouts-settings ' . ( $is_header_layout ? 'hidden' : '' ) . ' ">';
		echo '<tr>';
		echo '<td>';
		echo '<label>' . esc_html__( 'Layout', 'hestia-pro' ) . '</label>';
		echo '</td>';
		echo '<td>';
		echo '<select id="hestia-custom-layout" name="hestia-custom-layout">';
		echo '<option value="0">' . esc_html__( 'Select', 'hestia-pro' ) . '</option>';
		foreach ( $this->layouts as $layout_value => $layout_name ) {
			echo '<option ' . selected( $layout_value, $layout ) . ' value="' . esc_attr( $layout_value ) . '">' . esc_html( $layout_name ) . '</option>';
		}
		echo '</select>';
		echo '</td>';
		echo '</tr>';

		$hooks = Hestia_Custom_Layouts_Module::$hooks;
		$hook  = get_post_meta( $post->ID, 'custom-layout-options-hook', true );
		$class = ( $layout !== 'hooks' ? 'hidden' : '' );
		if ( ! empty( $hooks ) ) {
			echo '<tr class="' . esc_attr( $class ) . '">';
			echo '<td>';
			echo '<label>' . esc_html__( 'Hooks', 'hestia-pro' ) . '</label>';
			echo '</td>';
			echo '<td>';
			echo '<select id="hestia-custom-hook" name="hestia-custom-hook">';
			foreach ( $hooks as $hook_cat_slug => $hook_cat ) {
				echo '<optgroup label="' . esc_html( ucwords( $hook_cat_slug ) ) . '">';
				foreach ( $hook_cat as $hook_value ) {
					$hook_label = Hestia_View_Hooks::beautify_hook( $hook_value );
					echo '<option ' . selected( $hook_value, $hook ) . ' value="' . esc_attr( $hook_value ) . '">' . esc_html( $hook_label ) . '</option>';
				}
				echo '</optgroup>';
			}
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			$priority = get_post_meta( $post->ID, 'custom-layout-options-priority', true );
			if ( empty( $priority ) && $priority !== 0 ) {
				$priority = 10;
			}
			echo '<tr class="' . esc_attr( $class ) . '">';
			echo '<td>';
			echo '<label>' . esc_html__( 'Priority', 'hestia-pro' ) . '</label>';
			echo '</td>';
			echo '<td>';
			echo '<input value="' . esc_attr( $priority ) . '" type="number" id="hestia-custom-priority" name="hestia-custom-priority" min="1" max="150" step="1"/>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

		$this->render_conditional_logic_setup( $post );
		$this->render_rule_group_template();
		?>
		<input type="hidden" class="hestia-conditional-meta-collector" name="custom-layout-conditional-logic" id="custom-layout-conditional-logic" value="<?php echo esc_attr( $this->conditional_logic_value ); ?>"/>
		<?php
	}

	/**
	 * Get the conditional logic meta value.
	 *
	 * @param \WP_Post $post the post object.
	 *
	 * @return mixed|string
	 */
	private function get_conditional_logic_value( $post ) {
		$value = get_post_meta( $post->ID, 'custom-layout-conditional-logic', true );

		if ( empty( $value ) ) {
			$value = '{}';
		}

		return $value;
	}

	/**
	 * Render the conditional logic.
	 */
	private function render_conditional_logic_setup( $post ) {
		$value            = json_decode( $this->conditional_logic_value, true );
		$layout           = get_post_meta( $post->ID, 'custom-layout-options-layout', true );
		$class            = ( empty( $layout ) || in_array(
			$layout,
			array(
				'not_found',
				'offline',
				'server_error',
			),
			true
		) ) ? 'hidden' : '';
		$is_header_layout = get_post_meta( $post->ID, 'header-layout', true );
		if ( $is_header_layout ) {
			$class = '';
		}
		?>
		<div id="hestia-conditional" class="<?php echo esc_attr( $class ); ?>">
			<div>
				<label><?php echo esc_html__( 'Conditional Logic', 'hestia-pro' ); ?></label>
				<p class="<?php echo $is_header_layout ? 'hidden' : ''; ?>">
					<span class="dashicons dashicons-info"></span>
					<i>
						<?php echo esc_html__( 'If no conditional logic is selected, the Custom Layout will be applied site-wide.', 'hestia-pro' ); ?>
					</i>
				</p>
			</div>
			<div class="hestia-rules-wrapper">
				<div class="hestia-rule-groups">
					<?php
					if ( ! is_array( $value ) || empty( $value ) ) {
						$this->display_magic_tags();
						$this->render_rule_group();
					} else {
						$index = 0;
						foreach ( $value as $rule_group ) {
							$magic_tags = $this->get_magic_tags( $rule_group );
							$this->display_magic_tags( $magic_tags, $index );
							$this->render_rule_group( $rule_group );
							$index ++;
						}
					}
					?>
				</div>
				<div class="rule-group-actions">
					<button class="button button-primary hestia-add-rule-group"><?php esc_html_e( 'Add Rule Group', 'hestia-pro' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get magic tags based on current rules.
	 *
	 * @param array $rule_group Set of rules.
	 *
	 * @return array
	 */
	private function get_magic_tags( $rule_group ) {
		$all_magic_tags = array();
		foreach ( $rule_group as $rule ) {
			if ( $rule['condition'] !== '===' ) {
				return array();
			}

			if ( empty( $rule['root'] ) || empty( $rule['end'] ) ) {
				return array();
			}

			if ( ! array_key_exists( $rule['root'], self::$magic_tags ) ) {
				return array();
			}

			$end_array = self::$magic_tags[ $rule['root'] ];
			if ( ! array_key_exists( $rule['end'], $end_array ) ) {
				return array();
			}

			$all_magic_tags = array_merge( $all_magic_tags, $end_array[ $rule['end'] ] );
		}

		return $all_magic_tags;
	}

	/**
	 * Render rule group.
	 *
	 * @param array $rules The rules.
	 */
	private function render_rule_group( $rules = array() ) {
		if ( empty( $rules ) ) {
			$rules[] = array(
				'root'      => '',
				'condition' => '===',
				'end'       => '',
			);
		}
		?>
		<div class="hestia-rule-group-wrap">
			<div class="hestia-rule-group">
				<div class="hestia-group-inner">
					<?php foreach ( $rules as $rule ) { ?>
						<div class="individual-rule">
							<div class="rule-wrap root_rule">
								<select class="hestia-slim-select root-rule">
									<option value="" <?php echo $rule['root'] === '' ? 'selected' : ''; ?>><?php echo esc_html__( 'Select', 'hestia-pro' ); ?></option>
									<?php
									foreach ( $this->root_ruleset as $option_group_slug => $option_group ) {
										echo '<optgroup label="' . esc_attr( $option_group['label'] ) . '">';
										foreach ( $option_group['choices'] as $slug => $label ) {
											echo '<option value="' . esc_attr( $slug ) . '" ' . ( $slug === $rule['root'] ? 'selected' : '' ) . ' >' . esc_html( $label ) . '</option>';
										}
										echo '</optgroup>';
									}
									?>
								</select>
							</div>
							<div class="rule-wrap condition">
								<select class="hestia-slim-select condition-rule">
									<option value="==="
										<?php echo esc_attr( $rule['condition'] === '===' ? 'selected' : '' ); ?>>
										<?php echo esc_html__( 'is equal to', 'hestia-pro' ); ?></option>
									<option value="!=="
										<?php echo esc_attr( $rule['condition'] === '!==' ? 'selected' : '' ); ?>>
										<?php echo esc_html__( 'is not equal to', 'hestia-pro' ); ?></option>
								</select>
							</div>
							<div class="rule-wrap end_rule">
								<?php
								foreach ( $this->end_ruleset as $ruleset_slug => $options ) {
									$this->render_end_option( $ruleset_slug, $options, $rule['end'], $rule['root'] );
								}
								?>
							</div>
							<div class="actions-wrap">
								<button class="remove action button button-secondary">
									<i class="dashicons dashicons-no"></i>
								</button>
								<button class="duplicate action button button-primary">
									<i class="dashicons dashicons-plus"></i>
								</button>
							</div>
							<span class="operator and"><?php esc_html_e( 'AND', 'hestia-pro' ); ?></span>
						</div>
					<?php } ?>
				</div>
				<div class="rule-group-actions">
					<button class="button button-secondary hestia-remove-rule-group"><?php esc_html_e( 'Remove Rule Group', 'hestia-pro' ); ?></button>
				</div>
			</div>
			<span class="operator or"><?php esc_html_e( 'OR', 'hestia-pro' ); ?></span>
		</div>
		<?php
	}

	/**
	 * Display magic tags/
	 *
	 * @param array $magic_tags Array of magic tags.
	 *
	 * @return bool
	 */
	private function display_magic_tags( $magic_tags = '', $index = 0 ) {
		echo '<div class="hestia-magic-tags" id="hestia-magic-tags-group-' . $index . '">';
		if ( ! empty( $magic_tags ) ) {
			echo '<p>' . esc_html__( 'You can add the following tags in your template:', 'hestia-pro' ) . '</p>';
			echo '<ul class="hestia-available-tags-list">';
			foreach ( $magic_tags as $magic_tag ) {
				echo '<li>' . $magic_tag . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';

		return true;
	}

	/**
	 * Render the end option.
	 *
	 * @param string $slug     the ruleset slug.
	 * @param array  $args     the ruleset options.
	 * @param string $end_val  the ruleset end value.
	 * @param string $root_val the ruleset root value.
	 */
	private function render_end_option( $slug, $args, $end_val, $root_val ) {
		?>
		<div class="single-end-rule <?php echo esc_attr( join( ' ', $this->ruleset_map[ $slug ] ) ); ?>">
			<select name="<?php echo esc_attr( $slug ); ?>"
					class="hestia-slim-select end-rule">
				<option value="" <?php echo esc_attr( $end_val === '' ? 'selected' : '' ); ?>><?php echo esc_html__( 'Select', 'hestia-pro' ); ?></option>
				<?php
				switch ( $slug ) {
					case 'terms':
						foreach ( $args as $post_type_slug => $taxonomies ) {
							foreach ( $taxonomies as $taxonomy ) {
								if ( ! is_array( $taxonomy['terms'] ) || empty( $taxonomy['terms'] ) ) {
									continue;
								}
								echo '<optgroup label="' . $taxonomy['nicename'] . ' (' . $post_type_slug . ' - ' . $taxonomy['name'] . ')">';
								foreach ( $taxonomy['terms'] as $term ) {
									if ( ! $term instanceof \WP_Term ) {
										continue;
									}
									echo '<option value="' . esc_attr( $taxonomy['name'] ) . '|' . esc_attr( $term->slug ) . '" ' . esc_attr( ( $taxonomy['name'] ) . '|' . esc_attr( $term->slug ) === $end_val ? 'selected' : '' ) . '>' . esc_html( $term->name ) . '</option>';
								}
							}
							echo '</optgroup>';
						}
						break;
					case 'taxonomies':
						foreach ( $args as $post_type_slug => $taxonomies ) {
							foreach ( $taxonomies as $taxonomy ) {
								if ( ! is_array( $taxonomy['terms'] ) || empty( $taxonomy['terms'] ) ) {
									continue;
								}
								echo '<option value="' . esc_attr( $taxonomy['name'] ) . '" ' . esc_attr( (string) $taxonomy['name'] === $end_val ? 'selected' : '' ) . '>' . $taxonomy['nicename'] . ' (' . $post_type_slug . ' - ' . $taxonomy['name'] . ')' . '</option>';
							}
						}
						break;
					default:
						foreach ( $args as $value => $label ) {
							echo '<option value="' . esc_attr( $value ) . '" ' . esc_attr( (string) $value === $end_val ? 'selected' : '' ) . '>' . esc_html( $label ) . '</option>';
						}
						break;
				}
				?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render the rule group template.
	 */
	private function render_rule_group_template() {
		?>
		<div class="hestia-rule-group-template">
			<?php $this->render_rule_group(); ?>
		</div>
		<?php
	}
}
